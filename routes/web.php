<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RotatorController;

Route::get('/', [RotatorController::class, 'index'])->name('dashboard');
Route::get('/viewer', [RotatorController::class, 'viewer'])->name('viewer');
