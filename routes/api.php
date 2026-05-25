<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AraucariaObservationController;

Route::get('/api/observations', [AraucariaObservationController::class, 'index']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::post('/observations', [AraucariaObservationController::class, 'store']);
    Route::put('/observations/{observation}', [AraucariaObservationController::class, 'update']);
    Route::delete('/observations/{observation}', [AraucariaObservationController::class, 'destroy']);
});