# Laravel POS System - Quick Command Reference

## 🛠️ Setup Commands

### First Time Setup (If not done)
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create storage symlink (for product images)
php artisan storage:link
```

### Database Setup (Already Done ✅)
```bash
# Run migrations
php artisan migrate

# Run seeders to create roles, permissions, and default users
php artisan db:seed

# Fresh migration (drops all tables and recreates - USE WITH CAUTION)
php artisan migrate:fresh --seed
```

### Default User Credentials
```
Admin:
- Email: admin@pos.com
- Password: password

Manager:
- Email: manager@pos.com
- Password: password

Cashier:
- Email: cashier@pos.com
- Password: password
```

---

## 🚀 Running the Application

### Start Development Server
```bash
# Start Laravel development server
php artisan serve
# Application will be available at: http://localhost:8000

# In a new terminal, compile frontend assets
npm run dev
```

### Background Queue (if needed in future)
```bash
# Run queue worker
php artisan queue:work
```

---

## 🔍 Checking Status

### View Routes
```bash
# List all routes
php artisan route:list

# Filter by name
php artisan route:list --name=pos

# Filter by method
php artisan route:list --method=GET
```

### View Registered Middleware
```bash
php artisan route:list --columns=uri,name,middleware
```

### Check Database Tables
```bash
# Enter database CLI
php artisan tinker

# Check if tables exist
>>> DB::select('SHOW TABLES');

# Count users
>>> \App\Models\User::count();

# Get all roles
>>> Spatie\Permission\Models\Role::all();

# Get admin user
>>> \App\Models\User::where('email', 'admin@pos.com')->first();
```

---

## 🧪 Testing Commands

### Create Test Data
```bash
php artisan tinker

# Create a test category
>>> \App\Models\Category::create(['name' => 'Electronics', 'is_active' => true]);

# Create a test product
>>> \App\Models\Product::create([
    'name' => 'Test Product',
    'sku' => 'TEST001',
    'category_id' => 1,
    'unit_id' => 1,
    'purchase_price' => 100,
    'selling_price' => 150,
    'low_stock_alert' => 10,
    'created_by' => 1,
    'is_active' => true
]);

# Create stock for product
>>> \App\Models\Stock::create([
    'product_id' => 1,
    'quantity' => 100,
    'last_updated_by' => 1
]);
```

### Clear Cache (if needed)
```bash
# Clear all caches
php artisan optimize:clear

# Individual cache clears
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📦 Package Specific Commands

### Spatie Permission
```bash
# Clear permission cache
php artisan permission:cache-reset

# Create specific permission
php artisan tinker
>>> Spatie\Permission\Models\Permission::create(['name' => 'test-permission']);
```

---

## 🗄️ Database Commands

### Backup Database (Manual)
```bash
# Export database (replace with your credentials)
mysqldump -u root -p pos_system > backup.sql

# Import database
mysql -u root -p pos_system < backup.sql
```

### Reset Database
```bash
# Drop all tables and recreate
php artisan migrate:fresh

# Drop, recreate, and seed
php artisan migrate:fresh --seed
```

---

## 📁 Storage Commands

### File Storage
```bash
# Create storage link (for public file access)
php artisan storage:link

# Check if link exists
ls -la public/storage
```

### Clear uploaded files (CAUTION)
```bash
# Remove all product images
rm -rf storage/app/public/products/*

# Remove all expense receipts
rm -rf storage/app/public/expenses/*
```

---

## 🐛 Debugging Commands

### View Logs
```bash
# View latest log
tail -f storage/logs/laravel.log

# View last 50 lines
tail -n 50 storage/logs/laravel.log

# Clear logs
echo "" > storage/logs/laravel.log
```

### Check Permissions Issues
```bash
# Fix storage permissions (macOS/Linux)
chmod -R 775 storage bootstrap/cache

# Fix ownership (if needed)
chown -R www-data:www-data storage bootstrap/cache
```

### Check Composer Dependencies
```bash
# Show installed packages
composer show

# Check for updates
composer outdated

# Update all packages
composer update
```

---

## 🔐 User Management Commands

### Create User via Tinker
```bash
php artisan tinker

# Create user
>>> $user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
    'is_active' => true
]);

# Assign role
>>> $user->assignRole('cashier');

# Check user roles
>>> $user->roles;

# Remove role
>>> $user->removeRole('cashier');
```

### Reset User Password
```bash
php artisan tinker

>>> $user = \App\Models\User::where('email', 'admin@pos.com')->first();
>>> $user->password = bcrypt('newpassword');
>>> $user->save();
```

---

## 📊 Generate Reports (Via Tinker)

### Quick Sales Report
```bash
php artisan tinker

# Today's sales
>>> \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');

# This month's sales
>>> \App\Models\Sale::whereMonth('created_at', now()->month)->sum('total_amount');

# Total products
>>> \App\Models\Product::count();

# Low stock products
>>> \App\Models\Product::lowStock()->count();
```

---

## 🎨 Frontend Asset Commands

### Compile Assets
```bash
# Development mode (with hot reload)
npm run dev

# Production build (minified)
npm run build

# Watch for changes
npm run watch
```

### Clear Vite Cache
```bash
# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

---

## 🔥 Quick Useful Snippets

### Check if user has permission
```bash
php artisan tinker
>>> auth()->user()->can('product-create');
```

### Get all permissions for a role
```bash
php artisan tinker
>>> $role = Spatie\Permission\Models\Role::findByName('admin');
>>> $role->permissions->pluck('name');
```

### Count records
```bash
php artisan tinker
>>> \App\Models\Sale::count();
>>> \App\Models\Product::count();
>>> \App\Models\Customer::count();
>>> \App\Models\Expense::where('status', 'pending')->count();
```

---

## 🚨 Common Issues & Solutions

### Issue: 500 Error on Login
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Issue: Class not found
**Solution:**
```bash
composer dump-autoload
```

### Issue: Storage link not working
**Solution:**
```bash
# Remove existing link
rm public/storage

# Recreate link
php artisan storage:link
```

### Issue: Permission denied on storage
**Solution:**
```bash
# macOS/Linux
chmod -R 775 storage bootstrap/cache

# If that doesn't work
sudo chmod -R 777 storage bootstrap/cache
```

### Issue: Vite manifest not found
**Solution:**
```bash
npm install
npm run build
```

---

## 📝 Development Workflow

### When adding new features:
```bash
# 1. Create migration
php artisan make:migration create_something_table

# 2. Create model
php artisan make:model Something

# 3. Create controller
php artisan make:controller SomethingController

# 4. Run migration
php artisan migrate

# 5. Clear cache
php artisan optimize:clear
```

### Before deploying:
```bash
# 1. Run tests (when written)
php artisan test

# 2. Build assets
npm run build

# 3. Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

## 🎯 Next Development Phase Commands

### When creating views:
```bash
# Watch for CSS/JS changes
npm run dev

# Test a specific route
curl http://localhost:8000/api/endpoint

# Check route exists
php artisan route:list | grep "route-name"
```

---

**Note:** Always backup your database before running destructive commands like `migrate:fresh`!
