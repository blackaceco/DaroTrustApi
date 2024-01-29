<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupType;
use App\Models\Language;
use App\Models\SchemaFeatureGroupType;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    private $englishId;
    private $arabicId;
    private $kurdishId;

    public function __construct()
    {
        $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $categoryGroupType = $this->createGroupType("categories", "Group", "مجموعة", "گروپ", 4, false, [
        //     [
        //         'key' => "title",
        //         'type' => "short_text",
        //     ]
        // ]);

        $groups = [
            // 1
            // [
            //     'fields' => ['websiteId' => 1, 'featureTitle' => "documentary", 'groupTypeId' => $categoryGroupType->id],
            //     'details' => $this->translationGenerator("short_text", "title", "History", "تاريخ", "مێژوو"),
            // ],
            
        ];


        // creating
        // foreach ($groups as $group)
        //     Group::create($group['fields'])->details()->createMany($group['details']);
    }




    private function createGroupType($type, $title_en, $title_ar, $title_ku, $schemaFeatureId, $multiple, $schemaTypes)
    {
        $group = GroupType::create([
            'type' => $type
        ]);

        $group->details()->createMany([
            [
                'languageId' => $this->englishId,
                'title' => $title_en
            ],

            [
                'languageId' => $this->arabicId,
                'title' => $title_ar
            ],

            [
                'languageId' => $this->kurdishId,
                'title' => $title_ku
            ],
        ]);

        foreach ($schemaTypes as $type) {
            $group->schemaTypes()->create([
                'websiteId' => 1,
                'groupTypeId' => $group->id,
                'valueKey' => $type['key'],
                'valueType' => $type['type'],
            ]);
        }

        SchemaFeatureGroupType::create([
            'groupTypeId' => $group->id,
            'schemaFeatureId' => $schemaFeatureId,
            'multiple' => $multiple,
        ]);

        return $group;
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
}
