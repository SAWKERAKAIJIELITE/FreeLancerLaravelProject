<?php

namespace App\Services;

use App\Models\AccountRequest;
use App\Models\User;
use App\Enums\UserRole;
use App\Support\Authorization\RolePermissionMatrix;

class AuthorizationService
{
    public function canCreateAccountRequestType(User $actor, string $targetRole): bool
    {
        return match ($targetRole) {
            UserRole::SuperAdmin->value => $actor->hasPermission(RolePermissionMatrix::USERS_CREATE_ADMIN),
            UserRole::Networker->value => $actor->hasPermission(RolePermissionMatrix::USERS_CREATE_NETWORKER),
            UserRole::Educator->value => $actor->hasPermission(RolePermissionMatrix::USERS_CREATE_EDUCATOR),
            UserRole::Regular->value => $actor->hasPermission(RolePermissionMatrix::USERS_CREATE_REGULAR),
            default => false,
        };
    }

    public function canViewSignupRequest(User $actor, AccountRequest $accountRequest): bool
    {
        if ($actor->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_ALL)) {
            return true;
        }

        if ($actor->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_RELEVANT)) {
            return $accountRequest->isRelevantTo($actor);
        }

        return false;
    }

    public function canReviewSignupRequest(User $actor, AccountRequest $accountRequest): bool
    {
        if ($actor->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_REVIEW_ALL)) {
            return true;
        }

        if ($actor->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_REVIEW_RELEVANT)) {
            return $accountRequest->isRelevantTo($actor);
        }

        return false;
    }
}
