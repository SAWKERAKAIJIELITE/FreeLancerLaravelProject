<?php

namespace App\Services;

use App\Exceptions\InvalidReferralCodeException;
use App\Exceptions\RolesDoesnotFollowRulesException;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountRequestService
{
    public function create(array $validatedData, ?User $referrer): AccountRequest
    {
        return DB::transaction(function () use ($validatedData, $referrer) {

            $referredBy = $this->resolveReferrerId(
                $validatedData['referral_input'] ?? null,
                $referrer,
                $validatedData['role']
            );

            return AccountRequest::create([
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
                'referral_id' => $referredBy,
                'terms_accepted_at' => now(),
            ]);
        });
    }

    private function resolveReferrerId(
        ?string $referralCode = null,
        ?User $referrer,
        string $role_entered
    ) {
        // $roleHierarchy = [
        //     'super_admin' => 'educator',
        //     'educator' => 'regular',
        //     'regular' => 'regular',
        //     'new_user' => 'regular',
        // ];

        $role = $referrer?->role ?? 'new_user';

        if ($role_entered === 'regular' && $role === 'super_admin') {
            throw new RolesDoesnotFollowRulesException('Admins can only Add Educators');
        }
        if ($role_entered === 'educator' && $role !== 'super_admin') {
            throw new RolesDoesnotFollowRulesException('Only Admins can Add Educators');
        }

        if ($referralCode != null) {
            $referredBy = User::where(
                'referral_code',
                $referralCode
            )->orWhere(
                    'username',
                    $referralCode
                )->first();

            if ($referredBy == null)
                throw new InvalidReferralCodeException('No user found with this code or username');
            if ($referredBy->referral_code != $referrer?->referral_code)
                throw new InvalidReferralCodeException('You must use your own referral code');

            return $referredBy->id;

        } elseif ($role !== 'new_user') {
            throw new InvalidReferralCodeException('You must provide a referral code');
        } else
            return null; // New users can sign up without a referrer
    }
}
