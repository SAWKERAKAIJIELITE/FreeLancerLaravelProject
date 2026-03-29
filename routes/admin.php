<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::prefix('signup-requests')
            ->as('signup-requests.')
            ->controller(AdminController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');

                // Route::get('/{signupRequest}', 'show')->name('show');

                Route::post('/{id}/approve', 'new_approve')->name('approve');

                Route::post('/{id}/reject', 'new_reject')->name('reject');
            });
    });
