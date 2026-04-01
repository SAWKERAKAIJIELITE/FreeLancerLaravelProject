<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'super_admin';
    public const ROLE_USER = 'regular';
    public const ROLE_EDUCATOR = 'educator';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'referred_by',
        'birthdate',
        'country_id',
        'language_id',
        'phone_country_id',
        'phone',
        'terms_accepted_at',
        'referral_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'birthdate' => 'date',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isEducator(): bool
    {
        return $this->role === self::ROLE_EDUCATOR;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function scopeByReferralCode($query, string $code)
    {
        return $query->where('referral_code', $code);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function phoneCountry()
    {
        return $this->belongsTo(Country::class, 'phone_country_id');
    }

    // Users referred by this user
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    // Who referred this user
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function accountRequests()
    {
        return $this->hasMany(AccountRequest::class, 'referral_id');
    }

    public function reviews()
    {
        return $this->hasMany(AccountRequest::class, 'reviewed_by');
    }
}
