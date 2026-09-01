<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AraucariaObservationController;
use App\Http\Controllers\UserProfileController;

use App\Http\Controllers\AraucariaObservationUpdateController;

Route::get('/', function () { return view('welcome'); });
Route::get('/perfil/{username}', [UserProfileController::class, 'showByUsername'])->name('profile.username');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/feed-partial', [DashboardController::class, 'feedPartial'])->name('dashboard.feed-partial');
    Route::get('/dashboard/my-obs-partial', [DashboardController::class, 'myObsPartial'])->name('dashboard.my-obs-partial');
    Route::post('/observations', [AraucariaObservationController::class, 'store'])->name('observations.store');
    Route::get('/observations/{observation}', [AraucariaObservationController::class, 'show'])->name('observations.show');
    Route::put('/observations/{observation}', [AraucariaObservationController::class, 'update'])->name('observations.update');
    Route::delete('/observations/{observation}', [AraucariaObservationController::class, 'destroy']);
    Route::post('/observations/report/{observation}', [AraucariaObservationController::class, 'report'])->name('observations.report');

    // Atualizações Colaborativas de Fotos e Cuidados
    Route::post('/observations/{observation}/updates', [AraucariaObservationUpdateController::class, 'store'])->name('observations.updates.store');
    Route::delete('/observation-updates/{update}', [AraucariaObservationUpdateController::class, 'destroy'])->name('observations.updates.destroy');
    Route::patch('/observations/{observation}/toggle-shared', [AraucariaObservationUpdateController::class, 'toggleShared'])->name('observations.toggle-shared');
    Route::post('/observation-updates/{update}/report', [AraucariaObservationUpdateController::class, 'report'])->name('observations.updates.report');

    // Moderação
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/moderation/observations', [AraucariaObservationController::class, 'moderationIndex'])->name('moderation.index');
        Route::post('/moderation/observations/{report}/delete', [AraucariaObservationController::class, 'moderationDelete'])->name('moderation.delete');
        Route::post('/moderation/observations/{report}/assign', [AraucariaObservationController::class, 'moderationAssign'])->name('moderation.assign');
        Route::post('/moderation/observations/{report}/update-status', [AraucariaObservationController::class, 'moderationUpdateStatus'])->name('moderation.update-status');
    });
});