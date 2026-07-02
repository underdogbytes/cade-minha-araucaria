<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AraucariaObservationController;

Route::middleware('throttle.api')->group(function () {
    Route::get('/observations', [AraucariaObservationController::class, 'index']);
    Route::get('/observations/{observation}', [AraucariaObservationController::class, 'show']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/observations', [AraucariaObservationController::class, 'store']);
        Route::put('/observations/{observation}', [AraucariaObservationController::class, 'update']);
        Route::delete('/observations/{observation}', [AraucariaObservationController::class, 'destroy']);
    });
});

