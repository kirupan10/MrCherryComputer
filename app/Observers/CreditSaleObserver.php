<?php

namespace App\Observers;

use App\Models\CreditSale;
use Illuminate\Support\Facades\Log;

class CreditSaleObserver
{
    /**
     * Handle the CreditSale "updated" event.
     * Sync changes to the related Order if it exists.
     */
    public function updated(CreditSale $creditSale): void
    {
        // Check if this credit sale has a related order
        if ($creditSale->order) {
            try {
                $order = $creditSale->order;

                // Only sync if the order total differs from credit sale total
                if ($order->total != $creditSale->total_amount) {
                    // Update order total to match credit sale
                    $order->total = $creditSale->total_amount;
                }

                // Sync payment amounts (pay = paid_amount, due = due_amount)
                $newPay = $creditSale->paid_amount;
                $newDue = $creditSale->due_amount;

                if ($order->pay != $newPay || $order->due != $newDue) {
                    $order->pay = $newPay;
                    $order->due = $newDue;

                    // Save without triggering the OrderObserver to avoid infinite loop
                    $order->saveQuietly();

                    Log::info('Order synced after credit sale update', [
                        'credit_sale_id' => $creditSale->id,
                        'order_id' => $order->id,
                        'paid_amount' => $newPay,
                        'due_amount' => $newDue
                    ]);
                }

                // Sync customer if changed
                if ($order->customer_id != $creditSale->customer_id) {
                    $order->customer_id = $creditSale->customer_id;
                    $order->saveQuietly();
                }

            } catch (\Exception $e) {
                Log::error('Error syncing order from credit sale update', [
                    'credit_sale_id' => $creditSale->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
