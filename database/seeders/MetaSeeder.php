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
    }
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            $this->metaGenerator("home", ['en' => "Home",   'ar' => "الصفحه الرئيسيه"]),
            $this->metaGenerator("about", ['en' => "About",   'ar' => "عن"]),
            $this->metaGenerator("contact", ['en' => "Contact",   'ar' => "الاتصال"]),
            $this->metaGenerator("services", ['en' => "Services",   'ar' => "خدمات"]),
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
            'websiteName' => 'Daro Trust',
            'description' => "This should be a meta description texts instead of lorem ipsum dolor",
            'image' => "img/logo.svg",
            'keywords' => "daro,trust,daro_trust,daro-trust,transportation,service,industry"
        ];

        // Arabic
        $details[] = [
            'languageId' => $this->arabicId,
            'title' => $titles['ar'],
            'websiteName' => 'Daro Trust',
            'description' => "يجب أن يكون هذا نصوصا وصفا تعريفيا بدلا من lorem ipsum dolor",
            'image' => "img/logo.svg",
            'keywords' => "daro,trust,daro_trust,daro-trust,transportation,service,industry"
        ];


        return $details;
    }
}
