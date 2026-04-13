<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Events\SignupRequestCreated;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class UserCreationService
{
    public function __construct(
        private AuthorizationService $authorizationService,
    ) {
    }

    public function create(User $actor, array $data): AccountRequest
    {
        $targetRole = $data['role'];

        if (!$this->authorizationService->canCreateAccountRequestType($actor, $targetRole)) {
            throw new AuthorizationException('You are not allowed to add this type of user.');
        }

        return DB::transaction(function () use ($actor, $data) {

            $accountRequest =  AccountRequest::create([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'birthdate' => $data['birthdate'],
                'country_id' => $data['country_id'],
                'language_id' => $data['language_id'],
                'phone_country_id' => $data['phone_country_id'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $data['role'],
                'referral_id' => $actor->id,
                'who_fill_data_id'=> $actor->id,
                'terms_accepted_at' => now(),
            ]);
            event(new SignupRequestCreated($accountRequest->fresh()));
            return $accountRequest;
        });
    }
}
