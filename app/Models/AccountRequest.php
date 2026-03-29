<?php

namespace App\Models;

// use App\Enums\SignupRequestStatus;
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
        'country',
        'language',
        'country_code',
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
        'reviewed_by'
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
            'status' => 'string',
             // 'status'=> SignupRequestStatus::class
            // 'status'=> SignupRequestStatus::class
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    // public function scopePending($query)
    // {
    //     return $query->where('status', SignupRequestStatus::Pending);
    // }

    // public function scopeApproved($query)
    // {
    //     return $query->where('status', SignupRequestStatus::Approved);
    // }

    // public function scopeRejected($query)
    // {
    //     return $query->where('status', SignupRequestStatus::Rejected);
    // }

    // public function isPending(): bool
    // {
    //     return $this->status === SignupRequestStatus::Pending;
    // }

    // public function isApproved(): bool
    // {
    //     return $this->status === SignupRequestStatus::Approved;
    // }

    // public function isRejected(): bool
    // {
    //     return $this->status === SignupRequestStatus::Rejected;
    // }

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
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
