<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@pos.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'address' => 'Admin Address',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@pos.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '1234567891',
            'address' => 'Manager Address',
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        // Create cashier user
        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@pos.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '1234567892',
            'address' => 'Cashier Address',
            'is_active' => true,
        ]);
        $cashier->assignRole('cashier');
    }
}
