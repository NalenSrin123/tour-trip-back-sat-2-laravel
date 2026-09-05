<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Api\DestinationController as ApiDestinationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\AuthController;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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
