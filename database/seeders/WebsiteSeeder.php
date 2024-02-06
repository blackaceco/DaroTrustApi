<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Website;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $website = [
            'title' => "Daro Trust",
            'slug' => "daro-trust",
            'propertyId' => null,
        ];

        


        $languages = [];
        foreach (Language::get() as $lang)
        {
            $languages[$lang['id']] = [
                'active' => true,
                'default' => ($lang['abbreviation'] == "en"),
            ];
        }


        (Website::create($website))->languages()->sync( $languages );
    }
}
