<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AraucariaObservationController;

Route::get('/', function () {
    return view('welcome');
});

// Salva usando sessão do navegador
Route::middleware('auth')->group(function () {
    Route::post('/web/observations', [AraucariaObservationController::class, 'store']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
