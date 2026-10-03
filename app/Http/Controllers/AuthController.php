<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                  => 'nullable|string|max:255',
            'full_name'             => 'nullable|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'phone'                 => 'nullable|string',
        ]);

        $name = $data['full_name'] ?? $data['name'] ?? 'User';

        try {
            $hashedPassword = Hash::make($data['password']);

            $userId = DB::table('users')->insertGetId([
                'role'          => 'customer',
                'name'          => $name,
                'full_name'     => $name,
                'email'         => $data['email'],
                'password'      => $hashedPassword,
                'password_hash' => $hashedPassword,
                'phone'         => $data['phone'] ?? null,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $user = DB::table('users')->where('id', $userId)->first();
            if ($user) {
                $user->user_id = $user->id;
            }

            return $this->successResponse(
                $user,
                'Registered successfully',
                201,
                ['user' => $user]
            );
        } catch (Throwable $th) {
            return $this->errorResponse(
                'Registration failed, please try again',
                500,
                $th->getMessage()
            );
        }
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $user = DB::table('users')->where('email', $credentials['email'])->first();

            $passwordHash = $user?->password_hash ?? $user?->password;
            if (!$user || !Hash::check($credentials['password'], $passwordHash)) {
                return $this->errorResponse('Invalid email or password', 400);
            }

            if (!$user->is_active) {
                return $this->errorResponse('Account is inactive', 403);
            }

            $user->user_id = $user->id;
            $authUser = User::find($user->id);

            // Generate token (JWT if installed, otherwise Sanctum)
            $token = null;
            if (class_exists('Tymon\JWTAuth\Facades\JWTAuth')) {
                try {
                    $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($authUser);
                } catch (Throwable $e) {
                    $token = $authUser->createToken('api-token')->plainTextToken;
                }
            } else {
                $token = $authUser->createToken('api-token')->plainTextToken;
            }

            return $this->successResponse(
                [
                    'user'  => $user,
                    'token' => $token,
                ],
                'Login Successful',
                200,
                [
                    'token'        => $token,
                    'access_token' => $token,
                    'token_type'   => 'bearer',
                    'User'         => $user,
                    'Message'      => 'Login Successful',
                ]
            );
        } catch (Throwable $th) {
            return $this->errorResponse(
                'Login fail, please try again',
                500,
                $th->getMessage()
            );
        }
    }

    public function me(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        return $this->successResponse($user, 'User profile retrieved successfully');
    }

    public function logout(): JsonResponse
    {
        if (class_exists('Tymon\JWTAuth\Facades\JWTAuth')) {
            try {
                if ($jwt = \Tymon\JWTAuth\Facades\JWTAuth::getToken()) {
                    \Tymon\JWTAuth\Facades\JWTAuth::invalidate($jwt);
                }
            } catch (Throwable $e) {
                // ignore
            }
        }

        return $this->successResponse(null, 'Successfully logged out.');
    }

    public function refresh(): JsonResponse
    {
        if (class_exists('Tymon\JWTAuth\Facades\JWTAuth')) {
            try {
                $newToken = \Tymon\JWTAuth\Facades\JWTAuth::refresh();
                return $this->successResponse([
                    'access_token' => $newToken,
                    'token_type'   => 'bearer',
                ], 'Token refreshed successfully');
            } catch (Throwable $e) {
                return $this->errorResponse('Token refresh failed', 401, $e->getMessage());
            }
        }

        return $this->errorResponse('JWT not supported', 400);
    }
}
