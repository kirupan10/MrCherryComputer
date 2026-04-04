<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            margin-bottom: 16px;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }

        .right {
            text-align: right;
        }

        .totals {
            margin-top: 16px;
            width: 280px;
            margin-left: auto;
        }

        .totals td {
            border: none;
            padding: 4px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Invoice</div>
        <div>Invoice #: {{ $sale->invoice_number }}</div>
        <div>Date: {{ optional($sale->sale_date)->format('d M Y h:i A') }}</div>
        <div>Customer: {{ optional($sale->customer)->name ?? 'Walk-in Customer' }}</div>
        <div>Cashier: {{ optional($sale->user)->name }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="right">Qty</th>
                <th class="right">Unit Price</th>
                <th class="right">Tax</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="right">LKR {{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">LKR {{ number_format($item->tax_amount, 2) }}</td>
                    <td class="right">LKR {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">LKR {{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td class="right">LKR {{ number_format($sale->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td class="right">LKR {{ number_format($sale->discount_amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td class="right"><strong>LKR {{ number_format($sale->total_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Paid</td>
            <td class="right">LKR {{ number_format($sale->paid_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Due</td>
            <td class="right">LKR {{ number_format($sale->due_amount, 2) }}</td>
        </tr>
    </table>
</body>

</html>