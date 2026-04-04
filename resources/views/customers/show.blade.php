<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customer Details
            </h2>
            @can('customer-edit')
                <a href="{{ route('customers.edit', $customer) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Edit Customer
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Customer Info Card -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                        <p class="text-base font-semibold text-gray-900">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <p class="text-base text-gray-900">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-base text-gray-900">{{ $customer->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Company</label>
                        <p class="text-base text-gray-900">{{ $customer->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">GST Number</label>
                        <p class="text-base text-gray-900">{{ $customer->gst_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Credit Limit</label>
                        <p class="text-base text-gray-900">₹{{ number_format($customer->credit_limit, 2) }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-base text-gray-900">{{ $customer->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        @if($customer->is_active)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Sales Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">Lifetime Sales</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">₹{{ number_format($stats['total_sales'], 2) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">Lifetime Orders</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">Average Order Value</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">
                        ₹{{ number_format($stats['average_order_value'], 2) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">Last Purchase</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">
                        {{ $stats['last_purchase'] ? $stats['last_purchase']->format('d M, Y') : 'N/A' }}</p>
                </div>
            </div>

            <!-- Purchase History -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase History</h3>

                <form method="GET" action="{{ route('customers.show', $customer) }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"
                            class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full border-gray-300 rounded-lg">
                            <option value="">All</option>
                            <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>
                                Paid</option>
                            <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Order Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg">
                            <option value="">All</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>
                                Completed</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>
                                Pending</option>
                            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Filter</button>
                        <a href="{{ route('customers.show', $customer) }}"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg text-center">Clear</a>
                    </div>
                </form>

                <div class="mb-4 text-sm text-gray-600">
                    Showing {{ $filteredStats['filtered_orders'] }} orders totaling
                    <span
                        class="font-semibold text-gray-900">₹{{ number_format($filteredStats['filtered_sales'], 2) }}</span>
                    for current filters.
                </div>

                @if($sales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Payment
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sales as $sale)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $sale->invoice_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sale->sale_date->format('d M, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            ₹{{ number_format($sale->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $sale->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($sale->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('sales.show', $sale) }}"
                                                class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-900">Total Purchases
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                        ₹{{ number_format($sales->sum('total_amount'), 2) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No purchase history found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>