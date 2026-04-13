<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Events\SignupRequestReviewed;
use App\Exceptions\SignupRequestAlreadyProcessedException;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SignupRejectedNotification;


class SignupApprovalService
{
    public function __construct(
        private ReferralCodeGenerator $referralCodeGenerator,
        private AuthorizationService $authorizationService
    ) {
    }

    public function approve(AccountRequest $accountRequest, User $reviewer): User
    {
        $this->ensureReviewerCanReview($accountRequest, $reviewer);

        if ($accountRequest->status->value != 'pending') {
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

            if ($accountRequest->role == UserRole::Regular->value) {
                event(new Registered($user));
            }
            event(new SignupRequestReviewed($accountRequest->fresh()));

            return $user;
        });
    }

    public function reject(
        AccountRequest $accountRequest,
        ?string $reason = null,
        User $reviewer
    ): AccountRequest {

        $this->ensureReviewerCanReview($accountRequest, $reviewer);

        if ($accountRequest->status->value != 'pending') {
            throw new SignupRequestAlreadyProcessedException();
        }
        return DB::transaction(function () use ($accountRequest, $reason, $reviewer) {

            $accountRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $reviewer->id,
                'rejected_at' => now(),
                // 'resubmission_token' => Str::random(64),
                // 'resubmission_token_expires_at' => now()->addDays(3), // configurable
            ]);

            $accountRequest = $accountRequest->fresh();

            event(new SignupRequestReviewed($accountRequest));

            if ($accountRequest->role == UserRole::Regular->value) {
                Notification::route('mail', $accountRequest->email)
                    ->notify(new SignupRejectedNotification($accountRequest));
            }

            return $accountRequest;
        });
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

    private function ensureReviewerCanReview(AccountRequest $accountRequest, User $reviewer): void
    {
        if (!$this->authorizationService->canReviewSignupRequest($reviewer, $accountRequest)) {
            throw new AuthorizationException('You are not allowed to review this signup request.');
        }
    }
}
