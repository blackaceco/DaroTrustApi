<?php

namespace Database\Seeders\Navigation;

use App\Models\NavigationGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // # ---> 1
        $this->groupFieldGenerator("menu", [1]);

        // # ---> 2
        $this->groupFieldGenerator("menu_top", [2, 3]);

        // # ---> 3
        $this->groupFieldGenerator("menu_bottom", [4, 5]);


        # ---> 4
        $this->groupFieldGenerator("footer", [6, 7]);

        # ---> 5
        // $this->groupFieldGenerator("social", [7]);
    }


    private function groupFieldGenerator($navigation, $schemas)
    {
        $group = NavigationGroup::create([
            'websiteId' => 1,
            'navigation' => $navigation,
        ]);

        $group->schemas()->attach($schemas);
    }
}
