<?php

namespace App\Http\Controllers;

use App\Models\TourSchedule;
use App\Http\Requests\StoreTourScheduleRequest;
use App\Http\Requests\UpdateTourScheduleRequest;

class TourScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = TourSchedule::with('tour')->get();
        return response()->json($schedules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTourScheduleRequest $request)
    {
        $schedule = TourSchedule::create($request->validated());
        return response()->json($schedule, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TourSchedule $tourSchedule)
    {
        $tourSchedule->load('tour');
        return response()->json($tourSchedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTourScheduleRequest $request, TourSchedule $tourSchedule)
    {
        $tourSchedule->update($request->validated());
        return response()->json($tourSchedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TourSchedule $tourSchedule)
    {
        $tourSchedule->delete();
        return response()->json(null, 204);
    }
}
