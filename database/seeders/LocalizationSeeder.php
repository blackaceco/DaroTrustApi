<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Localization;
use Illuminate\Database\Seeder;

class LocalizationSeeder extends Seeder
{
    private $englishId;
    private $arabicId;

    public function __construct() {
        $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            $this->localizationGenerator("prev", "PREV", "السابق"),
            $this->localizationGenerator("next", "NEXT", "التالي"),
            $this->localizationGenerator("more", "More", "أكثر", "زیاتر"),
        ];



        // creating
        foreach ($data as $localizationField) {
            $localization = Localization::create($localizationField['fields']);

            foreach ($localizationField['translations'] as $translation) {
                $localization->details()->create($translation);
            }
        }
    }




    private function localizationGenerator($key, $value_en, $value_ar)
    {
        return [
            'fields' => [
                'websiteId' => 1,
                'key' => $key,
            ],

            'translations' => $this->translationGenerator($value_en, $value_ar)
        ];
    }


    private function translationGenerator($value_en, $value_ar)
    {
        $translations = [];


        // English
        $translations[] = [
            'languageId' => $this->englishId,
            'value' => $value_en,
        ];

        // Arabic
        $translations[] = [
            'languageId' => $this->arabicId,
            'value' => $value_ar,
        ];


        return $translations;
    }
}

