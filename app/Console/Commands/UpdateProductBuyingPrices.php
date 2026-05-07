<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProductBuyingPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-buying-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update buying prices for products that don\'t have one set';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating product buying prices...');

        // Get products with null or 0 buying price
        $products = Product::whereNull('buying_price')
            ->orWhere('buying_price', '<=', 0)
            ->get();

        $count = 0;
        $bar = $this->output->createProgressBar($products->count());

        foreach ($products as $product) {
            $sellingPrice = $product->selling_price;

            if ($sellingPrice < 10000) {
                // If selling price is below 10,000, set buying price = selling price
                $buyingPrice = $sellingPrice;
            } else {
                // If selling price >= 10,000, set buying price = selling price - 2000
                $buyingPrice = $sellingPrice - 2000;
            }

            $product->update(['buying_price' => $buyingPrice]);
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Updated {$count} products with calculated buying prices.");

        return Command::SUCCESS;
    }
}
