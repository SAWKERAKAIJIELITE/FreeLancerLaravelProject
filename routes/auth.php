<?php

use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::controller(AccountRequestController::class)->group(function () {
    Route::get('/register', 'create')->name('register');

    Route::post('/register', 'new_store')->name('signup-requests.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

