<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NetworkerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AccountRequest::class);

        $query = AccountRequest::query()->visibleTo(Auth::user());

        if ($request->username) {
            $query->where('username', 'like', "%{$request->username}%");
        }

        // if ($request->email) {
        //     $query->where('email', 'like', "%{$request->email}%");
        // }

        if ($request->country_id) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->reviewed_at) {
            $query->whereDate('approved_at', '<=', $request->reviewed_at)
                ->orWhereDate('rejected_at', '<=', $request->reviewed_at);
        }

        if ($request->requested_at) {
            $query->whereDate('created_at', '<=', $request->requested_at);
        }

        if ($request->referral_code) {
            $referralUser = User::byReferralCode($request->referral_code)->first();
            $query->where('referral_id', $referralUser?->id);
        }

        $requests = $query->latest()->paginate(10);

        return view('networker.dashboard', compact('requests'));
    }
}
