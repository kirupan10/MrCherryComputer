<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Details
            </h2>
            @can('user-edit')
            <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Edit User
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Info Card -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="flex items-start gap-6 mb-6">
                    <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-2xl">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-gray-500">{{ $user->email }}</p>
                        <div class="mt-2 flex gap-2">
                            @foreach($user->roles as $role)
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                                {{ $role->name === 'admin' ? 'bg-red-100 text-red-800' :
                                   ($role->name === 'manager' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($role->name) }}
                            </span>
                            @endforeach
                            @if($user->is_active)
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <p class="text-base text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Member Since</label>
                        <p class="text-base text-gray-900">{{ $user->created_at->format('d M, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Login</label>
                        <p class="text-base text-gray-900">{{ optional($user->last_login_at)->format('d M, Y h:i A') ?? 'Never' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email Verified</label>
                        @if($user->email_verified_at)
                        <span class="text-green-600 font-medium">✓ Verified</span>
                        @else
                        <span class="text-gray-500">Not verified</span>
                        @endif
                    </div>
                    @if($user->address)
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-base text-gray-900">{{ $user->address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Activity Stats -->
            @if($user->hasRole('cashier'))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total Sales</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $user->sales_count ?? 0 }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Sales Amount</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">₹{{ number_format($user->sales_sum_total_amount ?? 0, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Returns Processed</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $user->returns_count ?? 0 }}</div>
                </div>
            </div>

            <!-- Recent Sales -->
            @if($recentSales && $recentSales->count() > 0)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Sales</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentSales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $sale->invoice_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->sale_date->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($sale->customer)->name ?? 'Walk-in' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ₹{{ number_format($sale->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
