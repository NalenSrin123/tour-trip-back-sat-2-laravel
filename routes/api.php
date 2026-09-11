<?php

use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\ImageController;

Route::get('/tours', [TourController::class, 'index']);
Route::post('/tours', [TourController::class, 'store']);
Route::put('/tours/{id}', [TourController::class, 'update']);
Route::delete('/tours/{id}', [TourController::class, 'destroy']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::apiResource('destinations', DestinationController::class)
    ->only(['update', 'destroy']);
use App\Http\Controllers\Api\DestinationController;
Route::get('/destinations', [DestinationController::class, 'index']);
Route::post('/destinations', [DestinationController::class, 'store']);

Route::get('/tour-images', [ImageController::class, 'index']);
Route::get('/tour-images/{id}', [ImageController::class, 'show']);
Route::post('/tour-images', [ImageController::class, 'store']);
Route::put('/tour-images/{id}', [ImageController::class, 'update']);
Route::delete('/tour-images/{id}', [ImageController::class, 'destroy']);