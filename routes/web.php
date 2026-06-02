<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AraucariaObservationController;

Route::get('/', function () { return view('welcome'); });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/observations', [AraucariaObservationController::class, 'store'])->name('observations.store');
    Route::get('/observations/{observation}', [AraucariaObservationController::class, 'show'])->name('observations.show');
    Route::put('/observations/{observation}', [AraucariaObservationController::class, 'update'])->name('observations.update');
    Route::delete('/observations/{observation}', [AraucariaObservationController::class, 'destroy']);
});

Route::get('/limpar-tudo', function() {
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    return "Caches limpos com sucesso no servidor!";
});