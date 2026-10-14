<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => response()->json([
    'status' => 'ok',
    'app'    => config('app.name'),
]));

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('properties', PropertyController::class)->whereNumber('property');
});
