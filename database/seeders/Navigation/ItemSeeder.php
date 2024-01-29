<?php

namespace Database\Seeders\Navigation;

use App\Models\Language;
use App\Models\NavigationGroupItem;
use App\Models\NavigationItem;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    private $englishId;
    private $arabicId;
    private $kurdishId;
    private $order = 1;

    public function __construct()
    {
        $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;
        $this->kurdishId = Language::where('abbreviation', 'ku')->first()->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1
        $this->createItem(1, "banner", 1, [
            'title' => [
                'type' => "short_text",
                'value_en' => "MENU",
                'value_ar' => "قائمة",
                'value_ku' => "پێڕست",
            ]
        ]);


        // 2 , 6
        $this->topMenu();


        // 7 , 11
        $this->bottomMenu();
        

        // 23 , 28
        $this->footer();
    }


    private function topMenu()
    {
        /**
         * Banner title
         */
        // # ---> 2
        $this->createItem(2, "banner", 2, [
            'title' => [
                'type' => "short_text",
                'value_en' => "Links",
                'value_ar' => "الصلات",
                'value_ku' => "لینکەکان",
            ]
        ]);


        /**
         * Links
         */
        $items = [
            // # ---> 3
            '/' => ["Home", "الصفحة الرئيسية", "پەڕەی سەرەکی"],
            // # ---> 4
            '/about' => ["About us", "من نحن", "دەربارەی ئێمە"],
            // # ---> 5
            '/contact' => ["Contact Us", "اتصل بنا", "پەیوەندیمان پێوە بکە"],
            // # ---> 6
            '/trainings' => ["Trainings", "التدريبات", "ڕاهێنانەکان"],
        ];

        // creating items
        foreach ($items as $url => $item)
        {
            $this->createItem(3, "nav_item", 2, [
                'title' => [
                    'type' => "long_text",
                    'value_en' => $item[0],
                    'value_ar' => $item[1],
                    'value_ku' => $item[2],
                ],
                'url' => [
                    'type' => "long_text",
                    'value_en' => $url,
                    'value_ar' => $url,
                    'value_ku' => $url,
                ]
            ]);
        }
    }
    
    private function bottomMenu()
    {
        /**
         * Banner title
         */
        // # ---> 7
        $this->createItem(4, "banner", 3, [
            'title' => [
                'type' => "short_text",
                'value_en' => "Work",
                'value_ar' => "عمل",
                'value_ku' => "کار",
            ]
        ]);


        /**
         * Links
         */
        $items = [
            // # ---> 8
            '/promo' => ["Promo", "الترويجي", "پرۆمۆ"],
            // # ---> 9
            '/documentary' => ["Documentary", "وثائقي", "دۆکیومێنتاری"],
            // # ---> 10
            '/storytelling' => ["Storytelling", "القص", "چیرۆک"],
            // # ---> 11
            '/festivals' => ["Festival", "مهرجان", "فیستیڤاڵ"],
        ];

        // creating items
        foreach ($items as $url => $item)
        {
            $this->createItem(5, "nav_item", 3, [
                'title' => [
                    'type' => "long_text",
                    'value_en' => $item[0],
                    'value_ar' => $item[1],
                    'value_ku' => $item[2],
                ],
                'url' => [
                    'type' => "long_text",
                    'value_en' => $url,
                    'value_ar' => $url,
                    'value_ku' => $url,
                ]
            ]);
        }
    }


    private function footer()
    {
        // # ---> 12
        $this->createItem(6, "copyright", 4, [
            'title' => [
                'type' => "long_text",
                'value_en' => "All rights reserved to Real Story 2023",
                'value_ar' => "جميع الحقوق محفوظة لريل ستوري 2023",
                'value_ku' => "هەموو مافەکان پارێزراوە بۆ Real Story 2023",
            ]
        ]);


        // socials
        $socials = [
            // # ---> 13
            'facebook.com' => ["fa6-brands:facebook-f", "fa6-brands:facebook-f", "fa6-brands:facebook-f"],
            // # ---> 14
            'instagram.com' => ["fa6-brands:twitter", "fa6-brands:twitter", "fa6-brands:twitter"],
            // # ---> 15
            'youtube.com' => ["fa6-brands:instagram", "fa6-brands:instagram", "fa6-brands:instagram"],
            // // # ---> 16
            // 'linkedin.com' => ["linkedin", "linkedin", "linkedin"],
        ];

        // creating socials
        foreach ($socials as $url => $social)
            $this->createItem(7, "social", 4, [
                'icon' => [
                    'type' => "long_text",
                    'value_en' => $social[0],
                    'value_ar' => $social[1],
                    'value_ku' => $social[2],
                ],
                'url' => [
                    'type' => "long_text",
                    'value_en' => $url,
                    'value_ar' => $url,
                    'value_ku' => $url,
                ]
            ]);
    }







    private function createItem($schemaId, $featureTitle, $navigationGroupId, $details)
    {
        /**
         * Creating Navigation Item
         */
        $navigation_item = NavigationItem::create([
            'websiteId' => 1,
            'schemaId' => $schemaId,
            'featureTitle' => $featureTitle,
            'order' => $this->order++,
        ]);


        /**
         * Creating Navigation Item Details
         */
        $preparedDetails = [];
        foreach ($details as $key => $detail)
            $preparedDetails = array_merge(
                $preparedDetails,
                $this->translationGenerator($detail['type'], $key, $detail['value_en'], $detail['value_ar'], $detail['value_ku'])
            );

        $navigation_item->details()->createMany($preparedDetails);



        // append the item with the group
        NavigationGroupItem::create([
            'navigationGroupId' => $navigationGroupId,
            'itemId' => $navigation_item->id,
        ]);
    }


    private function translationGenerator($type, $key, $value_en, $value_ar, $value_ku)
    {
        $translations = [];

        // English
        $translations[] = [
            'itemId' => 1,
            'languageId' => $this->englishId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_en,
        ];

        // Arabic
        $translations[] = [
            'itemId' => 1,
            'languageId' => $this->arabicId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_ar,
        ];

        // Kurdish
        $translations[] = [
            'itemId' => 1,
            'languageId' => $this->kurdishId,
            'valueType' => $type,
            'key' => $key,
            'value' => $value_ku,
        ];


        return $translations;
    }
}
