<?php

use App\Http\Controllers\EducatorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:educator'])
    ->prefix('educator')
    ->as('educator.')
    ->group(function () {
        Route::get('/dashboard', [EducatorController::class, 'index'])->name('dashboard');
    });
