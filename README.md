# 🛒 MrCherry Computer - POS System

A comprehensive Point of Sale (POS) system built with Laravel 11 and MySQL, designed for single-shop retail operations with role-based access control.

---

## 📋 Project Overview

**MrCherry Computer POS System** is a full-featured inventory and sales management application designed for retail businesses. It supports multiple user roles (Admin, Manager, Cashier) with granular permissions, providing a complete solution for product management, sales processing, returns, expenses, and analytics.

### Key Features

✅ **Inventory Management** - Complete product and stock control  
✅ **Point of Sale** - Fast and intuitive billing interface  
✅ **Return Management** - Process returns and refunds  
✅ **Expense Tracking** - Monitor business expenses  
✅ **Customer Management** - Track customer purchases and credit  
✅ **User Management** - Role-based access control  
✅ **Reports & Analytics** - Comprehensive business insights  
✅ **Invoice Generation** - PDF bills and thermal receipts  
✅ **Low Stock Alerts** - Automated inventory monitoring  

---

## 🚀 Technology Stack

- **Backend Framework:** Laravel 11
- **Database:** MySQL 8.0+
- **Frontend:** Blade Templates + Alpine.js/Vue.js
- **CSS Framework:** Tailwind CSS
- **Authentication:** Laravel Breeze
- **Permissions:** Spatie Laravel Permission
- **PDF Generation:** DomPDF
- **Excel Export:** Laravel Excel
- **PHP Version:** 8.2+

---

## 📚 Documentation

This project includes comprehensive documentation to guide development:

| Document | Description |
|----------|-------------|
| [**WORKFLOW.md**](WORKFLOW.md) | Complete development workflow with 10 phases |
| [**FEATURE_GROUPING.md**](FEATURE_GROUPING.md) | Features grouped by similarity and dependencies |
| [**DATABASE_SCHEMA.md**](DATABASE_SCHEMA.md) | Detailed database structure with all tables |
| [**IMPLEMENTATION_GUIDE.md**](IMPLEMENTATION_GUIDE.md) | Step-by-step implementation instructions |
| [**RBAC_MATRIX.md**](RBAC_MATRIX.md) | Complete role-based access control matrix |

---

## ⚡ Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Node.js 18+ and npm

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd MrCherryComputer
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pos_system
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Create storage link**
   ```bash
   php artisan storage:link
   ```

7. **Start development servers**
   ```bash
   # Terminal 1
   php artisan serve
   
   # Terminal 2
   npm run dev
   ```

8. **Access the application**
   ```
   URL: http://localhost:8000
   ```

---

## 👥 User Roles & Default Credentials

Once seeded, you can login with:

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| **Admin** | admin@pos.com | password | Full system access |
| **Manager** | manager@pos.com | password | Most features except user management |
| **Cashier** | cashier@pos.com | password | POS and limited reports |

---

## 📦 Core Modules

### 1. Inventory Management
- Product CRUD operations
- Stock management with audit logs
- Low stock alerts
- Category and unit management

### 2. Point of Sale
- Fast product search and selection
- Shopping cart with discounts
- Multiple payment methods
- Invoice generation

### 3. Sales Management
- Complete sales history
- Edit/update sales (with permissions)
- Payment tracking
- Customer-linked transactions

### 4. Return Management
- Process product returns
- Automatic stock restoration
- Refund processing
- Return history

### 5. Customer Management
- Customer database
- Purchase history
- Credit/debit tracking
- Loyalty points (optional)

### 6. Expense Management
- Expense categorization
- Receipt uploads
- Approval workflow
- Daily/monthly summaries

### 7. Reports & Analytics
- Sales reports (daily, monthly, by user)
- Inventory reports
- Profit & loss statements
- Export to PDF/Excel

### 8. User Management
- Create users with roles
- Assign permissions
- Activity logging
- Role-based dashboards

---

## 🔐 Role-Based Access Control

The system implements granular permissions across three roles:

| Feature Area | Admin | Manager | Cashier |
|--------------|-------|---------|---------|
| User Management | ✓ | Limited | ✗ |
| Product Management | ✓ | ✓ | View Only |
| POS/Sales | ✓ | ✓ | ✓ |
| Returns | ✓ | ✓ | Limited |
| Expenses | ✓ | ✓ | ✗ |
| Reports | ✓ | ✓ | Own Sales |

See [RBAC_MATRIX.md](RBAC_MATRIX.md) for complete permission details.

---

## 📊 Database Structure

### Core Tables
- `users`, `roles`, `permissions`
- `categories`, `units`
- `products`, `stocks`, `stock_logs`
- `customers`
- `sales`, `sale_items`, `payments`
- `returns`, `return_items`
- `expenses`, `expense_categories`

See [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) for detailed schema.

---

## 🛠️ Development Phases

The project is divided into 10 development phases:

1. **Phase 1:** Foundation & Core Setup (1-2 days)
2. **Phase 2:** Master Data Management (2-3 days)
3. **Phase 3:** Inventory Management System (3-4 days)
4. **Phase 4:** Customer Management (1-2 days)
5. **Phase 5:** Point of Sale System (4-5 days)
6. **Phase 6:** Manual Billing System (1 day)
7. **Phase 7:** Return Management (2 days)
8. **Phase 8:** Expense Management (2 days)
9. **Phase 9:** Reports & Analytics (3-4 days)
10. **Phase 10:** Polish & Optimization (2-3 days)

**Total Estimated Time:** 20-25 working days

See [WORKFLOW.md](WORKFLOW.md) for detailed breakdown.

---

## 🎯 Development Progress

Track your progress using the checklists in each documentation file:

- [ ] Authentication & Authorization Setup
- [ ] Master Data (Categories, Units, Users)
- [ ] Product & Stock Management
- [ ] Customer Management
- [ ] POS System
- [ ] Invoice Generation & Printing
- [ ] Return Management
- [ ] Expense Management
- [ ] Reports & Analytics
- [ ] Dashboard & UI Polish

---

## 📝 Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Fresh start with seed data
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Generate IDE helpers
php artisan ide-helper:generate

# Code formatting
./vendor/bin/pint

# Create model with all resources
php artisan make:model ModelName -a
```

---

## 📦 Required Packages

### Composer Packages
```bash
composer require laravel/breeze --dev
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
composer require spatie/laravel-activitylog  # Optional
```

### NPM Packages
```bash
npm install alpinejs  # or vue
npm install chart.js  # for reports
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter SaleTest

# Generate coverage report
php artisan test --coverage
```

---

## 🚀 Deployment

### Pre-deployment Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Run migrations on production
- [ ] Seed initial data (roles, permissions)
- [ ] Set up cron jobs for scheduled tasks
- [ ] Configure file permissions (storage/, bootstrap/cache/)
- [ ] Set up SSL certificate
- [ ] Configure backups
- [ ] Test all critical features

### Production Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 🔒 Security Features

- CSRF protection on all forms
- XSS prevention through Blade escaping
- SQL injection prevention via Eloquent ORM
- Role-based authorization
- Input validation on all requests
- Secure file uploads
- Activity logging for audit trails

---

## 📈 Performance Optimization

- Database query optimization with eager loading
- Redis caching for frequently accessed data
- Database indexing on key columns
- Image optimization and lazy loading
- Asset minification and bundling

---

## 🤝 Contributing

This is a private project. For internal development:

1. Create a feature branch
2. Make your changes
3. Write/update tests
4. Submit a pull request
5. Get code review approval

---

## 📞 Support

For issues or questions:
- Check documentation files first
- Review implementation guide for common problems
- Contact the development team

---

## 📅 Version History

- **v1.0.0** (Planned) - Initial release with core features
  - Inventory management
  - POS system
  - Basic reporting

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 🙏 Acknowledgments

Built with:
- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - CSS Framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript
- [Spatie Packages](https://spatie.be) - Laravel Permission & Activity Log

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Spatie Permission Docs](https://spatie.be/docs/laravel-permission)

---

**Built with ❤️ for MrCherry Computer**


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
