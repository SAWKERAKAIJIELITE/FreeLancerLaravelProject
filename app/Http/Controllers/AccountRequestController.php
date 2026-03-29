<?php

namespace App\Http\Controllers;

// use App\Enums\Enums\SignupRequestStatus;
use App\Exceptions\InvalidReferralCodeException;
use App\Http\Requests\StoreAccountRequestRequest;
use App\Models\AccountRequest;
use App\Models\User;
use App\Services\AccountRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AccountRequestController extends Controller
{
    public function create(Request $request)
    {
        $referralCode = $request->query('ref');

        return view('auth.signup', compact('referralCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_num|unique:account_requests,username|unique:users,username',
            'first_name' => 'required|regex:/^[a-zA-Z., ]+$/',
            'middle_name' => 'nullable|regex:/^[a-zA-Z., ]+$/',
            'last_name' => 'required|regex:/^[a-zA-Z., ]+$/',
            'email' => 'required|email|unique:account_requests,email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:educator,regular',
            'referral_input' => 'nullable',
            'birthdate' => 'required|date',
            'country' => 'required|in:USA,Canada,Mexico',
            'language' => 'required|in:English,French,Spanish',
            'country_code' => 'required|in:+1,+44,+52',
            'phone' => 'required|string|numeric',
            'terms_accepted' => 'required|accepted',
        ]);

        $referralUser = false;

        if ($request->referral_input) {
            $referralUser = User::where('referral_code', $request->referral_input)
                ->orWhere('username', $request->referral_input)
                ->first();
            if (!$referralUser) {
                return back()
                    ->withErrors(['referral_input' => 'No user found with this code or username'])
                    ->withInput();
            }
        } else {
            $referralUser = User::where('role', 'super_admin')->first();
        }

        AccountRequest::create([
            ...$request->except(['referral_input', 'terms_accepted']),
            'referral_id' => $referralUser?->id,
        ]);

        return redirect('/email/verify')->with('success', 'Request submitted successfully!');
    }

    public function new_store(StoreAccountRequestRequest $request, AccountRequestService $service)
    {
        try {
            $service->create($request->validated());

            return view('auth.successful-signup');

        } catch (InvalidReferralCodeException $exception) {
            return redirect()
                ->back()
                ->withErrors($exception->getMessage());
        }
    }
}
