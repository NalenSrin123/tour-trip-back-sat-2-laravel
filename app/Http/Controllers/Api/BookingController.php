<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * 1. LIST BOOKINGS (GET /api/bookings)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Booking::with('user');

        // Optional filter: filter by status (e.g. ?status=pending)
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Optional filter: filter by user_id (e.g. ?user_id=1)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        // Order latest first and paginate (default 10 per page)
        $perPage = (int) $request->query('per_page', 10);
        $bookings = $query->latest('booking_id')->paginate($perPage);

        return BookingResource::collection($bookings);
    }

    /**
     * 2. CREATE BOOKING (POST /api/bookings)
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Generate a unique booking code: e.g., BK-20260905-ABCD
        $bookingCode = 'BK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        // Create the booking record
        $booking = Booking::create([
            'user_id'      => $validated['user_id'],
            'schedule_id'  => $validated['schedule_id'],
            'booking_code' => $bookingCode,
            'status'       => $validated['status'] ?? 'pending',
            'total_amount' => $validated['total_amount'],
        ]);

        // Load the relationship for the response
        $booking->load('user');

        return response()->json([
            'message' => 'Booking created successfully',
            'data'    => new BookingResource($booking),
        ], Response::HTTP_CREATED);
    }
}