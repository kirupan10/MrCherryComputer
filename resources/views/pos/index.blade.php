<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Point of Sale
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Products Section (Left - 2 columns) -->
                <div class="lg:col-span-2">
                    <!-- Search Bar -->
                    <div class="bg-white shadow-sm rounded-lg p-4 mb-4">
                        <div class="relative">
                            <input type="text" id="product-search"
                                placeholder="Search product by name, SKU, or barcode..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div id="search-results" class="mt-2 max-h-64 overflow-y-auto hidden"></div>
                    </div>

                    <!-- Featured Products Grid -->
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Products</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="products-grid">
                            @foreach($products as $product)
                            <div class="border rounded-lg p-3 hover:shadow-md transition cursor-pointer product-card"
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $product->selling_price }}"
                                 data-tax="{{ $product->tax_percentage ?? 0 }}"
                                 data-stock="{{ optional($product->stock)->quantity ?? 0 }}"
                                 onclick="addToCart(this)">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-24 object-cover rounded mb-2">
                                @else
                                <div class="w-full h-24 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                @endif
                                <h4 class="font-medium text-sm text-gray-900 truncate">{{ $product->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
                                <p class="text-sm font-bold text-blue-600 mt-1">₹{{ number_format($product->selling_price, 2) }}</p>
                                <p class="text-xs text-gray-500">Stock: {{ optional($product->stock)->quantity ?? 0 }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart Section (Right - 1 column) -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-sm rounded-lg p-4 sticky top-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Shopping Cart</h3>

                        <!-- Customer Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                            <select id="customer-select" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cart Items -->
                        <div id="cart-items" class="mb-4 max-h-64 overflow-y-auto border-t border-b border-gray-200 py-2">
                            <p class="text-sm text-gray-500 text-center py-8">No items in cart</p>
                        </div>

                        <!-- Cart Summary -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">₹<span id="subtotal">0.00</span></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium">₹<span id="tax-amount">0.00</span></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Discount:</span>
                                <input type="number" id="discount-input" value="0" min="0" step="0.01"
                                    class="w-20 text-right border-gray-300 rounded px-2 py-1 text-sm"
                                    onchange="updateTotals()">
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                <span>Total:</span>
                                <span class="text-blue-600">₹<span id="total-amount">0.00</span></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <button onclick="addManualItem()"
                                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg">
                                Add Manual Item
                            </button>
                            <button onclick="openPaymentModal()"
                                id="checkout-btn"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                Proceed to Payment
                            </button>
                            <button onclick="clearCart()"
                                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">
                                Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Payment</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select id="payment-method" class="w-full border-gray-300 rounded-lg">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                        <input type="text" id="modal-total" readonly class="w-full border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Paid Amount</label>
                        <input type="number" id="paid-amount" class="w-full border-gray-300 rounded-lg"
                               min="0" step="0.01" onchange="calculateChange()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Change</label>
                        <input type="text" id="change-amount" readonly class="w-full border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="sale-notes" rows="2" class="w-full border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button onclick="completeSale()"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                        Complete Sale
                    </button>
                    <button onclick="closePaymentModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let cart = [];

        // Product search
        let searchTimeout;
        document.getElementById('product-search').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value;

            if (query.length < 2) {
                document.getElementById('search-results').classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/pos/search-products?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    });
            }, 300);
        });

        function displaySearchResults(products) {
            const resultsDiv = document.getElementById('search-results');
            if (products.length === 0) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500 p-2">No products found</p>';
                resultsDiv.classList.remove('hidden');
                return;
            }

            resultsDiv.innerHTML = products.map(product => `
                <div class="p-2 hover:bg-gray-100 cursor-pointer border-b"
                     onclick='addToCartFromSearch(${JSON.stringify(product)})'>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-sm">${product.name}</p>
                            <p class="text-xs text-gray-500">${product.sku} | Stock: ${product.stock}</p>
                        </div>
                        <p class="font-bold text-blue-600">₹${product.price}</p>
                    </div>
                </div>
            `).join('');
            resultsDiv.classList.remove('hidden');
        }

        function addToCartFromSearch(product) {
            const existingItem = cart.find(item => item.product_id === product.id);

            if (existingItem) {
                if (existingItem.quantity >= product.stock) {
                    alert('Insufficient stock!');
                    return;
                }
                existingItem.quantity++;
            } else {
                cart.push({
                    product_id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    tax_percentage: parseFloat(product.tax_percentage || 0),
                    quantity: 1,
                    stock: product.stock
                });
            }

            updateCart();
            document.getElementById('product-search').value = '';
            document.getElementById('search-results').classList.add('hidden');
        }

        function addToCart(element) {
            const product = {
                product_id: parseInt(element.dataset.id),
                name: element.dataset.name,
                price: parseFloat(element.dataset.price),
                tax_percentage: parseFloat(element.dataset.tax),
                quantity: 1,
                stock: parseInt(element.dataset.stock)
            };

            if (product.stock <= 0) {
                alert('Out of stock!');
                return;
            }

            const existingItem = cart.find(item => item.product_id === product.product_id);

            if (existingItem) {
                if (existingItem.quantity >= product.stock) {
                    alert('Insufficient stock!');
                    return;
                }
                existingItem.quantity++;
            } else {
                cart.push(product);
            }

            updateCart();
        }

        function updateCart() {
            const cartDiv = document.getElementById('cart-items');

            if (cart.length === 0) {
                cartDiv.innerHTML = '<p class="text-sm text-gray-500 text-center py-8">No items in cart</p>';
                document.getElementById('checkout-btn').disabled = true;
                updateTotals();
                return;
            }

            cartDiv.innerHTML = cart.map((item, index) => {
                const itemTotal = item.price * item.quantity;
                const itemTax = (itemTotal * item.tax_percentage) / 100;

                return `
                    <div class="flex items-center justify-between py-2" data-index="${index}">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${item.name}</p>
                                <p class="text-xs text-gray-500">₹${item.price.toFixed(2)} × ${item.quantity}${item.is_manual ? ' (Manual)' : ''}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateQuantity(${index}, -1)"
                                class="bg-gray-200 hover:bg-gray-300 rounded px-2 py-1 text-sm">-</button>
                            <span class="text-sm font-medium w-8 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)"
                                class="bg-gray-200 hover:bg-gray-300 rounded px-2 py-1 text-sm">+</button>
                            <button onclick="removeFromCart(${index})"
                                class="text-red-600 hover:text-red-800 ml-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('checkout-btn').disabled = false;
            updateTotals();
        }

        function updateQuantity(index, change) {
            const item = cart[index];
            const newQuantity = item.quantity + change;

            if (newQuantity <= 0) {
                removeFromCart(index);
                return;
            }

            if (newQuantity > item.stock) {
                alert('Insufficient stock!');
                return;
            }

            item.quantity = newQuantity;
            updateCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
        }

        function addManualItem() {
            const name = prompt('Enter custom product/service name');
            if (!name) {
                return;
            }

            const price = parseFloat(prompt('Enter price', '0'));
            if (Number.isNaN(price) || price < 0) {
                alert('Invalid price.');
                return;
            }

            const quantity = parseFloat(prompt('Enter quantity', '1'));
            if (Number.isNaN(quantity) || quantity <= 0) {
                alert('Invalid quantity.');
                return;
            }

            cart.push({
                product_id: null,
                manual_name: name,
                name: name,
                price: price,
                tax_percentage: 0,
                quantity: quantity,
                stock: Number.MAX_SAFE_INTEGER,
                is_manual: true,
            });

            updateCart();
        }

        function clearCart() {
            if (confirm('Clear all items from cart?')) {
                cart = [];
                updateCart();
            }
        }

        function updateTotals() {
            let subtotal = 0;
            let taxAmount = 0;

            cart.forEach(item => {
                const itemSubtotal = item.price * item.quantity;
                const itemTax = (itemSubtotal * item.tax_percentage) / 100;
                subtotal += itemSubtotal;
                taxAmount += itemTax;
            });

            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const total = subtotal + taxAmount - discount;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('tax-amount').textContent = taxAmount.toFixed(2);
            document.getElementById('total-amount').textContent = total.toFixed(2);
        }

        function openPaymentModal() {
            if (cart.length === 0) return;

            const total = parseFloat(document.getElementById('total-amount').textContent);
            document.getElementById('modal-total').value = `₹${total.toFixed(2)}`;
            document.getElementById('paid-amount').value = total.toFixed(2);
            document.getElementById('change-amount').value = '₹0.00';
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }

        function calculateChange() {
            const total = parseFloat(document.getElementById('total-amount').textContent);
            const paid = parseFloat(document.getElementById('paid-amount').value) || 0;
            const change = Math.max(0, paid - total);
            document.getElementById('change-amount').value = `₹${change.toFixed(2)}`;
        }

        function completeSale() {
            const customerId = document.getElementById('customer-select').value || null;
            const paymentMethod = document.getElementById('payment-method').value;
            const paidAmount = parseFloat(document.getElementById('paid-amount').value);
            const notes = document.getElementById('sale-notes').value;

            const subtotal = parseFloat(document.getElementById('subtotal').textContent);
            const taxAmount = parseFloat(document.getElementById('tax-amount').textContent);
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const totalAmount = parseFloat(document.getElementById('total-amount').textContent);
            const changeAmount = paidAmount - totalAmount;

            if (paidAmount < totalAmount) {
                alert('Paid amount is less than total amount!');
                return;
            }

            const items = cart.map(item => ({
                product_id: item.product_id,
                manual_name: item.manual_name || null,
                quantity: item.quantity,
                price: item.price,
                tax_amount: (item.price * item.quantity * item.tax_percentage) / 100
            }));

            const saleData = {
                customer_id: customerId,
                items: items,
                subtotal: subtotal,
                tax_amount: taxAmount,
                discount_amount: discount,
                total_amount: totalAmount,
                payment_method: paymentMethod,
                paid_amount: paidAmount,
                change_amount: changeAmount,
                notes: notes
            };

            fetch('/pos/process-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(saleData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Sale completed successfully! Invoice: ${data.invoice_number}`);
                    window.location.href = `/pos/invoice/${data.sale_id}`;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error processing sale: ' + error);
            });
        }
    </script>
    @endpush
</x-app-layout>
