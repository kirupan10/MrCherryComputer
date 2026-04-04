<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sale Details - {{ $sale->invoice_number }}
            </h2>
            <div class="flex gap-2">
                @if(in_array($sale->status, ['pending', 'cancelled']))
                    <a href="{{ route('sales.edit', $sale) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                        Edit Sale
                    </a>
                @endif
                <a href="{{ route('sales.invoice', $sale) }}" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Print Invoice
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Sale Info Card -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Invoice Number</label>
                        <p class="text-base font-semibold text-gray-900">{{ $sale->invoice_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date & Time</label>
                        <p class="text-base text-gray-900">{{ $sale->sale_date->format('d M, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Customer</label>
                        <p class="text-base text-gray-900">{{ optional($sale->customer)->name ?? 'Walk-in Customer' }}
                        </p>
                        @if($sale->customer && $sale->customer->phone)
                            <p class="text-sm text-gray-500">{{ $sale->customer->phone }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cashier</label>
                        <p class="text-base text-gray-900">{{ $sale->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Sale Items -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sale Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->product->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        LKR {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        LKR {{ number_format($item->tax_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        LKR {{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                <div class="max-w-md ml-auto">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">LKR {{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax:</span>
                            <span class="text-gray-900">LKR {{ number_format($sale->tax_amount, 2) }}</span>
                        </div>
                        @if($sale->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-red-600">-LKR {{ number_format($sale->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span class="text-gray-900">Total Amount:</span>
                            <span class="text-gray-900">LKR {{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-4">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="text-gray-900">{{ ucfirst($sale->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Payment Status:</span>
                            @php
                                $statusClass = [
                                    'paid' => 'text-green-600',
                                    'pending' => 'text-yellow-600',
                                    'partial' => 'text-orange-600',
                                ][$sale->payment_status] ?? 'text-gray-600';
                            @endphp
                            <span class="font-semibold {{ $statusClass }}">{{ ucfirst($sale->payment_status) }}</span>
                        </div>
                        @if($sale->notes)
                            <div class="mt-4 pt-4 border-t">
                                <span class="block text-sm font-medium text-gray-600 mb-1">Notes:</span>
                                <p class="text-sm text-gray-900">{{ $sale->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>