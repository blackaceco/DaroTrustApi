<?php

namespace Database\Seeders\Schema\ItemSeeders;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
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
        // # ---> 85
        // $this->bannerFieldsArrayGenerator(28, "banner", [17]);


        // # ---> 100
        $this->videoFieldsArrayGenerator(29, "promo", "videos/1.mp4", [17]);
        // # ---> 101
        $this->videoFieldsArrayGenerator(29, "promo", "videos/2.mp4", [17]);
        // # ---> 102
        $this->videoFieldsArrayGenerator(29, "promo", "videos/1.mp4", [17]);
        // # ---> 103
        $this->videoFieldsArrayGenerator(29, "promo", "videos/2.mp4", [17]);
        // # ---> 104
        $this->videoFieldsArrayGenerator(29, "promo", "videos/1.mp4", [17]);
        // # ---> 105
        $this->videoFieldsArrayGenerator(29, "promo", "videos/2.mp4", [17]);
        
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

        $title_en = "Promo";
        $title_ar = "الترويجي";
        $title_ku = "پرۆمۆ";

        
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

    private function videoFieldsArrayGenerator($schemaId, $featureTitle, $video, $pageGroupIds)
    {
        $details = [];

        $title_en = "Promo Video";
        $title_ar = "فيديو ترويجي";
        $title_ku = "پڕۆمۆ ڤیدیۆ";

        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('video', 'video', $video, $video, $video));

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
}
