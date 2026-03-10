<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Product: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-lg">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit *</label>
                            <select name="unit_id" required class="w-full border-gray-300 rounded-lg">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price *</label>
                            <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                                step="0.01" min="0" required class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price *</label>
                            <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}"
                                step="0.01" min="0" required class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">MRP</label>
                            <input type="number" name="mrp" value="{{ old('mrp', $product->mrp) }}"
                                step="0.01" min="0" class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tax %</label>
                            <input type="number" name="tax_percentage" value="{{ old('tax_percentage', $product->tax_percentage) }}"
                                step="0.01" min="0" max="100" class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Alert *</label>
                            <input type="number" name="low_stock_alert" value="{{ old('low_stock_alert', $product->low_stock_alert) }}"
                                min="0" required class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div class="md:col-span-2">
                            @if($product->image)
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-32 w-32 object-cover rounded">
                            </div>
                            @endif
                            <label class="block text-sm font-medium text-gray-700 mb-2">Change Product Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 mr-2">
                                <span class="text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Update Product
                        </button>
                        <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
