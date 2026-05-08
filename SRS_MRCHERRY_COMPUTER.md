# Software Requirements Specification

## MrCherry Computer POS System

Business Inventory, Sales, and Point-of-Sale Platform

Prepared for: MrCherryComputers  
Prepared by: [Your Name / Group Name]  
Date: May 2026

---

## Table of Contents

1. Introduction  
   1.1 Purpose  
   1.2 Scope  
   1.3 Definitions, Acronyms, and Abbreviations  
   1.4 References  
2. Overall Description  
   2.1 Product Perspective  
   2.2 Product Functions  
   2.3 Users of the System  
   2.4 Operating Environment  
3. System Features / Functional Requirements  
4. Non-Functional Requirements  
5. System Architecture  
6. External Interface Requirements  
7. Other Requirements  
8. Route / API Reference  
9. Appendices  

---

## 1. Introduction

### 1.1 Purpose

This Software Requirements Specification document describes the functional and non-functional requirements of the MrCherry Computer POS System. The system is a web-based point-of-sale, inventory, customer, expense, return, and reporting platform designed for a computer retail shop. This document provides a clear understanding of the project for developers, supervisors, testers, and stakeholders.

### 1.2 Scope

The MrCherry Computer POS System supports day-to-day retail business operations for a computer shop. It allows authorized users to manage products, categories, units, customers, stock, sales, returns, expenses, invoices, and business reports.

The main scope includes:

- User authentication and role-based access control
- Admin, Manager, and Cashier user roles
- Product, category, and unit management
- Stock management with low-stock alerts
- POS sales processing
- Customer management
- Invoice, PDF, and thermal receipt generation
- Return and refund handling
- Expense tracking and approval
- Business reports and analytics
- Profile and password management
- Single-shop business operation support

### 1.3 Definitions, Acronyms, and Abbreviations

| Term | Description |
| --- | --- |
| POS | Point of Sale |
| SRS | Software Requirements Specification |
| RBAC | Role-Based Access Control |
| Admin | Main system user with full access |
| Manager | User who manages products, inventory, sales, returns, expenses, and reports |
| Cashier | User who processes sales and views limited records |
| SKU | Stock Keeping Unit |
| PDF | Portable Document Format |
| CRUD | Create, Read, Update, Delete |
| DB | Database |
| UI | User Interface |

### 1.4 References

1. Laravel Documentation: https://laravel.com/docs  
2. MySQL Documentation: https://dev.mysql.com/doc/  
3. Tailwind CSS Documentation: https://tailwindcss.com/docs  
4. Laravel Breeze Documentation: https://laravel.com/docs/starter-kits  
5. DomPDF Laravel Package: https://github.com/barryvdh/laravel-dompdf  
6. MrCherry Computer project repository and project documentation files

---

## 2. Overall Description

### 2.1 Product Perspective

The MrCherry Computer POS System is a web-based application built using a client-server architecture. The application is developed using Laravel, Blade templates, MySQL, JavaScript, Alpine.js, Livewire, and Tailwind CSS.

The system provides a central platform for managing retail shop operations. It replaces manual billing, manual stock books, and scattered sales records with a computerized solution.

| Component | Technology | Responsibility |
| --- | --- | --- |
| Frontend | Blade, Tailwind CSS, Alpine.js, Livewire | User interface, forms, tables, dashboards, POS screen |
| Backend | Laravel 10 | Authentication, authorization, business logic, routing, validation |
| Database | MySQL | Store users, products, stock, customers, sales, payments, returns, and expenses |
| Authentication | Laravel Breeze / Session Auth | Secure login, logout, password reset |
| PDF / Print | DomPDF, browser print, thermal invoice views | Invoice and receipt generation |
| Reporting | Laravel controllers, queries, export libraries | Sales, inventory, expense, and profit/loss reports |

### 2.2 Product Functions

The major functions of the system are:

- User login, logout, registration, and password reset
- Role-based dashboards
- User and role management
- Category and unit management
- Product creation, update, deletion, and search
- Barcode/SKU-based product lookup
- Stock updates and stock movement history
- Low-stock alert generation
- Customer record management
- POS cart and checkout
- Payment processing and change calculation
- Sales history and invoice generation
- PDF invoice download and receipt printing
- Return processing and stock restoration
- Expense creation, approval, rejection, and tracking
- Reports for sales, inventory, stock movement, expenses, profit/loss, and customers

### 2.3 Users of the System

#### 2.3.1 Admin

The Admin has full control over the system.

Responsibilities:

- Manage all users and roles
- Manage shops and system settings where applicable
- Manage products, categories, units, customers, stock, expenses, returns, and reports
- View all dashboards and business analytics
- Approve or reject expenses
- Delete records where permission is restricted to Admin

#### 2.3.2 Manager

The Manager handles operational management of the shop.

Responsibilities:

- Manage products, categories, units, stock, customers, sales, returns, and expenses
- View business reports
- Monitor daily sales and inventory
- Create cashier users where allowed
- Reprint invoices and manage daily shop activities

#### 2.3.3 Cashier

The Cashier performs sales-related operations.

Responsibilities:

- Access the POS screen
- Search and select products
- Add customers during sales
- Process payments
- Generate and print invoices
- View own sales records
- View limited dashboard information

### 2.4 Operating Environment

The system operates in a web browser and requires a server environment capable of running PHP, Laravel, and MySQL.

Recommended environment:

- Operating System: Windows, Linux, or macOS
- Web Server: Apache or Nginx
- PHP: 8.1 or higher
- Database: MySQL 8.0 or compatible version
- Composer for PHP dependency management
- Node.js and npm for frontend asset compilation
- Browser: Google Chrome, Microsoft Edge, Firefox, or similar modern browser

---

## 3. System Features / Functional Requirements

### 3.1 User Authentication

| ID | Description |
| --- | --- |
| FR-AUTH-01 | The system shall allow users to log in using registered email and password. |
| FR-AUTH-02 | The system shall allow authenticated users to log out securely. |
| FR-AUTH-03 | The system shall support password reset through email. |
| FR-AUTH-04 | The system shall allow users to update their own password. |
| FR-AUTH-05 | The system shall protect pages from unauthenticated access. |
| FR-AUTH-06 | The system shall redirect users to a dashboard after login. |

### 3.2 User Management

| ID | Description |
| --- | --- |
| FR-USER-01 | The system shall allow Admins to create users. |
| FR-USER-02 | The system shall allow authorized users to view user lists. |
| FR-USER-03 | The system shall allow Admins or permitted Managers to update user details. |
| FR-USER-04 | The system shall allow Admins to deactivate, suspend, or delete users. |
| FR-USER-05 | The system shall assign roles such as Admin, Manager, and Cashier. |
| FR-USER-06 | The system shall restrict user actions according to assigned role. |
| FR-USER-07 | The system shall allow users to manage their profile information. |

### 3.3 Category Management

| ID | Description |
| --- | --- |
| FR-CAT-01 | The system shall allow Admins and Managers to create product categories. |
| FR-CAT-02 | The system shall allow Admins and Managers to update categories. |
| FR-CAT-03 | The system shall allow Admins and Managers to delete categories. |
| FR-CAT-04 | The system shall support active/inactive category status. |
| FR-CAT-05 | The system shall support parent categories where applicable. |

### 3.4 Unit Management

| ID | Description |
| --- | --- |
| FR-UNIT-01 | The system shall allow Admins and Managers to create measurement units. |
| FR-UNIT-02 | The system shall allow Admins and Managers to update units. |
| FR-UNIT-03 | The system shall allow Admins and Managers to delete units. |
| FR-UNIT-04 | The system shall allow products to be assigned to units such as pcs, box, or item. |

### 3.5 Product Management

| ID | Description |
| --- | --- |
| FR-PROD-01 | The system shall allow Admins and Managers to add products. |
| FR-PROD-02 | The system shall store product name, SKU, barcode, category, unit, prices, tax, image, and stock alert level. |
| FR-PROD-03 | The system shall allow Admins and Managers to update product details. |
| FR-PROD-04 | The system shall allow Admins and Managers to delete or deactivate products. |
| FR-PROD-05 | The system shall allow users to search products by name, SKU, or barcode. |
| FR-PROD-06 | The system shall allow product image upload. |
| FR-PROD-07 | The system shall support low-stock filtering. |

### 3.6 Inventory Management

| ID | Description |
| --- | --- |
| FR-INV-01 | The system shall maintain current stock quantity for each product. |
| FR-INV-02 | The system shall reduce stock automatically after a sale. |
| FR-INV-03 | The system shall increase stock automatically after completed returns. |
| FR-INV-04 | The system shall allow Admins and Managers to add stock. |
| FR-INV-05 | The system shall allow manual stock adjustments with notes. |
| FR-INV-06 | The system shall keep stock logs for audit history. |
| FR-INV-07 | The system shall display low-stock alerts when stock goes below the configured threshold. |
| FR-INV-08 | The system shall prevent sales when requested quantity is greater than available stock. |

### 3.7 Customer Management

| ID | Description |
| --- | --- |
| FR-CUST-01 | The system shall allow customer records to be created. |
| FR-CUST-02 | The system shall store customer name, phone, email, address, balance, and loyalty information where applicable. |
| FR-CUST-03 | The system shall allow authorized users to update customer records. |
| FR-CUST-04 | The system shall allow authorized users to search customers. |
| FR-CUST-05 | The system shall show customer purchase history. |
| FR-CUST-06 | The system shall allow a customer to be linked to a sale. |

### 3.8 POS Management

| ID | Description |
| --- | --- |
| FR-POS-01 | The system shall provide a POS interface for sales processing. |
| FR-POS-02 | The system shall allow users to search and add products to a cart. |
| FR-POS-03 | The system shall calculate subtotal, tax, discount, total, paid amount, and change amount. |
| FR-POS-04 | The system shall support multiple payment methods such as cash and other configured methods. |
| FR-POS-05 | The system shall generate a unique invoice number for each sale. |
| FR-POS-06 | The system shall store sale items and payment details. |
| FR-POS-07 | The system shall allow users to print invoices after payment. |
| FR-POS-08 | The system shall allow users to view transaction history according to role. |

### 3.9 Sales Management

| ID | Description |
| --- | --- |
| FR-SALE-01 | The system shall allow Admins and Managers to view all sales. |
| FR-SALE-02 | The system shall allow Cashiers to view only their own sales. |
| FR-SALE-03 | The system shall allow sales to be filtered by date, customer, status, and invoice number. |
| FR-SALE-04 | The system shall allow authorized users to update sale status. |
| FR-SALE-05 | The system shall generate PDF invoices. |
| FR-SALE-06 | The system shall allow invoices to be downloaded and reprinted. |

### 3.10 Return Management

| ID | Description |
| --- | --- |
| FR-RET-01 | The system shall allow Admins and Managers to create return requests. |
| FR-RET-02 | The system shall allow users to search original sales for return processing. |
| FR-RET-03 | The system shall validate return quantities against sold quantities. |
| FR-RET-04 | The system shall prevent returning more items than originally sold. |
| FR-RET-05 | The system shall support return statuses such as pending, completed, and cancelled. |
| FR-RET-06 | The system shall restore stock when a return is completed. |
| FR-RET-07 | The system shall maintain return history. |

### 3.11 Expense Management

| ID | Description |
| --- | --- |
| FR-EXP-01 | The system shall allow Admins and Managers to create expense records. |
| FR-EXP-02 | The system shall allow expenses to be categorized. |
| FR-EXP-03 | The system shall support receipt file upload. |
| FR-EXP-04 | The system shall support expense approval and rejection. |
| FR-EXP-05 | The system shall allow only pending expenses to be edited. |
| FR-EXP-06 | The system shall allow Admins to approve or reject expenses. |
| FR-EXP-07 | The system shall include expenses in business reports. |

### 3.12 Reports and Analytics

| ID | Description |
| --- | --- |
| FR-REP-01 | The system shall provide a dashboard with sales and stock summary. |
| FR-REP-02 | The system shall generate sales reports. |
| FR-REP-03 | The system shall generate product sales reports. |
| FR-REP-04 | The system shall generate inventory reports. |
| FR-REP-05 | The system shall generate stock movement reports. |
| FR-REP-06 | The system shall generate expense reports. |
| FR-REP-07 | The system shall generate profit and loss reports. |
| FR-REP-08 | The system shall generate customer reports. |
| FR-REP-09 | The system shall support date range filtering in reports. |
| FR-REP-10 | The system shall support PDF or export formats where implemented. |

### 3.13 Profile Management

| ID | Description |
| --- | --- |
| FR-PROFILE-01 | The system shall allow users to view profile details. |
| FR-PROFILE-02 | The system shall allow users to update profile details. |
| FR-PROFILE-03 | The system shall allow users to change password. |

---

## 4. Non-Functional Requirements

### 4.1 Security Requirements

| ID | Description |
| --- | --- |
| NFR-SEC-01 | The system shall hash user passwords before storing them. |
| NFR-SEC-02 | The system shall enforce authentication for protected pages. |
| NFR-SEC-03 | The system shall enforce role-based access control on the server side. |
| NFR-SEC-04 | The system shall validate user input before saving data. |
| NFR-SEC-05 | The system shall prevent unauthorized users from accessing restricted routes. |
| NFR-SEC-06 | Uploaded files shall be validated for type and size. |
| NFR-SEC-07 | Sensitive environment values shall not be committed publicly. |

### 4.2 Performance Requirements

| ID | Description |
| --- | --- |
| NFR-PERF-01 | Product search in POS should return results quickly for normal shop inventory size. |
| NFR-PERF-02 | Dashboard and reports should load within an acceptable time for daily business use. |
| NFR-PERF-03 | Database queries should use proper indexes for products, sales, customers, and stock. |
| NFR-PERF-04 | The system should support multiple authenticated users working at the same time. |

### 4.3 Usability Requirements

| ID | Description |
| --- | --- |
| NFR-USE-01 | The user interface shall be simple and understandable for shop staff. |
| NFR-USE-02 | The POS screen shall allow fast product search and checkout. |
| NFR-USE-03 | The system shall display validation errors clearly. |
| NFR-USE-04 | Low-stock alerts shall be visible to authorized users. |
| NFR-USE-05 | The UI shall be responsive for desktop, laptop, and tablet use. |

### 4.4 Maintainability Requirements

| ID | Description |
| --- | --- |
| NFR-MAIN-01 | The system shall follow Laravel MVC structure. |
| NFR-MAIN-02 | Routes, controllers, models, migrations, and views shall be organized by feature. |
| NFR-MAIN-03 | Configuration shall be handled through Laravel configuration files and environment variables. |
| NFR-MAIN-04 | Code should follow consistent formatting and naming conventions. |

### 4.5 Reliability Requirements

| ID | Description |
| --- | --- |
| NFR-REL-01 | The system shall keep sales and stock records consistent during transactions. |
| NFR-REL-02 | The system shall prevent invalid stock changes. |
| NFR-REL-03 | The system shall maintain logs for stock movements. |
| NFR-REL-04 | The system shall keep invoice records after sales are completed. |

---

## 5. System Architecture

### 5.1 Technology Stack

| Layer | Technology | Purpose |
| --- | --- | --- |
| Backend | Laravel 10 | Server-side framework |
| Language | PHP 8.1+ | Backend programming language |
| Frontend | Blade Templates | Server-rendered UI |
| Frontend Interactivity | Alpine.js, Livewire | Dynamic UI behavior |
| Styling | Tailwind CSS, Tabler assets | Responsive UI design |
| Database | MySQL | Relational data storage |
| Authentication | Laravel Breeze | Login, logout, password reset |
| Authorization | Roles, permissions, middleware | Access control |
| PDF | Laravel DomPDF | Invoice/report PDF generation |
| Excel / Spreadsheet | PhpSpreadsheet | Spreadsheet export/import support |
| Barcode | Picqer Barcode Generator | Barcode generation |
| Build Tool | Vite | Frontend asset bundling |

### 5.2 High-Level Architecture

MrCherry Computer POS System follows a three-tier architecture:

- Presentation Tier: Blade views, Tailwind CSS, Alpine.js, Livewire, and JavaScript provide the user interface.
- Application Tier: Laravel controllers, middleware, services, models, and validation handle business logic.
- Data Tier: MySQL stores users, roles, products, stock, sales, payments, returns, customers, expenses, and reports data.

### 5.3 Backend Module Structure

| Module | Responsibility |
| --- | --- |
| Auth | Login, logout, registration, password reset |
| Dashboard | Role-based metrics and summary widgets |
| Users | User creation, editing, suspension, deletion, role assignment |
| Categories | Product category CRUD |
| Units | Product unit CRUD |
| Products | Product catalogue, image upload, SKU/barcode search |
| Stock | Stock quantity and stock movement logs |
| Customers | Customer CRUD and purchase history |
| POS | Cart, checkout, payments, invoices |
| Sales | Sales history, status updates, invoice download |
| Returns | Return creation, completion, cancellation, stock restoration |
| Expenses | Expense tracking, categories, approval workflow |
| Reports | Sales, inventory, stock movement, expenses, profit/loss, customer reports |

---

## 6. External Interface Requirements

### 6.1 User Interfaces

| Page / View | Description | Accessible By |
| --- | --- | --- |
| Login | User login page | Guest |
| Dashboard | Role-based business summary | Admin, Manager, Cashier |
| POS | Product search, cart, checkout, payment | Admin, Manager, Cashier |
| Products | Product list, create, edit, stock update | Admin, Manager |
| Categories | Category list, create, edit, delete | Admin, Manager |
| Units | Unit list, create, edit, delete | Admin, Manager |
| Customers | Customer list, create, edit, details | Admin, Manager |
| Sales | Sales list, details, invoice, status | Admin, Manager, Cashier with restrictions |
| Returns | Create and manage returns | Admin, Manager |
| Expenses | Create and manage expenses | Admin, Manager |
| Expense Categories | Manage expense categories | Admin |
| Reports | Business reports | Admin, Manager |
| Profile | View and update profile | All authenticated users |

### 6.2 Hardware Interfaces

The system does not require proprietary hardware. It may support:

- Desktop computer or laptop
- Barcode scanner that works as keyboard input
- Receipt printer or normal printer through browser print dialog
- Network connection to access the hosted system

### 6.3 Software Interfaces

| System | Interface Type | Purpose |
| --- | --- | --- |
| MySQL | Laravel database connection | Store application data |
| Browser | HTTP/HTTPS | Access web application |
| DomPDF | Laravel package | Generate PDF invoices and reports |
| Vite | Build tool | Compile frontend assets |
| Mail server | SMTP configuration | Password reset and email features where configured |

---

## 7. Other Requirements

### 7.1 Security Rules

- Only authenticated users can access protected system pages.
- Admin users can perform all operations.
- Managers can manage most operational features but cannot perform Admin-only actions.
- Cashiers can process POS transactions and view limited records.
- Passwords must be stored securely using hashing.
- Database credentials must be stored in the `.env` file.
- File uploads must be validated.
- Server-side authorization must be applied even if the UI hides restricted buttons.

### 7.2 Business Rules

- A sale must not be completed if product stock is insufficient.
- Stock must decrease after successful sale completion.
- Stock must increase after a completed return.
- Return quantity cannot exceed the sold quantity.
- Each sale must have a unique invoice number.
- Cashiers can view only their own sales unless permission is granted.
- Expenses require approval where the approval workflow is enabled.
- Deleted important records should use soft delete where applicable.
- Admin approval is required for sensitive actions such as expense approval, user role assignment, and selected deletions.

---

## 8. Route / API Reference

### 8.1 Authentication Routes

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/login` | Login page | Guest |
| POST | `/login` | Process login | Guest |
| POST | `/logout` | Logout user | Authenticated |
| GET | `/register` | Registration page | Guest / configured access |
| POST | `/register` | Process registration | Guest / configured access |
| GET | `/forgot-password` | Forgot password page | Guest |
| POST | `/forgot-password` | Send reset link | Guest |
| GET | `/reset-password/{token}` | Reset password form | Guest |
| POST | `/reset-password` | Process password reset | Guest |

### 8.2 Dashboard

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/dashboard` | Role-based dashboard | Authenticated |

### 8.3 POS

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/pos` | POS interface | Authenticated |
| GET | `/pos/search-products` | Search products for POS | Authenticated |
| POST | `/pos/process-sale` | Process sale transaction | Authenticated |
| GET | `/pos/invoice/{id}` | Print invoice | Authenticated |
| GET | `/pos/thermal-invoice/{id}` | Thermal invoice view | Authenticated |

### 8.4 Products

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/products` | Product list | Admin, Manager |
| GET | `/products/create` | Create product form | Admin, Manager |
| POST | `/products` | Store product | Admin, Manager |
| GET | `/products/{id}/edit` | Edit product form | Admin, Manager |
| PUT/PATCH | `/products/{id}` | Update product | Admin, Manager |
| DELETE | `/products/{id}` | Delete product | Admin, Manager |
| POST | `/products/{id}/update-stock` | Update product stock | Admin, Manager |

### 8.5 Categories and Units

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/categories` | Category list | Admin, Manager |
| POST | `/categories` | Store category | Admin, Manager |
| PUT/PATCH | `/categories/{id}` | Update category | Admin, Manager |
| DELETE | `/categories/{id}` | Delete category | Admin, Manager |
| GET | `/units` | Unit list | Admin, Manager |
| POST | `/units` | Store unit | Admin, Manager |
| PUT/PATCH | `/units/{id}` | Update unit | Admin, Manager |
| DELETE | `/units/{id}` | Delete unit | Admin, Manager |

### 8.6 Customers

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/customers` | Customer list | Admin, Manager |
| GET | `/customers/create` | Create customer form | Admin, Manager |
| POST | `/customers` | Store customer | Admin, Manager |
| GET | `/customers/{id}` | Customer details | Admin, Manager |
| GET | `/customers/{id}/edit` | Edit customer form | Admin, Manager |
| PUT/PATCH | `/customers/{id}` | Update customer | Admin, Manager |
| DELETE | `/customers/{id}` | Delete customer | Admin, Manager |
| GET | `/customers-search` | Search customers | Admin, Manager |

### 8.7 Sales

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/sales` | Sales list | Authenticated |
| GET | `/sales/{id}` | Sale details | Authenticated |
| GET | `/sales/{id}/edit` | Edit sale | Admin, Manager |
| GET | `/sales/{id}/invoice` | View invoice | Authenticated |
| GET | `/sales/{id}/download` | Download invoice | Authenticated |
| PATCH | `/sales/{id}/status` | Update sale status | Admin, Manager |
| DELETE | `/sales/{id}` | Delete sale | Admin |

### 8.8 Returns

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/returns` | Return list | Admin, Manager |
| GET | `/returns/create` | Create return form | Admin, Manager |
| POST | `/returns` | Store return | Admin, Manager |
| GET | `/returns/{id}` | Return details | Admin, Manager |
| POST | `/returns/{id}/complete` | Complete return | Admin, Manager |
| POST | `/returns/{id}/cancel` | Cancel return | Admin, Manager |
| DELETE | `/returns/{id}` | Delete return | Admin |
| GET | `/returns-search-sale` | Search sale for return | Admin, Manager |

### 8.9 Expenses

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/expenses` | Expense list | Admin, Manager |
| GET | `/expenses/create` | Create expense form | Admin, Manager |
| POST | `/expenses` | Store expense | Admin, Manager |
| GET | `/expenses/{id}` | Expense details | Admin, Manager |
| GET | `/expenses/{id}/edit` | Edit expense | Admin, Manager |
| PUT/PATCH | `/expenses/{id}` | Update expense | Admin, Manager |
| DELETE | `/expenses/{id}` | Delete expense | Admin |
| POST | `/expenses/{id}/approve` | Approve expense | Admin |
| POST | `/expenses/{id}/reject` | Reject expense | Admin |

### 8.10 Reports

| Method | Route | Description | Access |
| --- | --- | --- | --- |
| GET | `/reports` | Reports dashboard | Admin, Manager |
| GET | `/reports/sales` | Sales report | Admin, Manager |
| GET | `/reports/product-sales` | Product sales report | Admin, Manager |
| GET | `/reports/inventory` | Inventory report | Admin, Manager |
| GET | `/reports/stock-movement` | Stock movement report | Admin, Manager |
| GET | `/reports/expenses` | Expense report | Admin, Manager |
| GET | `/reports/profit-loss` | Profit and loss report | Admin, Manager |
| GET | `/reports/customers` | Customer report | Admin, Manager |

---

## 9. Appendices

### 9.1 Appendix A - Main Database Tables

| Table | Purpose |
| --- | --- |
| `users` | Stores system user accounts |
| `roles` | Stores role names such as Admin, Manager, Cashier |
| `permissions` | Stores permission definitions |
| `role_user` | Maps users to roles |
| `permission_role` | Maps permissions to roles |
| `categories` | Stores product categories |
| `units` | Stores product measurement units |
| `products` | Stores product details |
| `stocks` | Stores current stock quantity |
| `stock_logs` | Stores stock movement history |
| `customers` | Stores customer details |
| `sales` | Stores sale master records |
| `sale_items` | Stores products sold in each sale |
| `payments` | Stores sale payment information |
| `returns` | Stores return master records |
| `return_items` | Stores products returned |
| `expenses` | Stores business expenses |
| `expense_categories` | Stores expense category data |

### 9.2 Appendix B - Environment Variables Reference

| Variable | Description |
| --- | --- |
| `APP_NAME` | Application name |
| `APP_ENV` | Application environment such as local or production |
| `APP_KEY` | Laravel application encryption key |
| `APP_DEBUG` | Debug mode setting |
| `APP_URL` | Base application URL |
| `DB_CONNECTION` | Database connection type, usually mysql |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |
| `MAIL_MAILER` | Mail driver |
| `MAIL_HOST` | Mail server host |
| `MAIL_PORT` | Mail server port |
| `MAIL_USERNAME` | Mail username |
| `MAIL_PASSWORD` | Mail password |

### 9.3 Appendix C - Default Development Credentials

These credentials are for development/testing only and must be changed before production deployment.

| Role | Email | Password | Access Level |
| --- | --- | --- | --- |
| Admin | `admin@pos.com` | `password` | Full system access |
| Manager | `manager@pos.com` | `password` | Operational management access |
| Cashier | `cashier@pos.com` | `password` | POS and own-sales access |

### 9.4 Appendix D - Role Access Summary

| Feature Area | Admin | Manager | Cashier |
| --- | --- | --- | --- |
| Dashboard | Full | Operational | Limited |
| User Management | Full | Limited | No |
| Category Management | Full | Full | View only / No management |
| Unit Management | Full | Full | View only / No management |
| Product Management | Full | Full | View/Search only |
| Stock Management | Full | Full | View alerts only |
| POS | Full | Full | Full |
| Sales Management | Full | Full | Own sales only |
| Invoice Printing | Full | Full | Own invoices |
| Returns | Full | Full | Limited / No management depending on policy |
| Expenses | Full | Full | No |
| Reports | Full | Full | Limited |

