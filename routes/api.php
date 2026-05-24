<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AraucariaObservationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rota pública para alimentar o mapa frontend
Route::get('/observations', [AraucariaObservationController::class, 'index']);

// Rota protegida por autenticação (exemplo usando Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/observations', [AraucariaObservationController::class, 'store']);
});

// gambi temporária para forçar user id 1 para testar no postman
Route::post('/observations', function (\App\Http\Requests\StoreAraucariaObservationRequest $request) {
    auth()->loginUsingId(1);

    return app(\App\Http\Controllers\AraucariaObservationController::class)->store($request);
});