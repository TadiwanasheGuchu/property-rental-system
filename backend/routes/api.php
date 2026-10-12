<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', fn() => response()->json([
    'status' => 'ok',
    'app'    => config('app.name'),
]));

use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // TEMPORARY: used only to test the role middleware. Delete after Checkpoint 5.
    Route::get('/admin-check', fn() => response()->json(['message' => 'You are a super admin.']))
        ->middleware('role:super_admin');
});
