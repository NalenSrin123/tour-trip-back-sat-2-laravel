<?php

use App\Http\Controllers\DestinationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('destinations', DestinationController::class)
    ->only(['update', 'destroy']);
