<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sales History
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice, Customer..."
                        class="border-gray-300 rounded-lg">

                    <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From Date"
                        class="border-gray-300 rounded-lg">

                    <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="To Date"
                        class="border-gray-300 rounded-lg">

                    <select name="payment_status" class="border-gray-300 rounded-lg">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid
                        </option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                            Filter
                        </button>
                        <a href="{{ route('sales.index') }}"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg text-center">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Sales Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $sale->invoice_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->sale_date->format('d M, Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ $sale->sale_date->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($sale->customer)->name ?? 'Walk-in Customer' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    LKR {{ number_format($sale->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusClass = [
                                            'paid' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'partial' => 'bg-orange-100 text-orange-800',
                                        ][$sale->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($sale->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ ucfirst($sale->payment_method) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('sales.show', $sale) }}"
                                            class="text-blue-600 hover:text-blue-900">View</a>
                                        @if(in_array($sale->status, ['pending', 'cancelled']))
                                            <a href="{{ route('sales.edit', $sale) }}"
                                                class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                        @endif
                                        <a href="{{ route('sales.invoice', $sale) }}" target="_blank"
                                            class="text-green-600 hover:text-green-900">Invoice</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No sales found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($sales->count() > 0)
                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm text-gray-900">Total</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    LKR {{ number_format($sales->sum('total_amount'), 2) }}
                                </td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>