<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showSignupForm()
    {
        return view('auth.admin-signup');
    }
    public function signup(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            abort(403);
        }
        $request->validate([
            'username' => 'required|alpha_num|unique:account_requests,username|unique:users,username',
            'first_name' => 'required|regex:/^[a-zA-Z., ]+$/',
            'middle_name' => 'nullable|regex:/^[a-zA-Z., ]+$/',
            'last_name' => 'required|regex:/^[a-zA-Z., ]+$/',
            'email' => 'required|email|unique:account_requests,email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'birthdate' => 'required|date',
            'country' => 'required|in:USA,Canada,Mexico',
            'language' => 'required|in:English,French,Spanish',
            'country_code' => 'required|in:+1,+44,+52',
            'phone' => 'required|string|numeric',
        ]);
        $admin = User::create([
            ...$request->except(['password_confirmation']),
            'role' => 'super_admin',
        ]);
        $remember = $request->has('remember');
        Auth::login($admin, $remember);
        return redirect('/admin/dashboard');
    }
}
