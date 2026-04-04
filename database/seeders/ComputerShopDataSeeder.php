<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComputerShopDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@pos.com')->first() ?? User::first();
        $cashier = User::where('email', 'cashier@pos.com')->first() ?? $admin;

        if (!$admin) {
            return;
        }

        $piecesUnit = Unit::firstOrCreate(
            ['short_name' => 'pcs'],
            ['name' => 'Pieces', 'is_active' => true]
        );

        $categories = [
            ['name' => 'Processors'],
            ['name' => 'Motherboards'],
            ['name' => 'Graphics Cards'],
            ['name' => 'Memory (RAM)'],
            ['name' => 'Storage Devices'],
            ['name' => 'Power Supplies'],
            ['name' => 'Cases'],
            ['name' => 'Cooling'],
            ['name' => 'Monitors'],
            ['name' => 'Peripherals'],
        ];

        $categoryMap = [];
        foreach ($categories as $categoryData) {
            $slug = Str::slug($categoryData['name']);
            $category = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['name'] . ' inventory category',
                    'is_active' => true,
                ]
            );
            $categoryMap[$categoryData['name']] = $category;
        }

        $products = [
            ['name' => 'Intel Core i5-12400F', 'sku' => 'CPU-I5-12400F', 'category' => 'Processors', 'cost' => 16500, 'price' => 18999, 'stock' => 18, 'tax' => 18],
            ['name' => 'AMD Ryzen 5 5600', 'sku' => 'CPU-R5-5600', 'category' => 'Processors', 'cost' => 14800, 'price' => 17499, 'stock' => 15, 'tax' => 18],
            ['name' => 'MSI B550M PRO-VDH', 'sku' => 'MB-MSI-B550M', 'category' => 'Motherboards', 'cost' => 9800, 'price' => 11499, 'stock' => 10, 'tax' => 18],
            ['name' => 'ASUS PRIME H610M-E', 'sku' => 'MB-ASUS-H610M', 'category' => 'Motherboards', 'cost' => 7600, 'price' => 8999, 'stock' => 12, 'tax' => 18],
            ['name' => 'NVIDIA RTX 4060 8GB', 'sku' => 'GPU-RTX-4060', 'category' => 'Graphics Cards', 'cost' => 28400, 'price' => 32999, 'stock' => 7, 'tax' => 18],
            ['name' => 'AMD Radeon RX 7600', 'sku' => 'GPU-RX-7600', 'category' => 'Graphics Cards', 'cost' => 23900, 'price' => 27999, 'stock' => 6, 'tax' => 18],
            ['name' => 'Corsair Vengeance 16GB DDR4', 'sku' => 'RAM-COR-16-3200', 'category' => 'Memory (RAM)', 'cost' => 3350, 'price' => 4299, 'stock' => 24, 'tax' => 18],
            ['name' => 'G.Skill Ripjaws 32GB DDR4', 'sku' => 'RAM-GSK-32-3600', 'category' => 'Memory (RAM)', 'cost' => 6450, 'price' => 7899, 'stock' => 14, 'tax' => 18],
            ['name' => 'WD Blue SN570 1TB NVMe SSD', 'sku' => 'SSD-WD-1TB-NVME', 'category' => 'Storage Devices', 'cost' => 4300, 'price' => 5299, 'stock' => 20, 'tax' => 18],
            ['name' => 'Seagate Barracuda 2TB HDD', 'sku' => 'HDD-SEA-2TB', 'category' => 'Storage Devices', 'cost' => 3600, 'price' => 4499, 'stock' => 16, 'tax' => 18],
            ['name' => 'Cooler Master 650W Bronze PSU', 'sku' => 'PSU-CM-650B', 'category' => 'Power Supplies', 'cost' => 4100, 'price' => 5199, 'stock' => 13, 'tax' => 18],
            ['name' => 'Corsair CX750 750W PSU', 'sku' => 'PSU-COR-750', 'category' => 'Power Supplies', 'cost' => 6200, 'price' => 7399, 'stock' => 9, 'tax' => 18],
            ['name' => 'Ant Esports ICE-130AG Cabinet', 'sku' => 'CASE-ANT-130AG', 'category' => 'Cases', 'cost' => 2600, 'price' => 3499, 'stock' => 11, 'tax' => 18],
            ['name' => 'DeepCool AG400 CPU Cooler', 'sku' => 'COOL-DC-AG400', 'category' => 'Cooling', 'cost' => 1700, 'price' => 2299, 'stock' => 17, 'tax' => 18],
            ['name' => 'LG 24 inch IPS Monitor', 'sku' => 'MON-LG-24IPS', 'category' => 'Monitors', 'cost' => 9400, 'price' => 11299, 'stock' => 8, 'tax' => 18],
            ['name' => 'Logitech G102 Mouse', 'sku' => 'PER-LOG-G102', 'category' => 'Peripherals', 'cost' => 1050, 'price' => 1499, 'stock' => 30, 'tax' => 18],
            ['name' => 'Redragon K552 Keyboard', 'sku' => 'PER-RED-K552', 'category' => 'Peripherals', 'cost' => 2050, 'price' => 2899, 'stock' => 19, 'tax' => 18],
        ];

        $productMap = [];
        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'barcode' => 'BC-' . $productData['sku'],
                    'category_id' => $categoryMap[$productData['category']]->id,
                    'unit_id' => $piecesUnit->id,
                    'purchase_price' => $productData['cost'],
                    'selling_price' => $productData['price'],
                    'mrp' => $productData['price'] + 500,
                    'tax_percentage' => $productData['tax'],
                    'low_stock_alert' => 5,
                    'is_active' => true,
                    'created_by' => $admin->id,
                ]
            );

            Stock::updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $productData['stock'], 'last_updated_by' => $admin->id]
            );

            if (!StockLog::where('product_id', $product->id)->where('reference_type', 'seed')->exists()) {
                StockLog::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $productData['stock'],
                    'previous_quantity' => 0,
                    'current_quantity' => $productData['stock'],
                    'reference_type' => 'seed',
                    'reference_id' => 1,
                    'notes' => 'Initial seeded stock',
                    'created_by' => $admin->id,
                ]);
            }

            $productMap[$productData['sku']] = $product;
        }

        $customers = [
            ['name' => 'Rahul Mehta', 'phone' => '0777100001', 'email' => 'rahul.mehta@example.test', 'city' => 'Colombo', 'state' => 'Western'],
            ['name' => 'Nimali Perera', 'phone' => '0777100002', 'email' => 'nimali.perera@example.test', 'city' => 'Kandy', 'state' => 'Central'],
            ['name' => 'TechZone Solutions', 'phone' => '0777100003', 'email' => 'accounts@techzone.example.test', 'city' => 'Galle', 'state' => 'Southern'],
            ['name' => 'Dilan Fernando', 'phone' => '0777100004', 'email' => 'dilan.fernando@example.test', 'city' => 'Negombo', 'state' => 'Western'],
            ['name' => 'Sahan Computers', 'phone' => '0777100005', 'email' => 'sales@sahancomputers.example.test', 'city' => 'Kurunegala', 'state' => 'North Western'],
            ['name' => 'Ishara Silva', 'phone' => '0777100006', 'email' => 'ishara.silva@example.test', 'city' => 'Matara', 'state' => 'Southern'],
        ];

        $customerMap = [];
        foreach ($customers as $customerData) {
            $customer = Customer::updateOrCreate(
                ['phone' => $customerData['phone']],
                [
                    'name' => $customerData['name'],
                    'email' => $customerData['email'],
                    'address' => $customerData['city'] . ', ' . $customerData['state'],
                    'city' => $customerData['city'],
                    'state' => $customerData['state'],
                    'credit_limit' => 50000,
                    'current_balance' => 0,
                    'loyalty_points' => 0,
                    'is_active' => true,
                ]
            );

            $customerMap[$customerData['name']] = $customer;
        }

        $sales = [
            [
                'invoice' => 'INV-DEMO-0001',
                'customer' => 'Rahul Mehta',
                'date' => now()->subDays(8),
                'items' => [
                    ['sku' => 'CPU-I5-12400F', 'qty' => 1],
                    ['sku' => 'MB-ASUS-H610M', 'qty' => 1],
                    ['sku' => 'RAM-COR-16-3200', 'qty' => 2],
                ],
                'paid' => 'full',
                'method' => 'card',
            ],
            [
                'invoice' => 'INV-DEMO-0002',
                'customer' => 'Nimali Perera',
                'date' => now()->subDays(6),
                'items' => [
                    ['sku' => 'SSD-WD-1TB-NVME', 'qty' => 1],
                    ['sku' => 'PER-LOG-G102', 'qty' => 1],
                ],
                'paid' => 'partial',
                'method' => 'cash',
            ],
            [
                'invoice' => 'INV-DEMO-0003',
                'customer' => 'TechZone Solutions',
                'date' => now()->subDays(3),
                'items' => [
                    ['sku' => 'GPU-RTX-4060', 'qty' => 1],
                    ['sku' => 'PSU-COR-750', 'qty' => 1],
                    ['sku' => 'CASE-ANT-130AG', 'qty' => 1],
                ],
                'paid' => 'unpaid',
                'method' => 'bank_transfer',
            ],
        ];

        foreach ($sales as $saleData) {
            if (Sale::where('invoice_number', $saleData['invoice'])->exists()) {
                continue;
            }

            $subtotal = 0;
            $taxAmount = 0;
            foreach ($saleData['items'] as $item) {
                $product = $productMap[$item['sku']];
                $lineSubtotal = $product->selling_price * $item['qty'];
                $lineTax = $lineSubtotal * ((float) $product->tax_percentage / 100);
                $subtotal += $lineSubtotal;
                $taxAmount += $lineTax;
            }

            $totalAmount = round($subtotal + $taxAmount, 2);
            $paidAmount = match ($saleData['paid']) {
                'full' => $totalAmount,
                'partial' => round($totalAmount * 0.6, 2),
                default => 0,
            };

            $paymentStatus = $paidAmount <= 0 ? 'unpaid' : ($paidAmount < $totalAmount ? 'partial' : 'paid');

            $sale = Sale::create([
                'invoice_number' => $saleData['invoice'],
                'customer_id' => $customerMap[$saleData['customer']]->id,
                'sale_date' => $saleData['date'],
                'subtotal' => round($subtotal, 2),
                'discount_type' => null,
                'discount_value' => 0,
                'discount_amount' => 0,
                'tax_amount' => round($taxAmount, 2),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => round($totalAmount - $paidAmount, 2),
                'payment_status' => $paymentStatus,
                'payment_method' => $saleData['method'],
                'status' => 'completed',
                'notes' => 'Demo seeded sale',
                'created_by' => $cashier->id,
            ]);

            foreach ($saleData['items'] as $item) {
                $product = $productMap[$item['sku']];
                $lineSubtotal = $product->selling_price * $item['qty'];
                $lineTax = $lineSubtotal * ((float) $product->tax_percentage / 100);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->selling_price,
                    'tax_percentage' => $product->tax_percentage,
                    'tax_amount' => round($lineTax, 2),
                    'discount_amount' => 0,
                    'subtotal' => round($lineSubtotal, 2),
                    'total' => round($lineSubtotal + $lineTax, 2),
                ]);
            }

            if ($paidAmount > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_date' => $saleData['date'],
                    'amount' => $paidAmount,
                    'payment_method' => $saleData['method'],
                    'notes' => 'Demo seeded payment',
                    'created_by' => $cashier->id,
                ]);
            }
        }

        $expenseCategories = [
            'Rent',
            'Utilities',
            'Marketing',
            'Transportation',
            'Maintenance',
        ];

        foreach ($expenseCategories as $expenseCategoryName) {
            ExpenseCategory::firstOrCreate(
                ['name' => $expenseCategoryName],
                ['description' => $expenseCategoryName . ' expenses', 'is_active' => true]
            );
        }

        $expenses = [
            ['number' => 'EXP-DEMO-0001', 'category' => 'Rent', 'amount' => 85000, 'method' => 'bank_transfer', 'status' => 'approved', 'daysAgo' => 10, 'desc' => 'Monthly shop rent'],
            ['number' => 'EXP-DEMO-0002', 'category' => 'Utilities', 'amount' => 12450, 'method' => 'cash', 'status' => 'approved', 'daysAgo' => 6, 'desc' => 'Electricity and internet'],
            ['number' => 'EXP-DEMO-0003', 'category' => 'Marketing', 'amount' => 7800, 'method' => 'card', 'status' => 'pending', 'daysAgo' => 2, 'desc' => 'Social media ad campaign'],
        ];

        foreach ($expenses as $expenseData) {
            if (Expense::where('expense_number', $expenseData['number'])->exists()) {
                continue;
            }

            $category = ExpenseCategory::where('name', $expenseData['category'])->first();
            if (!$category) {
                continue;
            }

            Expense::create([
                'expense_number' => $expenseData['number'],
                'expense_category_id' => $category->id,
                'expense_date' => now()->subDays($expenseData['daysAgo'])->toDateString(),
                'amount' => $expenseData['amount'],
                'payment_method' => $expenseData['method'],
                'reference_number' => 'REF-' . Str::after($expenseData['number'], 'EXP-'),
                'description' => $expenseData['desc'],
                'status' => $expenseData['status'],
                'created_by' => $admin->id,
                'approved_by' => $expenseData['status'] === 'approved' ? $admin->id : null,
            ]);
        }
    }
}
