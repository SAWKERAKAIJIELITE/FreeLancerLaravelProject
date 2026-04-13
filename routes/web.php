<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Route::post('/signup', [AccountRequestController::class, 'store']);
// Route::get('/signup', [AccountRequestController::class, 'create'])->name('signup');

// Route::get('/admin/signup/{token}', function ($token) {
//     if ($token !== config('app.admin_signup_token')) {
//         abort(404);
//     }
//     return view('auth.admin-signup');
// });

Route::middleware(['auth', 'role:regular', 'verified'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index']);
});

require __DIR__ . '/metadata.php';
require __DIR__ . '/email-verification.php';
require __DIR__ . '/forgot-password.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/account-requests.php';
