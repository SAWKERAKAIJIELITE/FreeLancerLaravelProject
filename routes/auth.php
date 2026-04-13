<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')
    ->controller(AuthController::class)->group(function () {
        Route::get('/signup', 'create')->name('signup');
        Route::post('/signup', 'store')->name('signup.store');
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        });

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->as('admin.')
    ->controller(AdminAuthController::class)
    ->group(function () {
        Route::get('/signup', 'create');
        Route::post('/signup', 'store')->name('signup');
    });
