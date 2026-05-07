<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = collect([
            [
                'name' => 'Piece',
                'slug' => 'piece',
                'short_code' => 'pc'
            ],
            [
                'name' => 'Centimeters',
                'slug' => 'centimeters',
                'short_code' => 'cm'
            ],
            [
                'name' => 'Meters',
                'slug' => 'meters',
                'short_code' => 'm'
            ],
            [
                'name' => 'Kilogram',
                'slug' => 'kilogram',
                'short_code' => 'kg'
            ],
            [
                'name' => 'Litre',
                'slug' => 'litre',
                'short_code' => 'L'
            ],
        ]);

        $units->each(function ($unit){
            // Make seeding idempotent by using slug or name as unique key
            Unit::updateOrCreate([
                'slug' => $unit['slug']
            ], $unit);
        });
    }
}
