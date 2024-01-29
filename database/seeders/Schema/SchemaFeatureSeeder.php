<?php

namespace Database\Seeders\Schema;

use App\Models\CareerSchemaFeature;
use App\Models\SchemaFeature;
use App\Models\SchemaFeatureHierarchy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchemaFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1  ,  home:slider
        $this->schemaFeatureCreator("banner", 2, 10, true, false, false, [
            'image' => "image",
            'title' => "long_text",
            'url' => "long_text",
            'subtitle' => "short_text",
            'summary' => "textarea",
        ]);

        // # ---> 2  ,  home:about
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "textarea",
            'button_text' => "short_text",
            'url' => "long_text",
        ]);

        // # ---> 3  ,  home:gallery
        $this->schemaFeatureCreator("gallery", 7, 7, true, false, false, [
            'image' => "image",
        ]);

        // # ---> 4  ,  documentary
        $this->schemaFeatureCreator("documentary", 1, null, true, true, false, [
            'image' => "image",
            'video' => "video",
            'title' => "long_text",
            'author' => "short_text",
            'published_date' => "date",
            'description' => "editor",
        ]);

        // # ---> 5  ,  documentary:episode
        $this->schemaFeatureCreator("episode", 1, null, true, false, false, [
            'title' => "long_text",
            'video' => "video",
        ], 4);

        // # ---> 6  ,  home:article
        $this->schemaFeatureCreator("article", 0, null, true, false, false, [
            'image' => "image",
            'title' => "long_text",
            'summary' => "textarea",
            'url' => "long_text",
        ]);

        // # ---> 7  ,  home:link-banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 8  ,  home:link
        $this->schemaFeatureCreator("link", 0, null, true, false, false, [
            'title' => "long_text",
            'url' => "long_text",
        ]);

        // # ---> 9  ,  storytelling:banner  # commented , not used
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 10  ,  storytelling
        $this->schemaFeatureCreator("storytelling", 1, null, true, false, false, [
            'title' => "long_text",
            'image' => "image",
            'gallery' => "gallery",
            'author' => "short_text",
            'published_date' => "date",
            'summary' => "textarea",
            'description' => "editor",
        ]);

        // # ---> 11  ,  festival:banner  # commented , not used
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 12  ,  festival
        $this->schemaFeatureCreator("festival", 1, null, true, false, false, [
            'title' => "long_text",
            'image' => "image",
            'gallery' => "gallery",
            'published_date' => "date",
            'summary' => "textarea",
            'description' => "editor",
        ]);

        // # ---> 13  ,  training:banner  # commented , not used
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 14  ,  training
        $this->schemaFeatureCreator("training", 1, null, true, true, false, [
            'title' => "long_text",
            'image' => "image",
            'gallery' => "gallery",
            'author' => "short_text",
            'date' => "date",
            'summary' => "textarea",
            'description' => "editor",
        ]);

        // # ---> 15  ,  about:banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
            'image' => "image",
            'subtitle' => "long_text",
            'description' => "editor",
        ]);

        // # ---> 16  ,  about:mission-&-vision
        $this->schemaFeatureCreator("objective", 2, 2, true, false, false, [
            'title' => "long_text",
            'description' => "editor",
        ]);

        // # ---> 17  ,  about:slider
        $this->schemaFeatureCreator("slide", 6, null, true, false, false, [
            'image' => "image",
        ]);

        // # ---> 18  ,  about:service_banner
        $this->schemaFeatureCreator("service_banner", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "editor",
        ]);

        // # ---> 19  ,  about:service
        $this->schemaFeatureCreator("service", 3, null, true, false, false, [
            'title' => "long_text",
            'description' => "editor",
            'image' => "image",
        ]);

        // # ---> 20  ,  about:link-banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 21  ,  about:link
        $this->schemaFeatureCreator("link", 0, null, true, false, false, [
            'title' => "long_text",
            'url' => "long_text",
        ]);

        
        // # ---> 22  ,  contact:banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 23  ,  contact:coordinates
        $this->schemaFeatureCreator("coordinates", 1, 1, false, false, false, [
            'coordinates' => "map",
        ]);

        // # ---> 24  ,  contact:email
        $this->schemaFeatureCreator("email", 1, null, true, false, false, [
            'email' => "long_text",
        ]);

        // # ---> 25  ,  contact:phone
        $this->schemaFeatureCreator("phone", 1, null, true, false, false, [
            'phone' => "long_text",
        ]);

        // # ---> 26  ,  contact:address
        $this->schemaFeatureCreator("address", 1, null, true, false, false, [
            'address' => "long_text",
        ]);

        // # ---> 27  ,  contact:working_hours
        $this->schemaFeatureCreator("working_hours", 1, null, true, false, false, [
            'working_hours' => "long_text",
        ]);

        
        // # ---> 28  ,  promo:banner  # commented , not used
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        // # ---> 29  ,  promo:video
        $this->schemaFeatureCreator("promo", 1, null, true, false, false, [
            'title' => "long_text",
            'video' => "video",
        ]);
    }





    private function schemaFeatureCreator($featureTitle, $min, $max, $sortable, $groupable, $taggable, $types, $parentId = null)
    {
        /**
         * Schema Feature
         */
        $feature = SchemaFeature::create([
            'websiteId' => 1,
            'min' => $min,
            'max' => $max,
            'featureTitle' => $featureTitle,
            'sortable' => $sortable,
            'groupable' => $groupable,
            'taggable' => $taggable,
        ]);


        /**
         * Types
         */
        $preparedTypes = [];

        // preparing
        foreach ($types as $key => $type)
            $preparedTypes[] = [
                'valueKey' => $key,
                'valueType' => $type,
            ];

        // creating
        $feature->types()->createMany($preparedTypes);

        // parent
        if ($parentId != null)
            SchemaFeatureHierarchy::create([
                'childId' => $feature->id,
                'parentId' => $parentId,
            ]);
    }
}
