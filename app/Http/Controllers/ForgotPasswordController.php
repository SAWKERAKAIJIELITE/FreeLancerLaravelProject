<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        if($request->validated()['email']==null)
            return redirect()->back()->withErrors(['email'=>'no user has this email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return back()->with('status', __($status));
    }
}
