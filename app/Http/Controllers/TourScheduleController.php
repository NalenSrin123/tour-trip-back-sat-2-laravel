<?php

namespace App\Http\Controllers;

use App\Models\TourSchedule;
use App\Http\Requests\StoreTourScheduleRequest;
use App\Http\Requests\UpdateTourScheduleRequest;
use Illuminate\Http\JsonResponse;

class TourScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $schedules = TourSchedule::with('tour')->get();
        return $this->successResponse($schedules, 'Tour schedules retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTourScheduleRequest $request): JsonResponse
    {
        $schedule = TourSchedule::create($request->validated());
        return $this->successResponse($schedule, 'Tour schedule created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TourSchedule $tourSchedule): JsonResponse
    {
        $tourSchedule->load('tour');
        return $this->successResponse($tourSchedule, 'Tour schedule retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTourScheduleRequest $request, TourSchedule $tourSchedule): JsonResponse
    {
        $tourSchedule->update($request->validated());
        return $this->successResponse($tourSchedule, 'Tour schedule updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TourSchedule $tourSchedule): JsonResponse
    {
        $tourSchedule->delete();
        return $this->successResponse(null, 'Tour schedule deleted successfully');
    }
}
