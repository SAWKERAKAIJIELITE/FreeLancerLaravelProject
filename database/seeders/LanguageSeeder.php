<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = json_decode(
            File::get(database_path('data/languages.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        foreach ($items as $item) {
            Language::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'native_name' => $item['native_name'] ?? null,
                    'is_active' => $item['is_active'] ?? true,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]
            );
        }
    }
}
