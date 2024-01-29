<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Website;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $website = [
            'title' => "Real Story",
            'slug' => "real-story",
            'propertyId' => null,
        ];


        $languages = [];
        foreach (Language::get() as $lang)
        {
            $languages[$lang['id']] = [
                'active' => true,
                'default' => ($lang['abbreviation'] == env("DEFAULT_LANGUAGE_ABBREVATION", "en")),
            ];
        }


        (Website::create($website))->languages()->sync( $languages );
    }
}
