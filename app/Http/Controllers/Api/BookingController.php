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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookingController extends Controller
{
    /**
     * 1. LIST BOOKINGS (GET /api/bookings)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Booking::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        $perPage = (int) $request->query('per_page', 10);
        $bookings = $query->latest('booking_id')->paginate($perPage);

        return BookingResource::collection($bookings);
    }

    /**
     * 2. CREATE BOOKING (POST /api/bookings)
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $bookingCode = 'BK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $booking = Booking::create([
                'user_id'      => $validated['user_id'],
                'schedule_id'  => $validated['schedule_id'],
                'booking_code' => $bookingCode,
                'status'       => $validated['status'] ?? 'pending',
                'total_amount' => $validated['total_amount'],
            ]);

            $booking->load('user');

            return response()->json([
                'message' => 'Booking created successfully',
                'data'    => new BookingResource($booking),
            ], Response::HTTP_CREATED);

        } catch (Throwable $e) {
            Log::error('Booking store failed: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Failed to create booking',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 3. UPDATE BOOKING
     * PUT/PATCH /api/bookings/{booking_id}
     */

    public function update(Request $request, int $booking_id): JsonResponse
    {
        try {
            // Find booking
            $booking = DB::table('bookings')
                ->where('booking_id', $booking_id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'message' => 'Booking not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // Validate request
            $validated = $request->validate([
                'user_id'      => 'sometimes|integer|exists:users,user_id',
                'schedule_id'  => 'sometimes|integer|exists:tour_schedules,schedule_id',
                'status'       => 'sometimes|string|in:pending,confirmed,cancelled,complete',
                'total_amount' => 'sometimes|numeric|min:0',
            ]);

            // Update booking
            DB::table('bookings')
                ->where('booking_id', $booking_id)
                ->update($validated);

            // Get updated booking
            $updatedBooking = DB::table('bookings')
                ->where('booking_id', $booking_id)
                ->first();

            return response()->json([
                'message' => 'Booking updated successfully',
                'data'    => $updatedBooking,
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Throwable $e) {
            Log::error('Booking update failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'message' => 'Failed to update booking',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * 4. Delete Booking
     * DELETE /api/bookings/{booking_id}
     */
    public function destroy(int $booking_id): JsonResponse
    {
        try {
            $booking = DB::table('bookings')->where('booking_id', $booking_id)->first();

            if (!$booking) {
                return response()->json([
                    'message' => 'Booking not found',
                ], Response::HTTP_NOT_FOUND);
            }

            DB::table('bookings')->where('booking_id', $booking_id)->delete();

            return response()->json([
                'message' => 'Booking deleted successfully',
            ], Response::HTTP_OK);

        } catch (Throwable $e) {
            Log::error('Booking destroy failed: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Failed to delete booking',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
