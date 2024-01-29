<?php

namespace Database\Seeders\Navigation;

use App\Models\NavigationGroup;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # ---> 1
        $this->groupFieldGenerator("navigation", [1, 2]);

        # ---> 2
        $this->groupFieldGenerator("footer_start", [3, 4]);

        # ---> 3
        $this->groupFieldGenerator("footer_end", [5, 6]);

        # ---> 4
        $this->groupFieldGenerator("socials", [7]);
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
