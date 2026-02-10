<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('sale_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->dateTime('return_date');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('refund_amount', 12, 2);
            $table->enum('refund_method', ['cash', 'card', 'store_credit'])->default('cash');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['return_number', 'sale_id', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
