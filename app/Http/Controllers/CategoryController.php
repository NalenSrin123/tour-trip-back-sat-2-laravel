<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Display all categories
    public function index(): JsonResponse
    {
        $categories = DB::table('categories')
            ->orderBy('category_id', 'desc')
            ->get();

        return $this->successResponse($categories, 'Categories retrieved successfully');
    }

    // Store new category
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $id = DB::table('categories')->insertGetId([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'category_id'); // <-- tells Postgres which sequence/column to use

        $category = DB::table('categories')->where('category_id', $id)->first();

        return $this->successResponse($category, 'Category created successfully.', 201);
    }

    // Show one category
    public function show($id): JsonResponse
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }

        return $this->successResponse($category, 'Category retrieved successfully');
    }

    // Update category
    public function update(Request $request, $id): JsonResponse
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::table('categories')
            ->where('category_id', $id)
            ->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_at' => now(),
            ]);

        $updated = DB::table('categories')->where('category_id', $id)->first();

        return $this->successResponse($updated, 'Category updated successfully.');
    }

    // Delete category
    public function destroy($id): JsonResponse
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }

        DB::table('categories')
            ->where('category_id', $id)
            ->delete();

        return $this->successResponse(null, 'Category deleted successfully.');
    }
}
