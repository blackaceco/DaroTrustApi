<?php

namespace Database\Seeders\Schema\ItemSeeders;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
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
        // # ---> 73
        $this->bannerFieldsArrayGenerator(15, "banner", [11]);

        // # ---> 74
        $this->objectiveFieldsArrayGenerator(16, "objective", [11], "Mission", "مهمة", "ئەرک");

        // # ---> 75
        $this->objectiveFieldsArrayGenerator(16, "objective", [11], "Vision", "رؤية", "بینین");


        // # ---> 76
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/2.jpg");

        // # ---> 77
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/1.jpg");

        // # ---> 78
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/4.jpg");

        // # ---> 79
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/3.jpg");

        // # ---> 80
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/6.jpg");

        // # ---> 81
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/5.jpg");

        // # ---> 82
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/8.jpg");

        // # ---> 83
        $this->slideFieldsArrayGenerator(17, "slide", [12], "storytelling/7.jpg");

        // # ---> 84
        $this->serviceBannerFieldsArrayGenerator(18, "service_banner", [13]);

        // # ---> 85
        $this->serviceFieldsArrayGenerator(19, "service", [13], "storytelling/6.jpg", "Service Name", "اسم الخدمة", "ناوی خزمەت");

        // # ---> 86
        $this->serviceFieldsArrayGenerator(19, "service", [13], "storytelling/5.jpg", "Service Name", "اسم الخدمة", "ناوی خزمەت");

        // # ---> 87
        $this->serviceFieldsArrayGenerator(19, "service", [13], "storytelling/8.jpg", "Service Name", "اسم الخدمة", "ناوی خزمەت");

        // # ---> 88
        $this->serviceFieldsArrayGenerator(19, "service", [13], "storytelling/7.jpg", "Service Name", "اسم الخدمة", "ناوی خزمەت");


        // # ---> 89
        $this->LinkFieldsArrayGenerator(20, "banner", [14], "Discover", "اكتشف", "دۆزینەوە", false);
        // # ---> 90
        $this->LinkFieldsArrayGenerator(21, "link", [14], "Storytelling", "القص", "چیرۆک", true);
        // # ---> 91
        $this->LinkFieldsArrayGenerator(21, "link", [14], "Promo", "الترويجي", "پرۆمۆ", true);
        // # ---> 92
        $this->LinkFieldsArrayGenerator(21, "link", [14], "Festivals", "المهرجانات", "فیستیڤاڵەکان", true);
        // # ---> 93
        $this->LinkFieldsArrayGenerator(21, "link", [14], "Trainings", "التدريبات", "ڕاهێنانەکان", true);


        
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

        $title_en = "About us";
        $title_ar = "من نحن";
        $title_ku = "دەربارەی ئێمه";

        $subtitle_en = "We are Real Story";
        $subtitle_ar = "نحن قصة حقيقية";
        $subtitle_ku = "ئێمە چیرۆکی ڕاستەقینەین";

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('image', 'image', "about-bg.jpg", "about-bg.jpg", "about-bg.jpg"));
        $details = array_merge($details, $this->translationGenerator('long_text', 'subtitle', $subtitle_en, $subtitle_ar, $subtitle_ku));
        $details = array_merge($details, $this->translationGenerator('editor', 'description', $description_en, $description_ar, $description_ku));
        
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

    private function objectiveFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $title_en, $title_ar, $title_ku)
    {
        $details = [];

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('editor', 'description', $description_en, $description_ar, $description_ku));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'pageGroupIds' => $pageGroupIds,
        ];
    }

    private function slideFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $image)
    {
        $details = [];

        $details = array_merge($details, $this->translationGenerator('image', 'image', $image, $image, $image));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'pageGroupIds' => $pageGroupIds,
        ];
    }

    private function serviceBannerFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds)
    {
        $details = [];

        $title_en = "Our Services";
        $title_ar = "خدماتنا";
        $title_ku = "خزمەتگوزارییەکانمان";

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('editor', 'description', $description_en, $description_ar, $description_ku));
        
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

    private function serviceFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $image, $title_en, $title_ar, $title_ku)
    {
        $details = [];

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('editor', 'description', $description_en, $description_ar, $description_ku));
        $details = array_merge($details, $this->translationGenerator('image', 'image', $image, $image, $image));

        $this->items[] = [
            'fields' => [
                'websiteId' => 1,
                'schemaId' => $schemaId,
                'featureTitle' => $featureTitle,
            ],

            'details' => $details,

            'pageGroupIds' => $pageGroupIds,
        ];
    }

    private function LinkFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $title_en, $title_ar, $title_ku, $url = false)
    {
        $details = [];

        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        if ($url)
            $details = array_merge($details, $this->translationGenerator('long_text', 'url', "http://xyz.com", "http://xyz.com", "http://xyz.com"));

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
