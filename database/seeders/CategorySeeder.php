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
                'name' => 'Other',
                'slug' => 'other',
            ],
            [
                'name' => 'Preparing Foods',
                'slug' => 'kitchen-preparing-food',
            ],
            [
                'name' => 'Selling Goods',
                'slug' => 'selling-goods',
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
