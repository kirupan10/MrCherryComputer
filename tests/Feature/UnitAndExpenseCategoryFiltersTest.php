<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UnitAndExpenseCategoryFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_units_index_can_filter_by_search_and_status(): void
    {
        $user = $this->createAdminUser();

        Unit::create([
            'name' => 'UNIT_ACTIVE_KILOGRAM_X1',
            'short_name' => 'kg',
            'is_active' => true,
        ]);

        Unit::create([
            'name' => 'UNIT_INACTIVE_BOX_X2',
            'short_name' => 'bx',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get(route('units.index', [
            'search' => 'kilo',
            'status' => 'active',
        ]));

        $response->assertOk();
        $response->assertSeeText('UNIT_ACTIVE_KILOGRAM_X1');
        $response->assertDontSeeText('UNIT_INACTIVE_BOX_X2');
    }

    public function test_expense_categories_index_can_filter_by_search_and_status(): void
    {
        $user = $this->createAdminUser();

        ExpenseCategory::create([
            'name' => 'Utility Bills',
            'description' => 'Electricity and water',
            'is_active' => true,
        ]);

        ExpenseCategory::create([
            'name' => 'One-Time Setup',
            'description' => 'Initial purchase',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->get(route('expense-categories.index', [
            'search' => 'water',
            'status' => 'active',
        ]));

        $response->assertOk();
        $response->assertSee('Utility Bills');
        $response->assertDontSee('One-Time Setup');
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        return $user;
    }
}
