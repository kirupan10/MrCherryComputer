<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expense Date *</label>
                            <input type="date" name="expense_date"
                                value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="expense_category_id" required class="w-full border-gray-300 rounded-lg">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                            <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01"
                                min="0" required class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                            <input type="text" name="reference_number"
                                value="{{ old('reference_number', $expense->reference_number) }}"
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="3" required
                                class="w-full border-gray-300 rounded-lg">{{ old('description', $expense->description) }}</textarea>
                        </div>

                        @if($expense->receipt_image)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Receipt</label>
                                @if(Str::endsWith($expense->receipt_image, '.pdf'))
                                    <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800">View PDF</a>
                                @else
                                    <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt"
                                        class="h-32 rounded">
                                @endif
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Change Receipt/Document</label>
                            <input type="file" name="receipt" accept="image/*,application/pdf"
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        @role('admin|manager')
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-lg">
                                <option value="pending" {{ old('status', $expense->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('status', $expense->status) == 'paid' ? 'selected' : '' }}>
                                    Paid</option>
                                @role('admin')
                                <option value="approved" {{ old('status', $expense->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                @endrole
                            </select>
                        </div>
                        @endrole
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Update Expense
                        </button>
                        <a href="{{ route('expenses.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>