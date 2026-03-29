<?php

use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::controller(AccountRequestController::class)->group(function () {
        Route::get('/register', 'create')->name('register');

        Route::post('/register', 'new_store')->name('signup-requests.store');
    });
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

