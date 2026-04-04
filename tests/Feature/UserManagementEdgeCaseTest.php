<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_last_active_admin_cannot_be_deactivated_via_toggle(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)
            ->post(route('users.toggle-status', $admin));

        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
    }

    public function test_last_active_admin_cannot_be_demoted_via_update(): void
    {
        $admin = $this->createUserWithRole('admin');
        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'manager',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors();
        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_user_update_can_set_is_active_false_from_edit_form_payload(): void
    {
        $admin = $this->createUserWithRole('admin');
        $otherAdmin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)
            ->put(route('users.update', $otherAdmin), [
                'name' => $otherAdmin->name,
                'email' => $otherAdmin->email,
                'role' => 'admin',
                'is_active' => '0',
            ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $otherAdmin->id,
            'is_active' => false,
        ]);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }
}
