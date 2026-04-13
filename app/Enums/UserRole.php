<?php

namespace App\Enums;


enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Networker = 'networker';
    case Educator = 'educator';
    case Regular = 'regular';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
