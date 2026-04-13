<?php

namespace App\Models;

use App\Enums\SignupRequestStatus;
use App\Support\Authorization\RolePermissionMatrix;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'country_id',
        'language_id',
        'phone_country_id',
        'terms_accepted_at',
        'phone',
        'email',
        'password',
        'role',
        'referral_id',
        'status',
        'rejected_at',
        'approved_at',
        'user_id',
        'rejection_reason',
        'reviewed_by',
        'who_fill_data_id',
        'resubmitted_from_id',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'birthdate' => 'date',
            'password' => 'hashed',
            'status' => SignupRequestStatus::class,
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getReviewedAtAttribute()
    {
        return $this->approved_at ?? $this->rejected_at ?? null;
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

    public function isRelevantTo(User $user): bool
    {
        if (
            $user->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_ALL)
            || $user->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_REVIEW_ALL)
        ) {
            return true;
        }

        if ($user->isNetworker()) {
            return $this->referral_id === $user->id;
        }

        return false;
    }

    public function scopeVisibleTo(Builder $query, User $user)
    {
        if ($user->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_ALL)) {
            return $query;
        }

        if ($user->hasPermission(RolePermissionMatrix::SIGNUP_REQUESTS_VIEW_RELEVANT)) {
            return $query->where('referral_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function hasStatus(SignupRequestStatus|string $status): bool
    {
        $value = $status instanceof SignupRequestStatus ? $status->value : $status;

        return $this->status?->value === $value;
    }

    public function scopePending($query)
    {
        return $query->where('status', SignupRequestStatus::Pending);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', SignupRequestStatus::Approved);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', SignupRequestStatus::Rejected);
    }

    public function isPending(): bool
    {
        return $this->hasStatus(SignupRequestStatus::Pending);
    }

    public function isApproved(): bool
    {
        return $this->hasStatus(SignupRequestStatus::Approved);
    }

    public function isRejected(): bool
    {
        return $this->hasStatus(SignupRequestStatus::Rejected);
    }

    public function previousSubmission()
    {
        return $this->belongsTo(self::class, 'resubmitted_from_id');
    }

    public function resubmissions()
    {
        return $this->hasMany(self::class, 'resubmitted_from_id');
    }

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }

    public function who_filled_data()
    {
        return $this->belongsTo(User::class, 'who_fill_data_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
