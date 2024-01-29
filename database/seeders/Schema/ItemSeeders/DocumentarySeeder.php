<?php

namespace Database\Seeders\Schema\ItemSeeders;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentarySeeder extends Seeder
{
    private $englishId;
    private $arabicId;
    private $kurdishId;
    private $order = 1;
    private $items = [];

    public function __construct()
    {
        $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;
        $this->kurdishId = Language::where('abbreviation', 'ku')->first()->id;

        $this->order = Item::max('order') ?? 0 +1;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 20
        $this->documentaryFieldsArrayGenerator(4, "documentary", [4, 5], "home-banner/2.jpg", "videos/1.mp4", "Oil Cancer Series", "سلسلة سرطان الزيت", "زنجیرەی شێرپەنجەی ڕۆن");
        
        // # ---> 21
        $this->documentaryFieldsArrayGenerator(4, "documentary", [4, 5], "home-banner/1.jpg", "videos/2.mp4", "The 3Rd Side", "الجانب 3", "بەشی سێیەم");

        // # ---> 22
        $this->documentaryFieldsArrayGenerator(4, "documentary", [4, 5], "home-banner/2.jpg", "videos/1.mp4", "Oil Cancer Series", "سلسلة سرطان الزيت", "زنجیرەی شێرپەنجەی ڕۆن");
        
        // # ---> 23
        $this->documentaryFieldsArrayGenerator(4, "documentary", [4, 5], "home-banner/1.jpg", "videos/2.mp4", "The 3Rd Side", "الجانب 3", "بەشی سێیەم");
        

        // # ---> 24  -  30
        for ($i=0; $i < 7; $i++) { 
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/1.mp4", 20);
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/2.mp4", 20);
        }
        

        // # ---> 31  -  37
        for ($i=0; $i < 7; $i++) { 
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/1.mp4", 21);
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/2.mp4", 21);
        }
        

        // # ---> 38  -  44
        for ($i=0; $i < 7; $i++) { 
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/1.mp4", 22);
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/2.mp4", 22);
        }
        

        // # ---> 45  -  51
        for ($i=0; $i < 7; $i++) { 
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/1.mp4", 23);
            // # ---> #
            $this->episodeFieldsArrayGenerator(5, "episode", "videos/2.mp4", 23);
        }


        
        // ###############################################################
        // #                           Creating                          #
        // ###############################################################

        foreach ($this->items as $item)
            $this->createItem($item['fields'], $item['details'], $item['pageGroupIds'] ?? [], $item['groupId'] ?? null, $item['parentId'] ?? null);
    }








    private function createItem($fields, $details, $pageGroupIds, $groupId, $parentId = null)
    {
        // create item
        $item = Item::create($fields);

        // add groups
        if (!is_null($groupId ?? null))
            $item->groups()->attach($groupId);

        // prepare details
        foreach ($details as $key => $detail) {
            if (is_null($detail))
                continue;

            $details[$key] = [
                'itemId' => $item->id,
                'languageId' => $detail['languageId'],
                'valueType' => $detail['valueType'],
                'key' => $detail['key'],
                'value' => $detail['value'],
                'order' => $this->order++,
            ];
        }

        // attaching details
        $item->details()->createMany($details);

        // add page group if there is no any parentId available
        if (is_null($parentId))
            foreach ($pageGroupIds as $pageGroupId)
                $item->pageGroups()->attach($pageGroupId);

        // add parent if exist
        if (!is_null($parentId))
            ItemHierarchy::create([
                'childId' => $item->id,
                'parentId' => $parentId,
            ]);
    }

    private function translationGenerator($type, $key, $value_en, $value_ar, $value_ku)
    {
        $translations = [];

        // English
        $translations[] = [
            'languageId' => $this->englishId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_en,
        ];

        // Arabic
        $translations[] = [
            'languageId' => $this->arabicId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_ar,
        ];

        // Kurdish
        $translations[] = [
            'languageId' => $this->kurdishId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_ku,
        ];


        return $translations;
    }


    
    /**
     * Generating fields
     */
    private function documentaryFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $image, $video, $title_en, $title_ar, $title_ku)
    {
        $details = [];

        $author_en = "John Doe";
        $author_ar = "جون دو";
        $author_ku = "جۆن دۆی";

        $published_date_en = now()->subDays( rand(0, 75) );

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        $details = array_merge($details, $this->translationGenerator('image', 'image', $image, $image, $image));
        $details = array_merge($details, $this->translationGenerator('video', 'video', $video, $video, $video));
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('short_text', 'author', $author_en, $author_ar, $author_ku));
        $details = array_merge($details, $this->translationGenerator('date', 'published_date', $published_date_en, $published_date_en, $published_date_en));
        $details = array_merge($details, $this->translationGenerator('editor', 'description', $description_en, $description_ar, $description_ku));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'pageGroupIds' => $pageGroupIds,

            'groupId' => rand(1, 4)
        ];
    }

    private function episodeFieldsArrayGenerator($schemaId, $featureTitle, $video, $parentId)
    {
        $details = [];

        $title_en = "Episode 1: How it started";
        $title_ar = "الحلقة 1: كيف بدأت";
        $title_ku = "ئەڵقەی ۱: چۆن دەستی پێکرد";

        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('video', 'video', $video, $video, $video));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'parentId' => $parentId
        ];
    }
}
