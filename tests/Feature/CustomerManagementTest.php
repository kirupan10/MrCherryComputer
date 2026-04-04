<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_customer_with_company_and_gst(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->post(route('customers.store'), [
            'name' => 'Cherry Solutions',
            'email' => 'contact@cherry.test',
            'phone' => '0771234567',
            'company_name' => 'Cherry Pvt Ltd',
            'gst_number' => '27AABCU9603R1ZM',
            'address' => 'Main Street',
            'city' => 'Colombo',
            'state' => 'Western',
            'zip_code' => '10000',
            'credit_limit' => 10000,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'name' => 'Cherry Solutions',
            'company_name' => 'Cherry Pvt Ltd',
            'gst_number' => '27AABCU9603R1ZM',
            'zip_code' => '10000',
        ]);
    }

    public function test_customer_purchase_history_can_be_filtered_by_payment_status(): void
    {
        $user = $this->createAdminUser();
        $customer = Customer::create([
            'name' => 'History Customer',
            'phone' => '0779999999',
            'email' => 'history@customer.test',
            'is_active' => true,
        ]);

        Sale::create([
            'invoice_number' => 'INV-PAID-001',
            'customer_id' => $customer->id,
            'sale_date' => now()->subDay(),
            'subtotal' => 1000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 1000,
            'paid_amount' => 1000,
            'due_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $user->id,
        ]);

        Sale::create([
            'invoice_number' => 'INV-UNPAID-001',
            'customer_id' => $customer->id,
            'sale_date' => now(),
            'subtotal' => 1500,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 1500,
            'paid_amount' => 0,
            'due_amount' => 1500,
            'payment_status' => 'unpaid',
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('customers.show', [
            'customer' => $customer,
            'payment_status' => 'paid',
        ]));

        $response->assertOk();
        $response->assertSee('INV-PAID-001');
        $response->assertDontSee('INV-UNPAID-001');
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole('admin');

        return $user;
    }
}
