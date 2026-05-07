@extends('shop-types.tech.layouts.nexora')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <x-alert />

        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title mb-0">
                        {{ __('Import Historical Order') }}
                    </h1>
                    <div class="btn-list">
                        <a href="{{ route('orders.import.bulk') }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M12 11v6"/><path d="M9.5 13.5l2.5 -2.5l2.5 2.5"/></svg>
                            Bulk Import (CSV)
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/></svg>
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info mb-4">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12.01" y2="8"/><polyline points="11 12 12 12 12 16 13 16"/></svg>
                </div>
                <div>
                    <h4 class="alert-title">Manual Import Instructions</h4>
                    <div class="text-muted">
                        <ul class="mb-0">
                            <li>Use this form to manually enter old/historical orders from your previous system.</li>
                            <li>You can set custom order dates in the past to maintain accurate historical records.</li>
                            <li>Imported orders are marked as "Complete" status and won't affect current stock levels unless you want them to.</li>
                            <li>For bulk imports, use the <a href="{{ route('orders.import.bulk') }}" class="alert-link">CSV Import</a> feature.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('orders.import.store-manual') }}" method="POST" id="import-form">
            @csrf

            <div class="row">
                <!-- Main Form Section -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Order Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Invoice Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $invoicePrefix ?? 'INV' }}</span>
                                        <input type="text" name="invoice_no" id="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror"
                                               value="{{ old('invoice_no') }}" placeholder="{{ $invoicePrefix ?? 'INV' }}00001 or 00001" required
                                               data-max-number="{{ $invoiceStartingNumber ?? 1 }}">
                                    </div>
                                    @error('invoice_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Enter invoice number with or without prefix (e.g., {{ $invoicePrefix ?? 'INV' }}00001 or 00001). Must be less than {{ str_pad($invoiceStartingNumber ?? 1, 5, '0', STR_PAD_LEFT) }} (historical records only).</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Customer</label>
                                    <div class="input-group">
                                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}@if($customer->phone) - {{ $customer->phone }}@endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-primary btn-icon"
                                            id="addCustomerBtn"
                                            title="Add New Customer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 5l0 14" />
                                                <path d="M5 12l14 0" />
                                            </svg>
                                        </button>
                                    </div>
                                    <script src="{{ asset('vendor/tom-select/tom-select-latest.complete.min.js') }}"></script>
                                    <link href="{{ asset('vendor/tom-select/tom-select.bootstrap5.min.css') }}" rel="stylesheet">
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Order Date (Historical)</label>
                                     <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror"
                                         value="{{ old('order_date') ? \Carbon\Carbon::parse(old('order_date'))->format('Y-m-d') : '' }}" max="{{ date('Y-m-d') }}" required>
                                    <small class="form-hint">The original date when this order was placed (dd/mm/yyyy)</small>
                                    @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Import Notes</label>
                                    <textarea name="import_notes" rows="3" class="form-control @error('import_notes') is-invalid @enderror"
                                              placeholder="e.g., Imported from QuickBooks, Old POS system, etc.">{{ old('import_notes') }}</textarea>
                                    @error('import_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Products</h3>
                            <div class="card-actions">
                                <button type="button" id="btn-add-product" class="btn btn-sm btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                    Add Product
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="products-container">
                                <!-- Product rows will be added here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-md-4">
                    <div class="card sticky-top" style="top: 1rem;">
                        <div class="card-header">
                            <h3 class="card-title">Order Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Payment Type</label>
                                <select name="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                                    <option value="Cash" {{ old('payment_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Card" {{ old('payment_type') == 'Card' ? 'selected' : '' }}>Card</option>
                                    <option value="Bank Transfer" {{ old('payment_type') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Gift" {{ old('payment_type') == 'Gift' ? 'selected' : '' }}>Gift</option>
                                </select>
                                @error('payment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" name="discount_amount" id="discount_amount" step="0.01" min="0"
                                               class="form-control" value="{{ old('discount_amount', 0) }}" onchange="calculateTotal()">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Service Charges</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" name="service_charges" id="service_charges" step="0.01" min="0"
                                               class="form-control" value="{{ old('service_charges', 0) }}" onchange="calculateTotal()">
                                    </div>
                                </div>
                            </div>



                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong id="subtotal-display">LKR 0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Discount:</span>
                                <strong id="discount-display" class="text-danger">-LKR 0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Service Charges:</span>
                                <strong id="service-display" class="text-success">+LKR 0.00</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="h3 mb-0">Total:</span>
                                <span class="h3 mb-0 text-primary" id="total-display">LKR 0.00</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"/><path d="M9 15l3 3l3 -3"/><path d="M12 12l0 6"/></svg>
                                Import Historical Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('page-scripts')
<script>
    const products = @json($products);
    const warranties = @json($warranties);
    let productRowIndex = 0;

    // Open create customer modal
    function openCreateCustomerModal() {
        console.log('Opening create customer modal...');

        try {
            // Reset form
            const form = document.getElementById('addCustomerForm');
            if (!form) {
                console.error('Create customer form not found!');
                alert('Error: Customer form not found. Please refresh the page.');
                return;
            }
            form.reset();

            // Clear errors
            document.querySelectorAll('#addCustomerModal .invalid-feedback').forEach(el => el.textContent = '');
            document.querySelectorAll('#addCustomerModal .form-control').forEach(el => el.classList.remove('is-invalid'));

            // Show modal
            const modalEl = document.getElementById('addCustomerModal');
            if (!modalEl) {
                console.error('Create customer modal element not found!');
                alert('Error: Modal element not found. Please refresh the page.');
                return;
            }

            // Try multiple methods to show modal
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                console.log('Modal shown using Bootstrap');
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalEl).modal('show');
                console.log('Modal shown using jQuery');
            } else {
                // Manual fallback - add classes and backdrop
                console.log('Using manual modal method');
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.setAttribute('aria-modal', 'true');
                modalEl.removeAttribute('aria-hidden');

                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'addCustomerModalBackdrop';
                document.body.appendChild(backdrop);
                document.body.classList.add('modal-open');

                // Add close handlers
                const closeButtons = modalEl.querySelectorAll('[data-bs-dismiss="modal"]');
                closeButtons.forEach(btn => {
                    btn.onclick = function() {
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                        modalEl.removeAttribute('aria-modal');
                        modalEl.setAttribute('aria-hidden', 'true');
                        const backdrop = document.getElementById('addCustomerModalBackdrop');
                        if (backdrop) backdrop.remove();
                        document.body.classList.remove('modal-open');
                        // Remove ESC key handler
                        document.removeEventListener('keydown', escHandler);
                    };
                });

                // Add ESC key handler
                const escHandler = function(e) {
                    if (e.key === 'Escape') {
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                        modalEl.removeAttribute('aria-modal');
                        modalEl.setAttribute('aria-hidden', 'true');
                        const backdrop = document.getElementById('addCustomerModalBackdrop');
                        if (backdrop) backdrop.remove();
                        document.body.classList.remove('modal-open');
                        document.removeEventListener('keydown', escHandler);
                    }
                };
                document.addEventListener('keydown', escHandler);

                console.log('Modal shown using manual method');
            }
        } catch (error) {
            console.error('Error opening modal:', error);
            alert('Error opening customer form: ' + error.message);
        }
    }

    // Submit create customer form
    function submitCreateCustomer() {
        const form = document.getElementById('addCustomerForm');
        const formData = new FormData(form);
        const btn = document.querySelector('#addCustomerModal .btn-primary');
        const btnText = document.getElementById('createCustomerBtnText');
        const spinner = document.getElementById('createCustomerSpinner');

        // Clear previous errors
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

        // Show loading
        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');

        fetch('{{ route("customers.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal using multiple methods
                const modalEl = document.getElementById('addCustomerModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalEl).modal('hide');
                } else {
                    // Manual close
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    modalEl.removeAttribute('aria-modal');
                    modalEl.setAttribute('aria-hidden', 'true');
                    const backdrop = document.getElementById('addCustomerModalBackdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');
                }

                // Add new customer to select dropdown
                const customerSelect = document.getElementById('customer_id');
                const tomSelect = customerSelect.tomselect;

                if (tomSelect) {
                    tomSelect.addOption({
                        value: data.customer.id,
                        text: data.customer.name + (data.customer.phone ? ' - ' + data.customer.phone : '')
                    });
                    tomSelect.setValue(data.customer.id);
                } else {
                    const option = new Option(
                        data.customer.name + (data.customer.phone ? ' - ' + data.customer.phone : ''),
                        data.customer.id,
                        true,
                        true
                    );
                    customerSelect.add(option);
                }

                // Show success message
                console.log('Customer created successfully');
            } else {
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorDiv = document.getElementById(field + '_error');
                        const input = document.getElementById('customer_' + field);
                        if (errorDiv && input) {
                            errorDiv.textContent = data.errors[field][0];
                            input.classList.add('is-invalid');
                        }
                    });
                } else {
                    alert('Error: ' + (data.message || 'Failed to create customer'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the customer');
        })
        .finally(() => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
        });
    }

    // Product row management functions
    function addProductRow() {
        console.log('Adding product row...');
        const container = document.getElementById('products-container');
        if (!container) {
            console.error('products-container not found');
            return;
        }

        const rowIndex = productRowIndex++;

        const row = document.createElement('div');
        row.className = 'product-row border rounded p-3 mb-3';
        row.id = `product-row-${rowIndex}`;
        row.innerHTML = `
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0 required">Product <span class="badge bg-secondary ms-2 product-number">#1</span></label>
                        <button type="button" class="btn btn-sm btn-ghost-danger" onclick="removeProductRow(${rowIndex})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Remove
                        </button>
                    </div>
                    <input type="text" name="products[${rowIndex}][product_name]" class="form-control"
                           placeholder="Enter product name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Quantity</label>
                    <input type="number" name="products[${rowIndex}][quantity]" step="0.01" min="0.01"
                           class="form-control" value="1" onchange="calculateRowTotal(${rowIndex})" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Unit Price</label>
                    <div class="input-group">
                        <span class="input-group-text">LKR</span>
                        <input type="number" name="products[${rowIndex}][price]" id="price-${rowIndex}"
                               step="0.01" min="0" class="form-control" onchange="calculateRowTotal(${rowIndex})" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total</label>
                    <div class="input-group">
                        <span class="input-group-text">LKR</span>
                        <input type="text" id="row-total-${rowIndex}" class="form-control" readonly value="0.00">
                    </div>
                </div>
                <div class="col-md-6 mt-2">
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="products[${rowIndex}][serial_number]" class="form-control" placeholder="Optional">
                </div>
                <div class="col-md-6 mt-2">
                    <label class="form-label">Warranty</label>
                    <select name="products[${rowIndex}][warranty_id]" class="form-select">
                        <option value="">No Warranty</option>
                        ${warranties.map(w => `<option value="${w.id}">${w.name} (${w.duration} months)</option>`).join('')}
                    </select>
                </div>
            </div>
        `;

        container.appendChild(row);
        renumberProducts();
        console.log('Product row added successfully');
    }

    function openCustomerModal() {
        console.log('openCustomerModal called');
        const modalEl = document.getElementById('addCustomerModal');
        if (!modalEl) {
            console.error('Modal element not found');
            return;
        }

        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            // Get or create modal instance (reuse existing instance)
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalEl, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                console.log('Created new Bootstrap Modal instance');
            } else {
                console.log('Reusing existing Bootstrap Modal instance');
            }
            modalInstance.show();
            console.log('Modal opened with Bootstrap');
        } else {
            console.warn('Bootstrap unavailable, using fallback modal');
            showFallbackModal(modalEl);
        }
    }

    // Initialize after full page load (wait for deferred Bootstrap bundle)
    window.addEventListener('load', function() {
        console.log('Page loaded - initializing import form');

        // Initialize TomSelect for customer dropdown
        new TomSelect('#customer_id', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: "Search customer by name, phone, or email",
            searchField: ['text'],
            maxOptions: null,
            render: {
                option: function(data, escape) {
                    const parts = data.text.split(' - ');
                    const name = parts[0] || '';
                    const phone = parts[1] || '';
                    return '<div class="p-2">' +
                        '<div class="fw-bold text-dark">' + escape(name) + '</div>' +
                        (phone ? '<div class="small text-muted mt-1">' + escape(phone) + '</div>' : '') +
                        '</div>';
                },
                item: function(data, escape) {
                    const parts = data.text.split(' - ');
                    const name = parts[0] || '';
                    const phone = parts[1] || '';
                    return '<div>' +
                        '<span class="fw-bold">' + escape(name) + '</span>' +
                        (phone ? ' <small class="text-muted">(' + escape(phone) + ')</small>' : '') +
                        '</div>';
                }
            }
        });

        // Add event listener to Add Customer button
        const addCustomerBtn = document.getElementById('addCustomerBtn');
        if (addCustomerBtn) {
            addCustomerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Add customer button clicked');
                openCreateCustomerModal();
            });
            console.log('Add customer button listener attached');
        } else {
            console.error('Add customer button not found!');
        }

        // Add modal reset listener (Bootstrap path); fallback close also calls reset
        const modalElement = document.getElementById('addCustomerModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function modalHiddenHandler() {
                console.log('Modal hidden event - cleaning up');

                // Complete cleanup of all modal artifacts
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.body.removeAttribute('data-bs-overflow');
                document.body.removeAttribute('data-bs-padding-right');

                // Remove all backdrops (including orphaned ones)
                const allBackdrops = document.querySelectorAll('.modal-backdrop');
                allBackdrops.forEach(backdrop => {
                    console.log('Removing backdrop');
                    backdrop.remove();
                });

                // Reset form and toggle
                resetCustomerModal();

                console.log('Cleanup complete');
            });
            console.log('Modal reset listener attached');
        }

        // Add first product row
        addProductRow();

        // Handle form submission to combine prefix with invoice number
        const importForm = document.getElementById('import-form');
        const invoicePrefix = '{{ $invoicePrefix ?? "INV" }}';
        const maxInvoiceNumber = parseInt('{{ $invoiceStartingNumber ?? 1 }}');

        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                const invoiceInput = document.querySelector('input[name="invoice_no"]');
                if (invoiceInput && invoiceInput.value) {
                    const prefix = invoicePrefix;
                    let value = invoiceInput.value.trim();
                    // If value starts with prefix, extract numeric part
                    let numeric = value;
                    if (value.startsWith(prefix)) {
                        numeric = value.slice(prefix.length);
                    }
                    // Remove leading zeros, then pad to 5 digits
                    numeric = numeric.replace(/^0+/, '');
                    if (/^\d{1,5}$/.test(numeric)) {
                        const padded = numeric.padStart(5, '0');
                        invoiceInput.value = prefix + padded;
                    }
                }
            });
        }

        // Attach event listeners to all buttons
        const btnAddProduct = document.getElementById('btn-add-product');

        console.log('Button elements found:', {
            addProduct: !!btnAddProduct
        });

        if (btnAddProduct) {
            btnAddProduct.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Add Product clicked');
                addProductRow();
            });
        }
    });

    function removeProductRow(index) {
        const row = document.getElementById(`product-row-${index}`);
        if (row) {
            row.remove();
            calculateTotal();
            renumberProducts();
        }
    }

    function updateProductPrice(index) {
        const select = document.querySelector(`select[name="products[${index}][product_id]"]`);
        const priceInput = document.getElementById(`price-${index}`);

        if (select && select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            priceInput.value = parseFloat(price).toFixed(2);
            calculateRowTotal(index);
        }
    }

    function calculateRowTotal(index) {
        const quantity = parseFloat(document.querySelector(`input[name="products[${index}][quantity]"]`)?.value || 0);
        const price = parseFloat(document.getElementById(`price-${index}`)?.value || 0);
        const total = quantity * price;

        const totalInput = document.getElementById(`row-total-${index}`);
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }

        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;

        // Sum all product row totals
        document.querySelectorAll('.product-row').forEach(row => {
            const totalInput = row.querySelector('input[id^="row-total-"]');
            if (totalInput) {
                subtotal += parseFloat(totalInput.value || 0);
            }
        });

        const discount = parseFloat(document.getElementById('discount_amount')?.value || 0);
        const serviceCharges = parseFloat(document.getElementById('service_charges')?.value || 0);
        const total = subtotal - discount + serviceCharges;

        const subtotalDisplay = document.getElementById('subtotal-display');
        const discountDisplay = document.getElementById('discount-display');
        const serviceDisplay = document.getElementById('service-display');
        const totalDisplay = document.getElementById('total-display');

        if (subtotalDisplay) subtotalDisplay.textContent = `LKR ${subtotal.toFixed(2)}`;
        if (discountDisplay) discountDisplay.textContent = `-LKR ${discount.toFixed(2)}`;
        if (serviceDisplay) serviceDisplay.textContent = `+LKR ${serviceCharges.toFixed(2)}`;
        if (totalDisplay) totalDisplay.textContent = `LKR ${total.toFixed(2)}`;
    }

    function createProductRowFromText(product, quantity, price, serial, warrantyName) {
        const container = document.getElementById('products-container');
        const rowIndex = productRowIndex++;

        // Find matching warranty
        let selectedWarrantyId = '';
        if (warrantyName && warrantyName !== 'No Warranty') {
            const matchedWarranty = warranties.find(w =>
                w.name.toLowerCase().includes(warrantyName.toLowerCase())
            );
            if (matchedWarranty) {
                selectedWarrantyId = matchedWarranty.id;
            }
        }

        const row = document.createElement('div');
        row.className = 'product-row border rounded p-3 mb-3';
        row.id = `product-row-${rowIndex}`;
        row.innerHTML = `
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0 required">Product <span class="badge bg-secondary ms-2 product-number">#1</span></label>
                        <button type="button" class="btn btn-sm btn-ghost-danger" onclick="removeProductRow(${rowIndex})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Remove
                        </button>
                    </div>
                    <input type="text" name="products[${rowIndex}][product_name]" class="form-control"
                           value="${product.name}" placeholder="Enter product name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Quantity</label>
                    <input type="number" name="products[${rowIndex}][quantity]" step="0.01" min="0.01"
                           class="form-control" value="${quantity}" onchange="calculateRowTotal(${rowIndex})" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Unit Price</label>
                    <div class="input-group">
                        <span class="input-group-text">LKR</span>
                        <input type="number" name="products[${rowIndex}][price]" id="price-${rowIndex}"
                               step="0.01" min="0" class="form-control" value="${price}" onchange="calculateRowTotal(${rowIndex})" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total</label>
                    <div class="input-group">
                        <span class="input-group-text">LKR</span>
                        <input type="text" id="row-total-${rowIndex}" class="form-control" readonly value="${(quantity * price).toFixed(2)}">
                    </div>
                </div>
                <div class="col-md-6 mt-2">
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="products[${rowIndex}][serial_number]" class="form-control"
                           placeholder="Optional" value="${serial}">
                </div>
                <div class="col-md-6 mt-2">
                    <label class="form-label">Warranty</label>
                    <select name="products[${rowIndex}][warranty_id]" class="form-select">
                        <option value="">No Warranty</option>
                        ${warranties.map(w => `<option value="${w.id}" ${selectedWarrantyId === w.id ? 'selected' : ''}>${w.name} (${w.duration} months)</option>`).join('')}
                    </select>
                </div>
            </div>
        `;

        container.appendChild(row);
        renumberProducts();
    }

    function renumberProducts() {
        const numbers = document.querySelectorAll('.product-row .product-number');
        numbers.forEach((badge, idx) => {
            badge.textContent = `#${idx + 1}`;
        });
    }

    // Customer modal functionality
    function saveNewCustomer() {
        const form = document.getElementById('addCustomerForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitCustomerBtn');
        const errorDiv = document.getElementById('customerModalErrors');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creating...';
        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';

        fetch('{{ route("customers.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Add new customer to dropdown
                const select = document.getElementById('customer_id');
                const option = new Option(
                    `${data.customer.name}${data.customer.phone ? ' (' + data.customer.phone + ')' : ''}`,
                    data.customer.id,
                    true,
                    true
                );
                select.add(option);

                // Close modal properly
                if (modalInstance) {
                    modalInstance.hide();
                    console.log('Modal closed via Bootstrap instance');
                } else {
                    // Use the proper cleanup function for fallback
                    closeFallbackModal();
                }

                // Reset form
                form.reset();

                // Show success message
                console.log('Customer created successfully');
            } else {
                throw new Error(data.message || 'Failed to create customer');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.classList.remove('d-none');

            // Handle validation errors
            if (error.errors) {
                let errorHtml = '<div class="alert alert-danger mb-0"><ul class="mb-0">';
                for (const [field, messages] of Object.entries(error.errors)) {
                    messages.forEach(message => {
                        errorHtml += `<li>${message}</li>`;
                    });
                }
                errorHtml += '</ul></div>';
                errorDiv.innerHTML = errorHtml;
            } else if (error.message) {
                errorDiv.innerHTML = `<div class="alert alert-danger mb-0">${error.message}</div>`;
            } else {
                errorDiv.innerHTML = '<div class="alert alert-danger mb-0">An error occurred while creating the customer.</div>';
            }
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>Create Customer';
        });
    }
</script>
<style>
/* Fix TomSelect visibility and styling */
.ts-control .item {
    opacity: 1 !important;
    background-color: #fff !important;
    color: #212529 !important;
    font-weight: 500;
}
.ts-dropdown {
    background-color: #ffffff !important;
    border: 1px solid #dee2e6 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
}
.ts-dropdown .option {
    background-color: #ffffff !important;
    color: #212529 !important;
    padding: 12px 16px !important;
    border-bottom: 1px solid #f0f0f0 !important;
}
.ts-dropdown .option:last-child {
    border-bottom: none !important;
}
.ts-dropdown .option .name {
    font-weight: 600 !important;
    color: #000000 !important;
    font-size: 15px !important;
    display: block !important;
    margin-bottom: 4px !important;
}
.ts-dropdown .option .details {
    color: #6c757d !important;
    font-size: 13px !important;
    display: block !important;
}
.ts-dropdown .option:hover,
.ts-dropdown .active {
    background-color: #f8f9fa !important;
}
.ts-dropdown .option:hover .name,
.ts-dropdown .option:hover .details,
.ts-dropdown .active .name,
.ts-dropdown .active .details {
    color: #212529 !important;
}
</style>
@endpush

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    @csrf
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" name="name" required>
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customer_email" name="email">
                        <div class="invalid-feedback" id="email_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="customer_phone" name="phone">
                        <div class="invalid-feedback" id="phone_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Address</label>
                        <textarea class="form-control" id="customer_address" name="address" rows="2"></textarea>
                        <div class="invalid-feedback" id="address_error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitCreateCustomer()">
                    <span id="createCustomerBtnText">Create Customer</span>
                    <span id="createCustomerSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
