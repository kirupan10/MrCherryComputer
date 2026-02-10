# Database Schema - POS System

## Complete Database Schema Overview

### Table Relationships Diagram

```
users ──┬── sales
        ├── expenses
        ├── stock_logs
        └── role_user ── roles ── permissions

categories ── products ──┬── sale_items ── sales ──┬── payments
                         ├── stocks               ├── returns
                         └── return_items         └── customers

units ── products

expense_categories ── expenses
```

---

## Tables Detailed Schema

### 1. roles
**Purpose:** Define system roles (Admin, Manager, Cashier)

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(50) UNIQUE NOT NULL
slug                VARCHAR(50) UNIQUE NOT NULL
description         TEXT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 2. permissions
**Purpose:** Define system permissions

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(100) UNIQUE NOT NULL
slug                VARCHAR(100) UNIQUE NOT NULL
description         TEXT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 3. role_user
**Purpose:** Assign roles to users (many-to-many)

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
user_id             BIGINT UNSIGNED FOREIGN KEY (users.id)
role_id             BIGINT UNSIGNED FOREIGN KEY (roles.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (user_id, role_id)
```

---

### 4. permission_role
**Purpose:** Assign permissions to roles (many-to-many)

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
role_id             BIGINT UNSIGNED FOREIGN KEY (roles.id)
permission_id       BIGINT UNSIGNED FOREIGN KEY (permissions.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (role_id, permission_id)
```

---

### 5. users (extends default)
**Purpose:** System users with roles

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
email_verified_at   TIMESTAMP NULL
password            VARCHAR(255) NOT NULL
phone               VARCHAR(20) NULL
address             TEXT NULL
is_active           BOOLEAN DEFAULT TRUE
remember_token      VARCHAR(100) NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL (soft delete)

INDEX: (email, is_active)
```

---

### 6. categories
**Purpose:** Product categories

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(100) NOT NULL
slug                VARCHAR(100) UNIQUE NOT NULL
description         TEXT NULL
parent_id           BIGINT UNSIGNED NULL FOREIGN KEY (categories.id)
is_active           BOOLEAN DEFAULT TRUE
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL

INDEX: (slug, is_active)
```

---

### 7. units
**Purpose:** Product measurement units

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(50) NOT NULL (e.g., pcs, kg, box)
short_name          VARCHAR(10) NOT NULL (e.g., pc, kg)
is_active           BOOLEAN DEFAULT TRUE
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL
```

---

### 8. products
**Purpose:** Product master data

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(255) NOT NULL
sku                 VARCHAR(100) UNIQUE NULL
barcode             VARCHAR(100) UNIQUE NULL
category_id         BIGINT UNSIGNED FOREIGN KEY (categories.id)
unit_id             BIGINT UNSIGNED FOREIGN KEY (units.id)
description         TEXT NULL
image               VARCHAR(255) NULL
purchase_price      DECIMAL(12,2) NOT NULL DEFAULT 0
selling_price       DECIMAL(12,2) NOT NULL
mrp                 DECIMAL(12,2) NULL
tax_percentage      DECIMAL(5,2) DEFAULT 0
low_stock_alert     INT DEFAULT 10
is_active           BOOLEAN DEFAULT TRUE
created_by          BIGINT UNSIGNED FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL

INDEX: (sku, barcode, category_id, is_active)
```

---

### 9. stocks
**Purpose:** Current product stock levels

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
product_id          BIGINT UNSIGNED UNIQUE FOREIGN KEY (products.id)
quantity            DECIMAL(10,2) NOT NULL DEFAULT 0
last_updated_by     BIGINT UNSIGNED FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (product_id, quantity)
```

---

### 10. stock_logs
**Purpose:** Track all stock movements

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
product_id          BIGINT UNSIGNED FOREIGN KEY (products.id)
type                ENUM('in', 'out', 'adjustment', 'return') NOT NULL
quantity            DECIMAL(10,2) NOT NULL
previous_quantity   DECIMAL(10,2) NOT NULL
current_quantity    DECIMAL(10,2) NOT NULL
reference_type      VARCHAR(50) NULL (sale, purchase, adjustment)
reference_id        BIGINT UNSIGNED NULL
notes               TEXT NULL
created_by          BIGINT UNSIGNED FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (product_id, type, created_at)
```

---

### 11. customers
**Purpose:** Customer information

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) NULL
phone               VARCHAR(20) NOT NULL
address             TEXT NULL
city                VARCHAR(100) NULL
state               VARCHAR(100) NULL
zip_code            VARCHAR(20) NULL
credit_limit        DECIMAL(12,2) DEFAULT 0
current_balance     DECIMAL(12,2) DEFAULT 0
loyalty_points      INT DEFAULT 0
is_active           BOOLEAN DEFAULT TRUE
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL

INDEX: (phone, email, is_active)
```

---

### 12. sales
**Purpose:** Sales transaction master

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
invoice_number      VARCHAR(50) UNIQUE NOT NULL
customer_id         BIGINT UNSIGNED NULL FOREIGN KEY (customers.id)
sale_date           DATETIME NOT NULL
subtotal            DECIMAL(12,2) NOT NULL
discount_type       ENUM('fixed', 'percentage') NULL
discount_value      DECIMAL(12,2) DEFAULT 0
discount_amount     DECIMAL(12,2) DEFAULT 0
tax_amount          DECIMAL(12,2) DEFAULT 0
total_amount        DECIMAL(12,2) NOT NULL
paid_amount         DECIMAL(12,2) DEFAULT 0
due_amount          DECIMAL(12,2) DEFAULT 0
payment_status      ENUM('paid', 'partial', 'unpaid') DEFAULT 'paid'
payment_method      ENUM('cash', 'card', 'upi', 'bank_transfer', 'mixed') NULL
notes               TEXT NULL
status              ENUM('completed', 'pending', 'cancelled') DEFAULT 'completed'
created_by          BIGINT UNSIGNED FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL

INDEX: (invoice_number, customer_id, sale_date, created_by, status)
```

---

### 13. sale_items
**Purpose:** Individual items in a sale

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
sale_id             BIGINT UNSIGNED FOREIGN KEY (sales.id) ON DELETE CASCADE
product_id          BIGINT UNSIGNED FOREIGN KEY (products.id)
product_name        VARCHAR(255) NOT NULL (snapshot)
quantity            DECIMAL(10,2) NOT NULL
unit_price          DECIMAL(12,2) NOT NULL
tax_percentage      DECIMAL(5,2) DEFAULT 0
tax_amount          DECIMAL(12,2) DEFAULT 0
discount_amount     DECIMAL(12,2) DEFAULT 0
subtotal            DECIMAL(12,2) NOT NULL
total               DECIMAL(12,2) NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (sale_id, product_id)
```

---

### 14. payments
**Purpose:** Track multiple payments for a sale

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
sale_id             BIGINT UNSIGNED FOREIGN KEY (sales.id)
payment_date        DATETIME NOT NULL
amount              DECIMAL(12,2) NOT NULL
payment_method      ENUM('cash', 'card', 'upi', 'bank_transfer') NOT NULL
transaction_id      VARCHAR(100) NULL
notes               TEXT NULL
created_by          BIGINT UNSIGNED FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (sale_id, payment_date)
```

---

### 15. returns
**Purpose:** Product return master

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
return_number       VARCHAR(50) UNIQUE NOT NULL
sale_id             BIGINT UNSIGNED FOREIGN KEY (sales.id)
customer_id         BIGINT UNSIGNED NULL FOREIGN KEY (customers.id)
return_date         DATETIME NOT NULL
subtotal            DECIMAL(12,2) NOT NULL
tax_amount          DECIMAL(12,2) DEFAULT 0
total_amount        DECIMAL(12,2) NOT NULL
refund_amount       DECIMAL(12,2) NOT NULL
refund_method       ENUM('cash', 'card', 'store_credit') DEFAULT 'cash'
reason              TEXT NULL
status              ENUM('pending', 'completed', 'rejected') DEFAULT 'pending'
created_by          BIGINT UNSIGNED FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (return_number, sale_id, return_date)
```

---

### 16. return_items
**Purpose:** Individual items in a return

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
return_id           BIGINT UNSIGNED FOREIGN KEY (returns.id) ON DELETE CASCADE
sale_item_id        BIGINT UNSIGNED FOREIGN KEY (sale_items.id)
product_id          BIGINT UNSIGNED FOREIGN KEY (products.id)
product_name        VARCHAR(255) NOT NULL
quantity            DECIMAL(10,2) NOT NULL
unit_price          DECIMAL(12,2) NOT NULL
tax_amount          DECIMAL(12,2) DEFAULT 0
total               DECIMAL(12,2) NOT NULL
reason              TEXT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: (return_id, product_id)
```

---

### 17. expense_categories
**Purpose:** Categorize expenses

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(100) NOT NULL
description         TEXT NULL
is_active           BOOLEAN DEFAULT TRUE
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL
```

---

### 18. expenses
**Purpose:** Track business expenses

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
expense_number      VARCHAR(50) UNIQUE NOT NULL
expense_category_id BIGINT UNSIGNED FOREIGN KEY (expense_categories.id)
expense_date        DATE NOT NULL
amount              DECIMAL(12,2) NOT NULL
payment_method      ENUM('cash', 'card', 'bank_transfer', 'cheque') NOT NULL
reference_number    VARCHAR(100) NULL
description         TEXT NULL
receipt_image       VARCHAR(255) NULL
status              ENUM('pending', 'paid', 'approved') DEFAULT 'paid'
created_by          BIGINT UNSIGNED FOREIGN KEY (users.id)
approved_by         BIGINT UNSIGNED NULL FOREIGN KEY (users.id)
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL

INDEX: (expense_date, expense_category_id, status, created_by)
```

---

### 19. activity_logs (optional but recommended)
**Purpose:** Audit trail for important actions

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
user_id             BIGINT UNSIGNED NULL FOREIGN KEY (users.id)
action              VARCHAR(100) NOT NULL (created, updated, deleted)
model_type          VARCHAR(100) NOT NULL (Product, Sale, etc.)
model_id            BIGINT UNSIGNED NOT NULL
old_values          JSON NULL
new_values          JSON NULL
ip_address          VARCHAR(45) NULL
user_agent          TEXT NULL
created_at          TIMESTAMP

INDEX: (user_id, model_type, action, created_at)
```

---

### 20. settings (optional)
**Purpose:** Store application settings

```sql
id                  BIGINT UNSIGNED PRIMARY KEY
key                 VARCHAR(100) UNIQUE NOT NULL
value               TEXT NULL
type                VARCHAR(50) DEFAULT 'string'
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

## Indexes Summary

### High Priority Indexes
```sql
-- Users
CREATE INDEX idx_users_email_active ON users(email, is_active);

-- Products
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_barcode ON products(barcode);
CREATE INDEX idx_products_category ON products(category_id, is_active);

-- Sales
CREATE INDEX idx_sales_date ON sales(sale_date);
CREATE INDEX idx_sales_customer ON sales(customer_id);
CREATE INDEX idx_sales_user ON sales(created_by);
CREATE INDEX idx_sales_status ON sales(status);

-- Stock Logs
CREATE INDEX idx_stock_logs_product_date ON stock_logs(product_id, created_at);

-- Expenses
CREATE INDEX idx_expenses_date ON expenses(expense_date);
CREATE INDEX idx_expenses_category ON expenses(expense_category_id);
```

---

## Initial Seeder Data

### Roles
```php
- Admin (full access)
- Manager (most features except user management)
- Cashier (POS, limited reports)
```

### Permissions (examples)
```php
- manage_users
- manage_products
- manage_stock
- manage_sales
- edit_sales
- delete_sales
- manage_returns
- manage_expenses
- view_all_reports
- manage_categories
- manage_customers
```

### Default Units
```php
- Pieces (pcs)
- Kilogram (kg)
- Liter (ltr)
- Box
- Dozen (dz)
- Meter (m)
```

---

## Foreign Key Constraints

### ON DELETE Rules
- `CASCADE`: sale_items, return_items
- `SET NULL`: customers in sales (keep history)
- `RESTRICT`: products in sale_items (prevent deletion if sold)

---

## Estimated Database Size (1 year operation)

- **Products:** ~500-1000 records < 1 MB
- **Sales:** ~100,000 records = ~20 MB
- **Sale Items:** ~300,000 records = ~50 MB
- **Stock Logs:** ~500,000 records = ~75 MB
- **Customers:** ~5,000 records = ~2 MB
- **Expenses:** ~10,000 records = ~5 MB

**Total Estimated:** ~150-200 MB/year (data only)

---

## Backup Strategy

- **Daily:** Automated mysqldump
- **Weekly:** Full backup with stock snapshots
- **Monthly:** Archive old data (> 1 year)

