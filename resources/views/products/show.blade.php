<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Product Details
            </h2>
            <a href="{{ route('products.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">
                Back to Products
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-1">
                        @if($product->image)
                            <img src="{{ route('products.image', $product) }}" alt="{{ $product->name }}"
                                class="h-48 w-full rounded-lg object-cover border border-gray-200">
                        @else
                            <div
                                class="h-48 w-full rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                No Image
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ optional($product->category)->name ?? 'Uncategorized' }} |
                                    {{ optional($product->unit)->name ?? 'N/A' }}
                                </p>
                            </div>
                            @php
                                $currentStock = optional($product->stock)->quantity ?? 0;
                                $isLowStock = $currentStock <= $product->low_stock_alert;
                            @endphp
                            <span
                                class="px-3 py-1 text-xs font-semibold rounded-full {{ $isLowStock ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $isLowStock ? 'Low Stock' : 'Stock OK' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 text-sm">
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-500">Current Stock</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($currentStock, 2) }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-500">Low Stock Alert</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ number_format($product->low_stock_alert, 2) }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-500">Selling Price</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ number_format($product->selling_price, 2) }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-500">Purchase Price</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ number_format($product->purchase_price, 2) }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-500">SKU</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $product->sku ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-gray-500">Barcode</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $product->barcode ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($product->description)
                            <div class="mt-6 p-4 rounded-lg bg-gray-50 border border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-1">Description</p>
                                <p class="text-sm text-gray-600">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @hasanyrole('admin|manager')
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-lg font-semibold text-gray-800">Update Stock / Restock</h4>
                        <form action="{{ route('products.update-stock', $product) }}" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="type" class="w-full border-gray-300 rounded-lg">
                                    <option value="in">Stock In (Restock)</option>
                                    <option value="out">Stock Out</option>
                                    <option value="adjustment">Adjustment (Set/Correct)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" name="quantity" min="0.01" step="0.01" required
                                    class="w-full border-gray-300 rounded-lg" placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <input type="text" name="notes" class="w-full border-gray-300 rounded-lg"
                                    placeholder="Supplier / reference">
                            </div>

                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                                    Update Stock
                                </button>
                            </div>
                        </form>
                    </div>
                @endhasanyrole

                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-800">Stock Movement History</h4>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Previous</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($product->stockLogs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->type === 'in')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">IN</span>
                                    @elseif($log->type === 'out')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">OUT</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">ADJUSTMENT</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                    {{ number_format($log->quantity, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                    {{ number_format($log->previous_quantity, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                    {{ number_format($log->current_quantity, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ optional($log->creator)->name ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $log->notes ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No stock movement history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>