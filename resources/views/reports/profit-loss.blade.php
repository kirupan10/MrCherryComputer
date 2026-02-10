<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profit & Loss Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('reports.profit-loss') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', now()->subDays(30)->format('Y-m-d')) }}" 
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}" 
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- P&L Statement -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Profit & Loss Statement</h3>
                
                <!-- Revenue Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center py-3 border-b-2 border-gray-900">
                        <span class="text-lg font-bold text-gray-900">REVENUE</span>
                        <span class="text-lg font-bold text-gray-900">₹{{ number_format($report['total_revenue'] ?? 0, 2) }}</span>
                    </div>
                    <div class="pl-4 space-y-2 mt-2">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">Gross Sales</span>
                            <span class="text-gray-900">₹{{ number_format($report['gross_sales'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">Returns</span>
                            <span class="text-red-600">-₹{{ number_format($report['returns'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cost of Goods Sold -->
                <div class="mb-6">
                    <div class="flex justify-between items-center py-3 border-b border-gray-300">
                        <span class="text-base font-semibold text-gray-900">Cost of Goods Sold</span>
                        <span class="text-base font-semibold text-gray-900">₹{{ number_format($report['cogs'] ?? 0, 2) }}</span>
                    </div>
                </div>

                <!-- Gross Profit -->
                <div class="mb-6 bg-green-50 p-4 rounded">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-green-800">GROSS PROFIT</span>
                        <span class="text-lg font-bold text-green-800">₹{{ number_format($report['gross_profit'] ?? 0, 2) }}</span>
                    </div>
                    <div class="text-sm text-green-600 mt-1">
                        Margin: {{ number_format($report['gross_profit_margin'] ?? 0, 1) }}%
                    </div>
                </div>

                <!-- Operating Expenses -->
                <div class="mb-6">
                    <div class="flex justify-between items-center py-3 border-b-2 border-gray-900">
                        <span class="text-lg font-bold text-gray-900">OPERATING EXPENSES</span>
                        <span class="text-lg font-bold text-gray-900">₹{{ number_format($report['total_expenses'] ?? 0, 2) }}</span>
                    </div>
                    <div class="pl-4 space-y-2 mt-2">
                        @foreach($report['expenses_by_category'] ?? [] as $expense)
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">{{ $expense['category'] }}</span>
                            <span class="text-gray-900">₹{{ number_format($expense['amount'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Net Profit -->
                <div class="bg-{{ ($report['net_profit'] ?? 0) >= 0 ? 'blue' : 'red' }}-50 p-6 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-{{ ($report['net_profit'] ?? 0) >= 0 ? 'blue' : 'red' }}-800">
                            NET {{ ($report['net_profit'] ?? 0) >= 0 ? 'PROFIT' : 'LOSS' }}
                        </span>
                        <span class="text-2xl font-bold text-{{ ($report['net_profit'] ?? 0) >= 0 ? 'blue' : 'red' }}-800">
                            ₹{{ number_format(abs($report['net_profit'] ?? 0), 2) }}
                        </span>
                    </div>
                    <div class="text-base text-{{ ($report['net_profit'] ?? 0) >= 0 ? 'blue' : 'red' }}-600 mt-2">
                        Margin: {{ number_format($report['net_profit_margin'] ?? 0, 1) }}%
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Key Metrics</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="text-sm text-gray-500">Average Sale Value</div>
                            <div class="text-xl font-bold text-gray-900">₹{{ number_format($report['average_sale_value'] ?? 0, 2) }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="text-sm text-gray-500">Total Transactions</div>
                            <div class="text-xl font-bold text-gray-900">{{ $report['total_transactions'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
