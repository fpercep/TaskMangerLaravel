<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyDayController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStepController;
use App\Http\Controllers\UserController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/mi-dia', [MyDayController::class, 'index'])->name('mi-dia');

    Route::get('/users/search', [UserController::class, 'searchUsers'])->name('users.search');

    //Rutas de Proyectos
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    //Rutas de Miembros de Proyecto
    Route::get('/projects/{project}/members', [App\Http\Controllers\ProjectMemberController::class, 'index'])->name('projects.members.index');
    Route::post('/projects/{project}/members', [App\Http\Controllers\ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::patch('/projects/{project}/members/{user}', [App\Http\Controllers\ProjectMemberController::class, 'update'])->name('projects.members.update');
    Route::delete('/projects/{project}/members/{user}', [App\Http\Controllers\ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
    Route::post('/projects/{project}/members/sync', [App\Http\Controllers\ProjectMemberController::class, 'sync'])->name('projects.members.sync');
    Route::delete('/projects/{project}/members', [App\Http\Controllers\ProjectMemberController::class, 'destroyBulk'])->name('projects.members.destroy-bulk');

    // Rutas de Tareas
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}/duplicate', [TaskController::class, 'duplicate'])->name('tasks.duplicate');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Rutas de Pasos (Steps)
    Route::post('/tasks/{task}/steps', [TaskStepController::class, 'store'])->name('steps.store');
    Route::patch('/steps/{step}', [TaskStepController::class, 'update'])->name('steps.update');
    Route::patch('/steps/{step}/toggle', [TaskStepController::class, 'toggle'])->name('steps.toggle');
    Route::delete('/steps/{step}', [TaskStepController::class, 'destroy'])->name('steps.destroy');

    // Ajustes unificados (tabs: profile / config)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Retrocompatibilidad: /profile redirige a settings con tab profile
    Route::get('/profile', fn () => redirect()->route('settings.index', ['tab' => 'profile']))->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
