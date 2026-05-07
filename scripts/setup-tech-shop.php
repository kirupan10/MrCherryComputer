<?php

/**
 * Setup Script: Configure Shops for Tech Shop Type
 *
 * This script updates existing shops to use the tech_shop type
 * and enables all tech shop features.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shop;
use App\Enums\ShopType;

echo "=================================================\n";
echo "Tech Shop Setup Script\n";
echo "=================================================\n\n";

// Get all shops
$shops = Shop::all();

if ($shops->isEmpty()) {
    echo "❌ No shops found in the database.\n";
    echo "Please create a shop first.\n";
    exit(1);
}

echo "Found " . $shops->count() . " shop(s):\n\n";

foreach ($shops as $shop) {
    echo "Shop ID: {$shop->id}\n";
    echo "Name: {$shop->name}\n";
    echo "Current Type: " . ($shop->shop_type ? $shop->shop_type->value : 'null') . "\n";
    echo "Current Features: " . ($shop->enabled_features ? json_encode($shop->enabled_features) : 'null') . "\n";
    echo str_repeat("-", 50) . "\n";
}

echo "\n";

// Ask for confirmation
echo "Do you want to update ALL shops to tech_shop type? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'yes') {
    echo "\n❌ Operation cancelled.\n";
    exit(0);
}

echo "\n";

// Get tech shop features from config
$techFeatures = config('shop-features.tech_shop');

if (empty($techFeatures)) {
    echo "❌ Tech shop features not found in config.\n";
    exit(1);
}

echo "Tech Shop Features to Enable:\n";
foreach ($techFeatures as $feature) {
    echo "  ✅ {$feature}\n";
}
echo "\n";

// Update all shops
$updated = 0;
foreach ($shops as $shop) {
    try {
        $shop->shop_type = ShopType::TECH_SHOP;
        $shop->enabled_features = $techFeatures;
        $shop->save();

        echo "✅ Updated Shop #{$shop->id}: {$shop->name}\n";
        $updated++;
    } catch (Exception $e) {
        echo "❌ Failed to update Shop #{$shop->id}: {$e->getMessage()}\n";
    }
}

echo "\n";
echo "=================================================\n";
echo "Summary: Updated {$updated} out of {$shops->count()} shop(s)\n";
echo "=================================================\n\n";

// Verify updates
echo "Verifying updates...\n\n";

$techShops = Shop::where('shop_type', ShopType::TECH_SHOP)->get();

foreach ($techShops as $shop) {
    echo "Shop #{$shop->id}: {$shop->name}\n";
    echo "  Type: {$shop->shop_type->value}\n";
    echo "  Features: " . count($shop->enabled_features) . " enabled\n";

    // Check if shop has required features
    $hasProducts = $shop->hasFeature('products');
    $hasSerials = $shop->hasFeature('serial_numbers');
    $hasWarranty = $shop->hasFeature('warranty');
    $hasRepairs = $shop->hasFeature('repairs');

    echo "  ✅ Products: " . ($hasProducts ? 'Enabled' : 'Disabled') . "\n";
    echo "  ✅ Serial Numbers: " . ($hasSerials ? 'Enabled' : 'Disabled') . "\n";
    echo "  ✅ Warranty: " . ($hasWarranty ? 'Enabled' : 'Disabled') . "\n";
    echo "  ✅ Repairs: " . ($hasRepairs ? 'Enabled' : 'Disabled') . "\n";
    echo "\n";
}

echo "=================================================\n";
echo "✅ Setup Complete!\n";
echo "=================================================\n\n";

echo "Next Steps:\n";
echo "1. Access tech products: /tech/products\n";
echo "2. Manage serial numbers: /tech/serial-numbers\n";
echo "3. Handle warranty claims: /tech/warranty\n";
echo "4. Manage repair jobs: /tech/repairs\n";
echo "\nSee TECH_SHOP_QUICK_START.md for detailed usage instructions.\n";
