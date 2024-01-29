<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'title' => "English",
                'abbreviation' => "en",
                'direction' => "ltr"
            ],

            [
                'title' => "العربية",
                'abbreviation' => "ar",
                'direction' => "rtl"
            ],

            [
                'title' => "Kurdish",
                'abbreviation' => "ku",
                'direction' => "rtl"
            ],
        ];


        // creating
        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
