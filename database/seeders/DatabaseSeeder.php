<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed only seeders relevant to the single tech-shop setup.
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ShopTypeSampleSeeder::class,
            SampleDataSeeder::class,
        ]);

    }
}
