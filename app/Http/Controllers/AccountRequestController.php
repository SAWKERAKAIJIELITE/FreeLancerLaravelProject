<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidReferralCodeException;
use App\Exceptions\RolesDoesnotFollowRulesException;
use App\Http\Requests\StoreAccountRequestRequest;
use App\Models\User;
use App\Services\AccountRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AccountRequestController extends Controller
{
    public function create(Request $request)
    {
        $referralCode = $request->query('ref');

        $referrer = User::where('referral_code', $referralCode)
            ->orWhere('username', $referralCode)->first();
        $referrer_role = $referrer?->role;

        return view('auth.signup', compact('referralCode', 'referrer_role'));
    }

    public function new_store(StoreAccountRequestRequest $request, AccountRequestService $service)
    {
        try {
            $referrer = Auth::user();
            $service->create($request->validated(), $referrer);

            return view('auth.successful-signup');

        } catch (InvalidReferralCodeException | RolesDoesnotFollowRulesException $exception) {
            return redirect()
                ->back()
                ->withErrors($exception->getMessage());
        }
    }
}
