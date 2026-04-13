<?php

namespace App\Support\Authorization;

use App\Enums\UserRole;

class RolePermissionMatrix
{
    public const USERS_CREATE_ADMIN = 'users.create.admin';
    public const USERS_CREATE_NETWORKER = 'users.create.networker';
    public const USERS_CREATE_EDUCATOR = 'users.create.educator';
    public const USERS_CREATE_REGULAR = 'users.create.regular';

    public const SIGNUP_REQUESTS_VIEW_ALL = 'signup-requests.view.all';
    public const SIGNUP_REQUESTS_VIEW_RELEVANT = 'signup-requests.view.relevant';

    public const SIGNUP_REQUESTS_REVIEW_ALL = 'signup-requests.review.all';
    public const SIGNUP_REQUESTS_REVIEW_RELEVANT = 'signup-requests.review.relevant';

    public static function permissions(): array
    {
        return [
            UserRole::SuperAdmin->value => [
                self::USERS_CREATE_ADMIN,
                self::USERS_CREATE_NETWORKER,
                self::USERS_CREATE_EDUCATOR,
                self::USERS_CREATE_REGULAR,
                self::SIGNUP_REQUESTS_VIEW_ALL,
                self::SIGNUP_REQUESTS_REVIEW_ALL,
            ],

            UserRole::Networker->value => [
                self::USERS_CREATE_NETWORKER,
                self::USERS_CREATE_REGULAR,
                self::SIGNUP_REQUESTS_REVIEW_RELEVANT,
                self::SIGNUP_REQUESTS_VIEW_RELEVANT,
            ],

            UserRole::Educator->value => [
                //
            ],

            UserRole::Regular->value => [
                //
            ],
        ];
    }

    public static function hasPermission(string $role, string $permission): bool
    {
        return in_array(
            $permission,
            static::permissions()[$role] ?? [],
            true
        );
    }
}
