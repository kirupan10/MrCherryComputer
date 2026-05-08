<?php

namespace App\Livewire\Payment;

use App\Models\Customer;
use Livewire\Component;

class PaymentProcessor extends Component
{
    public $paymentCompleted = false;
    public $customers = [];
    public $paymentTypes = [];
    public $customerId;
    public $paymentType;
    public $paymentAmount;
    public $subTotal;
    public $total;
    public $cartItems = [];
    public $isProcessing = false;
    public $orderDetails;

    public function mount()
    {
        $this->customers = Customer::all();
        $this->paymentTypes = [
            'cash' => 'Cash',
            'card' => 'Card',
            'mobile' => 'Mobile Payment',
        ];

        $this->subTotal = 0;
        $this->total = 0;
        $this->cartItems = [];
    }

    public function loadPaymentData(array $cartData)
    {
        $this->cartItems = $cartData;
        $this->subTotal = array_sum(array_map(fn ($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 0), $cartData));
        $this->total = $this->subTotal;
    }

    public function processPayment()
    {
        $this->validate([
            'customerId' => 'required|exists:customers,id',
            'paymentType' => 'required|string',
            'paymentAmount' => 'required|numeric|min:0',
        ]);

        $this->isProcessing = true;

        sleep(1);

        $this->orderDetails = [
            'order_id' => rand(1000, 9999),
            'invoice_no' => 'INV-' . now()->format('YmdHis'),
            'transaction_id' => 'TXN-' . strtoupper(bin2hex(random_bytes(6))),
            'customer' => Customer::find($this->customerId),
            'total_amount' => $this->total,
            'payment_amount' => $this->paymentAmount,
            'change_amount' => max(0, $this->paymentAmount - $this->total),
            'created_at' => now(),
        ];

        $this->paymentCompleted = true;
        $this->isProcessing = false;
    }

    public function resetPayment()
    {
        $this->paymentCompleted = false;
        $this->paymentAmount = null;
        $this->paymentType = null;
        $this->customerId = null;
        $this->orderDetails = null;
    }

    public function render()
    {
        return view('livewire.payment.payment-processor');
    }
}
