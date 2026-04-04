<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expense Date *</label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}"
                                required class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="expense_category_id" required class="w-full border-gray-300 rounded-lg">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                            <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                            <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                                placeholder="e.g., Receipt #, Invoice #" class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="3" required
                                class="w-full border-gray-300 rounded-lg">{{ old('description') }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Receipt/Document</label>
                            <input type="file" name="receipt" accept="image/*,application/pdf"
                                class="w-full border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Accepted: Images, PDF (Max 2MB)</p>
                        </div>

                        @role('admin|manager')
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-lg">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                @role('admin')
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                @endrole
                            </select>
                        </div>
                        @endrole
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Create Expense
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