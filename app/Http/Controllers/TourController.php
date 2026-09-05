<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    // API for ទាញយកនិងស្វែងរកtour តាមឈ្មៅះ
    public function index(Request $request)
    {
        $query = Tour::query();

        // Search by name
        if ($request->has('search')) { 
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        //  Show 10 items for a page
        $tours = $query->paginate(10);

        return response()->json($tours, 200);
    }

    // API create new tour
    public function tourstore(Request $request)
    {
        
        $request->validate([
            'name' => 'required',
        ]);

        
        $tour = Tour::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($tour, 201);
    }

    // API update tour
    public function update(Request $request, $id)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], 404);
        }

        $request->validate([
            'name' => 'required',
        ]);

        $tour->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($tour, 200);
    }

    // API delete tour
    public function destroy($id)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], 404);
        }

        $tour->delete();

        return response()->json(['message' => 'Tour deleted successfully'], 200);
    }
}