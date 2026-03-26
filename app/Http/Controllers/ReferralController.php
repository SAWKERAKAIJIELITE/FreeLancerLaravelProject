<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountRequest::where('referral_id', Auth::id())->where('status', 'accepted');

        if ($request->username) {
            $query->where('username', 'like', "%{$request->username}%");
        }

        if ($request->email) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->country) {
            $query->where('country', $request->country);
        }

        if ($request->birthdate) {
            $query->whereDate('birthdate', $request->birthdate);
        }

        if ($request->referral_id) {
            if (is_numeric($request->referral_id)) {
                $query->where('referral_id', $request->referral_id);
            } else {
                $referralUser = User::where('username', $request->referral_id)->first();
                if ($referralUser) {
                    $query->where('referral_id', $referralUser->id);
                }
            }
        }

        $requests = $query->latest()->paginate(10);

        return view('dashboard', compact('requests'));
    }
}
