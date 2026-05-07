<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetails;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Process payment for an order
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_data' => 'required|array',
            'order_data.customer_id' => 'required|exists:customers,id',
            'order_data.payment_type' => 'required|string',
            'order_data.cart_items' => 'required|array|min:1',
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $orderData = $request->order_data;
            $cartItems = $orderData['cart_items'];
            $paymentAmount = $request->payment_amount;

            // Calculate totals
            $subTotal = 0;
            $totalProducts = count($cartItems);

            foreach ($cartItems as $item) {
                $subTotal += $item['price'] * $item['quantity'];
            }

            $total = $subTotal; // No VAT

            // Validate payment amount matches total
            if (abs($paymentAmount - $total) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount does not match order total',
                    'required_amount' => $total,
                    'provided_amount' => $paymentAmount
                ], 400);
            }

            // Check product availability
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product with ID {$item['id']} not found"
                    ], 404);
                }

                if ($product->quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$product->name}. Available: {$product->quantity}, Requested: {$item['quantity']}"
                    ], 400);
                }
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . strtoupper(uniqid());

            // Ensure user is authenticated and get their shop
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must be authenticated to process payments'
                ], 401);
            }

            $shopId = $user->getActiveShop()?->id;
            if (!$shopId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must have an active shop to process payments'
                ], 400);
            }

            // Create order with explicit shop_id for multi-tenancy
            $order = Order::create([
                'customer_id' => $orderData['customer_id'],
                'order_date' => now()->format('Y-m-d'),
                'shop_id' => $shopId,
                'created_by' => $user->id,
                // order_status field has been removed - all orders are treated as completed
                'total_products' => $totalProducts,
                'sub_total' => $subTotal * 100, // Store in cents
                'total' => $total * 100, // No VAT - removed as per business requirements
                'invoice_no' => $invoiceNumber,
                'payment_type' => $orderData['payment_type'],
                'pay' => $paymentAmount * 100,
                'due' => 0, // No due amount for complete payment
            ]);

            // Create order details in bulk and adjust product quantities using stored procedure
            $orderDetails = [];
            $totalCost = 0; // For gift expenses - sum of buying prices

            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                $buyingPrice = $product->buying_price ?? 0;
                $itemCost = $buyingPrice * $item['quantity'];
                $totalCost += $itemCost;

                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unitcost' => (int) ($item['price'] * 100), // Store in cents
                    'buying_price' => (int) ($buyingPrice * 100), // Store buying price
                    'total' => (int) (($item['price'] * $item['quantity']) * 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($orderDetails)) {
                OrderDetails::insert($orderDetails);
                // Use StockService to adjust stock for the order
                $stockService = app(\App\Services\StockService::class);
                $stockService->adjustStockAfterOrder($order->id);
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $paymentAmount,
                'payment_method' => $orderData['payment_type'],
                'status' => 'completed',
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'processed_at' => now(),
            ]);

            // If gift payment, create expense record for gift sales
            if ($orderData['payment_type'] === 'Gift' && $totalCost > 0) {
                \App\Models\Expense::create([
                    'type' => 'gift sales expenses',
                    'amount' => $totalCost,
                    'expense_date' => $order->order_date,
                    'notes' => 'Gift given - Invoice #' . $order->invoice_no,
                    'details' => [
                        'order_id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'customer_id' => $orderData['customer_id'],
                        'cost_value' => $totalCost,
                    ],
                    'shop_id' => $shopId,
                    'created_by' => $user->id,
                ]);

                // Also log as a business transaction
                \App\Models\BusinessTransaction::create([
                    'shop_id' => $shopId,
                    'created_by' => $user->id,
                    'transaction_date' => $order->order_date,
                    'transaction_type' => 'expense',
                    'vendor_name' => 'Gift Sales',
                    'receipt_number' => $order->invoice_no,
                    'reference_number' => null,
                    'paid_by' => null,
                    'paid_by_user_id' => null,
                    'total_amount' => $totalCost,
                    'discount_amount' => 0,
                    'net_amount' => $totalCost,
                    'description' => 'Gift given - Stock cost value',
                    'items' => null,
                    'category' => 'gift sales expenses',
                    'status' => 'completed',
                    'attachment_path' => null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'total_amount' => $total,
                    'payment_amount' => $paymentAmount,
                    'change_amount' => max(0, $paymentAmount - $total),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details for an order
     */
    public function show($orderId): JsonResponse
    {
        try {
            $order = Order::with(['payments', 'customer', 'details.product'])
                ->findOrFail($orderId);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }
}
