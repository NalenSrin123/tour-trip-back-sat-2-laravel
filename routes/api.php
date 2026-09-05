<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
