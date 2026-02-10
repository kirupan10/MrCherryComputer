<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pieces', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Liter', 'short_name' => 'ltr'],
            ['name' => 'Milliliter', 'short_name' => 'ml'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Dozen', 'short_name' => 'dz'],
            ['name' => 'Meter', 'short_name' => 'm'],
            ['name' => 'Pack', 'short_name' => 'pack'],
            ['name' => 'Bottle', 'short_name' => 'btl'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
