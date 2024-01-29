<?php

namespace Database\Seeders\Breadcrumb;

use App\Models\Breadcrumb;
use App\Models\BreadcrumbCategory;
use App\Models\Language;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private $englishId;
    private $arabicId;
    private $kurdishId;

    public function __construct()
    {
        // $this->englishId = Language::where('abbreviation', 'en')->first()->id;
        // $this->arabicId = Language::where('abbreviation', 'ar')->first()->id;
        // $this->kurdishId = Language::where('abbreviation', 'ku')->first()->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1
        // $this->schemaFeatureCreator("/", "documentary", 1, "Real Story", "قصة حقيقية", "چیرۆکی ڕاستەقینە");

        // # ---> 2
        // $this->schemaFeatureCreator("/works", "documentary", 2, "Works", "يعمل", "کار");

        // # ---> 3
        // $this->schemaFeatureCreator(null, "documentary", 3, "Documentary", "وثائقي", "دۆکیومێنتاری");
    }


    private function schemaFeatureCreator($path, $page, $level, $englishTitle, $arabicTitle, $kurdishTitle)
    {
        /**
         * Category
         */
        $category = BreadcrumbCategory::create([
            'websiteId' => 1,
            'path' => $path,
            'page' => $page,
            'level' => $level,
        ]);


        /**
         * English
         */
        Breadcrumb::create([
            'breadcrumbCategoryId' => $category->id,
            'languageId' => $this->englishId,
            'title' => $englishTitle,
        ]);

        /**
         * Arabic
         */
        Breadcrumb::create([
            'breadcrumbCategoryId' => $category->id,
            'languageId' => $this->arabicId,
            'title' => $arabicTitle,
        ]);

        /**
         * Kurdish
         */
        Breadcrumb::create([
            'breadcrumbCategoryId' => $category->id,
            'languageId' => $this->kurdishId,
            'title' => $kurdishTitle,
        ]);
    }
}
