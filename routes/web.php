<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MiDiaController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/mi-dia', [MiDiaController::class, 'index'])->name('mi-dia');
