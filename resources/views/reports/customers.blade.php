<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customer Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('reports.customers') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date"
                            value="{{ request('from_date', now()->subDays(90)->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-lg">
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
                    <div class="text-sm font-medium text-gray-500">Total Customers</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_customers'] ?? 0 }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Active Customers</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">{{ $summary['active_customers'] ?? 0 }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Revenue</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">LKR
                        {{ number_format($summary['total_revenue'] ?? 0, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Avg. Purchase Value</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">LKR
                        {{ number_format($summary['average_purchase'] ?? 0, 2) }}</div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Customers</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total
                                Purchases</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Spent
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg. Purchase
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Purchase
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topCustomers as $index => $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    #{{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer['phone'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ $customer['purchase_count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    LKR {{ number_format($customer['total_spent'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    LKR {{ number_format($customer['average_purchase'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $customer['last_purchase'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No customer data found for selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($topCustomers->count() > 0)
                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm text-gray-900">Total</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                    {{ $topCustomers->sum('purchase_count') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    LKR {{ number_format($topCustomers->sum('total_spent'), 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-app-layout>