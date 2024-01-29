<?php

namespace Database\Seeders\Breadcrumb;

use App\Models\BreadcrumbSchema;
use Illuminate\Database\Seeder;

class SchemaFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1
        $this->schemaFeatureCreator("feature", "documentary", 3);

        // # ---> 2
        $this->schemaFeatureCreator("feature", "storytelling", 3);

        // # ---> 3
        $this->schemaFeatureCreator("feature", "festival", 3);

        // # ---> 4
        $this->schemaFeatureCreator("feature", "training", 3);
    }


    private function schemaFeatureCreator($type, $page, $level)
    {
        /**
         * Schema Feature
         */
        $feature = BreadcrumbSchema::create([
            'websiteId' => 1,
            'type' => $type,
            'page' => $page,
            'level' => $level,
        ]);
    }
}
