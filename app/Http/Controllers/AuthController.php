<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'phone'      => 'nullable|string',
        ]);

        try {
            $userId = DB::table('users')->insertGetId([
                'role'          => 'customer',
                'full_name'     => $data['full_name'],
                'email'         => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'phone'         => $data['phone'] ?? null,
                'is_active'     => true,
                'created_at'    => now(),
            ], 'user_id');

            $user = DB::table('users')->where('user_id', $userId)->first();

            return response()->json([
                'message' => 'Registered successfully',
                'user'    => $user,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Registration failed, please try again',
                'error'   => $th->getMessage(),
            ], 500); // <- status code here
        }
    }

    public function login(Request $request){
        $credentails = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $user = DB::table('users')->where('email', $credentails['email'])->first();

            if (!$user || !Hash::check($credentails['password'], $user->password_hash)){
                return response()->json([
                    'Massage' => 'Invalid email or password',
                ], 400);
            }

            if (!$user->is_active){
                return response()->json([
                    'Message' => 'Account is inactive',
                ], 403);
            }

            $authUser = \App\Models\User::find($user->user_id);
            $token = $authUser->createToken('api-token')->plainTextToken;

            return response()->json([
                'Message' => 'Login Successful',
                'User' => $user,
                'token' => $token,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'Message' => 'Login fail, please try again',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
