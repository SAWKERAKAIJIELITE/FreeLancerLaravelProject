<?php

namespace App\Http\Controllers;

use App\Exceptions\SignupRequestAlreadyProcessedException;
use App\Http\Requests\RejectSignupRequestRequest;
use App\Models\AccountRequest;
use App\Models\User;
use App\Services\SignupApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function new_approve($id, SignupApprovalService $service): RedirectResponse
    {
        $accountRequest = AccountRequest::findOrFail($id);

        $this->authorize('review', $accountRequest);
        // dd($accountRequest->status);
        try {
            $service->approve($accountRequest, Auth::user());

            return redirect()
                ->back()
                ->with('success', 'Signup request approved successfully.');
        } catch (SignupRequestAlreadyProcessedException | AuthorizationException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
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

    public function new_reject(
        RejectSignupRequestRequest $request,
        $id,
        SignupApprovalService $service
    ): RedirectResponse {
$accountRequest = AccountRequest::findOrFail($id);
        // $this->authorize('review', $accountRequest);
        try {
            $service->reject(
                $accountRequest,
                $request->validated('rejection_reason'),
                Auth::user()
            );

            return redirect()
                ->back()
                ->with('success', 'Signup request rejected successfully.');
        } catch (SignupRequestAlreadyProcessedException |AuthorizationException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
    }
}
