<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Api\DestinationController as ApiDestinationController;

Route::get('/tours', [TourController::class, 'index']);
Route::post('/tours', [TourController::class, 'store']);
Route::put('/tours/{id}', [TourController::class, 'update']);
Route::delete('/tours/{id}', [TourController::class, 'destroy']);

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);

Route::apiResource('destinations', DestinationController::class)
    ->only(['update', 'destroy']);

Route::get('/destinations', [ApiDestinationController::class, 'index']);
Route::post('/destinations', [ApiDestinationController::class, 'store']);
