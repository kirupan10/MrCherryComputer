# MR Cherry Computers — Shop Management System

A Laravel 10 web application for managing the day-to-day operations of **MR Cherry Computers**, a tech/computer shop. It covers sales orders, inventory, customer management, warranty tracking, job/repair orders, finance, and reporting — all driven by a role-based permission system and real-time Livewire components.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+, Laravel 10 |
| Frontend | Blade templates, Livewire 3, Tailwind CSS |
| UI Framework | Tabler (via `mimisk13/laravel-tabler`) |
| Tables | Rappasoft Livewire Tables, PowerGrid |
| PDF / Export | DomPDF, PhpSpreadsheet |
| Barcodes | `picqer/php-barcode-generator` |
| Dev Tools | Laravel Debugbar, Query Detector |

---

## Features

- **Dashboard** — role-aware KPIs: revenue, orders, inventory stats
- **Products & Inventory** — barcode generation, serial number tracking, category/unit management
- **Sales Orders** — create orders, apply discounts, print receipts
- **Credit Sales & Purchases** — credit ledger with payment tracking
- **Customer & Vendor Management**
- **Warranty Management** — register warranties against products/serial numbers, handle claims
- **Job / Repair Orders** — create repair jobs, assign technicians, track job status history
- **Expense Tracking & Finance** — external funds, repayments, cheque management
- **Reports** — monthly business reports, sales reports, finance reports
- **User & Permission Management** — per-shop permissions, user suspension
- **Audit Logs** — action history across the system
- **Database Backups**

---

## How It Works

### Multi-Shop, Role-Based Architecture

The system is built around **Shops** and **Users**. Each shop has a `shop_type` (currently `tech_shop`). Features available to a shop are driven by `config/shop-features.php`.

Users belong to one of four roles:

| Role | Scope |
|---|---|
| `admin` | System-wide — sees all shops, manages users/permissions |
| `shop_owner` | Full access to their own shop |
| `manager` | Operational access to their shop |
| `employee` | Day-to-day tasks (orders, inventory) within their shop |

On login, the user is routed to a dashboard appropriate to their role. Shop-scoped users can only see data that belongs to their shop.

### Request Lifecycle

1. **Auth** — handled by Laravel Breeze (session-based). Routes in `routes/auth.php`.
2. **Role/Shop Guard** — middleware checks the user's role and resolves the active shop before entering protected routes.
3. **Shop-Type Routes** — all tech-shop routes live under `routes/shop-types/tech.php` and are prefixed accordingly.
4. **Controllers** — thin controllers delegate to Livewire components or service classes (e.g. `KpiService`).
5. **Livewire Components** — interactive tables (`Tables/`), payment flows (`Payment/`), PowerGrid grids for heavy listing pages.
6. **Models** — Eloquent models with scopes, observers (`Observers/`), and policies (`Policies/`) for authorization.
7. **Notifications** — Laravel notification classes in `app/Notifications/`.

---

## Local Setup

### Prerequisites

- PHP 8.1+
- Composer
- Node.js & npm
- MySQL / MariaDB

### Steps

```bash
# 1. Clone the repo
git clone <repo-url>
cd MrCherryComputer

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies and build assets
npm install && npm run build

# 4. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 5. Configure DB credentials in .env
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Run migrations and seed the database
php artisan migrate --seed

# 7. Start the development server
php artisan serve
```

The `--seed` flag runs `DatabaseSeeder`, which calls:

- `AdminUserSeeder` — system admin/manager/employee accounts
- `ShopTypeSampleSeeder` — the MR Cherry Computers shop and its staff accounts
- `CategorySeeder` — default product categories
- `UnitSeeder` — default units of measure
- `WarrantySeeder` — default warranty periods

---

## Seeded Login Accounts

### System-Level Accounts

These accounts have no shop assignment and are used to manage the system globally.

| Role | Email | Username | Password |
|---|---|---|---|
| Admin | admin@cherry.com | admin | `Aura@2026#` |
| Manager | manager@cherry.com | manager | `Aura@2026#` |
| Employee | employee@cherry.com | employee | `Aura@2026#` |

### MR Cherry Computers — Shop Accounts

These accounts are scoped to the **MR Cherry Computers** tech shop.

| Role | Email | Username | Password |
|---|---|---|---|
| Shop Owner | isai@cherry.com | tech_owner | `Password@123` |
| Manager | tech.mgr@cherry.com | tech_manager | `Password@123` |
| Employee | tech.staff@cherry.com | tech_staff | `Password@123` |

> **Tip:** Log in as `admin@cherry.com` first to explore the full system-level view, then use `isai@cherry.com` (shop owner) to see the shop-scoped dashboard and operations.

---

## Project Structure Highlights

```
app/
  Http/Controllers/   — Standard Laravel controllers
  Livewire/           — Livewire components (tables, forms, search)
  Models/             — Eloquent models
  Services/           — Business logic (KpiService, etc.)
  Policies/           — Authorization policies
  Observers/          — Model event listeners
  Enums/              — PHP 8.1 enums (ShopType, OrderStatus, etc.)
  ShopTypes/          — Shop-type-specific logic
config/
  shop-features.php   — Feature flags per shop type
database/
  migrations/         — Database schema
  seeders/            — Seed data
routes/
  web.php             — Core web routes
  shop-types/tech.php — Tech shop specific routes
resources/views/      — Blade templates
```

---

## Running Tests

```bash
php artisan test
```

---

## Useful Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Re-run seeds without wiping the DB
php artisan db:seed

# Fresh migration with seeding (⚠ wipes all data)
php artisan migrate:fresh --seed
```
