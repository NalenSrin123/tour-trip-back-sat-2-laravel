<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    // API for fetching and searching tours
    public function index(Request $request)
    {
        $query = Tour::query();

        // Search by title or description
        if ($request->has('search') && !empty($request->search)) { 
            $search = $request->search;
            $query->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
        }

        // Show 10 items for a page
        $tours = $query->paginate(10);

        return response()->json($tours, 200);
    }

    // API create new tour
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required_without:name|string|max:255',
            'name'             => 'sometimes|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'nullable|numeric|min:0',
            'duration_days'    => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'status'           => 'nullable|in:active,inactive',
            'category_id'      => 'nullable|integer',
            'destination_id'   => 'nullable|integer',
        ]);

        $title = $validated['title'] ?? $validated['name'] ?? null;

        $tour = Tour::create([
            'title'            => $title,
            'description'      => $validated['description'] ?? null,
            'price'            => $validated['price'] ?? 0,
            'duration_days'    => $validated['duration_days'] ?? 1,
            'max_participants' => $validated['max_participants'] ?? null,
            'status'           => $validated['status'] ?? 'active',
            'category_id'      => $validated['category_id'] ?? null,
            'destination_id'   => $validated['destination_id'] ?? null,
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

        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'name'             => 'sometimes|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'nullable|numeric|min:0',
            'duration_days'    => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'status'           => 'nullable|in:active,inactive',
            'category_id'      => 'nullable|integer',
            'destination_id'   => 'nullable|integer',
        ]);

        if (isset($validated['name']) && !isset($validated['title'])) {
            $validated['title'] = $validated['name'];
        }
        unset($validated['name']);

        $tour->update($validated);

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