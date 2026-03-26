<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountRequest::query();

        if ($request->username) {
            $query->where('username', 'like', "%{$request->username}%");
        }

        if ($request->email) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->country) {
            $query->where('country', $request->country);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->birthdate) {
            $query->whereDate('birthdate', $request->birthdate);
        }

        if ($request->referral_code) {
            $referralUser = User::where('referral_code', $request->referral_code)->
                orWhere('username', $request->referral_code)->first();
            $query->where('referral_id', $referralUser?->id);
        }

        $requests = $query->latest()->paginate(10);

        return view('dashboard', compact('requests'));
    }

    public function approve($id)
    {
        $request = AccountRequest::findOrFail($id);

        DB::transaction(function () use ($request) {

            $user = User::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name ?? null,
                'last_name' => $request->last_name,
                'birthdate' => $request->birthdate,
                'country' => $request->country,
                'language' => $request->language,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'referred_by' => $request->referral_id,
            ]);

            $request->update([
                'status' => 'accepted',
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);

            // TODO: send email here
            event(new Registered($user));
        });

        return back()->with('success', 'Approved');
    }

    public function reject($id)
    {
        $request = AccountRequest::findOrFail($id);

        $request->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Rejected');
    }
}
