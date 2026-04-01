<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'phone_code',
        'is_active',
        'sort_order',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function accountRequests(): HasMany
    {
        return $this->hasMany(AccountRequest::class);
    }
    public function phoneUsers(): HasMany
    {
        return $this->hasMany(User::class, 'phone_country_id');
    }

    public function phoneAccountRequests(): HasMany
    {
        return $this->hasMany(AccountRequest::class, 'phone_country_id');
    }
}
