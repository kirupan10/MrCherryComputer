# 🚀 Quick Reference Guide

This is a quick reference for developers working on the MrCherry Computer POS System.

---

## 📑 Document Index

| Document | Purpose |
|----------|---------|
| **README.md** | Project overview and quick start |
| **WORKFLOW.md** | Detailed 10-phase development plan |
| **FEATURE_GROUPING.md** | Features organized by dependencies |
| **DATABASE_SCHEMA.md** | Complete database structure |
| **IMPLEMENTATION_GUIDE.md** | Step-by-step implementation |
| **RBAC_MATRIX.md** | Role permissions reference |
| **THIS FILE** | Quick reference cheat sheet |

---

## ⚡ Quick Commands

### Setup
```bash
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### Development
```bash
php artisan serve          # Start Laravel server
npm run dev                # Start Vite dev server
php artisan migrate:fresh --seed  # Reset database
```

### Cache Management
```bash
php artisan optimize:clear  # Clear all caches
php artisan config:cache    # Cache config (production)
php artisan route:cache     # Cache routes (production)
```

### Code Generation
```bash
php artisan make:model Product -a         # Model + all
php artisan make:controller PosController --resource
php artisan make:migration create_sales_table
php artisan make:seeder RoleSeeder
```

---

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@pos.com | password |
| Manager | manager@pos.com | password |
| Cashier | cashier@pos.com | password |

---

## 📊 Database Tables Quick Reference

### Core Tables
- **users** - System users
- **roles** - Admin, Manager, Cashier
- **permissions** - Granular permissions

### Master Data
- **categories** - Product categories
- **units** - Measurement units (kg, pcs, etc.)

### Inventory
- **products** - Product master
- **stocks** - Current stock levels
- **stock_logs** - Stock movement history

### Sales
- **sales** - Sales master
- **sale_items** - Line items
- **payments** - Payment records

### Returns
- **returns** - Return master
- **return_items** - Returned items

### Others
- **customers** - Customer database
- **expenses** - Business expenses
- **expense_categories** - Expense types

---

## 🎯 Development Phases (Quick View)

1. ✅ **Auth & RBAC** (1-2 days)
2. ✅ **Master Data** (2-3 days) - Categories, Units, Users
3. ✅ **Inventory** (3-4 days) - Products, Stock
4. ✅ **Customers** (1-2 days)
5. ✅ **POS** (4-5 days) - Core sales system
6. ✅ **Invoicing** (1 day) - PDF, Printing
7. ✅ **Returns** (2 days)
8. ✅ **Expenses** (2 days)
9. ✅ **Reports** (3-4 days)
10. ✅ **Polish** (2-3 days)

**Total: 20-25 days**

---

## 🔑 Key Routes Structure

```php
// Auth (Laravel Breeze)
/login, /logout, /register

// Admin Only
/users (CRUD)
/settings

// Admin & Manager
/products (CRUD)
/categories (CRUD)
/expenses (CRUD)

// All Authenticated
/dashboard
/pos
/sales
/customers
/reports (role-filtered)
```

---

## 👥 Permission Quick Reference

### Admin
- Full system access
- User management
- System settings

### Manager
- Product management ✓
- Sales & POS ✓
- Reports (all) ✓
- User management ✗

### Cashier
- POS only ✓
- View own sales ✓
- Customer management ✓
- Product management ✗

---

## 📦 Required Packages Checklist

### Composer
- [ ] `laravel/breeze` (Auth)
- [ ] `spatie/laravel-permission` (RBAC)
- [ ] `barryvdh/laravel-dompdf` (PDF)
- [ ] `maatwebsite/excel` (Excel export)
- [ ] `spatie/laravel-activitylog` (Optional)

### NPM
- [ ] `alpinejs` or `vue` (Frontend)
- [ ] `chart.js` (Reports charts)

---

## 🗂️ Project Structure

```
app/
├── Http/Controllers/
│   ├── CategoryController.php
│   ├── ProductController.php
│   ├── POSController.php
│   ├── SaleController.php
│   └── ReportController.php
├── Models/
│   ├── Product.php
│   ├── Sale.php
│   ├── Customer.php
│   └── ...
└── Services/
    ├── POSService.php
    ├── StockService.php
    └── ReportService.php

resources/views/
├── layouts/
├── pos/
├── products/
├── sales/
└── reports/
```

---

## 💾 Database Relationships

```
User ──┬── Sales (created_by)
       ├── Expenses (created_by)
       └── StockLogs (created_by)

Category ──> Products

Unit ──> Products

Product ──┬── Stocks
          ├── SaleItems
          └── ReturnItems

Customer ──┬── Sales
           └── Returns

Sale ──┬── SaleItems
       ├── Payments
       └── Returns
```

---

## 🧪 Testing Commands

```bash
php artisan test                    # Run all tests
php artisan test --filter SaleTest  # Specific test
php artisan test --coverage         # Coverage report
```

---

## 🐛 Common Issues & Solutions

### Issue: Migration errors
```bash
# Solution: Reset migrations
php artisan migrate:fresh --seed
```

### Issue: Permission denied (storage)
```bash
# Solution: Fix permissions
chmod -R 775 storage bootstrap/cache
```

### Issue: Class not found
```bash
# Solution: Regenerate autoload
composer dump-autoload
```

### Issue: Config cached
```bash
# Solution: Clear config
php artisan config:clear
```

---

## 📈 Performance Tips

1. **Use eager loading**
   ```php
   Product::with(['category', 'unit'])->get();
   ```

2. **Cache frequently accessed data**
   ```php
   Cache::remember('products', 3600, fn() => Product::all());
   ```

3. **Add database indexes**
   ```php
   $table->index(['created_at', 'status']);
   ```

4. **Use chunk for large datasets**
   ```php
   Sale::chunk(100, function ($sales) { ... });
   ```

---

## 🔒 Security Checklist

- [ ] Use CSRF tokens on all forms
- [ ] Validate all inputs
- [ ] Use prepared statements (Eloquent)
- [ ] Hash passwords (Laravel default)
- [ ] Implement rate limiting
- [ ] Secure file uploads
- [ ] Add activity logging
- [ ] Use HTTPS in production
- [ ] Keep dependencies updated

---

## 📱 Mobile Responsive Breakpoints

```css
sm: 640px   /* Mobile landscape */
md: 768px   /* Tablet */
lg: 1024px  /* Laptop */
xl: 1280px  /* Desktop */
2xl: 1536px /* Large Desktop */
```

---

## 🎨 UI Components Needed

### Reusable Components
- [ ] Modal (for forms)
- [ ] Data table (with pagination)
- [ ] Search input
- [ ] Date picker
- [ ] Notification/toast
- [ ] Confirmation dialog
- [ ] Loading spinner
- [ ] Card component

### POS Specific
- [ ] Product search
- [ ] Shopping cart
- [ ] Calculator keypad
- [ ] Payment modal
- [ ] Receipt preview

---

## 📊 Report Types to Build

1. **Sales Reports**
   - Daily/monthly sales
   - Sales by product
   - Sales by user
   - Sales by customer

2. **Inventory Reports**
   - Current stock levels
   - Low stock items
   - Stock movement
   - Dead stock

3. **Financial Reports**
   - Profit & loss
   - Expense summary
   - Revenue trends

---

## 🔄 Workflow States

### Sale Status
- `pending` - Not completed
- `completed` - Payment done
- `cancelled` - Voided

### Payment Status
- `paid` - Fully paid
- `partial` - Partially paid
- `unpaid` - No payment

### Return Status
- `pending` - Awaiting approval
- `completed` - Approved & refunded
- `rejected` - Not approved

---

## 📞 API Endpoints (Future)

If building API later:

```
GET    /api/products
POST   /api/sales
GET    /api/customers
GET    /api/reports/sales
POST   /api/auth/login
```

---

## 🚀 Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Update database credentials
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up SSL certificate
- [ ] Configure backups
- [ ] Set up monitoring
- [ ] Test all features
- [ ] Set file permissions
- [ ] Configure cron jobs

---

## 📚 Helpful Resources

- Laravel Docs: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com
- Alpine.js: https://alpinejs.dev
- Spatie Permission: https://spatie.be/docs/laravel-permission
- Laravel Excel: https://docs.laravel-excel.com
- DomPDF: https://github.com/barryvdh/laravel-dompdf

---

## 💡 Pro Tips

1. **Always use migrations** - Never modify database directly
2. **Use seeders** - For test data and initial setup
3. **Implement soft deletes** - Keep data history
4. **Log important actions** - Use activity log
5. **Write tests** - At least for critical features
6. **Use form requests** - For validation
7. **Keep controllers thin** - Move logic to services
8. **Use transactions** - For multi-step operations
9. **Cache wisely** - Don't cache everything
10. **Document as you go** - Future you will thank you

---

## 🎯 Next Steps After Setup

1. Read WORKFLOW.md for detailed phases
2. Review DATABASE_SCHEMA.md
3. Start with Phase 1 (Authentication)
4. Follow IMPLEMENTATION_GUIDE.md
5. Check RBAC_MATRIX.md for permissions
6. Build incrementally, test often

---

**Need help? Check the main documentation files!**

