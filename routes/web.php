<?php

use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EducatorController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        $user = User::find($request->id);
        return redirect("/" . ($user->role == 'educator' ? 'educator' : 'user') . "/dashboard");
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

});

// Route::post('/signup', [AccountRequestController::class, 'store']);
// Route::get('/signup', [AccountRequestController::class, 'create'])->name('signup');

// Route::get('/admin/signup/{token}', function ($token) {
//     if ($token !== config('app.admin_signup_token')) {
//         abort(404);
//     }
//     return view('auth.admin-signup');
// });

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/signup', [AdminAuthController::class, 'showSignupForm'])->name('admin.signup');
    Route::post('/admin/signup', [AdminAuthController::class, 'signup']);
    // Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Route::post('/admin/dashboard/{id}/approve', [AdminController::class, 'approve']);
    // Route::post('/admin/dashboard/{id}/reject', [AdminController::class, 'reject']);
});

Route::middleware(['auth', 'role:educator', 'verified'])->group(function () {
    Route::get('/educator/dashboard', [EducatorController::class, 'index']);
});

Route::middleware(['auth', 'role:regular', 'verified'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index']);
});


require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
// require __DIR__ . '/user.php';
