<?php

use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NetworkerController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {

    Route::controller(AccountRequestController::class)->group(function () {
        Route::get('/add-networker', 'createNetworker');
        Route::get('/add-educator', 'createEducator');
        Route::get('/add-regular', 'createRegular');

        Route::post('/add-member', 'store')->name('add-member');

        Route::get('/signup-requests', 'index')->name('signup-requests.index');

        Route::get('/signup-requests/{id}', 'show')->name('signup-requests.show');

        Route::post('/signup-requests/{id}/approve', 'approve')->name('signup-requests.approve');

        Route::post('/signup-requests/{id}/reject', 'reject')->name('signup-requests.reject');
    });

    Route::get('/networker/signup-requests', [NetworkerController::class, 'index'])
        ->name('networker.signup-requests.index');

    // Route::post('/users', [UserCreationController::class, 'store'])
    //     ->name('users.store');
});

Route::get('/signup-requests/{id}/resubmit', [AuthController::class, 'resubmit'])
    ->middleware('signed')
    ->name('signup-requests.resubmit');
