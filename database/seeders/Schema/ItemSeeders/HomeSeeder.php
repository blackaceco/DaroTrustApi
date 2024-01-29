<?php

namespace Database\Seeders\Schema\ItemSeeders;

use App\Models\Item;
use App\Models\ItemHierarchy;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomeSeeder extends Seeder
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

        $this->order = Item::max('order') ?? 0 + 1;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1
        $this->sliderFieldsArrayGenerator(1, "banner", [1], "home-banner/2.jpg", "/documentary/20");
        // # ---> 2
        $this->sliderFieldsArrayGenerator(1, "banner", [1], "home-banner/1.jpg", "/documentary/21");


        // # ---> 3
        $this->aboutFieldsArrayGenerator(2, "banner", [2]);


        // # ---> 4
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/1.jpg");
        // # ---> 5
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/2.jpg");
        // # ---> 6
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/3.jpg");
        // # ---> 7
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/4.jpg");
        // # ---> 8
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/5.jpg");
        // # ---> 9
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/6.jpg");
        // # ---> 10
        $this->galleryFieldsArrayGenerator(3, "banner", [3], "home-about/7.jpg");



        //  +++ documentaries


        // # ---> 11
        $this->articleFieldsArrayGenerator(6, "article", [6], "articles/3.jpg");
        // # ---> 12
        $this->articleFieldsArrayGenerator(6, "article", [6], "articles/4.jpg");
        // # ---> 13
        $this->articleFieldsArrayGenerator(6, "article", [6], "articles/1.jpg");
        // # ---> 14
        $this->articleFieldsArrayGenerator(6, "article", [6], "articles/2.jpg");


        // # ---> 15
        $this->LinkFieldsArrayGenerator(7, "banner", [7], "Discover", "اكتشف", "دۆزینەوە", false);
        // # ---> 16
        $this->LinkFieldsArrayGenerator(8, "link", [7], "Storytelling", "القص", "چیرۆک", true);
        // # ---> 17
        $this->LinkFieldsArrayGenerator(8, "link", [7], "Promo", "الترويجي", "پرۆمۆ", true);
        // # ---> 18
        $this->LinkFieldsArrayGenerator(8, "link", [7], "Festivals", "المهرجانات", "فیستیڤاڵەکان", true);
        // # ---> 19
        $this->LinkFieldsArrayGenerator(8, "link", [7], "Trainings", "التدريبات", "ڕاهێنانەکان", true);



        // ###############################################################
        // #                           Creating                          #
        // ###############################################################

        foreach ($this->items as $item)
            $this->createItem($item['fields'], $item['details'], $item['pageGroupIds']);
    }








    private function createItem($fields, $details, $pageGroupIds, $parentId = null)
    {
        // create item
        $item = Item::create($fields);

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
    private function sliderFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $image, $url)
    {
        $details = [];

        $title_en = "Slider Title Example";
        $title_ar = "مثال على عنوان شريط التمرير";
        $title_ku = "نموونەی ناونیشانی سلایدەر";

        $subtitle_en = "Documentary";
        $subtitle_ar = "وثائقي";
        $subtitle_ku = "دۆکیومێنتاری";

        $summary_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $summary_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $summary_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        $details = array_merge($details, $this->translationGenerator('image', 'image', $image, $image, $image));
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('long_text', 'url', $url, $url, $url));
        $details = array_merge($details, $this->translationGenerator('short_text', 'subtitle', $subtitle_en, $subtitle_ar, $subtitle_ku));
        $details = array_merge($details, $this->translationGenerator('textarea', 'summary', $summary_en, $summary_ar, $summary_ku));

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

    private function aboutFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds)
    {
        $details = [];

        $title_en = "We are Real Story";
        $title_ar = "نحن قصة حقيقية";
        $title_ku = "ئێمە چیرۆکی ڕاستەقینەین";

        $btn_text_en = "About us";
        $btn_text_ar = "من نحن";
        $btn_text_ku = "دەربارەی ئێمه";

        $description_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $description_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $description_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('textarea', 'description', $description_en, $description_ar, $description_ku));
        $details = array_merge($details, $this->translationGenerator('short_text', 'button_text', $btn_text_en, $btn_text_ar, $btn_text_ku));
        $details = array_merge($details, $this->translationGenerator('long_text', 'url', "/about", "/about", "/about"));

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

    private function galleryFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $image)
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

            'pageGroupIds' => $pageGroupIds
        ];
    }

    private function articleFieldsArrayGenerator($schemaId, $featureTitle, $pageGroupIds, $image)
    {
        $details = [];

        $title_en = "An Article or Highlighted Content";
        $title_ar = "مقال أو محتوى مميز";
        $title_ku = "وتارێک یان ناوەڕۆکێکی بەرجەستەکراو";

        $summary_en = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.";
        $summary_ar = "في النشر والتصميم الجرافيكي ، لوريم إيبسوم هو نص عنصر نائب يستخدم بشكل شائع لإظهار الشكل المرئي لمستند أو محرف دون الاعتماد على محتوى ذي معنى. يمكن استخدام لوريم إيبسوم كعنصر نائب قبل توفر النسخة النهائية.";
        $summary_ku = "لە بڵاوکردنەوە و گرافیک دیزایندا، لۆرم ئیپسۆم دەقێکی جێ راگرە کە بە شێوەیەکی گشتی بەکاردێت بۆ نیشاندانی شێوەی بینراوی بەڵگەنامەیەک یان تایپفەیسێک بەبێ پشتبەستن بە ناوەڕۆکی مانادار. لەوانەیە لۆرێم ئایپسوم وەک جێ راگر بەکاربهێنرێت پێش ئەوەی کۆپی کۆتایی بەردەست بێت.";

        $details = array_merge($details, $this->translationGenerator('image', 'image', $image, $image, $image));
        $details = array_merge($details, $this->translationGenerator('long_text', 'title', $title_en, $title_ar, $title_ku));
        $details = array_merge($details, $this->translationGenerator('long_text', 'url', "http://xyz.com", "http://xyz.com", "http://xyz.com"));
        $details = array_merge($details, $this->translationGenerator('textarea', 'summary', $summary_en, $summary_ar, $summary_ku));

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
