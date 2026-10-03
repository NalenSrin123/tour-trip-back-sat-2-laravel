<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Create a new user (POST /api/users).
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Note: In App\Models\User, 'password' => 'hashed' is already in casts(),
        // so Laravel will automatically hash the password when assigned.
        $user = User::create($validated);

        return $this->successResponse(
            new UserResource($user),
            'User created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Update an existing user (PUT/PATCH /api/users/{user}).
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        // If password is not provided or null, remove it from the update payload
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->successResponse(
            new UserResource($user),
            'User updated successfully'
        );
    }
}