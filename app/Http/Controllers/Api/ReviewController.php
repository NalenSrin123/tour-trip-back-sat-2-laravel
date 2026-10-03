<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewManagement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // List all reviews for a specific tour
    public function index($tour_id): JsonResponse
    {
        $reviews = Review::with('user:id,name')->where('tour_id', $tour_id)->get();

        return $this->successResponse($reviews, 'Reviews retrieved successfully');
    }

    // Create a new tour review
    public function store(Request $request, $tour_id): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'tour_id' => $tour_id,
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Push to review_management table as pending
        ReviewManagement::create([
            'review_type' => 'tour',
            'review_id' => $review->id,
            'status' => 'pending'
        ]);

        return $this->successResponse(
            $review,
            'Review created and pending approval',
            201
        );
    }
}
