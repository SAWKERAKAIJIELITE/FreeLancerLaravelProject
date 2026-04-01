<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;

class ReferenceDataService
{
    public function countries()
    {
        return Cache::rememberForever('reference.countries', function () {
            return Country::query()
                ->active()
                ->orderBy('sort_order', 'asc')
                ->orderBy('name')
                ->get(['id', 'name', 'iso2', 'phone_code']);
        });
    }

    public function languages()
    {
        return Cache::rememberForever('reference.languages', function () {
            return Language::query()
                ->active()
                ->orderBy('sort_order','asc')
                ->orderBy('name')
                ->get(['id', 'name', 'native_name', 'code']);
        });
    }

    public function phoneCountries()
    {
        return Cache::rememberForever('reference.phone_countries', function () {
            return Country::query()
                ->active()
                ->orderBy('sort_order', 'asc')
                ->orderBy('name')
                ->get(['id', 'name', 'iso2', 'phone_code']);
        });
    }

    public function clearCache(): void
    {
        Cache::forget('reference.countries');
        Cache::forget('reference.languages');
        Cache::forget('reference.phone_countries');
    }
}
