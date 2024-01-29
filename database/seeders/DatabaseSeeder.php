<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Navigation\GroupSeeder as NavigationGroupSeeder;
use Database\Seeders\Navigation\ItemSeeder as NavigationItemSeeder;
use Database\Seeders\Navigation\SchemaFeatureSeeder as NavigationSchemaFeatureSeeder;
use Database\Seeders\Schema\PageGroupSeeder;
use Database\Seeders\Schema\SchemaFeatureSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Breadcrumb\SchemaFeatureSeeder as BreadcrumbSchemaFeatureSeeder;
use Database\Seeders\Breadcrumb\CategorySeeder as BreadcrumbCategorySeeder;
use Database\Seeders\Schema\ItemSeeder;

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

            /**
             * Item Seeders
             */
            ItemSeeder::class,


            /**
             * Breadcrumb seeders
             */
            BreadcrumbSchemaFeatureSeeder::class,
            BreadcrumbCategorySeeder::class,
        ]);
    }
}
