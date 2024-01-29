<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Meta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetaSeeder extends Seeder
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
            $this->metaGenerator("home", ['en' => "Home",     'ar' => "الصفحه الرئيسيه",     'ku' => "پەڕەی سەرەکی"]),
            $this->metaGenerator("about", ['en' => "About",     'ar' => "عن",     'ku' => "دەربارە"]),
            $this->metaGenerator("contact", ['en' => "Contact",     'ar' => "الاتصال",     'ku' => "پەیوەندی"]),
            $this->metaGenerator("training", ['en' => "Training",     'ar' => "تدريب",     'ku' => "ڕاهێنان"]),
            $this->metaGenerator("promo", ['en' => "Promo",     'ar' => "الترويجي",     'ku' => "پرۆمۆ"]),
            $this->metaGenerator("documentary", ['en' => "Documentary",     'ar' => "وثائقي",     'ku' => "دۆکیومێنتاری"]),
            $this->metaGenerator("storytelling", ['en' => "Storytelling",     'ar' => "القص",     'ku' => "چیرۆک"]),
            $this->metaGenerator("festival", ['en' => "Festival",     'ar' => "مهرجان",     'ku' => "فیستیڤاڵ"]),
        ];


        // creating
        foreach ($data as $meta) {
            $created_meta = Meta::create($meta['fields']);
            $created_meta->details()->createMany($meta['details']);
        }
    }


    private function metaGenerator($page, $titles)
    {
        return [
            'fields' => [
                'websiteId' => 1,
                'page' => $page
            ],

            'details' => $this->translationGenerator($titles)
        ];
    }


    private function translationGenerator($titles)
    {
        $details = [];


        // English
        $details[] = [
            'languageId' => $this->englishId,
            'title' => $titles['en'],
            'websiteName' => 'Real Story',
            'description' => "This should be a meta description texts instead of lorem ipsum dolor",
            'image' => "home-about\/6.jpg",
            'keywords' => "real,story,website,file"
        ];

        // Arabic
        $details[] = [
            'languageId' => $this->arabicId,
            'title' => $titles['ar'],
            'websiteName' => 'Real Story',
            'description' => "يجب أن يكون هذا نصوصا وصفا تعريفيا بدلا من lorem ipsum dolor",
            'image' => "home-about\/6.jpg",
            'keywords' => "real,story,website,file"
        ];

        // Kurdish
        $details[] = [
            'languageId' => $this->kurdishId,
            'title' => $titles['ku'],
            'websiteName' => 'Real Story',
            'description' => "ئەمە دەبێت تێکستێکی وەسفی مێتا بێت لەجیاتی لۆرم ئایپسۆم دۆلۆر",
            'image' => "home-about\/6.jpg",
            'keywords' => "real,story,website,file"
        ];


        return $details;
    }
}
