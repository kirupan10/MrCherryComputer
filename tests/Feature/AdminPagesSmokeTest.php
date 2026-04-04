<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_core_pages_render_successfully(): void
    {
        $admin = $this->createAdminUser();

        $category = Category::create([
            'name' => 'Smoke Category',
            'slug' => 'smoke-category',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Smoke Customer',
            'phone' => '0770000000',
            'email' => 'smoke.customer@example.test',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Smoke Product',
            'sku' => 'SMOKE-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'low_stock_alert' => 5,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Smoke Expense Category',
            'description' => 'Smoke test category',
            'is_active' => true,
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-SMOKE-001',
            'customer_id' => $customer->id,
            'sale_date' => now(),
            'subtotal' => 100,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100,
            'paid_amount' => 100,
            'due_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $expense = Expense::create([
            'expense_number' => 'EXP-SMOKE-001',
            'expense_category_id' => $expenseCategory->id,
            'expense_date' => today(),
            'amount' => 50,
            'payment_method' => 'cash',
            'description' => 'Smoke expense',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $return = ReturnModel::create([
            'return_number' => 'RET-SMOKE-001',
            'sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'return_date' => now(),
            'subtotal' => 20,
            'tax_amount' => 0,
            'total_amount' => 20,
            'refund_amount' => 20,
            'refund_method' => 'cash',
            'reason' => 'Smoke return',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('users.create'))->assertOk();
        $this->actingAs($admin)->get(route('users.show', $admin))->assertOk();
        $this->actingAs($admin)->get(route('users.edit', $admin))->assertOk();

        $this->actingAs($admin)->get(route('categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('categories.create'))->assertOk();
        $this->actingAs($admin)->get(route('categories.show', $category))->assertOk();
        $this->actingAs($admin)->get(route('categories.edit', $category))->assertOk();

        $this->actingAs($admin)->get(route('units.index'))->assertOk();
        $this->actingAs($admin)->get(route('units.create'))->assertOk();
        $this->actingAs($admin)->get(route('units.show', $unit))->assertOk();
        $this->actingAs($admin)->get(route('units.edit', $unit))->assertOk();

        $this->actingAs($admin)->get(route('products.index'))->assertOk();
        $this->actingAs($admin)->get(route('products.create'))->assertOk();
        $this->actingAs($admin)->get(route('products.show', $product))->assertOk();
        $this->actingAs($admin)->get(route('products.edit', $product))->assertOk();

        $this->actingAs($admin)->get(route('customers.index'))->assertOk();
        $this->actingAs($admin)->get(route('customers.create'))->assertOk();
        $this->actingAs($admin)->get(route('customers.show', $customer))->assertOk();
        $this->actingAs($admin)->get(route('customers.edit', $customer))->assertOk();

        $this->actingAs($admin)->get(route('expense-categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('expense-categories.create'))->assertOk();
        $this->actingAs($admin)->get(route('expense-categories.show', $expenseCategory))->assertOk();
        $this->actingAs($admin)->get(route('expense-categories.edit', $expenseCategory))->assertOk();

        $this->actingAs($admin)->get(route('expenses.index'))->assertOk();
        $this->actingAs($admin)->get(route('expenses.create'))->assertOk();
        $this->actingAs($admin)->get(route('expenses.show', $expense))->assertOk();
        $this->actingAs($admin)->get(route('expenses.edit', $expense))->assertOk();

        $this->actingAs($admin)->get(route('sales.index'))->assertOk();
        $this->actingAs($admin)->get(route('sales.show', $sale))->assertOk();
        $this->actingAs($admin)->get(route('sales.edit', $sale))->assertOk();

        $this->actingAs($admin)->get(route('returns.index'))->assertOk();
        $this->actingAs($admin)->get(route('returns.create'))->assertOk();
        $this->actingAs($admin)->get(route('returns.show', $return))->assertOk();
        $this->actingAs($admin)->get(route('returns.edit', $return))->assertOk();

        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
        $reportRange = [
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to' => now()->toDateString(),
        ];

        $this->actingAs($admin)->get(route('reports.sales', $reportRange))->assertOk();
        $this->actingAs($admin)->get(route('reports.product-sales', $reportRange))->assertOk();
        $this->actingAs($admin)->get(route('reports.inventory'))->assertOk();
        $this->actingAs($admin)->get(route('reports.stock-movement', $reportRange))->assertOk();
        $this->actingAs($admin)->get(route('reports.expenses', $reportRange))->assertOk();
        $this->actingAs($admin)->get(route('reports.profit-loss', $reportRange))->assertOk();
        $this->actingAs($admin)->get(route('reports.customers'))->assertOk();
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        return $user;
    }
}
