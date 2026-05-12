<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $defaultCategories = [
            [
                'name' => 'PROCESSOR',
                'slug' => 'processor',
            ],
            [
                'name' => 'MOTHERBOARD',
                'slug' => 'motherboard',
            ],
            [
                'name' => 'MEMORY(RAM)',
                'slug' => 'memory-ram',
            ],
            [
                'name' => 'STORAGE',
                'slug' => 'storage',
            ],
            [
                'name' => 'POWER SUPPLY',
                'slug' => 'power-supply',
            ],
            [
                'name' => 'CASE',
                'slug' => 'case',
            ],
            [
                'name' => 'COOLERS',
                'slug' => 'coolers',
            ],
            [
                'name' => 'GRAPHICS CARD',
                'slug' => 'graphics-card',
            ],
            [
                'name' => 'LAPTOP',
                'slug' => 'laptop',
            ],
            [
                'name' => 'POWER BANK',
                'slug' => 'power-bank',
            ],
            [
                'name' => 'OTHERS',
                'slug' => 'others',
            ],
        ];

        // Keep global categories (shop_id = null) for compatibility.
        foreach ($defaultCategories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                    'shop_id' => null,
                ],
                [
                    'name' => $category['name'],
                    'created_by' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        // Ensure these categories exist for every current shop.
        $shopIds = DB::table('shops')->pluck('id');

        foreach ($shopIds as $shopId) {
            foreach ($defaultCategories as $category) {
                DB::table('categories')->updateOrInsert(
                    [
                        'slug' => $category['slug'],
                        'shop_id' => $shopId,
                    ],
                    [
                        'name' => $category['name'],
                        'created_by' => null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }
    }
}
