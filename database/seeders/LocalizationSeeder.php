<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Localization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalizationSeeder extends Seeder
{
    private $englishId;
    private $arabicId;
    private $kurdishId;

    public function __construct() {
        $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;
        $this->kurdishId = Language::where('abbreviation', 'ku')->first()->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            $this->localizationGenerator("about_us", "About Us", "من نحن", "دەربارەی ئێمه"),
            $this->localizationGenerator("more", "More", "أكثر", "زیاتر"),
            $this->localizationGenerator("view", "View", "منظر", "پیشاندان"),

            $this->localizationGenerator("videos", "Videos", "الفيديوات", "ڤیدیۆکان"),
            $this->localizationGenerator("by", "By", "بواسطة", "لە لایەن"),
            $this->localizationGenerator("discover_more", "Discover More", "اكتشف المزيد", "زیاتر بدۆزەوە"),
            $this->localizationGenerator("all", "all", "كل", "هەموو"),
            $this->localizationGenerator("location", "location", "مكان", "شوێن"),
            $this->localizationGenerator("date", "date", "تاريخ", "بەروار"),
            $this->localizationGenerator("published", "published", "نشر", "بڵاو کرایەوە"),
            $this->localizationGenerator("read_more", "Read more", "طالع المزيد", "زیاتر بخوێنەوە"),

            $this->localizationGenerator("festivals_not_found", "Apologies, no festivals found.", "اعتذار ، لم يتم العثور على مهرجانات.", "داوای لێبوردن: هیچ ڤیستیڤاڵێک نەدۆزرایەوە"),
            $this->localizationGenerator("documentaries_not_found", "Apologies, no documentaries found.", "اعتذار ، لم يتم العثور على أفلام وثائقية.", "داوای لێبوردن: هیچ بەڵگەنامەیەک نەدۆزرایەوە"),
            $this->localizationGenerator("promos_not_found", "Apologies, no promos found.", "اعتذار ، لم يتم العثور على عروض ترويجية.", "داوای لێبوردن: هیچ پرۆمۆیەک نەدۆزرایەوە"),
            $this->localizationGenerator("trainings_not_found", "Apologies, no trainings found.", "اعتذار ، لم يتم العثور على تدريبات.", "داوای لێبوردن: هیچ ڕاهێنانێک نەدۆزرایەوە"),
            $this->localizationGenerator("storytelling_not_found", "Apologies, no storytelling found.", "اعتذار ، لم يتم العثور على سرد القصص.", "داوای لێبوردن: هیچ چیرۆکێک نەدۆزرایەوە"),
        ];



        // creating
        foreach ($data as $localizationField) {
            $localization = Localization::create($localizationField['fields']);

            foreach ($localizationField['translations'] as $translation) {
                $localization->details()->create($translation);
            }
        }
    }




    private function localizationGenerator($key, $value_en, $value_ar, $value_ku)
    {
        return [
            'fields' => [
                'websiteId' => 1,
                'key' => $key,
            ],

            'translations' => $this->translationGenerator($value_en, $value_ar, $value_ku)
        ];
    }


    private function translationGenerator($value_en, $value_ar, $value_ku)
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

        // Kurdish
        $translations[] = [
            'languageId' => $this->kurdishId,
            'value' => $value_ku,
        ];


        return $translations;
    }
}

