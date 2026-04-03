<?php

namespace App\Http\Controllers;

use App\Exceptions\SignupRequestAlreadyProcessedException;
use App\Http\Requests\RejectSignupRequestRequest;
use App\Models\AccountRequest;
use App\Models\User;
use App\Services\SignupApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($request->country_id) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->reviewed_at) {
            $query->whereDate('approved_at', $request->reviewed_at)
            ->orWhereDate('rejected_at', $request->reviewed_at);
        }

        if ($request->referral_code) {
            $referralUser = User::where('referral_code', $request->referral_code)->
                orWhere('username', $request->referral_code)->first();
            $query->where('referral_id', $referralUser?->id);
        }

        $requests = $query->latest()->paginate(10);

        return view('dashboard', compact('requests'));
    }

    public function new_approve($id, SignupApprovalService $service): RedirectResponse
    {
        $accountRequest = AccountRequest::findOrFail($id);

        $this->authorize('review', $accountRequest);
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
