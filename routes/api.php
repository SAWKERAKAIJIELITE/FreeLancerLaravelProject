<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReferralController;

Route::post('/signup-request', [AccountRequestController::class, 'store']);


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/requests', [AdminController::class, 'index']);
    Route::post('/admin/requests/{id}/approve', [AdminController::class, 'approve']);
    Route::post('/admin/requests/{id}/reject', [AdminController::class, 'reject']);
});


Route::middleware(['auth'])->get('/my-referrals', [ReferralController::class, 'myReferrals']);
