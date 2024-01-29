<?php

namespace Database\Seeders\Schema\ItemSeeders;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
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
        // # ---> 57
        // $this->bannerFieldsArrayGenerator(13, "banner", [10]);

        // # ---> 68
        $this->trainingFieldsArrayGenerator(14, "training", [10], "storytelling/2.jpg", "storytelling/1.jpg", "Storytelling Title", "عنوان رواية القصص", "ناونیشانی چیرۆک");
        
        // # ---> 69
        $this->trainingFieldsArrayGenerator(14, "training", [10], "storytelling/4.jpg", "storytelling/3.jpg", "Storytelling Title", "عنوان رواية القصص", "ناونیشانی چیرۆک");

        // # ---> 70
        $this->trainingFieldsArrayGenerator(14, "training", [10], "storytelling/5.jpg", "storytelling/8.jpg", "Storytelling Title", "عنوان رواية القصص", "ناونیشانی چیرۆک");

        // # ---> 71
        $this->trainingFieldsArrayGenerator(14, "training", [10], "storytelling/6.jpg", "storytelling/1.jpg", "Storytelling Title", "عنوان رواية القصص", "ناونیشانی چیرۆک");
        
        // # ---> 72
        $this->trainingFieldsArrayGenerator(14, "training", [10], "storytelling/7.jpg", "storytelling/3.jpg", "Storytelling Title", "عنوان رواية القصص", "ناونیشانی چیرۆک");


        
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
    private function bannerFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds)
    {
        $details = [];

        $title_en = "Storytelling";
        $title_ar = "القص";
        $title_ku = "چیرۆک";

        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'pageGroupIds' => $pageGroupIds
        ];
    }

    private function trainingFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $gallery, $image, $title_en, $title_ar, $title_ku)
    {
        $details = [];

        $author_en = "John Doe";
        $author_ar = "جون دو";
        $author_ku = "جۆن دۆی";

        $published_date_en = now()->subDays( rand(0, 75) );

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('image', 'image', $image, $image, $image));
        $details = array_merge($details, $this->translationGenerator('gallery', 'gallery', $gallery, $gallery, $gallery));
        $details = array_merge($details, $this->translationGenerator('short_text', 'author', $author_en, $author_ar, $author_ku));
        $details = array_merge($details, $this->translationGenerator('date', 'published_date', $published_date_en, $published_date_en, $published_date_en));
        $details = array_merge($details, $this->translationGenerator('textarea', 'summary', $description_en, $description_ar, $description_ku));
        $details = array_merge($details, $this->translationGenerator('editor', 'description', $description_en, $description_ar, $description_ku));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'pageGroupIds' => $pageGroupIds,

            'groupId' => rand(5, 8)
        ];
    }
}
