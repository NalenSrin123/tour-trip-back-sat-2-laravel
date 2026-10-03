<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    // List all profiles with user info
    public function index()
    {
        $profiles = UserProfile::with('user')->get();
        return response()->json($profiles, 200);
    }

    // Edit=Update user profile
    public function update(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update full_name, phone
        if ($request->has('full_name')) $user->full_name = $request->full_name;
        if ($request->has('phone')) $user->phone = $request->phone;
        $user->save();

        // Update date_of_birth, address, verify_status
        $profile = UserProfile::firstOrCreate(['user_id' => $user_id]);
        $profile->update($request->only(['date_of_birth', 'address', 'verify_status']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'data'    => $profile->load('user')
        ], 200);
    }
}