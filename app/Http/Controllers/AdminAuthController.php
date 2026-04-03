<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ReferralCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;


class AdminAuthController extends Controller
{
    public function showSignupForm()
    {
        return view('auth.admin-signup');
    }

    public function signup(Request $request, ReferralCodeGenerator $referralCodeGenerator)
    {
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            abort(403);
        }
        $request->validate([
            'username' => 'required|alpha_num|min:3|max:50|unique:account_requests,username|unique:users,username',
            'first_name' => 'required|string|min:2|max:100|regex:/^[\pL\s\.\,\-\']+$/u',
            'middle_name' => 'nullable|string|min:2|max:100|regex:/^[\pL\s\.\,\-\']+$/u',
            'last_name' => 'required|string|min:2|max:100|regex:/^[\pL\s\.\,\-\']+$/u',
            'email' => 'required|email|string|max:255|unique:account_requests,email|unique:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'birthdate' => 'required|date||before:today',
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('is_active', true),
            ],
            'language_id' => [
                'required',
                'integer',
                Rule::exists('languages', 'id')->where('is_active', true),
            ],
            'phone_country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where('is_active', true),
            ],
            'phone' => 'required|string|numeric|min:6,max:20',
        ]);
        $admin = User::create([
            ...$request->except(['password_confirmation']),
            'referral_code' => $referralCodeGenerator->generate(),
            'role' => 'super_admin',
        ]);
        $remember = $request->has('remember');
        Auth::login($admin, $remember);
        return redirect('/admin/signup-requests');
    }
}
