<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Process Return
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Sale -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Find Sale</h3>
                <div class="flex gap-4">
                    <input type="text" id="invoice-search" placeholder="Enter Invoice Number..."
                        class="flex-1 border-gray-300 rounded-lg">
                    <button onclick="searchSale()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                        Search
                    </button>
                </div>
                <div id="search-message" class="mt-2 text-sm"></div>
            </div>

            <!-- Return Form (Hidden until sale is found) -->
            <div id="return-form-container" class="bg-white shadow-sm rounded-lg p-6" style="display: none;">
                <form id="return-form" action="{{ route('returns.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sale_id" id="sale-id">

                    <!-- Sale Details -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Sale Details</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Invoice:</span>
                                <strong id="sale-invoice" class="block text-gray-900"></strong>
                            </div>
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <strong id="sale-date" class="block text-gray-900"></strong>
                            </div>
                            <div>
                                <span class="text-gray-500">Customer:</span>
                                <strong id="sale-customer" class="block text-gray-900"></strong>
                            </div>
                            <div>
                                <span class="text-gray-500">Total:</span>
                                <strong id="sale-total" class="block text-gray-900"></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Return Items -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Select Items to Return</h3>
                        <div id="items-container"></div>
                    </div>

                    <!-- Return Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Return Date *</label>
                            <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Refund Method *</label>
                            <select name="refund_method" required class="w-full border-gray-300 rounded-lg">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="store_credit">Store Credit</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Return *</label>
                            <textarea name="reason" rows="3" required
                                class="w-full border-gray-300 rounded-lg"></textarea>
                        </div>
                    </div>

                    <!-- Refund Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span>Total Refund Amount:</span>
                            <span id="refund-total">LKR 0.00</span>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Process Return
                        </button>
                        <a href="{{ route('returns.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let saleData = null;

            async function searchSale() {
                const invoice = document.getElementById('invoice-search').value.trim();
                const messageDiv = document.getElementById('search-message');

                if (!invoice) {
                    messageDiv.className = 'mt-2 text-sm text-red-600';
                    messageDiv.textContent = 'Please enter an invoice number';
                    return;
                }

                try {
                    const response = await fetch(`{{ route('returns.search-sale') }}?invoice=${invoice}`);
                    const data = await response.json();

                    if (data.success) {
                        saleData = data.sale;
                        displaySaleDetails();
                        messageDiv.className = 'mt-2 text-sm text-green-600';
                        messageDiv.textContent = 'Sale found! Select items to return.';
                    } else {
                        messageDiv.className = 'mt-2 text-sm text-red-600';
                        messageDiv.textContent = data.message || 'Sale not found';
                        document.getElementById('return-form-container').style.display = 'none';
                    }
                } catch (error) {
                    messageDiv.className = 'mt-2 text-sm text-red-600';
                    messageDiv.textContent = 'Error searching for sale';
                }
            }

            function displaySaleDetails() {
                document.getElementById('sale-id').value = saleData.id;
                document.getElementById('sale-invoice').textContent = saleData.invoice_number;
                document.getElementById('sale-date').textContent = new Date(saleData.sale_date).toLocaleDateString();
                document.getElementById('sale-customer').textContent = saleData.customer?.name || 'Walk-in';
                document.getElementById('sale-total').textContent = `LKR ${parseFloat(saleData.total_amount).toFixed(2)}`;

                const itemsContainer = document.getElementById('items-container');
                itemsContainer.innerHTML = saleData.items.map(item => `
                    <div class="flex items-center gap-4 p-4 border rounded-lg mb-2">
                        <input type="checkbox" name="items[${item.id}][selected]" value="1"
                            onchange="updateRefund()" class="return-item-checkbox rounded border-gray-300">
                        <div class="flex-1">
                            <div class="font-medium">${item.product.name}</div>
                            <div class="text-sm text-gray-500">Price: LKR ${parseFloat(item.unit_price).toFixed(2)} × Qty: ${item.quantity}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm">Return Qty:</label>
                            <input type="number" name="items[${item.id}][quantity]"
                                min="0" max="${item.quantity}" value="0"
                                onchange="updateRefund()"
                                class="return-quantity w-20 border-gray-300 rounded">
                        </div>
                        <div class="font-semibold w-24 text-right">
                            <span class="item-subtotal" data-price="${item.unit_price}">LKR 0.00</span>
                        </div>
                    </div>
                `).join('');

                document.getElementById('return-form-container').style.display = 'block';
            }

            function updateRefund() {
                let total = 0;
                document.querySelectorAll('.return-item-checkbox').forEach((checkbox, index) => {
                    if (checkbox.checked) {
                        const quantityInput = document.querySelectorAll('.return-quantity')[index];
                        const subtotalSpan = document.querySelectorAll('.item-subtotal')[index];
                        const price = parseFloat(subtotalSpan.dataset.price);
                        const quantity = parseInt(quantityInput.value) || 0;
                        const subtotal = price * quantity;

                        subtotalSpan.textContent = `LKR ${subtotal.toFixed(2)}`;
                        total += subtotal;
                    } else {
                        document.querySelectorAll('.item-subtotal')[index].textContent = 'LKR 0.00';
                    }
                });

                document.getElementById('refund-total').textContent = `LKR ${total.toFixed(2)}`;
            }
        </script>
    @endpush
</x-app-layout>