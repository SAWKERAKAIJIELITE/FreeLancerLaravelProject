<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255|string|exists:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ]);
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            return match ($user->role) {
                'super_admin' => redirect('/admin/signup-requests'),
                'educator' => redirect('/educator/dashboard'),
                default => redirect('/user/dashboard'),
            };
        }

        return back()->withErrors(['Invalid credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
