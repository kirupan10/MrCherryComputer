<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesAndReturnsEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_index_filters_unpaid_status_correctly(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = Customer::create([
            'name' => 'Unpaid Filter Customer',
            'phone' => '0772000000',
            'email' => 'unpaid.filter@example.test',
            'is_active' => true,
        ]);

        Sale::create([
            'invoice_number' => 'INV-UNPAID-001',
            'customer_id' => $customer->id,
            'sale_date' => now()->subDay(),
            'subtotal' => 100,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100,
            'paid_amount' => 0,
            'due_amount' => 100,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        Sale::create([
            'invoice_number' => 'INV-PAID-001',
            'customer_id' => $customer->id,
            'sale_date' => now()->subDay(),
            'subtotal' => 100,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100,
            'paid_amount' => 100,
            'due_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('sales.index', ['payment_status' => 'unpaid']))
            ->assertOk()
            ->assertSee('INV-UNPAID-001')
            ->assertDontSee('INV-PAID-001');
    }

    public function test_cashier_sales_stats_and_list_are_scoped_to_cashier_records(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $otherUser = $this->createUserWithRole('manager');
        $customer = Customer::create([
            'name' => 'Cashier Scope Customer',
            'phone' => '0773000000',
            'email' => 'cashier.scope@example.test',
            'is_active' => true,
        ]);

        Sale::create([
            'invoice_number' => 'INV-CASHIER-001',
            'customer_id' => $customer->id,
            'sale_date' => now(),
            'subtotal' => 200,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 200,
            'paid_amount' => 200,
            'due_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $cashier->id,
        ]);

        Sale::create([
            'invoice_number' => 'INV-OTHER-001',
            'customer_id' => $customer->id,
            'sale_date' => now(),
            'subtotal' => 900,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 900,
            'paid_amount' => 900,
            'due_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $otherUser->id,
        ]);

        $this->actingAs($cashier)
            ->get(route('sales.index'))
            ->assertOk()
            ->assertSee('INV-CASHIER-001')
            ->assertDontSee('INV-OTHER-001')
            ->assertViewHas('stats', function (array $stats) {
                return (float) $stats['total_sales'] === 200.0
                    && (int) $stats['completed_count'] === 1;
            });
    }

    public function test_returns_search_sale_q_uses_sale_item_unit_price(): void
    {
        $admin = $this->createUserWithRole('admin');

        [$sale, $product] = $this->createCompletedSaleWithProduct($admin);

        $response = $this->actingAs($admin)
            ->getJson(route('returns.search-sale', ['q' => $sale->invoice_number]));

        $response->assertOk();

        $firstResult = $response->json('0');
        $this->assertNotNull($firstResult);
        $this->assertSame($sale->invoice_number, $firstResult['invoice_number']);
        $this->assertSame((string) $product->id, (string) $firstResult['items'][0]['product_id']);
        $this->assertSame(150.0, (float) $firstResult['items'][0]['price']);
    }

    public function test_pending_return_can_complete_for_soft_deleted_product(): void
    {
        $admin = $this->createUserWithRole('admin');

        [$sale, $product] = $this->createCompletedSaleWithProduct($admin);

        $return = ReturnModel::create([
            'return_number' => 'RET-EDGE-001',
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'return_date' => now(),
            'subtotal' => 150,
            'tax_amount' => 0,
            'total_amount' => 150,
            'refund_amount' => 150,
            'refund_method' => 'cash',
            'reason' => 'Defective item',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        ReturnItem::create([
            'return_id' => $return->id,
            'sale_item_id' => $sale->items->first()->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 150,
            'tax_amount' => 0,
            'total' => 150,
            'reason' => 'Defective item',
        ]);

        $product->delete();

        $response = $this->actingAs($admin)
            ->post(route('returns.complete', $return));

        $response->assertRedirect();

        $this->assertDatabaseHas('returns', [
            'id' => $return->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 9,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 1,
            'reference_type' => 'return',
            'reference_id' => $return->id,
        ]);
    }

    private function createCompletedSaleWithProduct(User $admin): array
    {
        $category = Category::create([
            'name' => 'Returns Category',
            'slug' => 'returns-category',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Returns Customer',
            'phone' => '0775000000',
            'email' => 'returns.customer@example.test',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Returns Product',
            'sku' => 'RET-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'tax_percentage' => 0,
            'low_stock_alert' => 2,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $product->id,
            'quantity' => 8,
            'last_updated_by' => $admin->id,
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-RET-001',
            'customer_id' => $customer->id,
            'sale_date' => now()->subDay(),
            'subtotal' => 150,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 150,
            'paid_amount' => 150,
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
            'quantity' => 1,
            'unit_price' => 150,
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'subtotal' => 150,
            'total' => 150,
        ]);

        return [$sale->load('items'), $product];
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }
}
