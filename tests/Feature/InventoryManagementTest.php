<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_initial_stock_and_log(): void
    {
        $user = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Mouse',
            'sku' => 'SKU-MSE-100',
            'barcode' => 'BAR-MSE-100',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 200,
            'selling_price' => 350,
            'mrp' => 400,
            'tax_percentage' => 5,
            'low_stock_alert' => 3,
            'initial_stock' => 8,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('sku', 'SKU-MSE-100')->first();
        $this->assertNotNull($product);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 8,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 8,
            'previous_quantity' => 0,
            'current_quantity' => 8,
            'reference_type' => 'initial_stock',
        ]);
    }

    public function test_product_index_low_stock_filter_only_shows_low_stock_items(): void
    {
        $user = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'is_active' => true,
        ]);
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $lowStockProduct = Product::create([
            'name' => 'Low Stock Item',
            'sku' => 'LOW-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'low_stock_alert' => 5,
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        $normalProduct = Product::create([
            'name' => 'Normal Stock Item',
            'sku' => 'NOR-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'low_stock_alert' => 5,
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $lowStockProduct->id,
            'quantity' => 2,
            'last_updated_by' => $user->id,
        ]);

        Stock::create([
            'product_id' => $normalProduct->id,
            'quantity' => 20,
            'last_updated_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('products.index', ['low_stock' => 1]));

        $response->assertOk();
        $response->assertSee('Low Stock Item');
        $response->assertDontSee('Normal Stock Item');
    }

    public function test_product_stock_can_be_updated_and_logged(): void
    {
        $user = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Gadgets',
            'slug' => 'gadgets',
            'is_active' => true,
        ]);
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Keyboard',
            'sku' => 'KEY-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 400,
            'selling_price' => 600,
            'low_stock_alert' => 2,
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $product->id,
            'quantity' => 10,
            'last_updated_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('products.update-stock', $product), [
            'type' => 'out',
            'quantity' => 3,
            'notes' => 'Manual stock correction',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 7,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 3,
            'previous_quantity' => 10,
            'current_quantity' => 7,
            'notes' => 'Manual stock correction',
        ]);
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        return $user;
    }
}
