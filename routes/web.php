<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AraucariaObservationController;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () { return view('welcome'); });
Route::get('/perfil/{username}', [UserProfileController::class, 'showByUsername'])->name('profile.username');

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
    Route::post('/observations/report/{observation}', [AraucariaObservationController::class, 'report'])->name('observations.report');

    // Moderação
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/moderation/observations', [AraucariaObservationController::class, 'moderationIndex'])->name('moderation.index');
        Route::post('/moderation/observations/{report}/delete', [AraucariaObservationController::class, 'moderationDelete'])->name('moderation.delete');
        Route::post('/moderation/observations/{report}/assign', [AraucariaObservationController::class, 'moderationAssign'])->name('moderation.assign');
        Route::post('/moderation/observations/{report}/update-status', [AraucariaObservationController::class, 'moderationUpdateStatus'])->name('moderation.update-status');
    });
});