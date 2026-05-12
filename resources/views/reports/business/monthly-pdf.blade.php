<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Business Report - {{ $selectedMonth->format('F Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-bottom: 2px solid #333;
            padding-bottom: 3px;
        }
        h3 {
            font-size: 12px;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        .metric {
            display: inline-block;
            width: 48%;
            margin-bottom: 8px;
        }
        .metric-label {
            color: #666;
            font-size: 9px;
        }
        .metric-value {
            font-size: 14px;
            font-weight: bold;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $activeShop->name }}</h1>
        <p>Monthly Business Report - {{ $selectedMonth->format('F Y') }}</p>
        <p style="font-size: 9px; color: #666;">Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <!-- Executive Summary -->
    <div class="summary-box">
        <h2>Executive Summary</h2>
        <div>
            <div class="metric">
                <div class="metric-label">Total Revenue</div>
                <div class="metric-value">৳{{ number_format($reportData['sales_revenue'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Gross Profit</div>
                <div class="metric-value positive">৳{{ number_format($reportData['gross_profit'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Net Profit</div>
                <div class="metric-value {{ $reportData['net_profit'] >= 0 ? 'positive' : 'negative' }}">৳{{ number_format($reportData['net_profit'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Net Cash Flow</div>
                <div class="metric-value {{ $reportData['net_cash_flow'] >= 0 ? 'positive' : 'negative' }}">৳{{ number_format($reportData['net_cash_flow'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Profit & Loss Statement -->
    <h2>Profit & Loss Statement</h2>
    <table>
        <tr>
            <td><strong>Revenue</strong></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Total Sales ({{ $reportData['total_orders'] }} orders)</td>
            <td class="text-right">৳{{ number_format($reportData['sales_revenue'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Cost of Goods Sold</strong></td>
            <td class="text-right negative">-৳{{ number_format($reportData['cogs'], 2) }}</td>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <td><strong>Gross Profit</strong></td>
            <td class="text-right positive"><strong>৳{{ number_format($reportData['gross_profit'], 2) }} ({{ number_format($reportData['gross_profit_margin'], 1) }}%)</strong></td>
        </tr>
        <tr>
            <td><strong>Operating Expenses</strong></td>
            <td class="text-right negative">-৳{{ number_format($reportData['total_expenses'], 2) }}</td>
        </tr>
        <tr style="background-color: #f3e5f5;">
            <td><strong>Net Profit</strong></td>
            <td class="text-right {{ $reportData['net_profit'] >= 0 ? 'positive' : 'negative' }}"><strong>৳{{ number_format($reportData['net_profit'], 2) }} ({{ number_format($reportData['net_profit_margin'], 1) }}%)</strong></td>
        </tr>
    </table>

    <!-- Cash Flow Statement -->
    <h2>Cash Flow Statement</h2>
    <table>
        <tr>
            <td><strong>Cash Inflow (Money Received)</strong></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Cash Sales</td>
            <td class="text-right">৳{{ number_format($reportData['cash_inflow']['cash_sales'], 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Card Sales</td>
            <td class="text-right">৳{{ number_format($reportData['cash_inflow']['card_sales'], 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Credit Payments Received</td>
            <td class="text-right">৳{{ number_format($reportData['cash_inflow']['credit_received'], 2) }}</td>
        </tr>
        <tr style="background-color: #e8f5e9;">
            <td><strong>Total Inflow</strong></td>
            <td class="text-right positive"><strong>৳{{ number_format($reportData['total_inflow'], 2) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Cash Outflow (Money Paid)</strong></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Supplier Payments</td>
            <td class="text-right">৳{{ number_format($reportData['cash_outflow']['supplier_payments'], 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Operating Expenses</td>
            <td class="text-right">৳{{ number_format($reportData['cash_outflow']['expenses'], 2) }}</td>
        </tr>
        <tr style="background-color: #ffebee;">
            <td><strong>Total Outflow</strong></td>
            <td class="text-right negative"><strong>৳{{ number_format($reportData['total_outflow'], 2) }}</strong></td>
        </tr>
        <tr style="background-color: #e3f2fd;">
            <td><strong>Net Cash Flow</strong></td>
            <td class="text-right {{ $reportData['net_cash_flow'] >= 0 ? 'positive' : 'negative' }}"><strong>৳{{ number_format($reportData['net_cash_flow'], 2) }}</strong></td>
        </tr>
    </table>

    <!-- Supplier Transactions -->
    <h2>Supplier Transactions</h2>
    <table>
        <tr>
            <td>Total Purchases This Month</td>
            <td class="text-right">৳{{ number_format($reportData['total_purchases'], 2) }}</td>
        </tr>
        <tr>
            <td>Amount Paid to Suppliers</td>
            <td class="text-right positive">৳{{ number_format($reportData['purchases_paid'], 2) }}</td>
        </tr>
        <tr style="background-color: #fff9c4;">
            <td><strong>Outstanding Payables</strong></td>
            <td class="text-right"><strong>৳{{ number_format($reportData['purchases_due'], 2) }}</strong></td>
        </tr>
    </table>

    <!-- Customer Receivables -->
    <h2>Customer Receivables</h2>
    <table>
        <tr>
            <td>Credit Sales Outstanding (This Month)</td>
            <td class="text-right">৳{{ number_format($reportData['credit_sales_outstanding'], 2) }}</td>
        </tr>
    </table>

    @if($reportData['expense_breakdown']->count() > 0)
    <!-- Expense Breakdown -->
    <h2>Expense Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['expense_breakdown'] as $expense)
            <tr>
                <td>{{ $expense->category ?? 'Uncategorized' }}</td>
                <td class="text-right">৳{{ number_format($expense->total, 2) }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #f0f0f0;">
                <td><strong>Total</strong></td>
                <td class="text-right"><strong>৳{{ number_format($reportData['total_expenses'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($reportData['top_products']->count() > 0)
    <!-- Top Products -->
    <h2>Top 10 Products by Revenue</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['top_products'] as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                <td class="text-right">৳{{ number_format($product->total_revenue, 2) }}</td>
                <td class="text-right positive">৳{{ number_format($product->total_revenue - $product->total_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Confidential - {{ $activeShop->name }} | Page {PAGE_NUM} | Generated by Cherry Computers POS</p>
    </div>
</body>
</html>
