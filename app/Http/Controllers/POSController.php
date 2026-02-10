<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'unit', 'stock'])
            ->where('is_active', true)
            ->get();

        $customers = Customer::where('is_active', true)->get();

        return view('pos.index', compact('products', 'customers'));
    }

    public function searchProducts(Request $request)
    {
        $search = $request->input('q', '');

        $products = Product::with(['category', 'unit', 'stock'])
            ->where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'price' => $product->selling_price,
                    'tax_percentage' => $product->tax_percentage,
                    'unit' => $product->unit->name,
                    'category' => $product->category->name,
                    'stock' => $product->stock?->quantity ?? 0,
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                ];
            });

        return response()->json($products);
    }

    public function processSale(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,upi,bank_transfer,cheque',
            'paid_amount' => 'required|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Check stock availability
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $stock = $product->stock;

                if (!$stock || $stock->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
            }

            // Create sale
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'subtotal' => $validated['subtotal'],
                'tax_amount' => $validated['tax_amount'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'status' => 'completed',
                'notes' => $validated['notes'],
                'sold_by' => Auth::id(),
            ]);

            // Create sale items and update stock
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => ($item['price'] * $item['quantity']) + ($item['tax_amount'] ?? 0),
                ]);

                // Update stock
                $stock = $product->stock;
                $previousQuantity = $stock->quantity;
                $newQuantity = $previousQuantity - $item['quantity'];

                $stock->update([
                    'quantity' => $newQuantity,
                    'last_updated_by' => Auth::id(),
                ]);

                // Log stock movement
                StockLog::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'previous_quantity' => $previousQuantity,
                    'current_quantity' => $newQuantity,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'notes' => "Sale invoice: {$sale->invoice_number}",
                    'created_by' => Auth::id(),
                ]);
            }

            // Create payment
            Payment::create([
                'sale_id' => $sale->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['paid_amount'],
                'status' => 'completed',
                'received_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function printInvoice($id)
    {
        $sale = Sale::with(['customer', 'items.product.unit', 'payments', 'soldBy'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pos.invoice', compact('sale'));
        return $pdf->download("invoice-{$sale->invoice_number}.pdf");
    }

    public function thermalInvoice($id)
    {
        $sale = Sale::with(['customer', 'items.product.unit', 'payments', 'soldBy'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pos.thermal-invoice', compact('sale'))
            ->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width

        return $pdf->download("receipt-{$sale->invoice_number}.pdf");
    }
}
