<?php

namespace Database\Seeders;

use App\Models\NavigationGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavigationGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'websiteId' => 1,
                'navigation' => "First",
                'schemas' => [
                    [
                        'websiteId' => 1,
                        'featureTitle' => 'nav_item',
                        'min' => 1,
                        'max' => 8,
                        'sortable' => '1',
                        'details' => [
                            [
                                'valueKey' => 'title',
                                'valueType' => 'long_text',
                            ],
                            [
                                'valueKey' => 'url',
                                'valueType' => 'long_text',
                            ]
                        ]
                    ]
                ]
            ],

            [
                'websiteId' => 1,
                'navigation' => "Second",
                'schemas' => [
                    [
                        'websiteId' => 1,
                        'featureTitle' => 'nav_item',
                        'min' => 1,
                        'max' => 2,
                        'sortable' => '1',
                        'details' => [
                            [
                                'valueKey' => 'title',
                                'valueType' => 'long_text',
                            ],
                            [
                                'valueKey' => 'url',
                                'valueType' => 'long_text',
                            ]
                        ]
                    ]
                ]
            ],

            [
                'websiteId' => 1,
                'navigation' => "Third",
                'schemas' => [
                    [
                        'websiteId' => 1,
                        'featureTitle' => 'nav_item',
                        'min' => 1,
                        'max' => 4,
                        'sortable' => '1',
                        'details' => [
                            [
                                'valueKey' => 'title',
                                'valueType' => 'long_text',
                            ],
                            [
                                'valueKey' => 'url',
                                'valueType' => 'long_text',
                            ]
                        ]
                    ]
                ]
            ],
        ];

        // creating
        foreach ($data as $navigation) {
            $group = NavigationGroup::create([
                'websiteId' => $navigation['websiteId'],
                'navigation' => $navigation['navigation'],
            ]);

            // creating schemas
            foreach ($navigation['schemas'] as $schema) {
                $schema_item = $group->schemas()->create([
                    'websiteId' => $schema['websiteId'],
                    'featureTitle' => $schema['featureTitle'],
                    'min' => $schema['min'],
                    'max' => $schema['max'],
                    'sortable' => $schema['sortable'],
                ]);

                // creating details
                foreach ($schema['details'] as $detail) {
                    $schema_item->details()->create([
                        'valueKey' => $detail['valueKey'],
                        'valueType' => $detail['valueType'],
                    ]);
                }
            }
        }
    }
}
