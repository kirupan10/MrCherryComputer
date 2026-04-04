<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportDataContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_pages_render_with_expected_data_contracts(): void
    {
        $admin = $this->createAdminUser();

        [$category, $product] = $this->seedReportData($admin);

        $dateRange = [
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to' => now()->toDateString(),
        ];

        $this->actingAs($admin)
            ->get(route('reports.sales', $dateRange + ['group_by' => 'week']))
            ->assertOk()
            ->assertSee('Total Sales');

        $this->actingAs($admin)
            ->get(route('reports.product-sales', $dateRange + ['category_id' => $category->id]))
            ->assertOk()
            ->assertSee('Top Selling Products');

        $this->actingAs($admin)
            ->get(route('reports.inventory', ['category_id' => $category->id]))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('reports.stock-movement', $dateRange + ['type' => 'in']))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('reports.expenses', $dateRange))
            ->assertOk()
            ->assertSee('Expenses by Category');

        $this->actingAs($admin)
            ->get(route('reports.profit-loss', $dateRange))
            ->assertOk()
            ->assertSee('Profit & Loss Statement', false);

        $this->actingAs($admin)
            ->get(route('reports.customers', [
                'from_date' => now()->subDays(30)->toDateString(),
                'to_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Top Customers');
    }

    public function test_reports_handle_empty_ranges_gracefully(): void
    {
        $admin = $this->createAdminUser();

        $emptyRange = [
            'date_from' => now()->subYears(5)->toDateString(),
            'date_to' => now()->subYears(5)->addDay()->toDateString(),
        ];

        $this->actingAs($admin)
            ->get(route('reports.sales', $emptyRange))
            ->assertOk()
            ->assertSee('No sales data found for selected period.');

        $this->actingAs($admin)
            ->get(route('reports.product-sales', $emptyRange))
            ->assertOk()
            ->assertSee('No product sales data found for selected period.');

        $this->actingAs($admin)
            ->get(route('reports.stock-movement', $emptyRange))
            ->assertOk()
            ->assertSee('No stock movement data found for selected period.');

        $this->actingAs($admin)
            ->get(route('reports.expenses', $emptyRange))
            ->assertOk()
            ->assertSee('No expense data found for selected period.');
    }

    public function test_stock_movement_includes_same_day_records_at_date_boundaries(): void
    {
        $admin = $this->createAdminUser();

        $category = Category::create([
            'name' => 'Boundary Category',
            'slug' => 'boundary-category',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Boundary Product',
            'sku' => 'BOUND-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 130,
            'low_stock_alert' => 2,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        StockLog::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 1,
            'previous_quantity' => 0,
            'current_quantity' => 1,
            'reference_type' => 'test',
            'reference_id' => 1,
            'notes' => 'Boundary movement',
            'created_by' => $admin->id,
        ]);

        $today = now()->toDateString();

        $this->actingAs($admin)
            ->get(route('reports.stock-movement', [
                'date_from' => $today,
                'date_to' => $today,
                'type' => 'in',
            ]))
            ->assertOk()
            ->assertSee('Boundary Product');
    }

    public function test_inventory_low_stock_filter_and_soft_deleted_expense_category_are_handled(): void
    {
        $admin = $this->createAdminUser();

        $category = Category::create([
            'name' => 'Filter Category',
            'slug' => 'filter-category',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $lowStockProduct = Product::create([
            'name' => 'Low Stock Product',
            'sku' => 'LOW-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 140,
            'low_stock_alert' => 5,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        $normalStockProduct = Product::create([
            'name' => 'Normal Stock Product',
            'sku' => 'NORM-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 140,
            'low_stock_alert' => 5,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $lowStockProduct->id,
            'quantity' => 2,
            'last_updated_by' => $admin->id,
        ]);

        Stock::create([
            'product_id' => $normalStockProduct->id,
            'quantity' => 20,
            'last_updated_by' => $admin->id,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Soft Delete Category',
            'description' => 'Will be soft deleted',
            'is_active' => true,
        ]);

        Expense::create([
            'expense_number' => 'EXP-SOFT-001',
            'expense_category_id' => $expenseCategory->id,
            'expense_date' => now()->toDateString(),
            'amount' => 75,
            'payment_method' => 'cash',
            'description' => 'Soft delete category expense',
            'status' => 'approved',
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        $expenseCategory->delete();

        $this->actingAs($admin)
            ->get(route('reports.inventory', ['low_stock_only' => 1]))
            ->assertOk()
            ->assertSee('Low Stock Product')
            ->assertDontSee('Normal Stock Product');

        $this->actingAs($admin)
            ->get(route('reports.expenses', [
                'date_from' => now()->subDay()->toDateString(),
                'date_to' => now()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Uncategorized');
    }

    private function seedReportData(User $admin): array
    {
        $category = Category::create([
            'name' => 'Report Category',
            'slug' => 'report-category',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Report Customer',
            'phone' => '0771000000',
            'email' => 'report.customer@example.test',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Report Product',
            'sku' => 'REP-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'low_stock_alert' => 5,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $product->id,
            'quantity' => 20,
            'last_updated_by' => $admin->id,
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-REP-001',
            'customer_id' => $customer->id,
            'sale_date' => now()->subDays(2),
            'subtotal' => 300,
            'discount_amount' => 0,
            'tax_amount' => 30,
            'total_amount' => 330,
            'paid_amount' => 330,
            'due_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 150,
            'tax_percentage' => 10,
            'tax_amount' => 30,
            'discount_amount' => 0,
            'subtotal' => 300,
            'total' => 330,
        ]);

        StockLog::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 20,
            'previous_quantity' => 0,
            'current_quantity' => 20,
            'reference_type' => 'stock',
            'reference_id' => 1,
            'notes' => 'Initial stock',
            'created_by' => $admin->id,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Report Expenses',
            'description' => 'Report test expenses',
            'is_active' => true,
        ]);

        Expense::create([
            'expense_number' => 'EXP-REP-001',
            'expense_category_id' => $expenseCategory->id,
            'expense_date' => now()->subDays(1)->toDateString(),
            'amount' => 100,
            'payment_method' => 'cash',
            'description' => 'Electricity bill',
            'status' => 'approved',
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
        ]);

        ReturnModel::create([
            'return_number' => 'RET-REP-001',
            'sale_id' => $sale->id,
            'customer_id' => $customer->id,
            'return_date' => now()->subDay(),
            'subtotal' => 50,
            'tax_amount' => 0,
            'total_amount' => 50,
            'refund_amount' => 50,
            'refund_method' => 'cash',
            'reason' => 'Returned item',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        return [$category, $product];
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        return $user;
    }
}
