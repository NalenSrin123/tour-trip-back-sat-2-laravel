<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->prefix('auth')->group(function (): void {
	Route::get('me', [AuthController::class, 'me']);
	Route::post('logout', [AuthController::class, 'logout']);
	Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware('auth:api')->group(function (): void {
	Route::get('settings', [SystemSettingController::class, 'index']);
	Route::post('settings', [SystemSettingController::class, 'store']);
	Route::get('settings/{setting}', [SystemSettingController::class, 'show']);
	Route::match(['put', 'patch'], 'settings/{setting}', [SystemSettingController::class, 'update']);
	Route::delete('settings/{setting}', [SystemSettingController::class, 'destroy']);
});
