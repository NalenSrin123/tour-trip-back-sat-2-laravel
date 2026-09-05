<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Update an existing category.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::where('category_id', $id)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully.',
            'data'    => $category
        ], 200);
    }

    /**
     * Delete a category.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::where('category_id', $id)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}