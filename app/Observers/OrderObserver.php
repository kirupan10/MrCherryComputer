<?php

namespace App\Observers;

use App\Models\Order;
use App\Enums\CreditStatus;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * Sync changes to the related CreditSale if it exists.
     */
    public function updated(Order $order): void
    {
        // Check if this order has a credit sale
        if ($order->creditSale && $order->payment_type === 'Credit Sales') {
            try {
                $creditSale = $order->creditSale;

                // Calculate the difference in total amount
                $oldTotal = $creditSale->total_amount;
                $newTotal = $order->total;

                if ($oldTotal != $newTotal) {
                    // Update credit sale amounts
                    $difference = $newTotal - $oldTotal;

                    // Update total amount
                    $creditSale->total_amount = $newTotal;

                    // Update due amount (add/subtract the difference)
                    $creditSale->due_amount = max(0, $creditSale->due_amount + $difference);

                    // Update status based on amounts
                    if ($creditSale->due_amount <= 0) {
                        $creditSale->status = CreditStatus::PAID;
                        $creditSale->due_amount = 0;
                    } elseif ($creditSale->paid_amount > 0) {
                        $creditSale->status = CreditStatus::PARTIAL;
                    } else {
                        $creditSale->status = CreditStatus::PENDING;
                    }

                    // Save credit sale without triggering observer
                    $creditSale->saveQuietly();

                    Log::info('Credit sale synced after order update', [
                        'order_id' => $order->id,
                        'credit_sale_id' => $creditSale->id,
                        'old_total' => $oldTotal,
                        'new_total' => $newTotal,
                        'new_due_amount' => $creditSale->due_amount
                    ]);
                }

                // Always sync order's pay/due to match credit sale
                // This ensures consistency even when only basic info is updated
                if ($order->pay != $creditSale->paid_amount || $order->due != $creditSale->due_amount) {
                    $order->pay = $creditSale->paid_amount;
                    $order->due = $creditSale->due_amount;
                    $order->saveQuietly();

                    Log::info('Order pay/due synced from credit sale', [
                        'order_id' => $order->id,
                        'order_pay' => $order->pay,
                        'order_due' => $order->due
                    ]);
                }

                // Sync other relevant fields
                if ($order->customer_id != $creditSale->customer_id) {
                    $creditSale->customer_id = $order->customer_id;
                    $creditSale->saveQuietly();
                }

            } catch (\Exception $e) {
                Log::error('Error syncing credit sale from order update', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
