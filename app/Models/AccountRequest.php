<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    protected $fillable = [
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'country',
        'language',
        'country_code',
        'terms_accepted',
        'referral_input',
        'phone',
        'email',
        'password',
        'role',
        'referral_id',
        'status',
        'rejected_at',
        'approved_at',
        'user_id'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
