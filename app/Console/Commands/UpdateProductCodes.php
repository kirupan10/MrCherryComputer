<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateProductCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all product codes to PRD format (PRD0001, PRD0002, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating product codes...');
        
        $products = Product::orderBy('id')->get();
        $count = 0;
        
        foreach ($products as $index => $product) {
            $newCode = 'PRD' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            
            $product->code = $newCode;
            $product->save();
            
            $this->line("Updated Product ID {$product->id}: {$product->name} -> {$newCode}");
            $count++;
        }
        
        $this->info("\n✓ Successfully updated {$count} product codes!");
        
        return 0;
    }
}
