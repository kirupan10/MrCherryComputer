<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_category(): void
    {
        $user = $this->createAdminUser();

        $createResponse = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Office Supplies',
            'slug' => '',
            'description' => 'Category for office products',
            'is_active' => 1,
        ]);

        $createResponse->assertRedirect(route('categories.index'));

        $category = Category::where('name', 'Office Supplies')->first();
        $this->assertNotNull($category);

        $updateResponse = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Office Essentials',
            'slug' => 'office-essentials',
            'description' => 'Updated category name',
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Office Essentials',
            'slug' => 'office-essentials',
        ]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $user = $this->createAdminUser();
        $category = Category::create([
            'name' => 'Delete Guard Category',
            'slug' => 'delete-guard-category',
            'is_active' => true,
        ]);
        $unit = Unit::create([
            'name' => 'Piece',
            'short_name' => 'pc',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Linked Product',
            'sku' => 'LNK-001',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 100,
            'selling_price' => 150,
            'low_stock_alert' => 2,
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->from(route('categories.index'))
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHasErrors('error');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    public function test_category_with_children_cannot_be_deleted(): void
    {
        $user = $this->createAdminUser();
        $parentCategory = Category::create([
            'name' => 'Parent Category',
            'slug' => 'parent-category',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Child Category',
            'slug' => 'child-category',
            'parent_id' => $parentCategory->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->from(route('categories.index'))
            ->delete(route('categories.destroy', $parentCategory));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHasErrors('error');

        $this->assertDatabaseHas('categories', [
            'id' => $parentCategory->id,
            'deleted_at' => null,
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
