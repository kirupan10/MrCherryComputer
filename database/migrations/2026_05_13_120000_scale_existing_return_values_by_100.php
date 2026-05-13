<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Scale legacy return money values by 100 and resync return header totals.
     */
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('return_sale_items')->update([
                'unitcost' => DB::raw('unitcost * 100'),
                'total' => DB::raw('total * 100'),
            ]);

            DB::table('return_sales')->update([
                'sub_total' => DB::raw('sub_total * 100'),
                'total' => DB::raw('total * 100'),
            ]);

            $itemSums = DB::table('return_sale_items')
                ->select('return_sale_id', DB::raw('SUM(total) as item_total'))
                ->groupBy('return_sale_id')
                ->get();

            foreach ($itemSums as $row) {
                DB::table('return_sales')
                    ->where('id', $row->return_sale_id)
                    ->update([
                        'sub_total' => $row->item_total,
                        'total' => $row->item_total,
                    ]);
            }
        });
    }

    /**
     * Best-effort rollback: divide by 100 and resync totals from items.
     */
    public function down(): void
    {
        DB::transaction(function () {
            DB::table('return_sale_items')->update([
                'unitcost' => DB::raw('unitcost / 100'),
                'total' => DB::raw('total / 100'),
            ]);

            DB::table('return_sales')->update([
                'sub_total' => DB::raw('sub_total / 100'),
                'total' => DB::raw('total / 100'),
            ]);

            $itemSums = DB::table('return_sale_items')
                ->select('return_sale_id', DB::raw('SUM(total) as item_total'))
                ->groupBy('return_sale_id')
                ->get();

            foreach ($itemSums as $row) {
                DB::table('return_sales')
                    ->where('id', $row->return_sale_id)
                    ->update([
                        'sub_total' => $row->item_total,
                        'total' => $row->item_total,
                    ]);
            }
        });
    }
};
