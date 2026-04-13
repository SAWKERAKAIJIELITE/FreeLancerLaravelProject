<?php

namespace App\Enums;


enum SignupRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'accepted';
    case Rejected = 'rejected';
}
