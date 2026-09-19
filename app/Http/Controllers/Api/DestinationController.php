<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Destination::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('name') && $request->name != '') {
            $query->where('name', $request->name);
        }

        $destinations = $query->get();

        return response()->json([
            'success' => true,
            'data' => $destinations
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $destination = Destination::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Destination created successfully',
            'data' => $destination
        ], 201);
    }
}
