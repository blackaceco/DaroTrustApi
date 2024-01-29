<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Navigation\GroupSeeder as NavigationGroupSeeder;
use Database\Seeders\Navigation\ItemSeeder as NavigationItemSeeder;
use Database\Seeders\Navigation\SchemaFeatureSeeder as NavigationSchemaFeatureSeeder;
use Database\Seeders\Schema\ItemSeeders\AboutSeeder;
use Database\Seeders\Schema\ItemSeeders\ContactSeeder;
use Database\Seeders\Schema\ItemSeeders\DocumentarySeeder;
use Database\Seeders\Schema\ItemSeeders\FestivalSeeder;
use Database\Seeders\Schema\ItemSeeders\HomeSeeder;
use Database\Seeders\Schema\ItemSeeders\PromoSeeder;
use Database\Seeders\Schema\ItemSeeders\StorytellingSeeder;
use Database\Seeders\Schema\ItemSeeders\TrainingSeeder;
use Database\Seeders\Schema\PageGroupSeeder;
use Database\Seeders\Schema\SchemaFeatureSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Breadcrumb\SchemaFeatureSeeder as BreadcrumbSchemaFeatureSeeder;
use Database\Seeders\Breadcrumb\CategorySeeder as BreadcrumbCategorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,

            LanguageSeeder::class,
            WebsiteSeeder::class,
            MetaSeeder::class,
            LocalizationSeeder::class,


            /**
             * Navigation Seeders
             */
            NavigationSchemaFeatureSeeder::class,
            NavigationGroupSeeder::class,
            NavigationItemSeeder::class,


            /**
             * Schema Seeders
             */
            SchemaFeatureSeeder::class,
            PageGroupSeeder::class,

            GroupSeeder::class,

            HomeSeeder::class,
            DocumentarySeeder::class,
            StorytellingSeeder::class,
            FestivalSeeder::class,
            TrainingSeeder::class,
            AboutSeeder::class,
            ContactSeeder::class,
            PromoSeeder::class,


            /**
             * Breadcrumb seeders
             */
            BreadcrumbSchemaFeatureSeeder::class,
            BreadcrumbCategorySeeder::class,
        ]);
    }
}
