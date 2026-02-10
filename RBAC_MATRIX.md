# Role-Based Access Control Matrix

## Complete Permission Structure

---

## Roles Overview

### 1. Admin (Owner)
**Full system access** - Can perform all operations

### 2. Manager
**Most operations** - Cannot manage system users or settings

### 3. Cashier
**Limited access** - Can only perform sales and view own records

---

## Detailed Access Matrix

### Authentication & Authorization
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| Login | ✓ | ✓ | ✓ | All roles |
| Logout | ✓ | ✓ | ✓ | All roles |
| Change own password | ✓ | ✓ | ✓ | All roles |
| Reset password | ✓ | ✓ | ✗ | Manager can reset cashier passwords |

---

### User Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View all users | ✓ | ✓ | ✗ | Manager sees all except admin |
| Create user | ✓ | ✓ | ✗ | Manager can create cashiers only |
| Edit user | ✓ | ✓ | ✗ | Manager cannot edit admin |
| Delete/deactivate user | ✓ | ✓ | ✗ | Manager cannot delete admin |
| View user activity | ✓ | ✓ | ✗ | Audit logs |
| Assign roles | ✓ | ✗ | ✗ | Admin only |

---

### Category Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View categories | ✓ | ✓ | ✓ | View only for cashier |
| Create category | ✓ | ✓ | ✗ | - |
| Edit category | ✓ | ✓ | ✗ | - |
| Delete category | ✓ | ✓ | ✗ | Soft delete |
| Activate/deactivate category | ✓ | ✓ | ✗ | - |

---

### Unit Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View units | ✓ | ✓ | ✓ | View only for cashier |
| Create unit | ✓ | ✓ | ✗ | - |
| Edit unit | ✓ | ✓ | ✗ | - |
| Delete unit | ✓ | ✓ | ✗ | - |

---

### Product Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View all products | ✓ | ✓ | ✓ | All can view |
| Search products | ✓ | ✓ | ✓ | All can search |
| Create product | ✓ | ✓ | ✗ | - |
| Edit product | ✓ | ✓ | ✗ | - |
| Delete product | ✓ | ✓ | ✗ | Soft delete |
| Upload product image | ✓ | ✓ | ✗ | - |
| Bulk import products | ✓ | ✓ | ✗ | CSV/Excel |
| View product details | ✓ | ✓ | ✓ | All can view |

---

### Stock Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View current stock | ✓ | ✓ | ✓ | All can view |
| Add stock | ✓ | ✓ | ✗ | Receive inventory |
| Update stock | ✓ | ✓ | ✗ | Adjust quantity |
| View stock logs | ✓ | ✓ | ✗ | History of changes |
| View low stock alerts | ✓ | ✓ | ✓ | Notifications |
| Manual stock adjustment | ✓ | ✓ | ✗ | With reason |

---

### Customer Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View all customers | ✓ | ✓ | ✓ | All can view |
| Search customers | ✓ | ✓ | ✓ | All can search |
| Create customer | ✓ | ✓ | ✓ | During POS transaction |
| Edit customer | ✓ | ✓ | ✓ | Basic info |
| Delete customer | ✓ | ✓ | ✗ | Soft delete |
| View customer details | ✓ | ✓ | ✓ | - |
| View purchase history | ✓ | ✓ | ✓ | Customer's orders |
| Manage credit limit | ✓ | ✓ | ✗ | Set credit |
| View customer balance | ✓ | ✓ | ✓ | Outstanding amount |

---

### Point of Sale (POS)
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| Access POS | ✓ | ✓ | ✓ | All can use |
| Search products | ✓ | ✓ | ✓ | In POS |
| Select products | ✓ | ✓ | ✓ | Add to cart |
| Apply item discount | ✓ | ✓ | ✓ (limit) | Cashier max 10% |
| Apply total discount | ✓ | ✓ | ✓ (limit) | Cashier max 5% |
| Override discount limit | ✓ | ✓ | ✗ | Manager approval |
| Process payment | ✓ | ✓ | ✓ | All methods |
| Hold/park sale | ✓ | ✓ | ✓ | Resume later |
| Select customer | ✓ | ✓ | ✓ | - |
| Cancel transaction | ✓ | ✓ | ✓ | Before payment |

---

### Sales Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View all sales | ✓ | ✓ | ✗ | Cashier sees own only |
| View own sales | ✓ | ✓ | ✓ | - |
| View sale details | ✓ | ✓ | Own | Full details |
| Edit sale | ✓ | ✓ | ✗ | Within time limit |
| Cancel sale | ✓ | ✓ | ✗ | Refund required |
| Search sales | ✓ | ✓ | Own | Filter by date/customer |
| View sales by user | ✓ | ✓ | ✗ | Performance tracking |

---

### Invoice & Printing
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| Generate invoice | ✓ | ✓ | ✓ | After sale |
| Print receipt | ✓ | ✓ | ✓ | Thermal printer |
| Reprint receipt | ✓ | ✓ | ✓ | Own sales |
| Reprint any receipt | ✓ | ✓ | ✗ | Admin/Manager only |
| Download PDF | ✓ | ✓ | ✓ | Own invoices |
| Email invoice | ✓ | ✓ | ✓ | To customer |

---

### Return Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View all returns | ✓ | ✓ | ✗ | Cashier sees own |
| Process return | ✓ | ✓ | ✓ (limit) | Cashier <7 days, <₹1000 |
| Approve return | ✓ | ✓ | ✗ | Above cashier limit |
| View return history | ✓ | ✓ | Own | - |
| Issue refund | ✓ | ✓ | ✓ (approved) | - |
| Cancel return | ✓ | ✓ | ✗ | - |

---

### Manual Billing
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| Create manual bill | ✓ | ✓ | ✗ | Custom items |
| Custom product entry | ✓ | ✓ | ✗ | Not in inventory |
| Custom price entry | ✓ | ✓ | ✗ | Override price |

---

### Expense Management
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View all expenses | ✓ | ✓ | ✗ | - |
| Create expense | ✓ | ✓ | ✗ | - |
| Edit expense | ✓ | ✓ | ✗ | Own expenses |
| Delete expense | ✓ | ✓ | ✗ | Soft delete |
| Approve expense | ✓ | ✗ | ✗ | Admin approval |
| View expense categories | ✓ | ✓ | ✗ | - |
| Manage expense categories | ✓ | ✓ | ✗ | - |
| Upload receipt | ✓ | ✓ | ✗ | Image upload |

---

### Reports & Analytics
| Feature | Admin | Manager | Cashier | Notes |
|---------|-------|---------|---------|-------|
| View dashboard | ✓ | ✓ | ✓ (limited) | Role-specific |
| Sales reports | ✓ | ✓ | Own only | Daily/monthly |
| Inventory reports | ✓ | ✓ | ✗ | Stock levels |
| Profit & loss | ✓ | ✓ | ✗ | Financial summary |
| Expense reports | ✓ | ✓ | ✗ | By category |
| User performance | ✓ | ✓ | Own only | Sales by user |
| Top products | ✓ | ✓ | ✓ | Best sellers |
| Low stock report | ✓ | ✓ | ✗ | Inventory alert |
| Customer reports | ✓ | ✓ | ✗ | Purchase history |
| Export reports | ✓ | ✓ | ✗ | PDF/Excel |
| Date range filter | ✓ | ✓ | ✓ | All reports |
| Print reports | ✓ | ✓ | ✗ | - |

---

### Dashboard Widgets
| Widget | Admin | Manager | Cashier | Notes |
|--------|-------|---------|---------|-------|
| Today's sales | ✓ | ✓ | Own | Total amount |
| Sales count | ✓ | ✓ | Own | Number of bills |
| Top products | ✓ | ✓ | ✓ | Quick view |
| Low stock alerts | ✓ | ✓ | ✓ | Notifications |
| Recent transactions | ✓ | ✓ | Own | Last 10 |
| Sales chart | ✓ | ✓ | Own | 7-day trend |
| Top customers | ✓ | ✓ | ✗ | By purchase |
| Today's expenses | ✓ | ✓ | ✗ | Total amount |
| Pending returns | ✓ | ✓ | ✗ | Approval needed |
| Staff performance | ✓ | ✓ | ✗ | Sales by user |

---

## Permission Names (for Spatie Permission Package)

### User Management
- `view_users`
- `create_users`
- `edit_users`
- `delete_users`
- `assign_roles`

### Product Management
- `view_products`
- `create_products`
- `edit_products`
- `delete_products`
- `manage_stock`

### Sales
- `access_pos`
- `create_sales`
- `view_all_sales`
- `edit_sales`
- `cancel_sales`
- `apply_discount`
- `override_discount`

### Returns
- `view_all_returns`
- `create_returns`
- `approve_returns`
- `process_refunds`

### Expenses
- `view_expenses`
- `create_expenses`
- `edit_expenses`
- `delete_expenses`
- `approve_expenses`

### Reports
- `view_all_reports`
- `view_profit_loss`
- `view_user_performance`
- `export_reports`

### Customers
- `view_customers`
- `create_customers`
- `edit_customers`
- `delete_customers`
- `manage_credit`

### Master Data
- `manage_categories`
- `manage_units`

---

## Middleware Setup

### Route Groups
```php
// Admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve']);
});

// Admin & Manager
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('expenses', ExpenseController::class);
});

// All authenticated
Route::middleware(['auth'])->group(function () {
    Route::get('pos', [POSController::class, 'index']);
    Route::post('sales', [SaleController::class, 'store']);
});
```

---

## Database Seeder Example

```php
// RoleSeeder.php
public function run()
{
    // Create roles
    $admin = Role::create(['name' => 'Admin', 'slug' => 'admin']);
    $manager = Role::create(['name' => 'Manager', 'slug' => 'manager']);
    $cashier = Role::create(['name' => 'Cashier', 'slug' => 'cashier']);
    
    // Admin gets all permissions
    $admin->givePermissionTo(Permission::all());
    
    // Manager permissions
    $manager->givePermissionTo([
        'view_products', 'create_products', 'edit_products',
        'manage_stock', 'view_all_sales', 'edit_sales',
        'create_expenses', 'view_expenses',
        'view_all_reports', 'manage_categories',
    ]);
    
    // Cashier permissions
    $cashier->givePermissionTo([
        'access_pos', 'create_sales', 'view_customers',
        'create_customers', 'create_returns',
    ]);
}
```

---

## Frontend Role Checks (Blade)

```blade
@role('admin')
    <a href="/users">Manage Users</a>
@endrole

@hasanyrole('admin|manager')
    <a href="/products">Manage Products</a>
@endhasanyrole

@can('edit_sales')
    <button>Edit Sale</button>
@endcan

@cannot('delete_products')
    <span class="text-gray-400">Delete not allowed</span>
@endcannot
```

---

## Business Rules by Role

### Cashier Limits
- **Discount:** Maximum 10% per item, 5% total
- **Returns:** Only within 7 days and below ₹1,000
- **Sales Edit:** Cannot edit any sale
- **Reports:** Own sales only, cannot see other users
- **Credit Sales:** Cannot exceed customer credit limit without approval

### Manager Limits
- **User Management:** Can create/edit cashiers only, not admins
- **Expense Approval:** Cannot approve, only create
- **System Settings:** Cannot modify
- **Backup:** Cannot access

### Admin Privileges
- **Full Access:** All features and settings
- **Audit:** Can view all activity logs
- **System:** Can modify configurations
- **Backup:** Can backup/restore database

---

## Access Denied Handling

```php
// In controller
if (!auth()->user()->can('edit_sales')) {
    abort(403, 'You do not have permission to edit sales.');
}

// Or return with message
if (!auth()->user()->hasRole('admin')) {
    return redirect()->back()->with('error', 'Admin access required.');
}
```

---

This RBAC matrix ensures clear separation of duties and maintains security while allowing necessary operational flexibility for each role.

