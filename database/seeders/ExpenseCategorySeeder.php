<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Rent', 'description' => 'Shop rent and property costs'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, internet bills'],
            ['name' => 'Salaries', 'description' => 'Employee salaries and wages'],
            ['name' => 'Inventory Purchase', 'description' => 'Stock and product purchases'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotional expenses'],
            ['name' => 'Maintenance', 'description' => 'Equipment and shop maintenance'],
            ['name' => 'Transportation', 'description' => 'Delivery and transport costs'],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office materials'],
            ['name' => 'Insurance', 'description' => 'Business insurance premiums'],
            ['name' => 'Miscellaneous', 'description' => 'Other business expenses'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}
