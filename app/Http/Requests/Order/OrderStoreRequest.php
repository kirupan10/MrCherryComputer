<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required_if:payment_type,Credit Sales|required_if:payment_type,Gift|nullable|exists:customers,id',
            'order_type' => 'required|in:dine_in,take_away,delivery',
            'payment_type' => 'required|in:Cash,Card,Bank Transfer,Credit Sales,Gift',
            'pay' => 'nullable|numeric|min:0',
            'cart_items' => 'required|json|min:3',
            'date' => 'nullable|date',
            'reference' => 'nullable|string',
            'invoice_no' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            // Credit Sales specific fields
            'credit_days' => 'required_if:payment_type,Credit Sales|nullable|integer|min:1|max:365',
            'initial_payment' => 'nullable|numeric|min:0',
            'credit_notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.exists' => 'Selected customer does not exist.',
            'customer_id.required_if' => 'Customer selection is required for credit sales or gift payments.',
            'order_type.required' => 'Please select an order type.',
            'order_type.in' => 'Invalid order type selected. Valid options are: Dine in, Take away, Delivery.',
            'payment_type.required' => 'Please select a payment method.',
            'payment_type.in' => 'Invalid payment method selected. Valid methods are: Cash, Card, Bank Transfer, Credit Sales, Gift',
            'cart_items.required' => 'Cart cannot be empty.',
            'cart_items.json' => 'Invalid cart data format.',
            'cart_items.min' => 'Cart data is too short.',
            'credit_days.required_if' => 'Credit days is required for credit sales.',
            'credit_days.integer' => 'Credit days must be a valid number.',
            'credit_days.min' => 'Credit days must be at least 1 day.',
            'credit_days.max' => 'Credit days cannot exceed 365 days.',
            'initial_payment.numeric' => 'Initial payment must be a valid amount.',
            'initial_payment.min' => 'Initial payment cannot be negative.',
            'credit_notes.max' => 'Credit notes cannot exceed 500 characters.',
        ];
    }

    public function prepareForValidation(): void
    {
        \Log::info('OrderStoreRequest prepareForValidation called', [
            'cart_items_raw' => $this->cart_items,
            'customer_id' => $this->customer_id,
            'payment_type' => $this->payment_type
        ]);

        // Get cart items from JSON
        $cartItems = json_decode($this->cart_items, true) ?? [];

        \Log::info('Cart items decoded', ['cart_items' => $cartItems]);

        // Calculate totals from cart items
        $totalProducts = array_sum(array_column($cartItems, 'quantity'));
        $subTotal = array_sum(array_column($cartItems, 'total'));
        $total = $subTotal; // No VAT
        $payAmount = (float) ($this->pay ?? 0);

        // Convert to integers (multiply by 100 to store cents)
        $subTotalInt = (int) round($subTotal * 100);
        $totalInt = (int) round($total * 100);
        $payInt = (int) round($payAmount * 100);
        $dueInt = $totalInt - $payInt;

        $this->merge([
            'order_date' => Carbon::now()->format('Y-m-d'),
            // order_status field has been removed - all orders are treated as completed
            'total_products' => $totalProducts,
            'sub_total' => $subTotalInt,
            'total' => $totalInt,
            'invoice_no' => $this->generateInvoiceNumber(),
            'pay' => $payInt,
            'due' => $dueInt,
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        // Get letterhead configuration for invoice prefix and starting number
        $user = auth()->user();
        $activeShop = $user ? $user->getActiveShop() : null;

        $prefix = 'INV';
        $startingNumber = 1;
        $configPath = null;

        if ($activeShop) {
            $configPath = storage_path('app/letterhead_config_shop_' . $activeShop->id . '.json');
            if (file_exists($configPath)) {
                $config = json_decode(file_get_contents($configPath), true);
                $prefix = $config['invoice_prefix'] ?? 'INV';
                $startingNumber = $config['invoice_starting_number'] ?? 1;
            }
        }

        // Get the maximum invoice number for this shop with the current prefix
        // This is more efficient than fetching all orders
        $query = \App\Models\Order::where('invoice_no', 'like', $prefix . '%');

        if ($activeShop) {
            $query->where('shop_id', $activeShop->id);
        }

        // Use a more efficient query to get just the max number
        $maxInvoice = $query->orderByRaw('CAST(SUBSTRING(invoice_no, LENGTH(?) + 1) AS UNSIGNED) DESC', [$prefix])
            ->value('invoice_no');

        $maxNumber = 0;
        if ($maxInvoice) {
            $numberStr = preg_replace('/[^0-9]/', '', $maxInvoice);
            if ($numberStr) {
                $maxNumber = (int) $numberStr;
            }
        }

        // Always use maxNumber + 1 to ensure no duplicates
        // If config has a higher starting number and no orders exist, use the config value
        $nextNumber = max($maxNumber + 1, $startingNumber);

        // Update letterhead config with the next invoice number for future use
        // This keeps the config in sync with actual database state
        if ($configPath && file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);
            $config['invoice_starting_number'] = $nextNumber + 1; // Set to next available number
            file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        // Format as PREFIX00001, PREFIX00002, etc. (5 digits)
        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
