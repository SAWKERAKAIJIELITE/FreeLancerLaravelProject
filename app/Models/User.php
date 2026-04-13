<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use App\Support\Authorization\RolePermissionMatrix;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function hasRole(UserRole|string $role): bool
    {
        $value = $role instanceof UserRole ? $role->value : $role;

        return $this->role?->value === $value;
    }

    public function hasPermission(string $permission): bool
    {
        return RolePermissionMatrix::hasPermission($this->role?->value ?? '', $permission);
    }

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
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::SuperAdmin);
    }

    public function isNetworker(): bool
    {
        return $this->hasRole(UserRole::Networker);
    }

    public function isRegular(): bool
    {
        return $this->hasRole(UserRole::Regular);
    }

    public function isEducator(): bool
    {
        return $this->hasRole(UserRole::Educator);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function scopeByReferralCode(Builder $query, string $code)
    {
        return $query->where('referral_code', $code);
    }

    public function scopeSuperAdmin(Builder $query)
    {
        return $query->where('role', UserRole::SuperAdmin->value);
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

    public function filledByMe()
    {
        return $this->hasMany(AccountRequest::class, 'who_fill_data_id');
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
