<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseWorkflowEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_pending_expense(): void
    {
        $admin = $this->createUserWithRole('admin');
        $expense = $this->createPendingExpense($admin);

        $response = $this->actingAs($admin)
            ->post(route('expenses.approve', $expense));

        $response->assertRedirect();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);
    }

    public function test_manager_cannot_force_approved_status_via_update(): void
    {
        $manager = $this->createUserWithRole('manager');
        $expense = $this->createPendingExpense($manager);

        $response = $this->actingAs($manager)
            ->put(route('expenses.update', $expense), [
                'expense_category_id' => $expense->expense_category_id,
                'amount' => 120,
                'expense_date' => now()->toDateString(),
                'payment_method' => 'cash',
                'reference_number' => 'REF-UPDATED',
                'description' => 'Manager attempted approval via update',
                'status' => 'approved',
            ]);

        $response->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => 'pending',
            'approved_by' => null,
        ]);
    }

    public function test_manager_does_not_see_admin_approval_actions_on_expense_show(): void
    {
        $admin = $this->createUserWithRole('admin');
        $manager = $this->createUserWithRole('manager');
        $expense = $this->createPendingExpense($admin);

        $this->actingAs($manager)
            ->get(route('expenses.show', $expense))
            ->assertOk()
            ->assertDontSee('Approve Expense')
            ->assertDontSee('Reject Expense');
    }

    private function createPendingExpense(User $creator): Expense
    {
        $category = ExpenseCategory::create([
            'name' => 'Workflow Expense Category',
            'description' => 'Expense workflow test category',
            'is_active' => true,
        ]);

        return Expense::create([
            'expense_number' => 'EXP-WORK-001-' . now()->format('Hisu'),
            'expense_category_id' => $category->id,
            'expense_date' => now()->toDateString(),
            'amount' => 100,
            'payment_method' => 'cash',
            'reference_number' => 'REF-WORK-001',
            'description' => 'Pending workflow expense',
            'status' => 'pending',
            'created_by' => $creator->id,
        ]);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }
}
