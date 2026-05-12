<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monthly Business Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Month Selector -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ shop_route('reports.business.monthly') }}" class="flex items-center gap-4">
                        <label for="month" class="font-medium">Select Month:</label>
                        <input type="month"
                               id="month"
                               name="month"
                               value="{{ $selectedMonth->format('Y-m') }}"
                               class="border-gray-300 rounded-md shadow-sm">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Generate Report
                        </button>
                        <a href="{{ shop_route('reports.business.monthly.pdf', ['month' => $selectedMonth->format('Y-m')]) }}"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export PDF
                        </a>
                    </form>
                </div>
            </div>

            <!-- Shop & Period Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-2">{{ $activeShop->name }}</h3>
                    <p class="text-gray-600">Report Period: {{ $selectedMonth->format('F Y') }}</p>
                </div>
            </div>

            <!-- Key Metrics Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Revenue -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Total Revenue</p>
                            <h3 class="text-3xl font-bold mt-1">?{{ number_format($reportData['sales_revenue'], 2) }}</h3>
                        </div>
                        <svg class="w-12 h-12 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-sm mt-2">{{ $reportData['total_orders'] }} orders | Avg: ?{{ number_format($reportData['average_order_value'], 2) }}</p>
                </div>

                <!-- Gross Profit -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Gross Profit</p>
                            <h3 class="text-3xl font-bold mt-1">?{{ number_format($reportData['gross_profit'], 2) }}</h3>
                        </div>
                        <svg class="w-12 h-12 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-sm mt-2">{{ number_format($reportData['gross_profit_margin'], 1) }}% margin</p>
                </div>

                <!-- Net Profit -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Net Profit</p>
                            <h3 class="text-3xl font-bold mt-1">?{{ number_format($reportData['net_profit'], 2) }}</h3>
                        </div>
                        <svg class="w-12 h-12 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-sm mt-2">{{ number_format($reportData['net_profit_margin'], 1) }}% margin</p>
                </div>

                <!-- Cash Flow -->
                <div class="bg-gradient-to-br from-{{ $reportData['net_cash_flow'] >= 0 ? 'teal' : 'red' }}-500 to-{{ $reportData['net_cash_flow'] >= 0 ? 'teal' : 'red' }}-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-80">Net Cash Flow</p>
                            <h3 class="text-3xl font-bold mt-1">?{{ number_format($reportData['net_cash_flow'], 2) }}</h3>
                        </div>
                        <svg class="w-12 h-12 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-sm mt-2">In: ?{{ number_format($reportData['total_inflow'], 2) }} | Out: ?{{ number_format($reportData['total_outflow'], 2) }}</p>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Revenue Breakdown -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Revenue & Costs</h3>
                        <table class="w-full">
                            <tr class="border-b">
                                <td class="py-2 text-gray-600">Total Sales Revenue</td>
                                <td class="py-2 text-right font-semibold">?{{ number_format($reportData['sales_revenue'], 2) }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2 text-gray-600">Cost of Goods Sold (COGS)</td>
                                <td class="py-2 text-right font-semibold text-red-600">-?{{ number_format($reportData['cogs'], 2) }}</td>
                            </tr>
                            <tr class="border-b bg-green-50">
                                <td class="py-2 font-semibold">Gross Profit</td>
                                <td class="py-2 text-right font-bold text-green-600">?{{ number_format($reportData['gross_profit'], 2) }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2 text-gray-600">Operating Expenses</td>
                                <td class="py-2 text-right font-semibold text-red-600">-?{{ number_format($reportData['total_expenses'], 2) }}</td>
                            </tr>
                            <tr class="bg-purple-50">
                                <td class="py-2 font-bold">Net Profit</td>
                                <td class="py-2 text-right font-bold text-purple-600">?{{ number_format($reportData['net_profit'], 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Cash Flow Breakdown -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Cash Flow Analysis</h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 font-medium mb-2">INFLOW (Money Received):</p>
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-1 text-gray-600">Cash Sales</td>
                                    <td class="py-1 text-right">?{{ number_format($reportData['cash_inflow']['cash_sales'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">Card Sales</td>
                                    <td class="py-1 text-right">?{{ number_format($reportData['cash_inflow']['card_sales'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">Credit Payments Received</td>
                                    <td class="py-1 text-right">?{{ number_format($reportData['cash_inflow']['credit_received'], 2) }}</td>
                                </tr>
                                <tr class="border-t font-semibold">
                                    <td class="py-1">Total Inflow</td>
                                    <td class="py-1 text-right text-green-600">?{{ number_format($reportData['total_inflow'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-2">OUTFLOW (Money Paid):</p>
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-1 text-gray-600">Supplier Payments</td>
                                    <td class="py-1 text-right">?{{ number_format($reportData['cash_outflow']['supplier_payments'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">Operating Expenses</td>
                                    <td class="py-1 text-right">?{{ number_format($reportData['cash_outflow']['expenses'], 2) }}</td>
                                </tr>
                                <tr class="border-t font-semibold">
                                    <td class="py-1">Total Outflow</td>
                                    <td class="py-1 text-right text-red-600">?{{ number_format($reportData['total_outflow'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Breakdown -->
            @if($reportData['expense_breakdown']->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Expense Breakdown by Category</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($reportData['expense_breakdown'] as $expense)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600">{{ $expense->category ?? 'Uncategorized' }}</p>
                            <p class="text-xl font-bold text-gray-800">?{{ number_format($expense->total, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Purchases & Receivables -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Purchases -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Supplier Transactions</h3>
                        <table class="w-full">
                            <tr class="border-b">
                                <td class="py-2 text-gray-600">Total Purchases</td>
                                <td class="py-2 text-right font-semibold">?{{ number_format($reportData['total_purchases'], 2) }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-2 text-gray-600">Amount Paid</td>
                                <td class="py-2 text-right font-semibold text-green-600">?{{ number_format($reportData['purchases_paid'], 2) }}</td>
                            </tr>
                            <tr class="bg-yellow-50">
                                <td class="py-2 font-semibold">Outstanding Payables</td>
                                <td class="py-2 text-right font-bold text-yellow-600">?{{ number_format($reportData['purchases_due'], 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Receivables -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Customer Receivables</h3>
                        <table class="w-full">
                            <tr class="border-b">
                                <td class="py-2 text-gray-600">Credit Sales (This Month)</td>
                                <td class="py-2 text-right font-semibold text-yellow-600">?{{ number_format($reportData['credit_sales_outstanding'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-sm text-gray-500" colspan="2">
                                    <i>Outstanding amount from credit sales made this month</i>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            @if($reportData['top_products']->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Top 10 Products by Revenue</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-right">Quantity Sold</th>
                                    <th class="px-4 py-2 text-right">Revenue</th>
                                    <th class="px-4 py-2 text-right">Cost</th>
                                    <th class="px-4 py-2 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['top_products'] as $product)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $product->name }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($product->total_quantity) }}</td>
                                    <td class="px-4 py-2 text-right">?{{ number_format($product->total_revenue, 2) }}</td>
                                    <td class="px-4 py-2 text-right">?{{ number_format($product->total_cost, 2) }}</td>
                                    <td class="px-4 py-2 text-right font-semibold text-green-600">?{{ number_format($product->total_revenue - $product->total_cost, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Daily Breakdown Chart -->
            @if($reportData['daily_breakdown']->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Daily Revenue Trend</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-right">Orders</th>
                                    <th class="px-4 py-2 text-right">Revenue</th>
                                    <th class="px-4 py-2 text-left">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['daily_breakdown'] as $day)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-right">{{ $day->orders }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">?{{ number_format($day->revenue, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <div class="w-full bg-gray-200 rounded h-2">
                                            <div class="bg-blue-500 h-2 rounded" style="width: {{ ($day->revenue / $reportData['sales_revenue']) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
