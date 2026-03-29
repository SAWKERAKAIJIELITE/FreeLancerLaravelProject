<?php

namespace App\Services;

// use App\Enums\SignupRequestStatus;
use App\Exceptions\InvalidReferralCodeException;
use App\Exceptions\SignupRequestAlreadyProcessedException;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class SignupApprovalService
{
    public function __construct(
        private ReferralCodeGenerator $referralCodeGenerator
    ) {
    }

    public function approve(AccountRequest $accountRequest, User $reviewer): User
    {
        $this->ensureReviewerIsAdmin($reviewer);

        // dd($accountRequest->status);
        if ($accountRequest->status!='pending') {
            throw new SignupRequestAlreadyProcessedException();
        }

        return DB::transaction(function () use ($accountRequest, $reviewer) {

            $user = User::create($this->buildUserData($accountRequest));

            $accountRequest->update([
                'status' => 'accepted',
                'rejection_reason' => null,
                'reviewed_by' => $reviewer->id,
                'approved_at' => now(),
                'user_id'=> $user->id
            ]);

            event(new Registered($user));

            return $user;
        });
    }

    public function reject(
        AccountRequest $accountRequest,
        ?string $reason = null,
        User $reviewer
    ): AccountRequest {

        $this->ensureReviewerIsAdmin($reviewer);

        if ($accountRequest->status != 'pending') {
            throw new SignupRequestAlreadyProcessedException();
        }

        $accountRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer->id,
            'rejected_at' => now(),
        ]);

        return $accountRequest->refresh();
    }

    private function buildUserData(AccountRequest $accountRequest): array
    {
        return [
            'first_name' => $accountRequest->first_name,
            'middle_name' => $accountRequest->middle_name??null,
            'last_name' => $accountRequest->last_name,
            'birthdate' => $accountRequest->birthdate,
            'country' => $accountRequest->country,
            'language' => $accountRequest->language,
            'country_code' => $accountRequest->country_code,
            'phone' => $accountRequest->phone,
            'email' => $accountRequest->email,
            'username' => $accountRequest->username,
            'password' => $accountRequest->password,
            'role' => $accountRequest->role,
            'referral_code' => $this->referralCodeGenerator->generate(),
            'referred_by' => $accountRequest->referral_id,
            'terms_accepted_at' => $accountRequest->terms_accepted_at,
        ];
    }

    private function ensureReviewerIsAdmin(User $reviewer): void
    {
        if (!$reviewer->isAdmin()) {
            throw new AuthorizationException('Only admins can review signup requests.');
        }
    }

    private function resolveReferrer(?string $referralCode): ?User
    {
        if (blank($referralCode)) {
            return null;
        }

        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer) {
            throw new InvalidReferralCodeException();
        }

        return $referrer;
    }
}
