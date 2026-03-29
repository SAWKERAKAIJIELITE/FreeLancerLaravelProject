<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class ReferralCodeGenerator
{
    public function generate(int $length = 10): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
