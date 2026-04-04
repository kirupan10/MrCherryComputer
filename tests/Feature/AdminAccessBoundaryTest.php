<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_access_admin_only_user_routes(): void
    {
        $manager = $this->createUserWithRole('manager');

        $this->actingAs($manager)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(route('users.create'))
            ->assertForbidden();
    }

    public function test_manager_cannot_approve_or_reject_expense(): void
    {
        $admin = $this->createUserWithRole('admin');
        $manager = $this->createUserWithRole('manager');

        $category = ExpenseCategory::create([
            'name' => 'Boundary Category',
            'description' => 'Boundary test',
            'is_active' => true,
        ]);

        $expense = Expense::create([
            'expense_number' => 'EXP-ACCESS-001',
            'expense_category_id' => $category->id,
            'expense_date' => now()->toDateString(),
            'amount' => 99,
            'payment_method' => 'cash',
            'description' => 'Pending approval test',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($manager)
            ->post(route('expenses.approve', $expense))
            ->assertForbidden();

        $this->actingAs($manager)
            ->post(route('expenses.reject', $expense), ['rejection_reason' => 'Not allowed'])
            ->assertForbidden();
    }

    public function test_manager_cannot_delete_sale_via_admin_only_route(): void
    {
        $admin = $this->createUserWithRole('admin');
        $manager = $this->createUserWithRole('manager');

        $customer = Customer::create([
            'name' => 'Boundary Customer',
            'phone' => '0776000000',
            'email' => 'boundary.customer@example.test',
            'is_active' => true,
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-ACCESS-001',
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

        $this->actingAs($manager)
            ->delete(route('sales.destroy', $sale))
            ->assertForbidden();
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }
}
