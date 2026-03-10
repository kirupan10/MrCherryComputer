<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Sale - {{ $sale->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer_id" class="w-full border-gray-300 rounded-lg">
                            <option value="">Walk-in Customer</option>
                            @foreach(\App\Models\Customer::where('is_active', true)->orderBy('name')->get() as $customer)
                            <option value="{{ $customer->id }}" {{ (string) old('customer_id', $sale->customer_id) === (string) $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full border-gray-300 rounded-lg" required>
                                @foreach(['cash','card','upi','bank_transfer','mixed'] as $method)
                                <option value="{{ $method }}" {{ old('payment_method', $sale->payment_method) === $method ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $method)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <select name="payment_status" class="w-full border-gray-300 rounded-lg" required>
                                @foreach(['paid','partial','unpaid'] as $status)
                                <option value="{{ $status }}" {{ old('payment_status', $sale->payment_status) === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Paid Amount</label>
                            <input type="number" step="0.01" min="0" name="paid_amount" value="{{ old('paid_amount', $sale->paid_amount) }}" class="w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sale Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-lg" required>
                                @foreach(['pending','completed','cancelled'] as $status)
                                <option value="{{ $status }}" {{ old('status', $sale->status) === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('notes', $sale->notes) }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Update Sale</button>
                        <a href="{{ route('sales.show', $sale) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
