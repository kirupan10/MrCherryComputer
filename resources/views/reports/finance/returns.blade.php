<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Returns Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('reports.sales.finance.returns') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <input type="text" name="product" value="{{ $filters['product'] ?? '' }}" class="w-full border-gray-300 rounded-lg" placeholder="Search product">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Return Rate (%)</label>
                        <input type="number" step="0.01" name="min_rate" value="{{ $filters['min_rate'] ?? '' }}" class="w-full border-gray-300 rounded-lg" placeholder="0">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Return Rates</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Returned</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Return Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($results as $row)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $row->product_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ (int) $row->total_sold }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ (int) $row->total_returned }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format((float) $row->return_rate, 2) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No return data found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
