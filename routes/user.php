<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:regular'])
    ->prefix('user')
    ->as('user.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class,'index'])->name('dashboard');
    });
