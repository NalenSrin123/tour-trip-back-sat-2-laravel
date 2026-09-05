<?php

use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/tours', [TourController::class, 'index']);
Route::post('/tours', [TourController::class, 'tourstore']);
Route::put('/tours/{id}', [TourController::class, 'update']);
Route::delete('/tours/{id}', [TourController::class, 'destroy']);
