<?php

namespace App\Services;

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

        if ($accountRequest->status != 'pending') {
            throw new SignupRequestAlreadyProcessedException();
        }

        return DB::transaction(function () use ($accountRequest, $reviewer) {

            $user = User::create($this->buildUserData($accountRequest));

            $accountRequest->update([
                'status' => 'accepted',
                'rejection_reason' => null,
                'reviewed_by' => $reviewer->id,
                'approved_at' => now(),
                'user_id' => $user->id,
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
            'middle_name' => $accountRequest->middle_name ?? null,
            'last_name' => $accountRequest->last_name,
            'birthdate' => $accountRequest->birthdate,
            'country_id' => $accountRequest->country_id,
            'language_id' => $accountRequest->language_id,
            'phone_country_id' => $accountRequest->phone_country_id,
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
}
