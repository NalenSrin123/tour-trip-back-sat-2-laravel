<?php

use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::get('/tours', [TourController::class, 'index']);
Route::post('/tours', [TourController::class, 'tourstore']);