<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Display all categories
    public function index()
    {
        $categories = DB::table('categories')
            ->orderBy('category_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    // Store new category
    public function store(Request $request)
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

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    // Show one category
    public function show($id)
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
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

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $updated,
        ]);
    }

    // Delete category
    public function destroy($id)
    {
        $category = DB::table('categories')
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        DB::table('categories')
            ->where('category_id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
