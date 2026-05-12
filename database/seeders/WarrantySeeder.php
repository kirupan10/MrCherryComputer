<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarrantySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $warranties = [
            [
                'name' => '1 Year Warranty',
                'slug' => '1-year',
                'duration' => '1 year',
                'years' => 1,
            ],
            [
                'name' => '2 Years Warranty',
                'slug' => '2-years',
                'duration' => '2 years',
                'years' => 2,
            ],
            [
                'name' => '3 Years Warranty',
                'slug' => '3-years',
                'duration' => '3 years',
                'years' => 3,
            ],
            [
                'name' => '5 Years Warranty',
                'slug' => '5-years',
                'duration' => '5 years',
                'years' => 5,
            ],
            [
                'name' => '10 Years Warranty',
                'slug' => '10-years',
                'duration' => '10 years',
                'years' => 10,
            ],
        ];

        $shopIds = DB::table('shops')->pluck('id');

        // Ensure warranties exist for each current shop.
        foreach ($shopIds as $shopId) {
            foreach ($warranties as $warranty) {
                DB::table('warranties')->updateOrInsert(
                    [
                        'slug' => $warranty['slug'],
                        'shop_id' => $shopId,
                    ],
                    [
                        'name' => $warranty['name'],
                        'duration' => $warranty['duration'],
                        'years' => $warranty['years'],
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
