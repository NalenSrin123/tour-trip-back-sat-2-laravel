<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    // API for ទាញយកនិងស្វែងរកtour តាមឈ្មៅះ
    public function index(Request $request)
    {
        $query = Tour::with('schedules');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        //  Show 10 items for a page
        $tours = $query->paginate(10);

        return response()->json($tours, 200);
    }

    // API create new tour
    public function store(Request $request)
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
}