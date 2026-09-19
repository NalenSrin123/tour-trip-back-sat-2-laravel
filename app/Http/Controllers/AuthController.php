<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
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

            return response()->json([
                'message' => 'Registered successfully',
                'user'    => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Registration failed, please try again',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentails = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $user = DB::table('users')->where('email', $credentails['email'])->first();

            $passwordHash = $user?->password_hash ?? $user?->password;
            if (!$user || !Hash::check($credentails['password'], $passwordHash)) {
                return response()->json([
                    'Massage' => 'Invalid email or password',
                ], 400);
            }

            if (!$user->is_active) {
                return response()->json([
                    'Message' => 'Account is inactive',
                ], 403);
            }

            $user->user_id = $user->id;
            $authUser = User::find($user->id);
            $token = $authUser->createToken('api-token')->plainTextToken;

            return response()->json([
                'Message' => 'Login Successful',
                'User'    => $user,
                'token'   => $token,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => 'Login fail, please try again',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
