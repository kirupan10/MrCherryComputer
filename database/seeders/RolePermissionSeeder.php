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
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',

            // Product Management
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_stock',

            // Sales
            'access_pos',
            'create_sales',
            'view_all_sales',
            'edit_sales',
            'cancel_sales',
            'apply_discount',
            'override_discount',

            // Returns
            'view_all_returns',
            'create_returns',
            'approve_returns',
            'process_refunds',

            // Expenses
           'view_expenses',
            'create_expenses',
            'edit_expenses',
            'delete_expenses',
            'approve_expenses',

            // Reports
            'view_all_reports',
            'view_profit_loss',
            'view_user_performance',
            'export_reports',

            // Customers
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            'manage_credit',

            // Master Data
            'manage_categories',
            'manage_units',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $cashier = Role::create(['name' => 'cashier']);

        // Admin gets all permissions
        $admin->givePermissionTo(Permission::all());

        // Manager permissions
        $manager->givePermissionTo([
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_stock',
            'access_pos',
            'create_sales',
            'view_all_sales',
            'edit_sales',
            'apply_discount',
            'create_returns',
            'approve_returns',
            'view_expenses',
            'create_expenses',
            'edit_expenses',
            'view_all_reports',
            'view_profit_loss',
            'view_user_performance',
            'export_reports',
            'view_customers',
            'create_customers',
            'edit_customers',
            'manage_categories',
            'manage_units',
            'view_users',
            'create_users',
            'edit_users',
        ]);

        // Cashier permissions
        $cashier->givePermissionTo([
            'view_products',
            'access_pos',
            'create_sales',
            'view_customers',
            'create_customers',
            'edit_customers',
            'create_returns',
        ]);
    }
}
