<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    // API for fetching and searching tours
    public function index(Request $request): JsonResponse
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

        return $this->successResponse($tours, 'Tours retrieved successfully');
    }

    // API create new tour
    public function store(Request $request): JsonResponse
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

        return $this->successResponse($tour, 'Tour created successfully', 201);
    }

    // API update tour
    public function update(Request $request, $id): JsonResponse
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->errorResponse('Tour not found', 404);
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

        return $this->successResponse($tour, 'Tour updated successfully');
    }

    // API delete tour
    public function destroy($id): JsonResponse
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->errorResponse('Tour not found', 404);
        }

        $tour->delete();

        return $this->successResponse(null, 'Tour deleted successfully');
    }
}