<?php

namespace Database\Seeders\Navigation;

use App\Models\NavigationItemSchema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchemaFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1
        $this->schemaFeatureCreator("banner", 1, 1, false, [
            'title' => "short_text",
        ]);



        // # ---> 2
        $this->schemaFeatureCreator("banner", 1, 1, false, [
            'title' => "short_text",
        ]);

        // # ---> 3
        $this->schemaFeatureCreator("nav_item", 1, 8, true, [
            'title' => "long_text",
            'url' => "long_text",
        ]);



        // # ---> 4
        $this->schemaFeatureCreator("banner", 1, 1, false, [
            'title' => "short_text",
        ]);

        // # ---> 5
        $this->schemaFeatureCreator("nav_item", 1, 8, true, [
            'title' => "long_text",
            'url' => "long_text",
        ]);

        # ---> 6
        $this->schemaFeatureCreator("copyright", 1, 1, false, [
            'copyright' => "long_text",
        ]);

        # ---> 7
        $this->schemaFeatureCreator("social", 1, 10, true, [
            'icon' => "icon",
            'url' => "long_text",
        ]);
    }


    
    private function schemaFeatureCreator($featureTitle, $min, $max, $sortable, $types)
    {
        /**
         * Schema Feature
         */
        $feature = NavigationItemSchema::create([
            'websiteId' => 1,
            'min' => $min,
            'max' => $max,
            'featureTitle' => $featureTitle,
            'sortable' => $sortable,
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
        $feature->details()->createMany($preparedTypes);
    }
}
