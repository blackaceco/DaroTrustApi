<?php

namespace Database\Seeders;

use App\Models\Breadcrumb;
use App\Models\BreadcrumbCategory;
use App\Models\BreadcrumbSchema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BreadcrumbCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schema_data = [
            [
                'websiteId' => 1,
                'page' => 'articles',
                'type' => 'feature',
                'level' => 3
            ]
        ];

        $data = [
            // 1
            [
                'websiteId' => 1,
                'path' => "/",
                'page' => "articles",
                'level' => 1,
                'data' => [
                    'title' => 'Home',
                    'languageId' => 1,
                ]
            ],

            // 2
            [
                'websiteId' => 1,
                'path' => "articles",
                'page' => "articles",
                'level' => 2,
                'data' => [
                    'title' => 'Articles',
                    'languageId' => 1,
                ]
            ],

            // 3
            [
                'websiteId' => 1,
                'path' => null,
                'page' => "articles",
                'level' => 3,
                'data' => [
                    'title' => 'Detail',
                    'languageId' => 1,
                ]
            ],
        ];


        // creating

        foreach ($schema_data as $schema) {
            BreadcrumbSchema::create($schema);
        }

        foreach ($data as $category) {
            $breadcrumb_category = BreadcrumbCategory::create([
                'websiteId' => $category['websiteId'],
                'path' => $category['path'],
                'page' => $category['page'],
                'level' => $category['level'],
            ]);

            Breadcrumb::create([
                'breadcrumbCategoryId' => $breadcrumb_category->id,
                'languageId' => $category['data']['languageId'],
                'title' => $category['data']['title'],
            ]);
        }
    }
}
