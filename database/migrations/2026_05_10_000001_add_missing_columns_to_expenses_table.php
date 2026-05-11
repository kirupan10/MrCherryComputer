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
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'expense_number')) {
                $table->string('expense_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('expenses', 'status')) {
                $table->string('status')->default('pending')->after('expense_date');
            }
            if (!Schema::hasColumn('expenses', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            }
            if (!Schema::hasColumn('expenses', 'receipt_image')) {
                $table->string('receipt_image')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['expense_number', 'status', 'approved_by', 'receipt_image']);
        });
    }
};
