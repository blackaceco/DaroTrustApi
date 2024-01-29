<?php

namespace Database\Seeders\Schema;

use App\Models\NavigationGroup;
use App\Models\PageGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1  ,  home:slider
        $this->groupFieldGenerator("home", "slider", [
            1 => ['primary' => true],
        ]);

        // # ---> 2  ,  home:about
        $this->groupFieldGenerator("home", "about", [
            2 => ['primary' => true],
        ]);

        // # ---> 3  ,  home:gallery
        $this->groupFieldGenerator("home", "gallery", [
            3 => ['primary' => true],
        ]);

        // # ---> 4  ,  documentary
        $this->groupFieldGenerator("documentary", "index", [
            4 => ['primary' => true],
        ]);

        // # ---> 5  ,  home:documentary
        $this->groupFieldGenerator("home", "documentary", [
            4 => ['primary' => false],
        ]);

        // # ---> 6  ,  home:article
        $this->groupFieldGenerator("home", "article", [
            6 => ['primary' => true],
        ]);

        // # ---> 7  ,  home:links
        $this->groupFieldGenerator("home", "links", [
            7 => ['primary' => true],
            8 => ['primary' => true],
        ]);

        // # ---> 8  ,  storytelling
        $this->groupFieldGenerator("storytelling", "storytelling", [
            // 9 => ['primary' => true],  // banner
            10 => ['primary' => true],
        ]);

        // # ---> 9  ,  festival
        $this->groupFieldGenerator("festival", "festival", [
            // 11 => ['primary' => true],  // banner
            12 => ['primary' => true],
        ]);

        // # ---> 10  ,  training
        $this->groupFieldGenerator("training", "training", [
            // 13 => ['primary' => true],
            14 => ['primary' => true],
        ]);

        // # ---> 11  ,  about
        $this->groupFieldGenerator("about", "about", [
            15 => ['primary' => true],  // banner
            16 => ['primary' => true],  // objectives
        ]);

        // # ---> 12  ,  about:slider
        $this->groupFieldGenerator("about", "slider", [
            17 => ['primary' => true],  // slider
        ]);

        // # ---> 13  ,  about:service_banner
        $this->groupFieldGenerator("about", "services", [
            18 => ['primary' => true],  // banner
            19 => ['primary' => true],  // service
        ]);

        // # ---> 14  ,  about:links
        $this->groupFieldGenerator("about", "links", [
            20 => ['primary' => true],  // banner
            21 => ['primary' => true],  // link
        ]);

        // # ---> 15  ,  contact:banner
        $this->groupFieldGenerator("contact", "banner", [
            22 => ['primary' => true],  // banner
            23 => ['primary' => true],  // coordinates
        ]);

        // # ---> 16  ,  contact:contact
        $this->groupFieldGenerator("contact", "contact", [
            24 => ['primary' => true],  // email
            25 => ['primary' => true],  // phone
            26 => ['primary' => true],  // address
            27 => ['primary' => true],  // working_hours
        ]);

        // # ---> 17  ,  promo:banner
        // $this->groupFieldGenerator("promo", "banner", [
        //     28 => ['primary' => true],  // banner
        // ]);

        // # ---> 17  ,  promo:video
        $this->groupFieldGenerator("promo", "promo", [
            29 => ['primary' => true],  // video
        ]);
    }




    private function groupFieldGenerator($page, $type, $schemas)
    {
        $group = PageGroup::create([
            'websiteId' => 1,
            'page' => $page,
            'type' => $type,
        ]);

        $group->features()->attach($schemas);
    }
}
