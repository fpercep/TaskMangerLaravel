<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MiDiaController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mi-dia', [MiDiaController::class, 'index'])->name('mi-dia');

    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Rutas de Tareas
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update_status');

    // Ajustes unificados (tabs: profile / config)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Retrocompatibilidad: /profile redirige a settings con tab profile
    Route::get('/profile', fn () => redirect()->route('settings.index', ['tab' => 'profile']))->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
