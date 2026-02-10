# Laravel POS System - Route & API Reference

## 🔐 Authentication Routes

| Method | Route | Description | Access |
|--------|-------|-------------|--------|
| GET | `/login` | Login page | Guest |
| POST | `/login` | Process login | Guest |
| POST | `/logout` | Logout user | Authenticated |
| GET | `/register` | Registration page | Guest |
| POST | `/register` | Process registration | Guest |
| GET | `/forgot-password` | Forgot password | Guest |
| POST | `/forgot-password` | Send reset link | Guest |
| GET | `/reset-password/{token}` | Reset password form | Guest |
| POST | `/reset-password` | Process password reset | Guest |

---

## 🏠 Dashboard

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/dashboard` | DashboardController@index | All Authenticated |

Shows role-based metrics:
- **Admin/Manager:** Total sales, today's sales, low stock alerts, pending expenses
- **Cashier:** Own sales only

---

## 🛒 Point of Sale (POS)

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/pos` | POSController@index | All Authenticated |
| GET | `/pos/search-products` | POSController@searchProducts | All Authenticated |
| POST | `/pos/process-sale` | POSController@processSale | All Authenticated |
| GET | `/pos/invoice/{id}` | POSController@printInvoice | All Authenticated |
| GET | `/pos/thermal-invoice/{id}` | POSController@thermalInvoice | All Authenticated |

### API Endpoints:

**Search Products**
```
GET /pos/search-products?q=laptop
Response: [{ id, name, sku, barcode, price, tax_percentage, unit, category, stock, image }]
```

**Process Sale**
```
POST /pos/process-sale
Body: {
  customer_id: 1,
  items: [{
    product_id: 1,
    quantity: 2,
    price: 100,
    tax_amount: 18
  }],
  subtotal: 200,
  tax_amount: 36,
  discount_amount: 0,
  total_amount: 236,
  payment_method: 'cash',
  paid_amount: 250,
  change_amount: 14,
  notes: 'Optional note'
}
Response: { success: true, sale_id: 1, invoice_number: 'INV-20240101-00001' }
```

---

## 📦 Products

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/products` | ProductController@index | Admin, Manager |
| GET | `/products/create` | ProductController@create | Admin, Manager |
| POST | `/products` | ProductController@store | Admin, Manager |
| GET | `/products/{id}/edit` | ProductController@edit | Admin, Manager |
| PUT/PATCH | `/products/{id}` | ProductController@update | Admin, Manager |
| DELETE | `/products/{id}` | ProductController@destroy | Admin, Manager |
| POST | `/products/{id}/update-stock` | ProductController@updateStock | Admin, Manager |

### Features:
- Image upload
- Initial stock entry
- Manual stock adjustments (in/out/adjustment)
- Low stock filtering
- Search by name/SKU/barcode
- Category filtering

---

## 📋 Categories

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/categories` | CategoryController@index | Admin, Manager |
| GET | `/categories/create` | CategoryController@create | Admin, Manager |
| POST | `/categories` | CategoryController@store | Admin, Manager |
| GET | `/categories/{id}/edit` | CategoryController@edit | Admin, Manager |
| PUT/PATCH | `/categories/{id}` | CategoryController@update | Admin, Manager |
| DELETE | `/categories/{id}` | CategoryController@destroy | Admin, Manager |

### Features:
- Auto-generated slugs
- Parent category support
- Active/inactive status

---

## 📐 Units

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/units` | UnitController@index | Admin, Manager |
| GET | `/units/create` | UnitController@create | Admin, Manager |
| POST | `/units` | UnitController@store | Admin, Manager |
| GET | `/units/{id}/edit` | UnitController@edit | Admin, Manager |
| PUT/PATCH | `/units/{id}` | UnitController@update | Admin, Manager |
| DELETE | `/units/{id}` | UnitController@destroy | Admin, Manager |

---

## 👥 Customers

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/customers` | CustomerController@index | Admin, Manager |
| GET | `/customers/create` | CustomerController@create | Admin, Manager |
| POST | `/customers` | CustomerController@store | Admin, Manager |
| GET | `/customers/{id}` | CustomerController@show | Admin, Manager |
| GET | `/customers/{id}/edit` | CustomerController@edit | Admin, Manager |
| PUT/PATCH | `/customers/{id}` | CustomerController@update | Admin, Manager |
| DELETE | `/customers/{id}` | CustomerController@destroy | Admin, Manager |
| GET | `/customers-search` | CustomerController@search | Admin, Manager |

### API:
**Search Customers**
```
GET /customers-search?q=john
Response: [{ id, name, phone, email, company_name }]
```

---

## 🧾 Sales

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/sales` | SaleController@index | All Authenticated |
| GET | `/sales/{id}` | SaleController@show | All Authenticated |
| GET | `/sales/{id}/edit` | SaleController@edit | Admin, Manager |
| GET | `/sales/{id}/invoice` | SaleController@invoice | All Authenticated |
| GET | `/sales/{id}/download` | SaleController@downloadInvoice | All Authenticated |
| PATCH | `/sales/{id}/status` | SaleController@updateStatus | Admin, Manager |
| DELETE | `/sales/{id}` | SaleController@destroy | Admin Only |

### Role-Based Filtering:
- **Admin/Manager:** View all sales
- **Cashier:** View only own sales

### Features:
- Search by invoice number or customer
- Filter by status
- Date range filtering
- Invoice PDF generation
- Status management (pending/completed/cancelled)

---

## 💰 Expenses

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/expenses` | ExpenseController@index | Admin, Manager |
| GET | `/expenses/create` | ExpenseController@create | Admin, Manager |
| POST | `/expenses` | ExpenseController@store | Admin, Manager |
| GET | `/expenses/{id}` | ExpenseController@show | Admin, Manager |
| GET | `/expenses/{id}/edit` | ExpenseController@edit | Admin, Manager |
| PUT/PATCH | `/expenses/{id}` | ExpenseController@update | Admin, Manager |
| DELETE | `/expenses/{id}` | ExpenseController@destroy | Admin Only |
| POST | `/expenses/{id}/approve` | ExpenseController@approve | Admin Only |
| POST | `/expenses/{id}/reject` | ExpenseController@reject | Admin Only |

### Features:
- Receipt file upload
- Approval workflow
- Auto-approval for admin-created expenses
- Only pending expenses can be edited
- Payment method tracking

---

## 🏷️ Expense Categories

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/expense-categories` | ExpenseCategoryController@index | Admin Only |
| GET | `/expense-categories/create` | ExpenseCategoryController@create | Admin Only |
| POST | `/expense-categories` | ExpenseCategoryController@store | Admin Only |
| GET | `/expense-categories/{id}/edit` | ExpenseCategoryController@edit | Admin Only |
| PUT/PATCH | `/expense-categories/{id}` | ExpenseCategoryController@update | Admin Only |
| DELETE | `/expense-categories/{id}` | ExpenseCategoryController@destroy | Admin Only |

---

## ↩️ Returns

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/returns` | ReturnController@index | Admin, Manager |
| GET | `/returns/create` | ReturnController@create | Admin, Manager |
| POST | `/returns` | ReturnController@store | Admin, Manager |
| GET | `/returns/{id}` | ReturnController@show | Admin, Manager |
| POST | `/returns/{id}/complete` | ReturnController@complete | Admin, Manager |
| POST | `/returns/{id}/cancel` | ReturnController@cancel | Admin, Manager |
| DELETE | `/returns/{id}` | ReturnController@destroy | Admin Only |
| GET | `/returns-search-sale` | ReturnController@searchSale | Admin, Manager |

### API:
**Search Sale for Return**
```
GET /returns-search-sale?q=INV-20240101-00001
Response: [{
  id, invoice_number, customer_name, total_amount, created_at,
  items: [{ product_id, product_name, quantity, price, total }]
}]
```

### Features:
- Search original sale
- Validate return quantities
- Prevent over-returns
- Automatic stock restoration when completed
- Status: pending → completed/cancelled

---

## 📊 Reports

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/reports` | ReportController@index | Admin, Manager |
| GET | `/reports/sales` | ReportController@sales | Admin, Manager |
| GET | `/reports/product-sales` | ReportController@productSales | Admin, Manager |
| GET | `/reports/inventory` | ReportController@inventory | Admin, Manager |
| GET | `/reports/stock-movement` | ReportController@stockMovement | Admin, Manager |
| GET | `/reports/expenses` | ReportController@expenses | Admin, Manager |
| GET | `/reports/profit-loss` | ReportController@profitLoss | Admin, Manager |
| GET | `/reports/customers` | ReportController@customers | Admin, Manager |

### Report Types:

**1. Sales Report**
```
GET /reports/sales?date_from=2024-01-01&date_to=2024-12-31&group_by=day&format=pdf
Parameters:
- date_from (required): Start date
- date_to (required): End date
- group_by (optional): day|month|year
- format (optional): excel|pdf
```

**2. Product Sales Report**
```
GET /reports/product-sales?date_from=2024-01-01&date_to=2024-12-31&limit=20&format=excel
Parameters:
- date_from (required)
- date_to (required)
- limit (optional): Number of products (default: 20)
- format (optional): excel|pdf
```

**3. Inventory Report**
```
GET /reports/inventory?category_id=1&low_stock=1&format=pdf
Parameters:
- category_id (optional): Filter by category
- low_stock (optional): Show only low stock items
- format (optional): excel|pdf
```

**4. Stock Movement Report**
```
GET /reports/stock-movement?date_from=2024-01-01&date_to=2024-12-31&product_id=1&type=out
Parameters:
- date_from (required)
- date_to (required)
- product_id (optional): Filter by product
- type (optional): in|out|adjustment
```

**5. Expense Report**
```
GET /reports/expenses?date_from=2024-01-01&date_to=2024-12-31&category_id=1&format=excel
Parameters:
- date_from (required)
- date_to (required)
- category_id (optional): Filter by category
- format (optional): excel|pdf
```

**6. Profit & Loss Report**
```
GET /reports/profit-loss?date_from=2024-01-01&date_to=2024-12-31&format=pdf
Parameters:
- date_from (required)
- date_to (required)
- format (optional): pdf
```

**7. Customer Report**
```
GET /reports/customers?format=excel
Parameters:
- format (optional): excel
```

---

## 👤 Users

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/users` | UserController@index | Admin Only |
| GET | `/users/create` | UserController@create | Admin Only |
| POST | `/users` | UserController@store | Admin Only |
| GET | `/users/{id}` | UserController@show | Admin Only |
| GET | `/users/{id}/edit` | UserController@edit | Admin Only |
| PUT/PATCH | `/users/{id}` | UserController@update | Admin Only |
| DELETE | `/users/{id}` | UserController@destroy | Admin Only |
| POST | `/users/{id}/toggle-status` | UserController@toggleStatus | Admin Only |

### Features:
- Role assignment
- User statistics (sales, expenses)
- Active/inactive status toggle
- Prevent self-deletion
- Prevent deactivating self

---

## 👤 Profile

| Method | Route | Controller@Method | Access |
|--------|-------|-------------------|--------|
| GET | `/profile` | ProfileController@edit | All Authenticated |
| PATCH | `/profile` | ProfileController@update | All Authenticated |
| DELETE | `/profile` | ProfileController@destroy | All Authenticated |

---

## 🔑 Permission Summary

### Permissions by Role

**Admin (Full Access):**
- All product operations
- All customer operations
- All sales operations (including delete)
- All expense operations (including approve/reject)
- All return operations
- All report access
- User management
- Expense category management

**Manager:**
- View dashboard (all metrics)
- POS operations
- Product management
- Customer management
- Sales management (no delete)
- Expense management (no approve)
- Return management
- Report access

**Cashier:**
- View dashboard (own sales only)
- POS operations
- View own sales only
- Cannot edit products, customers, or master data
- No report access
- No user management

---

## 📝 Response Formats

### Success Response (JSON)
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### Error Response (JSON)
```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

### Flash Messages (Blade)
- Success: `session('success')`
- Error: `session('error')`
- Validation: `$errors`

---

## 🔒 Authorization Flow

1. User logs in → Laravel Breeze authentication
2. System checks user role → Spatie Permission
3. Middleware checks route access → CheckRole middleware
4. Controller checks specific permissions → role-based logic
5. View renders based on user permissions

---

## 📊 Database Auto-Generated Fields

### Invoice Numbers
- Format: `INV-YYYYMMDD-00001`
- Auto-increments daily
- Generated in Sale model

### Expense Numbers
- Format: `EXP-YYYYMMDD-00001`
- Auto-increments daily
- Generated in Expense model

### Return Numbers
- Format: `RET-YYYYMMDD-00001`
- Auto-increments daily
- Generated in ReturnModel

---

**Note:** All routes except authentication require the user to be logged in and verified.
