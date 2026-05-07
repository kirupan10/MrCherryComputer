<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        mt_srand(20260506);
        $now = now();

        $shop = DB::table('shops')->orderBy('id')->first();
        if (!$shop) {
            $ownerId = DB::table('users')->insertGetId([
                'name' => 'Kasun Perera',
                'username' => 'tech_owner',
                'email' => 'tech@nexora.com',
                'password' => Hash::make('Password@123'),
                'role' => 'shop_owner',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $shopId = DB::table('shops')->insertGetId([
                'name' => 'TechPro Computer Shop',
                'shop_type' => 'tech_shop',
                'address' => 'No. 128, Galle Road, Wellawatte, Colombo 06',
                'phone' => '+94 77 345 6789',
                'email' => 'tech@nexora.com',
                'owner_id' => $ownerId,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('users')->where('id', $ownerId)->update([
                'shop_id' => $shopId,
            ]);
        } else {
            $shopId = $shop->id;
        }

        $userIds = DB::table('users')
            ->where('shop_id', $shopId)
            ->pluck('id')
            ->all();

        if (empty($userIds)) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => 'Kasun Perera',
                'username' => 'tech_owner',
                'email' => 'tech@nexora.com',
                'password' => Hash::make('Password@123'),
                'role' => 'shop_owner',
                'shop_id' => $shopId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $orderIds = DB::table('orders')->where('shop_id', $shopId)->pluck('id')->all();
        if (!empty($orderIds)) {
            DB::table('order_details')->whereIn('order_id', $orderIds)->delete();
        }

        DB::table('orders')->where('shop_id', $shopId)->delete();
        DB::table('credit_purchases')->where('shop_id', $shopId)->delete();
        DB::table('expenses')->where('shop_id', $shopId)->delete();
        DB::table('products')->where('shop_id', $shopId)->delete();
        DB::table('customers')->where('shop_id', $shopId)->delete();
        DB::table('categories')->where('shop_id', $shopId)->delete();

        $categoryNames = [
            'Motherboard', 'Processor', 'Memory', 'Storage', 'Power Supply',
            'Graphics Card', 'Monitor', 'Laptop', 'Keyboard', 'Mouse',
            'Headset', 'Speakers', 'Cooling', 'Cables', 'Adapters',
            'UPS', 'Printers', 'Networking', 'Accessories', 'Software',
        ];

        foreach ($categoryNames as $name) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($name), 'shop_id' => $shopId],
                [
                    'name' => $name,
                    'created_by' => $userIds[0],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $categoryIds = DB::table('categories')
            ->where('shop_id', $shopId)
            ->pluck('id')
            ->all();

        $unitId = DB::table('units')->whereNull('shop_id')->value('id');
        if (!$unitId) {
            $unitId = DB::table('units')->insertGetId([
                'name' => 'Piece',
                'slug' => 'piece',
                'short_code' => 'pc',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $brands = ['Acer', 'Asus', 'Dell', 'HP', 'Lenovo', 'MSI', 'Gigabyte', 'Samsung', 'Seagate', 'Logitech'];
        $productTypes = ['Laptop', 'Monitor', 'SSD', 'HDD', 'RAM', 'GPU', 'Motherboard', 'Keyboard', 'Mouse', 'Router'];

        $products = [];
        for ($i = 1; $i <= 200; $i++) {
            $brand = $brands[$i % count($brands)];
            $type = $productTypes[$i % count($productTypes)];
            $name = $brand . ' ' . $type . ' ' . (100 + $i);
            $buying = random_int(5000, 250000);
            $markup = random_int(10, 35) / 100;
            $selling = round($buying * (1 + $markup), 2);

            $products[] = [
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $i,
                'code' => sprintf('PRD%05d', $i),
                'barcode' => sprintf('%012d', 100000000000 + $i),
                'quantity' => random_int(5, 120),
                'buying_price' => $buying,
                'selling_price' => $selling,
                'quantity_alert' => random_int(1, 10),
                'category_id' => $categoryIds[$i % count($categoryIds)],
                'unit_id' => $unitId,
                'shop_id' => $shopId,
                'created_by' => $userIds[$i % count($userIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('products')->insert($products);

        $customerNames = [
            'Kasun Perera', 'Nimal Silva', 'Saman Jayasinghe', 'Tharindu Fernando', 'Chathura Wijesinghe',
            'Isuru Bandara', 'Ruwan Kumara', 'Dinesh Perera', 'Sajith Liyanage', 'Harsha Gunawardena',
            'Madhuri Perera', 'Ishani Silva', 'Nethmi Jayasuriya', 'Sewmi Fernando', 'Lakshi Perera',
            'Kavindi Silva', 'Dilini Jayasinghe', 'Shanika Fernando', 'Thilini Perera', 'Sanduni Kumari',
        ];

        $cities = ['Colombo', 'Kandy', 'Galle', 'Kurunegala', 'Negombo', 'Matara', 'Jaffna', 'Batticaloa', 'Anuradhapura', 'Ratnapura'];

        $customers = [];
        for ($i = 1; $i <= 50; $i++) {
            $name = $customerNames[$i % count($customerNames)] . ' ' . $i;
            $city = $cities[$i % count($cities)];
            $phone = '+94 7' . random_int(0, 9) . ' ' . random_int(100, 999) . ' ' . random_int(1000, 9999);

            $customers[] = [
                'name' => $name,
                'email' => 'customer' . $i . '@example.lk',
                'phone' => $phone,
                'address' => 'No. ' . random_int(10, 299) . ', Main Street, ' . $city,
                'shop_id' => $shopId,
                'created_by' => $userIds[$i % count($userIds)],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('customers')->insert($customers);

        $productRows = DB::table('products')
            ->where('shop_id', $shopId)
            ->get(['id', 'name', 'selling_price', 'buying_price'])
            ->all();

        $customerRows = DB::table('customers')
            ->where('shop_id', $shopId)
            ->pluck('id')
            ->all();

        for ($i = 1; $i <= 120; $i++) {
            $orderDate = Carbon::today()->subDays(random_int(0, 30));
            $itemsCount = random_int(1, 4);
            $orderDetails = [];
            $subTotal = 0;
            $totalProducts = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $product = $productRows[array_rand($productRows)];
                $qty = random_int(1, 3);
                $unitCost = (float) $product->selling_price;
                $lineTotal = $unitCost * $qty;

                $orderDetails[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unitcost' => $unitCost,
                    'buying_price' => $product->buying_price,
                    'total' => $lineTotal,
                    'created_at' => $orderDate->copy()->setTime(10, 0, 0),
                    'updated_at' => $orderDate->copy()->setTime(10, 0, 0),
                ];

                $subTotal += $lineTotal;
                $totalProducts += $qty;
            }

            $discount = random_int(0, 4) === 0 ? round($subTotal * (random_int(2, 8) / 100), 2) : 0;
            $serviceCharges = 0;
            $total = $subTotal - $discount + $serviceCharges;

            $orderId = DB::table('orders')->insertGetId([
                'customer_id' => $customerRows[array_rand($customerRows)],
                'order_date' => $orderDate->format('Y-m-d'),
                'total_products' => $totalProducts,
                'sub_total' => $subTotal,
                'discount_amount' => $discount,
                'service_charges' => $serviceCharges,
                'total' => $total,
                'invoice_no' => 'INV' . $now->format('Ym') . '-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'payment_type' => random_int(0, 1) === 0 ? 'cash' : 'card',
                'order_type' => 'dine_in',
                'pay' => $total,
                'due' => 0,
                'shop_id' => $shopId,
                'created_by' => $userIds[$i % count($userIds)],
                'created_at' => $orderDate->copy()->setTime(12, 0, 0),
                'updated_at' => $orderDate->copy()->setTime(12, 0, 0),
            ]);

            foreach ($orderDetails as &$detail) {
                $detail['order_id'] = $orderId;
            }
            unset($detail);

            DB::table('order_details')->insert($orderDetails);
        }

        $vendorNames = ['Redline Distribution', 'Game Street', 'Nanotek', 'Ugreen Sri Lanka', 'Epiq Distribution'];
        for ($i = 1; $i <= 60; $i++) {
            $purchaseDate = Carbon::today()->subDays(random_int(0, 30));
            $total = random_int(5000, 250000);
            $paid = random_int(0, 1) === 1 ? $total : random_int(0, $total);
            $due = $total - $paid;
            $status = $due === 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending');

            DB::table('credit_purchases')->insert([
                'shop_id' => $shopId,
                'created_by' => $userIds[$i % count($userIds)],
                'vendor_name' => $vendorNames[$i % count($vendorNames)],
                'vendor_phone' => '+94 7' . random_int(0, 9) . ' ' . random_int(100, 999) . ' ' . random_int(1000, 9999),
                'vendor_address' => 'Colombo',
                'total_amount' => $total,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'purchase_date' => $purchaseDate->format('Y-m-d'),
                'due_date' => $purchaseDate->copy()->addDays(30)->format('Y-m-d'),
                'status' => $status,
                'purchase_type' => $due === 0 ? 'cash' : 'credit',
                'reference_number' => 'PO-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'created_at' => $purchaseDate->copy()->setTime(14, 0, 0),
                'updated_at' => $purchaseDate->copy()->setTime(14, 0, 0),
            ]);
        }

        $expenseTypes = ['Transport', 'Food', 'Supplies', 'Utilities', 'Rent', 'Marketing'];
        for ($i = 1; $i <= 30; $i++) {
            $expenseDate = Carbon::today()->subDays(random_int(0, 30));
            $type = $expenseTypes[$i % count($expenseTypes)];
            $note = $type . ' expense';

            DB::table('expenses')->insert([
                'type' => $type,
                'notes' => $note,
                'details' => json_encode(['note' => $note], JSON_UNESCAPED_SLASHES),
                'amount' => random_int(300, 25000),
                'expense_date' => $expenseDate->format('Y-m-d'),
                'shop_id' => $shopId,
                'created_by' => $userIds[$i % count($userIds)],
                'created_at' => $expenseDate->copy()->setTime(16, 0, 0),
                'updated_at' => $expenseDate->copy()->setTime(16, 0, 0),
            ]);
        }
    }
}
