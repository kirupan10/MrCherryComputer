<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;
use App\Enums\ShopType;
use Illuminate\Support\Facades\Hash;

class ShopTypeSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('Password@123');

        // Single-shop setup: only seed one tech shop and its users.
        $shopData = [
            [
                'type' => ShopType::TECH_SHOP,
                'shop_name' => 'TechPro Computer Shop',
                'owner' => ['name' => 'James Tech', 'username' => 'tech_owner', 'email' => 'tech@nexora.com'],
                'manager' => ['name' => 'Lisa Manager', 'username' => 'tech_manager', 'email' => 'tech.mgr@nexora.com'],
                'employee' => ['name' => 'Tom Staff', 'username' => 'tech_staff', 'email' => 'tech.staff@nexora.com'],
            ],
        ];

        $this->command->info('Creating sample shops and users for each shop type...');

        foreach ($shopData as $data) {
            // Create owner first
            $owner = User::updateOrCreate(
                ['email' => $data['owner']['email']],
                [
                    'name' => $data['owner']['name'],
                    'username' => $data['owner']['username'],
                    'password' => $password,
                    'role' => User::ROLE_SHOP_OWNER,
                ]
            );

            // Create shop
            $shop = Shop::updateOrCreate(
                ['email' => $data['owner']['email']],
                [
                    'name' => $data['shop_name'],
                    'shop_type' => $data['type'],
                    'owner_id' => $owner->id,
                    'is_active' => true,
                    'phone' => '555-' . rand(1000, 9999),
                    'address' => '123 Main Street, City',
                ]
            );

            // Update owner with shop_id
            $owner->update(['shop_id' => $shop->id]);

            // Create manager
            $manager = User::updateOrCreate(
                ['email' => $data['manager']['email']],
                [
                    'name' => $data['manager']['name'],
                    'username' => $data['manager']['username'],
                    'password' => $password,
                    'role' => User::ROLE_MANAGER,
                    'shop_id' => $shop->id,
                ]
            );

            // Create employee
            $employee = User::updateOrCreate(
                ['email' => $data['employee']['email']],
                [
                    'name' => $data['employee']['name'],
                    'username' => $data['employee']['username'],
                    'password' => $password,
                    'role' => User::ROLE_EMPLOYEE,
                    'shop_id' => $shop->id,
                ]
            );

            $this->command->info("✓ Created {$data['type']->value} shop: {$data['shop_name']} with 3 users");
        }

        $this->command->info('');
        $this->command->info('Sample shops and users created successfully!');
    }
}
