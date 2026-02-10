<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Product Management
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            // Category Management
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',

            // Unit Management
            'unit-list',
            'unit-create',
            'unit-edit',
            'unit-delete',

            // Customer Management
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',

            // Sales Management
            'sale-list',
            'sale-create',
            'sale-edit',
            'sale-delete',
            'sale-view',

            // Return Management
            'return-list',
            'return-create',
            'return-view',

            // Expense Management
            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-delete',
            'expense-approve',

            // Expense Category Management
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',

            // User Management
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // Report Management
            'report-view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $cashier = Role::firstOrCreate(['name' => 'cashier']);

        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        // Manager permissions
        $manager->syncPermissions([
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',
            'unit-list',
            'unit-create',
            'unit-edit',
            'unit-delete',
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',
            'sale-list',
            'sale-create',
            'sale-edit',
            'sale-view',
            'return-list',
            'return-create',
            'return-view',
            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-approve',
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',
            'report-view',
        ]);

        // Cashier permissions
        $cashier->syncPermissions([
            'product-list',
            'customer-list',
            'customer-create',
            'customer-edit',
            'sale-list',
            'sale-create',
            'sale-view',
            'return-list',
            'return-create',
            'return-view',
        ]);
    }
}
