<?php

use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController as WebDestinationController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\UserController as ListUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Tours
Route::get('/tours', [TourController::class, 'index']);
Route::post('/tours', [TourController::class, 'store']);
Route::put('/tours/{id}', [TourController::class, 'update']);
Route::delete('/tours/{id}', [TourController::class, 'destroy']);

// Tour Reviews
Route::get('/tours/{tour_id}/reviews', [ReviewController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tours/{tour_id}/reviews', [ReviewController::class, 'store']);
});

// Users
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);

// Destinations
Route::apiResource('destinations', DestinationController::class)
    ->only(['update', 'destroy']);
Route::get('/destinations', [ApiDestinationController::class, 'index']);
Route::post('/destinations', [ApiDestinationController::class, 'store']);
// Destinations
Route::get('/destinations', [DestinationController::class, 'index']);
Route::post('/destinations', [DestinationController::class, 'store']);
Route::apiResource('destinations', WebDestinationController::class)->only(['update', 'destroy']);

// Users
Route::get('/users', [ListUserController::class, 'index']);
Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::patch('/{user}', [UserController::class, 'update']);
});

// Bookings
Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::put('/bookings/{booking_id}', [BookingController::class, 'update']);
Route::delete('/bookings/{booking_id}', [BookingController::class, 'destroy']);

