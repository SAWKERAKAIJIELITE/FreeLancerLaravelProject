<?php

namespace App\Services;

// use App\Exceptions\InvalidReferralCodeException;
use App\Events\SignupRequestCreated;
use App\Exceptions\InvalidReferralCodeException;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Exceptions\SignupRequestConflictException;


class AccountRequestService
{
    public function create(array $validatedData): AccountRequest
    {
        try {
            return DB::transaction(function () use ($validatedData) {

                $this->ensureNoPendingConflict($validatedData);

                $previousRejected = $this->findPreviousRejectedRequest($validatedData);

                $referredBy = null;
                if ($validatedData['referral_input'] != null) {
                    $referredBy = User::byReferralCode($validatedData['referral_input'])->first();
                    if ($referredBy == null)
                        throw new InvalidReferralCodeException('No user found with this code');
                }

                // $referredBy = $this->resolveReferrerId(
                //     $validatedData['referral_input'] ?? null,
                //     $validatedData['role']
                // );

                $accountRequest = AccountRequest::create([
                    'first_name' => $validatedData['first_name'],
                    'middle_name' => $validatedData['middle_name'] ?? null,
                    'last_name' => $validatedData['last_name'],
                    'birthdate' => $validatedData['birthdate'],
                    'country_id' => $validatedData['country_id'],
                    'language_id' => $validatedData['language_id'],
                    'phone_country_id' => $validatedData['phone_country_id'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'],
                    'username' => $validatedData['username'],
                    'password' => $validatedData['password'],
                    'role' => $validatedData['role'],
                    'referral_id' => $referredBy->id ?? User::superAdmin()->first()?->id, // Default to first super admin
                    'terms_accepted_at' => now(),
                    'resubmitted_from_id' => $previousRejected?->id,
                ]);
                event(new SignupRequestCreated($accountRequest->fresh()));
                return $accountRequest;
            });
        } catch (QueryException $exception) {
            throw $this->mapDatabaseException($exception);
        }
    }

    private function ensureNoPendingConflict(array $validatedData): void
    {
        $exists = AccountRequest::query()
            ->pending()
            ->where('email', $validatedData['email'])
            ->exists();

        if ($exists) {
            throw new SignupRequestConflictException(
                'A pending signup request already exists for this email or username.'
            );
        }
    }

    private function findPreviousRejectedRequest(array $validatedData): ?AccountRequest
    {
        if ($validatedData['resubmitted_from_id'] != null) {
            return AccountRequest::query()
                ->rejected()
                ->whereKey($validatedData['resubmitted_from_id'])
                ->where('email', $validatedData['email'])
                ->first();
        }
        return AccountRequest::query()
            ->rejected()
            ->where('email', $validatedData['email'])
            ->latest('id')
            ->first();
    }

    private function mapDatabaseException(QueryException $exception): SignupRequestConflictException
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'SIGNUP_REQUEST_EMAIL_EXISTS_IN_USERS')) {
            return new SignupRequestConflictException(
                'This email already belongs to an existing user account.'
            );
        }

        if (str_contains($message, 'SIGNUP_REQUEST_USERNAME_EXISTS_IN_USERS')) {
            return new SignupRequestConflictException(
                'This username already belongs to an existing user account.'
            );
        }

        if (str_contains($message, 'signup_requests_pending_email_unique')) {
            return new SignupRequestConflictException(
                'A pending signup request already exists for this email.'
            );
        }

        if (str_contains($message, 'signup_requests_pending_username_unique')) {
            return new SignupRequestConflictException(
                'A pending signup request already exists for this username.'
            );
        }

        return new SignupRequestConflictException(
            'Unable to submit signup request because of a data conflict.'
        );
    }

    // private function resolveReferrerId(
    //     ?string $referralCode = null,
    //     string $role_entered
    // ) {

    //     // $role = $referrer?->role->value ?? 'new_user';

    //     // if ($role_entered === 'educator' && $role !== 'super_admin') {
    //     //     throw new RolesDoesnotFollowRulesException('Only Admins can Add Educators');
    //     // }

    //     if ($referralCode != null) {
    //         $referredBy = User::where(
    //             'referral_code',
    //             $referralCode
    //         )->orWhere(
    //                 'username',
    //                 $referralCode
    //             )->first();

    //         if ($referredBy == null)
    //             throw new InvalidReferralCodeException('No user found with this code or username');
    //         if ($referredBy->referral_code != $referrer?->referral_code)
    //             throw new InvalidReferralCodeException('You must use your own referral code');

    //         return $referredBy->id;

    //     } elseif ($role !== 'new_user') {
    //         throw new InvalidReferralCodeException('You must provide a referral code');
    //     } else
    //         return null; // New users can sign up without a referrer
    // }
}
