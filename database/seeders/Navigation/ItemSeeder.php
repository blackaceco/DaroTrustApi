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
    private $order = 1;

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
        # ---> 1
        $this->createItem(1, "banner", 1, [
            'logo' => [
                'type' => "image",
                'value_en' => "img/logo.svg",
                'value_ar' => "img/logo.svg",
            ]
        ]);




        # 2 , 5
        $this->nav_items();;


        

        # 6 , 9
        $this->footer();



        # 10 , 13
        $this->socials();
    }




    /**
     * Create the navigation items
     */
    private function nav_items()
    {
        $items = [
            // home
            [
                'title' => [
                    'type' => "long_text",
                    'value_en' => "Home",
                    'value_ar' => "الصفحة الرئيسية",
                ],
    
                'url' => [
                    'type' => "long_text",
                    'value_en' => "/",
                    'value_ar' => "/",
                ],
            ],

            // about-us
            [
                'title' => [
                    'type' => "long_text",
                    'value_en' => "About Us",
                    'value_ar' => "معلومات عنا",
                ],
    
                'url' => [
                    'type' => "long_text",
                    'value_en' => "/about-us",
                    'value_ar' => "/about-us",
                ],
            ],

            // services
            [
                'title' => [
                    'type' => "long_text",
                    'value_en' => "Services",
                    'value_ar' => "خدمات",
                ],
    
                'url' => [
                    'type' => "long_text",
                    'value_en' => "/services",
                    'value_ar' => "/services",
                ],
            ],

            // contacts
            [
                'title' => [
                    'type' => "long_text",
                    'value_en' => "Contacts",
                    'value_ar' => "الاتصال",
                ],
    
                'url' => [
                    'type' => "long_text",
                    'value_en' => "/contacts",
                    'value_ar' => "/contacts",
                ],
            ],
        ];


        // loop
        foreach ($items as $item) {
            $this->createItem(2, "nav_item", 1, $item);
        }
    }


    /**
     * Create the footer items
     */
    private function footer()
    {
        # ---> 6
        $this->createItem(3, "banner", 2, [
            'title' => [
                'type' => "long_text",
                'value_en' => "Talk with us about your next project",
                'value_ar' => "تحدث معنا عن مشروعك القادم",
            ],

            'description' => [
                'type' => "textarea",
                'value_en' => "Join us on a journey where reliability meets innovation, and customer satisfaction is not just a goal but a standard.",
                'value_ar' => "انضم إلينا في رحلة حيث تجتمع الموثوقية مع الابتكار، ورضا العملاء ليس مجرد هدف بل معيار.",
            ],
        ]);

        # ---> 7
        $this->createItem(4, "nav_item", 2, [
            'title' => [
                'type' => "long_text",
                'value_en' => "Contact us",
                'value_ar' => "اتصل بنا",
            ],

            'url' => [
                'type' => "long_text",
                'value_en' => "/contacts",
                'value_ar' => "/contacts",
            ],
        ]);

        # ---> 8
        $this->createItem(5, "banner", 3, [
            'logo' => [
                'type' => "image",
                'value_en' => "img/logo-mask.svg",
                'value_ar' => "img/logo-mask.svg",
            ]
        ]);

        # ---> 9
        $this->createItem(6, "copyright", 3, [
            'copyright' => [
                'type' => "long_text",
                'value_en' => "All rights reserved to Dark Trust 2023",
                'value_ar' => "جميع الحقوق محفوظة لشركة Dark Trust 2023",
            ]
        ]);
    }


    /**
     * Create the social items
     */
    private function socials()
    {
        // socials
        $socials = [
            # ---> 10
            'facebook.com' => ["jam:facebook", "jam:facebook"],
            # ---> 11
            'instagram.com' => ["bi:instagram", "bi:instagram"],
            # ---> 12
            'youtube.com' => ["cib:youtube", "cib:youtube"],
            # ---> 13
            'linkedin.com' => ["brandico:linkedin", "brandico:linkedin"],
        ];

        // creating socials
        foreach ($socials as $url => $social)
            $this->createItem(7, "social", 4, [
                'icon' => [
                    'type' => "long_text",
                    'value_en' => $social[0],
                    'value_ar' => $social[1],
                ],
                'url' => [
                    'type' => "long_text",
                    'value_en' => $url,
                    'value_ar' => $url,
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
                $this->translationGenerator($detail['type'], $key, $detail['value_en'], $detail['value_ar'])
            );

        $navigation_item->details()->createMany($preparedDetails);



        // append the item with the group
        NavigationGroupItem::create([
            'navigationGroupId' => $navigationGroupId,
            'itemId' => $navigation_item->id,
        ]);
    }


    private function translationGenerator($type, $key, $value_en, $value_ar)
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


        return $translations;
    }
}
