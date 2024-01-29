<?php

namespace Database\Seeders\Schema;

use App\Models\PageGroup;
use Illuminate\Database\Seeder;

class PageGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # ---> 1  ,  home:slider
        $this->groupFieldGenerator("home", "slider", [
            1 => ['primary' => true],
        ]);

        # ---> 2  ,  home:feature
        $this->groupFieldGenerator("home", "feature", [
            2 => ['primary' => true],
            3 => ['primary' => true],
        ]);

        # ---> 3  ,  home:about
        $this->groupFieldGenerator("home", "about", [
            4 => ['primary' => true],
            5 => ['primary' => true],
        ]);

        # ---> 4  ,  home:services
        $this->groupFieldGenerator("home", "service", [
            6 => ['primary' => true],
            7 => ['primary' => false],
        ]);

        # ---> 5  ,  services
        $this->groupFieldGenerator("services", "service", [
            7 => ['primary' => true],
        ]);





        # ---> 6  ,  about:about
        $this->groupFieldGenerator("about", "about", [
            8 => ['primary' => true],
            9 => ['primary' => true],
            10 => ['primary' => true],
        ]);

        # ---> 7  ,  about:commitment
        $this->groupFieldGenerator("about", "commitment", [
            11 => ['primary' => true],
            12 => ['primary' => true],
            13 => ['primary' => true],
        ]);





        # ---> 8  ,  contact:content
        $this->groupFieldGenerator("contact", "content", [
            14 => ['primary' => true],
            15 => ['primary' => true],
            16 => ['primary' => true],
        ]);

        # ---> 9  ,  contact:contact
        $this->groupFieldGenerator("contact", "contact", [
            17 => ['primary' => true],
        ]);

        # ---> 10  ,  contact:article
        $this->groupFieldGenerator("contact", "article", [
            18 => ['primary' => true],
        ]);





        # ---> 11  ,  service:service
        $this->groupFieldGenerator("service", "service", [
            19 => ['primary' => true],
            20 => ['primary' => true],
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
