<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@cherry.com',
                'name' => 'admin',
                'username' => 'admin',
                'password' => 'Aura@2026#',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'email' => 'manager@cherry.com',
                'name' => 'manager',
                'username' => 'manager',
                'password' => 'Aura@2026#',
                'role' => User::ROLE_MANAGER,
            ],
            [
                'email' => 'employee@cherry.com',
                'name' => 'employee',
                'username' => 'employee',
                'password' => 'Aura@2026#',
                'role' => User::ROLE_EMPLOYEE,
            ],
        ];

        foreach ($users as $account) {
            $plainPassword = $account['password'];

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make($plainPassword),
                    'role' => $account['role'],
                ]
            );

            // Keep seeded passwords consistent even when users already exist.
            if (!Hash::check($plainPassword, $user->password)) {
                $user->password = Hash::make($plainPassword);
                $user->save();
            }
        }
    }
}
