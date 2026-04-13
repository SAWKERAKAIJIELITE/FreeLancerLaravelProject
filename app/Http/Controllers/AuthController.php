<?php

namespace App\Http\Controllers;

use App\Enums\SignupRequestStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidReferralCodeException;
use App\Http\Requests\SignupRequest;
use App\Models\AccountRequest;
use App\Models\User;
use App\Services\AccountRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        $referralCode = $request->query('ref');

        if ($referralCode != null) {
            $referrer = User::byReferralCode($referralCode)->first();
            if ($referrer == null) {
                // throw new InvalidReferralCodeException('No User Found with this referral code');
                return 'No User Found with this referral code';
            }
        }
        // $referrer = User::byReferralCode($referralCode)->first();
        // if ($referrer == null) {
        //     // throw new InvalidReferralCodeException('No User Found with this referral code');
        //     return 'No User Found with this referral code';
        // }
        return view('auth.public.signup', compact('referralCode'));
    }

    public function store(SignupRequest $request, AccountRequestService $service)
    {
        try {
            $service->create($request->validated());

            return view('auth.successful-signup');

        } catch (Throwable $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    public function resubmit($id): View
    {
        $accountRequest = AccountRequest::findOrFail($id);

        abort_unless($accountRequest->status === SignupRequestStatus::Rejected, 404);

        return view('auth.public.signup', [
            'prefill' => $accountRequest,
            'rejectionReason' => $accountRequest->rejection_reason,
            'resubmittingFromId' => $accountRequest->id,
            'referralCode'=>$accountRequest->referral->referral_code
        ]);
    }

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
                UserRole::SuperAdmin => redirect('/signup-requests'),
                UserRole::Networker => redirect('/networker/signup-requests'),
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
