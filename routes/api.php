<?php

use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::get('settings', [SystemSettingController::class, 'index']);
Route::post('settings', [SystemSettingController::class, 'store']);
Route::delete('settings/{setting}', [SystemSettingController::class, 'destroy']);
