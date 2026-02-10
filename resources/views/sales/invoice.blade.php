<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .invoice-container { max-width: 800px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { font-size: 28px; margin-bottom: 5px; }
        .header p { font-size: 14px; color: #666; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .invoice-info div { flex: 1; }
        .invoice-info h3 { font-size: 14px; margin-bottom: 10px; color: #333; }
        .invoice-info p { margin: 3px 0; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        thead { background-color: #f5f5f5; }
        th { padding: 12px; text-align: left; border-bottom: 2px solid #ddd; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 30px; float: right; width: 300px; }
        .totals table { margin: 0; }
        .totals td { padding: 8px; }
        .totals .grand-total { font-size: 16px; font-weight: bold; background-color: #f5f5f5; }
        .footer { clear: both; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Point of Sale System</p>
            <p style="margin-top: 10px; font-size: 16px; font-weight: bold;">TAX INVOICE</p>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div>
                <h3>Bill To:</h3>
                @if($sale->customer)
                <p><strong>{{ $sale->customer->name }}</strong></p>
                @if($sale->customer->company_name)
                <p>{{ $sale->customer->company_name }}</p>
                @endif
                <p>{{ $sale->customer->phone }}</p>
                @if($sale->customer->email)
                <p>{{ $sale->customer->email }}</p>
                @endif
                @if($sale->customer->address)
                <p>{{ $sale->customer->address }}</p>
                @endif
                @if($sale->customer->gst_number)
                <p><strong>GST:</strong> {{ $sale->customer->gst_number }}</p>
                @endif
                @else
                <p><strong>Walk-in Customer</strong></p>
                @endif
            </div>
            <div style="text-align: right;">
                <h3>Invoice Details:</h3>
                <p><strong>Invoice #:</strong> {{ $sale->invoice_number }}</p>
                <p><strong>Date:</strong> {{ $sale->sale_date->format('d M, Y') }}</p>
                <p><strong>Time:</strong> {{ $sale->sale_date->format('h:i A') }}</p>
                <p><strong>Cashier:</strong> {{ $sale->user->name }}</p>
                <p><strong>Payment:</strong> {{ ucfirst($sale->payment_method) }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹{{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">₹{{ number_format($sale->tax_amount, 2) }}</td>
                </tr>
                @if($sale->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td><strong>Total Amount:</strong></td>
                    <td class="text-right"><strong>₹{{ number_format($sale->total_amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            @if($sale->notes)
            <p style="margin-top: 10px;"><em>Note: {{ $sale->notes }}</em></p>
            @endif
            <p style="margin-top: 15px; font-size: 11px;">
                This is a computer-generated invoice and does not require a signature.
            </p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
