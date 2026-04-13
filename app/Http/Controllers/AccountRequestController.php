<?php

namespace App\Http\Controllers;

use App\Enums\SignupRequestStatus;
use App\Enums\UserRole;
use App\Exceptions\SignupRequestAlreadyProcessedException;
use App\Http\Requests\RejectSignupRequestRequest;
use App\Http\Requests\StoreAccountRequestRequest;
use App\Models\AccountRequest;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Services\SignupApprovalService;
use App\Services\UserCreationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AccountRequestController extends Controller
{
    public function createNetworker(Request $request, AuthorizationService $authorizationService)
    {
        $referralCode = $request->query('ref');

        $referrer = User::byReferralCode($referralCode)->first();
        if ($request->user() != $referrer) {
            abort(403);
        }
        // $this->authorize('createType', UserRole::Networker->value);
        if (
            !$authorizationService->canCreateAccountRequestType(
                $request->user(),
                UserRole::Networker->value
            )
        ) {
            abort(403, 'Unauthorized');
        }
        $referral_role = UserRole::Networker->value;

        return view('auth.add-member', compact('referralCode', 'referral_role'));
    }

    public function createEducator(Request $request, AuthorizationService $authorizationService)
    {
        $referralCode = $request->query('ref');

        $referrer = User::byReferralCode($referralCode)->first();
        if ($request->user() != $referrer) {
            abort(403);
        }
        // $this->authorize('create', UserRole::Educator->value);
        if (
            !$authorizationService->canCreateAccountRequestType(
                $request->user(),
                UserRole::Educator->value
            )
        ) {
            abort(403, 'Unauthorized');
        }
        $referral_role = UserRole::Educator->value;

        return view('auth.add-member', compact('referralCode', 'referral_role'));
    }

    public function createRegular(Request $request, AuthorizationService $authorizationService)
    {
        $referralCode = $request->query('ref');

        $referrer = User::byReferralCode($referralCode)->first();
        if ($request->user() != $referrer) {
            abort(403);
        }
        // $this->authorize('create', UserRole::Regular->value);
        if (
            !$authorizationService->canCreateAccountRequestType(
                $request->user(),
                UserRole::Regular->value
            )
        ) {
            abort(403, 'Unauthorized');
        }
        $referral_role = UserRole::Regular->value;

        return view('auth.add-member', compact('referralCode', 'referral_role'));
    }

    public function store(StoreAccountRequestRequest $request, UserCreationService $service)
    {
        try {
            $service->create(Auth::user(), $request->validated());

            return view('auth.successful-signup');

        } catch (Throwable $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

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

        return view('dashboard', compact('requests'));
    }

    public function show($id)
    {
        $accountRequest = AccountRequest::findOrFail($id);
        $this->authorize('view', $accountRequest);

        return view('signup-requests.show', compact('accountRequest'));
    }

    public function approve($id, SignupApprovalService $service): RedirectResponse
    {
        $accountRequest = AccountRequest::findOrFail($id);

        // $this->authorize('review', $accountRequest);
        app(
            AuthorizationService::class
        )->canReviewSignupRequest(Auth::user(), $accountRequest);
        // dd('approving');
        try {
            $service->approve($accountRequest, Auth::user());

            return back()->with('success', 'Signup request approved successfully.');
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', $exception->getMessage());
        }
    }

    public function reject(
        RejectSignupRequestRequest $request,
        $id,
        SignupApprovalService $service
    ): RedirectResponse {
        $accountRequest = AccountRequest::findOrFail($id);
        $this->authorize('review', $accountRequest);
        try {
            $service->reject(
                $accountRequest,
                $request->validated('rejection_reason'),
                Auth::user()
            );

            return back()->with('success', 'Signup request rejected successfully.');
        } catch (SignupRequestAlreadyProcessedException | AuthorizationException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
