<?php

namespace App\Policies;

use App\Models\AccountRequest;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Support\Authorization\RolePermissionMatrix;

class AccountRequestPolicy
{
    public function review(User $user, AccountRequest $accountRequest): bool
    {
        return app(
            AuthorizationService::class
        )->canReviewSignupRequest($user, $accountRequest);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_ALL)
            || $user->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_RELEVANT);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AccountRequest $accountRequest): bool
    {
        return app(
            AuthorizationService::class
        )->canViewSignupRequest($user, $accountRequest);
    }

    /**
     * Determine whether the user can create models.
     */
    public function createAccountRequest(User $user, string $targetRole): bool
    {
        dd('create policy hit');
        return app(
            AuthorizationService::class
        )->canCreateAccountRequestType($user, $targetRole);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AccountRequest $accountRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AccountRequest $accountRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AccountRequest $accountRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AccountRequest $accountRequest): bool
    {
        return false;
    }
}
