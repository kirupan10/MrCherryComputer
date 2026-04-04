<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSTransactionEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_allows_partial_payment_and_sets_sale_statuses_correctly(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'POS Partial Customer',
            'phone' => '0774000000',
            'email' => 'pos.partial@example.test',
            'is_active' => true,
        ]);

        [$product, $stock] = $this->createStockedProduct($user, 10);

        $payload = [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'manual_name' => null,
                    'quantity' => 2,
                    'price' => 150,
                    'tax_amount' => 0,
                ],
            ],
            'subtotal' => 300,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 300,
            'payment_method' => 'cash',
            'paid_amount' => 120,
            'change_amount' => 0,
            'notes' => 'Partial payment test',
        ];

        $response = $this->actingAs($user)
            ->postJson(route('pos.process-sale'), $payload);

        $response->assertOk()->assertJson(['success' => true]);

        $saleId = $response->json('sale_id');
        $this->assertNotNull($saleId);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'payment_status' => 'partial',
            'total_amount' => 300,
            'paid_amount' => 120,
            'due_amount' => 180,
            'status' => 'completed',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $saleId,
            'payment_method' => 'cash',
            'amount' => 120,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('stocks', [
            'id' => $stock->id,
            'quantity' => 8,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'previous_quantity' => 10,
            'current_quantity' => 8,
            'reference_type' => 'sale',
        ]);
    }

    public function test_pos_rejects_mixed_payment_method_for_api_requests(): void
    {
        $user = User::factory()->create();
        [$product] = $this->createStockedProduct($user, 5);

        $payload = [
            'customer_id' => null,
            'items' => [
                [
                    'product_id' => $product->id,
                    'manual_name' => null,
                    'quantity' => 1,
                    'price' => 150,
                    'tax_amount' => 0,
                ],
            ],
            'subtotal' => 150,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 150,
            'payment_method' => 'mixed',
            'paid_amount' => 150,
            'change_amount' => 0,
        ];

        $response = $this->actingAs($user)
            ->postJson(route('pos.process-sale'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }

    private function createStockedProduct(User $user, float $quantity): array
    {
        $category = Category::create([
            'name' => 'POS Category',
            'slug' => 'pos-category',
            'is_active' => true,
        ]);

        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'POS Product',
            'sku' => 'POS-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'tax_percentage' => 0,
            'low_stock_alert' => 2,
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        $stock = Stock::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'last_updated_by' => $user->id,
        ]);

        return [$product, $stock];
    }
}
