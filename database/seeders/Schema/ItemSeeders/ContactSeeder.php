<?php

namespace Database\Seeders\Schema\ItemSeeders;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
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
        // # ---> 94
        $this->bannerFieldsArrayGenerator(22, "banner", [15]);

        // # ---> 95
        $this->coordinatesFieldsArrayGenerator(23, "coordinates", [15]);

        // # ---> 96
        $this->contactFieldsArrayGenerator(24, "email", [16], "info@domain.com", "info@domain.com", "info@domain.com");

        // # ---> 97
        $this->contactFieldsArrayGenerator(25, "phone", [16], "+9647700000000", "+9647700000000", "+9647700000000");

        // # ---> 98
        $this->contactFieldsArrayGenerator(26, "address", [16], "Address xxxxxx", "العنوان xxxxxx", "ناونیشانی xxxxxx");

        // # ---> 99
        $this->contactFieldsArrayGenerator(27, "working_hours", [16], "Working hours: 09:00am to 05:00pm", "ساعات العمل: 09:00 صباحا – 05:00 مساء", "کاتژمێرەکانی کارکردن: 09:00 بەیانی تا 05:00ی ئێوارە");


        
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

        $title_en = "Contact us";
        $title_ar = "اتصل بنا";
        $title_ku = "پەیوەندیمان پێوە بکە";

        
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

    private function coordinatesFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds)
    {
        $details = [];

        $coordinates = "36.22156,43.7853093";

        
        $details = array_merge($details, $this->translationGenerator('map', 'coordinates', $coordinates, $coordinates, $coordinates));
        
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

    private function contactFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $value_en, $value_ar, $value_ku)
    {
        $details = [];

        $details = array_merge($details, $this->translationGenerator('long_text', $featureTitle, $value_en, $value_ar, $value_ku));
        
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
