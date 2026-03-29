<?php

namespace App\Services;

use App\Exceptions\InvalidReferralCodeException;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountRequestService
{
    public function create(array $validatedData): AccountRequest
    {
        return DB::transaction(function () use ($validatedData) {

        // dd($validatedData['referral_input']);

        $referredBy = $this->resolveReferrerId(
                $validatedData['referral_input'] ?? null
            );

            return AccountRequest::create([
                'first_name' => $validatedData['first_name'],
                'middle_name' => $validatedData['middle_name'] ?? null,
                'last_name' => $validatedData['last_name'],
                'birthdate' => $validatedData['birthdate'],
                'country' => $validatedData['country'],
                'language' => $validatedData['language'],
                'country_code' => $validatedData['country_code'],
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

    private function resolveReferrerId(?string $referralCode = null)
    {
        if ($referralCode!=null) {
            $referredBy = User::where(
                'referral_code',
                $referralCode
            )->orWhere(
                    'username',
                    $referralCode
                )->first();
            // dd($referredBy);

            if ($referredBy==null)
                throw new InvalidReferralCodeException('No user found with this code or username');
        } else
            $referredBy = User::where('role', 'super_admin')->first();

        return $referredBy->id;
    }
}
