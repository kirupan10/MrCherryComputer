<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dupes = \Illuminate\Support\Facades\DB::table('restaurant_menu_categories')
    ->select('shop_id', 'name', \Illuminate\Support\Facades\DB::raw('MIN(id) as keep_id'), \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
    ->groupBy('shop_id', 'name')
    ->having('cnt', '>', 1)
    ->get();

$deleted = 0;
foreach ($dupes as $d) {
    $deleted += \Illuminate\Support\Facades\DB::table('restaurant_menu_categories')
        ->where('shop_id', $d->shop_id)
        ->where('name', $d->name)
        ->where('id', '!=', $d->keep_id)
        ->delete();
}
echo "Deleted {$deleted} duplicate category rows.\n";
