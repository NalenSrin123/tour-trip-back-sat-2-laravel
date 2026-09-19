<?php

namespace App\Http\Controllers;

use App\Models\Tour_Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    // 1. GET ALL (Read All)
    public function index()
    {
        $images = Tour_Image::all();
        return response()->json($images, 200);
    }

    // 2. GET ONE (Read One)
    public function show($id)
    {
        $image = Tour_Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json($image, 200);
    }

    // 3. CREATE (Store New Image URL)
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'   => 'required',
            'image_url' => 'required|string',
        ]);

        $image = Tour_Image::create([
            'tour_id'   => $request->tour_id,
            'image_url' => $request->image_url,
        ]);

        return response()->json($image, 201);
    }

    // 4. UPDATE (Edit Image URL)
    public function update(Request $request, $id)
    {
        $image = Tour_Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $image->update($request->all());

        return response()->json($image, 200);
    }

    // 5. DELETE (Delete Record)
    public function destroy($id)
    {
        $image = Tour_Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $image->delete();

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}