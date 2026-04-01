<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = json_decode(
            File::get(database_path('data/restcountries-v2.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        foreach ($items as $item) {
            Country::updateOrCreate(
                ['iso2' => $item['alpha2Code']],
                [
                    'name' => $item['name'],
                    'iso3' => $item['alpha3Code'] ?? null,
                    'phone_code' => $item['callingCodes'][0],
                    'is_active' => $item['is_active'] ?? true,
                    'sort_order' => $item['numericCode'] ?? 0,
                ]
            );
        }
    }
}
