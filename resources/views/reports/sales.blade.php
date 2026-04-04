<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sales Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="date_from"
                            value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Group By</label>
                        <select name="group_by" class="w-full border-gray-300 rounded-lg">
                            <option value="day" {{ request('group_by') == 'day' ? 'selected' : '' }}>Daily</option>
                            <option value="week" {{ request('group_by') == 'week' ? 'selected' : '' }}>Weekly</option>
                            <option value="month" {{ request('group_by') == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ request('group_by') == 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Sales</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">LKR
                        {{ number_format($summary['total_sales'] ?? 0, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Number of Transactions</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_transactions'] ?? 0 }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Average Sale</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">LKR
                        {{ number_format($summary['average_sale'] ?? 0, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Tax Collected</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">LKR
                        {{ number_format($summary['total_tax'] ?? 0, 2) }}</div>
                </div>
            </div>

            <!-- Sales Data Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Sales by {{ ucfirst(request('group_by', 'day')) }}
                    </h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sales Count
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Amount
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax Amount</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Average Sale
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($salesData as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $data['period'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ $data['count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    LKR {{ number_format($data['total'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    LKR {{ number_format($data['tax'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    LKR {{ number_format($data['average'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No sales data found for selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>