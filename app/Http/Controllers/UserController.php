<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function currentUserId(): ?int
    {
        $id = Auth::id();

        return is_numeric($id) ? (int) $id : null;
    }

    private function activeAdminCount(): int
    {
        return User::role('admin')
            ->where('is_active', true)
            ->count();
    }

    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles');

        $stats = [
            'total_sales' => $user->sales()->count(),
            'sales_amount' => $user->sales()->sum('total_amount'),
            'expenses_created' => $user->expenses()->count(),
            'expenses_approved' => $user->approvedExpenses()->count(),
            'returns_processed' => $user->returns()->count(),
        ];

        $recentSales = $user->sales()->with('customer')->latest()->take(5)->get();
        $recentExpenses = $user->expenses()->with('category')->latest()->take(5)->get();

        return view('users.show', compact('user', 'stats', 'recentSales', 'recentExpenses'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $requestedRole = $validated['role'];
        $requestedActive = $request->boolean('is_active');

        if ($user->hasRole('admin') && $user->is_active && $this->activeAdminCount() <= 1) {
            if (!$requestedActive || $requestedRole !== 'admin') {
                return back()->withErrors(['error' => 'You cannot deactivate or demote the last active admin account.']);
            }
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $requestedActive,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Sync role
        $user->syncRoles([$requestedRole]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === $this->currentUserId()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        if ($user->hasRole('admin') && $user->is_active && $this->activeAdminCount() <= 1) {
            return back()->withErrors(['error' => 'You cannot delete the last active admin account.']);
        }

        // Check if user has related records
        if ($user->sales()->count() > 0 || $user->expenses()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete user with existing records. Please deactivate instead.']);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === $this->currentUserId()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        if ($user->hasRole('admin') && $user->is_active && $this->activeAdminCount() <= 1) {
            return back()->withErrors(['error' => 'You cannot deactivate the last active admin account.']);
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }
}
