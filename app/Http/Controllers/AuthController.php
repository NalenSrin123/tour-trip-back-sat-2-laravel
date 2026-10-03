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
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'phone'      => 'nullable|string',
        ]);

        try {
            $hashedPassword = Hash::make($data['password']);

            $userId = DB::table('users')->insertGetId([
                'role'          => 'customer',
                'name'          => $data['full_name'],
                'full_name'     => $data['full_name'],
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
            $token = $authUser->createToken('api-token')->plainTextToken;

            return $this->successResponse(
                [
                    'user'  => $user,
                    'token' => $token,
                ],
                'Login Successful',
                200,
                [
                    'token'   => $token,
                    'User'    => $user,
                    'Message' => 'Login Successful',
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
}
