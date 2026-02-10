<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Inventory Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('reports.inventory') }}" class="flex gap-4">
                    <select name="category_id" class="flex-1 border-gray-300 rounded-lg">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="flex items-center">
                        <input type="checkbox" name="low_stock_only" id="low_stock_only" value="1"
                            {{ request('low_stock_only') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 mr-2">
                        <label for="low_stock_only" class="text-sm text-gray-700">Low Stock Only</label>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Products</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total_products'] ?? 0 }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Stock Value</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">₹{{ number_format($summary['total_stock_value'] ?? 0, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Low Stock Items</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ $summary['low_stock_count'] ?? 0 }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Out of Stock</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ $summary['out_of_stock_count'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Current Inventory</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Value</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inventory as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $item['sku'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item['category'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="font-medium {{ $item['is_low_stock'] ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $item['current_stock'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₹{{ number_format($item['purchase_price'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₹{{ number_format($item['selling_price'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                ₹{{ number_format($item['stock_value'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item['current_stock'] == 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                                @elseif($item['is_low_stock'])
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                                @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    In Stock
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No inventory data found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
