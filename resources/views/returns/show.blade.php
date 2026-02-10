<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Return Details - {{ $return->return_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Return Info Card -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Return Number</label>
                        <p class="text-base font-semibold text-gray-900">{{ $return->return_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Original Invoice</label>
                        <a href="{{ route('sales.show', $return->sale) }}" class="text-base text-blue-600 hover:text-blue-800">
                            {{ $return->sale->invoice_number }}
                        </a>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Return Date</label>
                        <p class="text-base text-gray-900">{{ $return->return_date->format('d M, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Customer</label>
                        <p class="text-base text-gray-900">{{ optional($return->sale->customer)->name ?? 'Walk-in Customer' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Processed By</label>
                        <p class="text-base text-gray-900">{{ $return->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Refund Method</label>
                        <p class="text-base text-gray-900">{{ ucfirst($return->refund_method) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Refund Amount</label>
                        <p class="text-base font-bold text-gray-900">₹{{ number_format($return->refund_amount, 2) }}</p>
                    </div>
                    <div></div>
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Reason</label>
                        <p class="text-base text-gray-900">{{ $return->reason }}</p>
                    </div>
                </div>
            </div>

            <!-- Returned Items -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Returned Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Original Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Returned Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Refund Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($return->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ₹{{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    ₹{{ number_format($item->refund_amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900 text-right">Total Refund:</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                    ₹{{ number_format($return->refund_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
