<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with optional role filtering and search.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role if provided in query string (e.g., ?role=admin)
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        // Search by name or email if search parameter is provided (e.g., ?search=john)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $users = $query->get();

        return response()->json([
            'status' => 'success',
            'data'   => $users
        ], 200);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully!',
            'data'    => $user
        ], 201);
    }
}