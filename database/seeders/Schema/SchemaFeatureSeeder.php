<?php

namespace Database\Seeders\Schema;

use App\Models\SchemaFeature;
use App\Models\SchemaFeatureHierarchy;
use Illuminate\Database\Seeder;

class SchemaFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # ---> 1  ,  home:slider
        $this->schemaFeatureCreator("slider", 1, null, true, false, false, [
            'video' => "attachment",
            'title' => "long_text",
        ]);
        
        # ---> 2  ,  home:feature_banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "textarea",
        ]);
        
        # ---> 3  ,  home:feature
        $this->schemaFeatureCreator("feature", 3, 3, true, false, false, [
            'banner' => "short_text",
            'title' => "long_text",
            'image' => "image",
            'summary' => "short_text",
            'description' => "editor_basic",
        ]);
        
        # ---> 4  ,  home:about_banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);
        
        # ---> 5  ,  home:about
        $this->schemaFeatureCreator("about", 2, 2, true, false, false, [
            'title' => "short_text",
            'description' => "textarea",
            'image' => "image",
            'button_text' => "short_text",
            'button_url' => "long_text",
        ]);
        
        # ---> 6  ,  home:service
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);
        




        # ---> 7  ,  services
        $this->schemaFeatureCreator("service", 1, null, true, false, false, [
            'banner' => "short_text",
            'title' => "long_text",
            'image' => "image",
            'description' => "editor_basic",
        ]);
        




        # ---> 8  ,  about:banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "short_text",
            'summary' => "long_text",
            'image' => "image",
        ]);

        # ---> 9  ,  about:article
        $this->schemaFeatureCreator("article", 1, 1, false, false, false, [
            'image' => "image",
            'banner' => "short_text",
            'title' => "short_text",
            'description' => "editor_basic",
        ]);

        # ---> 10  ,  about:quote
        $this->schemaFeatureCreator("quote", 1, 1, false, false, false, [
            'mask' => "image",
            'content' => "textarea",
        ]);
        
        # ---> 11  ,  about:commitment_banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "editor_basic",
            'image' => "image",
        ]);

        # ---> 12  ,  about:commitments
        $this->schemaFeatureCreator("commitment", 1, null, true, false, false, [
            'commitment' => "short_text",
        ]);

        # ---> 13  ,  about:commitment_link
        $this->schemaFeatureCreator("link", 0, null, true, false, false, [
            'title' => "long_text",
            'url' => "long_text",
        ]);
        
        # ---> 14  ,  contact:banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'banner' => "short_text",
            'title' => "long_text",
            'image' => "image",
        ]);
        
        # ---> 15  ,  contact:message
        $this->schemaFeatureCreator("message", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "textarea",
            'name_placeholder' => "short_text",
            'email_placeholder' => "short_text",
            'subject_placeholder' => "short_text",
            'message_placeholder' => "short_text",
            'send_button_text' => "short_text",
        ]);

        # ---> 16  ,  contact:detail
        $this->schemaFeatureCreator("detail", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "textarea",
        ]);

        # ---> 17  ,  contact:contact
        $this->schemaFeatureCreator("contact", 1, null, true, false, false, [
            'icon' => "icon",
            'value' => "long_text",
        ]);
        
        # ---> 18  ,  contact:article
        $this->schemaFeatureCreator("article", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "textarea",
            'location' => "map",
        ]);
        
        # ---> 19  ,  service:banner
        $this->schemaFeatureCreator("banner", 1, 1, false, false, false, [
            'title' => "long_text",
        ]);

        # ---> 20  ,  service:value
        $this->schemaFeatureCreator("value", 1, 1, false, false, false, [
            'title' => "long_text",
            'description' => "textarea",
            'button_text' => "long_text",
            'button_url' => "long_text",
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
