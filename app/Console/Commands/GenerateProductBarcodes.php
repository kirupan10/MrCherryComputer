<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class GenerateProductBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-barcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate barcodes for all products based on their product codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating barcodes for all products...');

        $count = 0;

        // Process all products in chunks to avoid memory issues
        Product::chunk(100, function ($products) use (&$count) {
            foreach ($products as $product) {
                // Generate barcode from product code
                $newBarcode = Product::generateBarcode($product->shop_id, $product->code);

                // Update barcode
                $product->barcode = $newBarcode;
                $product->save();

                $count++;
                $this->line("Generated barcode for {$product->code}: {$newBarcode}");
            }
        });

        $this->info("Successfully generated barcodes for {$count} products!");

        return 0;
    }
}
