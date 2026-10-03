<?php

namespace App\Http\Controllers;

use App\Models\Tour_Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    // 1. GET ALL (Read All)
    public function index(): JsonResponse
    {
        $images = Tour_Image::all();
        return $this->successResponse($images, 'Images retrieved successfully');
    }

    // 2. GET ONE (Read One)
    public function show($id): JsonResponse
    {
        $image = Tour_Image::find($id);

        if (!$image) {
            return $this->errorResponse('Image not found', 404);
        }

        return $this->successResponse($image, 'Image retrieved successfully');
    }

    // 3. CREATE (Store New Image URL)
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tour_id'   => 'required',
            'image_url' => 'required|string',
        ]);

        $image = Tour_Image::create([
            'tour_id'   => $request->tour_id,
            'image_url' => $request->image_url,
        ]);

        return $this->successResponse($image, 'Image created successfully', 201);
    }

    // 4. UPDATE (Edit Image URL)
    public function update(Request $request, $id): JsonResponse
    {
        $image = Tour_Image::find($id);

        if (!$image) {
            return $this->errorResponse('Image not found', 404);
        }

        $image->update($request->all());

        return $this->successResponse($image, 'Image updated successfully');
    }

    // 5. DELETE (Delete Record)
    public function destroy($id): JsonResponse
    {
        $image = Tour_Image::find($id);

        if (!$image) {
            return $this->errorResponse('Image not found', 404);
        }

        $image->delete();

        return $this->successResponse(null, 'Deleted successfully');
    }
}