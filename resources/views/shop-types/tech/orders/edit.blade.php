@extends('shop-types.tech.layouts.nexora')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <x-alert/>

        <form id="editOrderForm" action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row row-deck row-cards">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ __('Edit Order') }}
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="customer_id">
                                            {{ __('Customer') }}
                                        </label>

                                        <div class="input-group">
                                            <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                                <option value="">Select a customer</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" @selected(old('customer_id', $order->customer_id) == $customer->id)>
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

                                        @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="order_date">
                                            {{ __('Order Date') }}
                                        </label>

                                        <input id="order_date" name="order_date" type="date"
                                               class="form-control @error('order_date') is-invalid @enderror"
                                               value="{{ old('order_date', $order->order_date ? $order->order_date->format('Y-m-d') : '') }}" required>

                                        @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">You can select any date including past dates</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="invoice_no">
                                            {{ __('Invoice Number') }}
                                        </label>

                                        <input id="invoice_no" name="invoice_no" type="text"
                                               class="form-control @error('invoice_no') is-invalid @enderror"
                                               value="{{ old('invoice_no', $order->invoice_no) }}"
                                               required
                                               placeholder="e.g., APFIN01001"
                                               pattern="[A-Za-z0-9-]+"
                                               title="Only letters, numbers, and dashes allowed">

                                        @error('invoice_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-hint">✏️ You can edit the invoice number if needed</small>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="payment_type">
                                            {{ __('Payment Type') }}
                                        </label>
                                        <select id="payment_type" name="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required data-original-type="{{ $order->payment_type }}" {{ $order->payment_type == 'Credit Sales' ? 'disabled' : '' }}>
                                            <option value="Cash" @selected(old('payment_type', $order->payment_type) == 'Cash')>Cash</option>
                                            <option value="Card" @selected(old('payment_type', $order->payment_type) == 'Card')>Card</option>
                                            <option value="Bank Transfer" @selected(old('payment_type', $order->payment_type) == 'Bank Transfer')>Bank Transfer</option>
                                            <option value="Gift" @selected(old('payment_type', $order->payment_type) == 'Gift')>Gift</option>
                                            @if($order->payment_type == 'Credit Sales')
                                                <option value="Credit Sales" @selected(old('payment_type', $order->payment_type) == 'Credit Sales')>Credit Sales</option>
                                            @endif
                                        </select>
                                        <!-- Hidden input to submit value when disabled -->
                                        @if($order->payment_type == 'Credit Sales')
                                            <input type="hidden" name="payment_type" value="Credit Sales">
                                        @endif
                                        @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($order->payment_type == 'Credit Sales')
                                            <small class="form-hint text-warning">⚠️ This is a Credit Sales order - payment type cannot be changed</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Products Section -->
                            <div class="mt-4">
                                <div class="alert mb-3" style="border: 1px solid #000; background: #fff;">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 9v4" />
                                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                            <path d="M12 16h.01" />
                                        </svg>
                                        <span><strong>Edit Mode:</strong> All product details are already loaded. You can modify quantities, prices, serial numbers, and warranties directly!</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">
                                        Order Products <span class="badge bg-dark ms-2" id="productCount">{{ count($order->details) }}</span>
                                    </h4>
                                    <button type="button" class="btn btn-primary btn-sm" id="addProductBtn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                        Add Product
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productsTable">
                                        <thead style="background: #fff; color: #000; border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                            <tr>
                                                <th>Product</th>
                                                <th style="width: 100px;">Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>Warranty</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productRows">
                                            @foreach($order->details as $index => $detail)
                                            <tr class="product-row" data-index="{{ $index }}">
                                                <td>
                                                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $detail->id }}">
                                                    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $detail->product_id }}">
                                                    <div class="p-2" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                                                        <strong>{{ $detail->product->name ?? $detail->product_name ?? 'Product #'.$detail->product_id }}</strong>
                                                        @if($detail->product)
                                                        <br>
                                                        <small class="text-muted">{{ $detail->product->code }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 d-flex align-items-center">
                                                        <span class="me-2" style="white-space: nowrap;"><strong>SN:</strong></span>
                                                        <input type="text" name="products[{{ $index }}][serial_number]" class="form-control" value="{{ $detail->serial_number }}" placeholder="Enter serial number">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="products[{{ $index }}][quantity]" class="form-control quantity-input" value="{{ $detail->quantity }}" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="products[{{ $index }}][unitcost]" class="form-control unitcost-input" value="{{ $detail->unitcost }}" step="0.01" min="0" required>
                                                </td>
                                                <td>
                                                    <select name="products[{{ $index }}][warranty_id]" class="form-select warranty-select">
                                                        <option value="">NW</option>
                                                        @foreach($warranties as $warranty)
                                                            @php
                                                                $duration = intval($warranty->duration);
                                                                if ($duration == 0) continue;
                                                                if ($duration >= 12) {
                                                                    $years = $duration / 12;
                                                                    $displayText = $years . 'Y';
                                                                } else {
                                                                    $displayText = $duration . 'M';
                                                                }
                                                            @endphp
                                                            <option value="{{ $warranty->id }}"
                                                                    data-name="{{ $warranty->name }}"
                                                                    data-duration="{{ $warranty->duration }}"
                                                                    @selected($detail->warranty_id == $warranty->id)>
                                                                {{ $displayText }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control total-input" value="{{ number_format($detail->total, 2) }}" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-product-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M4 7l16 0" />
                                                            <path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Update Order') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card" style="position: sticky; top: 20px;">
                        <div class="card-header" style="background: #fff; color: #000; border-bottom: 2px solid #000;">
                            <h3 class="card-title mb-0">{{ __('Order Summary') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Invoice No:</strong>
                                <div id="invoiceNoDisplay" class="fs-5">{{ $order->invoice_no }}</div>
                            </div>
                            <hr>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Total Products:</strong>
                                <span id="totalProducts">{{ $order->total_products }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Sub Total:</strong>
                                <span id="subTotal" class="fw-bold">{{ number_format($order->sub_total, 2) }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Discount:</strong>
                                <span id="discountDisplay" class="fw-bold">{{ number_format($order->discount_amount ?? 0, 2) }}</span>
                            </div>
                            <div class="mb-2 d-flex justify-content-between">
                                <strong>Service Charges:</strong>
                                <span id="serviceChargesDisplay" class="fw-bold">{{ number_format($order->service_charges ?? 0, 2) }}</span>
                            </div>
                            <hr style="border-top: 2px solid #000;">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <strong class="fs-5">Total Amount:</strong>
                                <span id="totalAmount" class="fs-4 fw-bold">{{ number_format($order->total, 2) }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="discount_amount_sidebar">
                                    <strong>Discount (LKR):</strong>
                                </label>
                                <input type="number"
                                       id="discount_amount_sidebar"
                                       name="discount_amount"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('discount_amount', $order->discount_amount ?? 0) }}"
                                       placeholder="0.00">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="service_charges">
                                    <strong>Service Charges (LKR):</strong>
                                </label>
                                <input type="number"
                                       id="service_charges"
                                       name="service_charges"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('service_charges', $order->service_charges ?? 0) }}"
                                       placeholder="0.00">
                            </div>
                            <hr>
                            <div>
                                <strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Edit Customer Modal -->
        <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editCustomerForm">
                        @csrf
                        <input type="hidden" id="edit_customer_id" name="customer_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_customer_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="edit_customer_name" name="name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_customer_phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="edit_customer_phone" name="phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_customer_email" class="form-label">Email</label>
                                        <input type="text" class="form-control" id="edit_customer_email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_customer_address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="edit_customer_address" name="address">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_customer_account_holder" class="form-label">Account Holder</label>
                                        <input type="text" class="form-control" id="edit_customer_account_holder" name="account_holder">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_customer_account_number" class="form-label">Account Number</label>
                                        <input type="text" class="form-control" id="edit_customer_account_number" name="account_number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_customer_bank_name" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" id="edit_customer_bank_name" name="bank_name">
                                    </div>
                                </div>
                            </div>

                            <div id="customer_error_message" class="alert alert-danger d-none"></div>
                            <div id="customer_success_message" class="alert alert-success d-none"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Customer Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<!-- TomSelect CSS -->
<link href="{{ asset('vendor/tom-select/tom-select.bootstrap5.min.css') }}" rel="stylesheet">

<style>
    /* Basic styling only */
    .product-row {
        /* No fancy effects */
    }

    #invoice_no {
        font-weight: 600;
    }

    /* TomSelect customer dropdown styling - matching import single order page */
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
    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #f8f9fa !important;
    }
</style>

<!-- TomSelect JS -->
<script src="{{ asset('vendor/tom-select/tom-select-latest.complete.min.js') }}"></script>

<script>
    let productIndex = {{ count($order->details) }};
    const productsData = @json($products);
    const customersData = @json($customers);

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TomSelect for customer dropdown - matching import single order page style
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

        // Add Customer button event listener
        const addCustomerBtn = document.getElementById('addCustomerBtn');
        if (addCustomerBtn) {
            addCustomerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Open customer creation in new tab
                window.open('{{ route("customers.create") }}', '_blank');
            });
        }

        const addProductBtn = document.getElementById('addProductBtn');
        const productRows = document.getElementById('productRows');
        const discountInputSidebar = document.getElementById('discount_amount_sidebar');
        const editCustomerBtn = document.getElementById('editCustomerBtn');
        const customerSelect = document.getElementById('customer_id');

        // Verify critical elements exist
        if (!addProductBtn) {
            console.error('Add product button not found');
        }

        if (!productRows) {
            console.error('Product rows container not found');
        }

        if (!editCustomerBtn) {
            console.warn('Edit customer button not found');
        }

        if (!customerSelect) {
            console.warn('Customer select not found');
        }

        console.log('Order edit page initialized');

        // Edit Customer Button Click
        if (editCustomerBtn && customerSelect) {
            editCustomerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Edit customer button clicked');

                const selectedCustomerId = customerSelect.value;

                if (!selectedCustomerId) {
                    alert('Please select a customer first');
                    return;
                }

                console.log('Selected customer ID:', selectedCustomerId);

                // Find customer in data
                const customer = customersData.find(c => c.id == selectedCustomerId);

                console.log('Found customer:', customer);

            if (customer) {
                document.getElementById('edit_customer_id').value = customer.id;
                document.getElementById('edit_customer_name').value = customer.name || '';
                document.getElementById('edit_customer_phone').value = customer.phone || '';
                document.getElementById('edit_customer_email').value = customer.email || '';
                document.getElementById('edit_customer_address').value = customer.address || '';
                document.getElementById('edit_customer_account_holder').value = customer.account_holder || '';
                document.getElementById('edit_customer_account_number').value = customer.account_number || '';
                document.getElementById('edit_customer_bank_name').value = customer.bank_name || '';

                // Hide messages
                document.getElementById('customer_error_message').classList.add('d-none');
                document.getElementById('customer_success_message').classList.add('d-none');

                // Show modal - try multiple methods
                const modalElement = document.getElementById('editCustomerModal');
                if (modalElement) {
                    console.log('Modal element found, attempting to show...');

                    // Method 1: Use Bootstrap 5 Modal API
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = new bootstrap.Modal(modalElement);
                        bsModal.show();
                        console.log('Opened with Bootstrap 5 Modal API');
                    }
                    // Method 2: Use jQuery if Bootstrap JS is loaded via jQuery
                    else if (typeof $ !== 'undefined' && $.fn.modal) {
                        $(modalElement).modal('show');
                        console.log('Opened with jQuery modal');
                    }
                    // Method 3: Direct DOM manipulation
                    else {
                        console.log('Using direct DOM manipulation');
                        modalElement.classList.add('show');
                        modalElement.style.display = 'block';
                        modalElement.setAttribute('aria-modal', 'true');
                        modalElement.setAttribute('role', 'dialog');
                        modalElement.removeAttribute('aria-hidden');

                        // Add backdrop
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.id = 'customModalBackdrop';
                        document.body.appendChild(backdrop);
                        document.body.classList.add('modal-open');

                        console.log('Modal opened with direct DOM manipulation');
                    }
                } else {
                    console.error('Modal element not found');
                    alert('Error: Modal not found in page');
                }
            } else {
                alert('Customer data not found');
            }
        });

        // Handle Customer Form Submission
        document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const customerId = document.getElementById('edit_customer_id').value;
            const formData = new FormData(this);
            const errorMessage = document.getElementById('customer_error_message');
            const successMessage = document.getElementById('customer_success_message');

            // Hide previous messages
            errorMessage.classList.add('d-none');
            successMessage.classList.add('d-none');

            fetch(`/customers/${customerId}/update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update customer in dropdown
                    const option = customerSelect.querySelector(`option[value="${customerId}"]`);
                    if (option) {
                        option.textContent = `${data.customer.name}${data.customer.phone ? ' - ' + data.customer.phone : ''}`;
                        option.dataset.name = data.customer.name;
                        option.dataset.email = data.customer.email || '';
                        option.dataset.phone = data.customer.phone || '';
                        option.dataset.address = data.customer.address || '';
                    }

                    // Update customersData array
                    const customerIndex = customersData.findIndex(c => c.id == customerId);
                    if (customerIndex !== -1) {
                        customersData[customerIndex] = data.customer;
                    }

                    // Show success message
                    successMessage.textContent = data.message;
                    successMessage.classList.remove('d-none');

                    // Close modal after 1.5 seconds
                    setTimeout(() => {
                        closeCustomerModal();
                    }, 1500);
                } else {
                    errorMessage.textContent = data.message || 'An error occurred';
                    errorMessage.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'An error occurred while updating customer details';
                errorMessage.classList.remove('d-none');
            });
        });
        }

        // Function to close modal
        function closeCustomerModal() {
            const modalElement = document.getElementById('editCustomerModal');

            // Try Bootstrap API first
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalElement).modal('hide');
            } else {
                // Direct DOM manipulation
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
                modalElement.removeAttribute('role');

                // Remove backdrop
                const backdrop = document.getElementById('customModalBackdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
            }
        }

        // Add close button handlers
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                closeCustomerModal();
            });
        });

        // Add new product row
        if (addProductBtn && productRows) {
            addProductBtn.addEventListener('click', function() {
                // Check current number of product rows
                const currentRows = productRows.querySelectorAll('.product-row').length;
                if (currentRows >= 11) {
                    // Creative error message
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Limit Reached',
                        text: 'Maximum 11 different items allowed per order. Please create a new order for additional items to prevent PDF generation issues.',
                        confirmButtonColor: '#000',
                    });
                    return;
                }

                // Use current row count as the new index to ensure no gaps
                const newIndex = currentRows;
                const newRow = createProductRow(newIndex);
                productRows.insertAdjacentHTML('beforeend', newRow);

                attachRowEvents();
                calculateTotals();

                // Update product count
                updateProductCount();
            });
        }

        // Update product count badge
        function updateProductCount() {
            if (!productRows) return;
            const count = productRows.querySelectorAll('.product-row').length;
            const badge = document.getElementById('productCount');
            if (badge) {
                badge.textContent = count;
            }
        }

        // Update invoice number display
        const invoiceInput = document.getElementById('invoice_no');
        if (invoiceInput) {
            invoiceInput.addEventListener('input', function() {
                // Update summary display
                const display = document.getElementById('invoiceNoDisplay');
                if (display) {
                    display.textContent = this.value || '{{ $order->invoice_no }}';
                }
            });
        }

        // Create product row HTML
        function createProductRow(index) {
            let productOptions = '<option value="">Select Product</option>';
            productsData.forEach(product => {
                productOptions += `<option value="${product.id}" data-price="${product.selling_price}">${product.name} (${product.code})</option>`;
            });

            let warrantyOptions = '<option value="">No Warranty</option>';
            @foreach($warranties as $warranty)
                warrantyOptions += `<option value="{{ $warranty->id }}" data-name="{{ $warranty->name }}" data-duration="{{ $warranty->duration }}">{{ $warranty->name }} ({{ $warranty->duration }})</option>`;
            @endforeach

            return `
                <tr class="product-row" data-index="${index}">
                    <td>
                        <input type="hidden" name="products[${index}][id]" value="">
                        <select name="products[${index}][product_id]" class="form-select product-select" required>
                            ${productOptions}
                        </select>
                        <div class="mt-2 d-flex align-items-center">
                            <span class="me-2" style="white-space: nowrap;"><strong>SN:</strong></span>
                            <input type="text" name="products[${index}][serial_number]" class="form-control" placeholder="Enter serial number">
                        </div>
                    </td>
                    <td>
                        <input type="number" name="products[${index}][quantity]" class="form-control quantity-input" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="products[${index}][unitcost]" class="form-control unitcost-input" value="0" step="0.01" min="0" required>
                    </td>
                    <td>
                        <select name="products[${index}][warranty_id]" class="form-select warranty-select">
                            ${warrantyOptions}
                        </select>
                        <input type="number" name="products[${index}][warranty_years]" class="form-control mt-1 warranty-years-input" min="0" placeholder="Or years">
                    </td>
                    <td>
                        <input type="text" class="form-control total-input" value="0.00" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Attach events to rows
        function attachRowEvents() {
            // Product selection change
            document.querySelectorAll('.product-select').forEach(select => {
                select.removeEventListener('change', handleProductChange);
                select.addEventListener('change', handleProductChange);
            });

            // Quantity/price change
            document.querySelectorAll('.quantity-input, .unitcost-input').forEach(input => {
                input.removeEventListener('input', handleQuantityPriceChange);
                input.addEventListener('input', handleQuantityPriceChange);
            });

            // Remove product
            document.querySelectorAll('.remove-product-btn').forEach(btn => {
                btn.removeEventListener('click', handleRemoveProduct);
                btn.addEventListener('click', handleRemoveProduct);
            });

            // Warranty interactions
            document.querySelectorAll('.warranty-select').forEach(select => {
                select.removeEventListener('change', handleWarrantySelect);
                select.addEventListener('change', handleWarrantySelect);
            });

            document.querySelectorAll('.warranty-years-input').forEach(input => {
                input.removeEventListener('input', handleWarrantyYears);
                input.addEventListener('input', handleWarrantyYears);
            });
        }

        function handleProductChange(e) {
            const row = e.target.closest('.product-row');
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.dataset.price || 0;
            const unitcostInput = row.querySelector('.unitcost-input');
            unitcostInput.value = price;
            calculateRowTotal(row);
        }

        function handleQuantityPriceChange(e) {
            const row = e.target.closest('.product-row');
            calculateRowTotal(row);
        }

        function handleRemoveProduct(e) {
            if (document.querySelectorAll('.product-row').length > 1) {
                const row = e.target.closest('.product-row');
                row.remove();
                reindexProducts();
                calculateTotals();
                updateProductCount();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove',
                    text: 'At least one product is required in the order',
                    confirmButtonColor: '#000',
                });
            }
        }

        function reindexProducts() {
            document.querySelectorAll('.product-row').forEach((row, index) => {
                row.setAttribute('data-index', index);
                row.querySelectorAll('input, select').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name && name.includes('products[')) {
                        const newName = name.replace(/products\[\d+\]/, `products[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        function handleWarrantySelect(e) {
            const row = e.target.closest('.product-row');
            if (e.target.value) {
                row.querySelector('.warranty-years-input').value = '';
            }
        }

        function handleWarrantyYears(e) {
            const row = e.target.closest('.product-row');
            if (e.target.value) {
                row.querySelector('.warranty-select').value = '';
            }
        }

        function calculateRowTotal(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitcost = parseFloat(row.querySelector('.unitcost-input').value) || 0;
            const total = quantity * unitcost;
            row.querySelector('.total-input').value = total.toFixed(2);
            calculateTotals();
        }

        function calculateTotals() {
            let subTotal = 0;
            let totalProducts = 0;

            document.querySelectorAll('.product-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
                const unitcost = parseFloat(row.querySelector('.unitcost-input')?.value) || 0;
                const total = quantity * unitcost;
                subTotal += total;
                totalProducts += quantity;
            });

            const discount = parseFloat(discountInputSidebar?.value) || 0;
            const serviceChargesEl = document.getElementById('service_charges');
            const serviceCharges = parseFloat(serviceChargesEl?.value) || 0;
            const finalTotal = subTotal - discount + serviceCharges;

            const totalProductsEl = document.getElementById('totalProducts');
            const subTotalEl = document.getElementById('subTotal');
            const discountDisplayEl = document.getElementById('discountDisplay');
            const serviceChargesDisplayEl = document.getElementById('serviceChargesDisplay');
            const totalAmountEl = document.getElementById('totalAmount');

            if (totalProductsEl) totalProductsEl.textContent = totalProducts;
            if (subTotalEl) subTotalEl.textContent = subTotal.toFixed(2);
            if (discountDisplayEl) discountDisplayEl.textContent = discount.toFixed(2);
            if (serviceChargesDisplayEl) serviceChargesDisplayEl.textContent = serviceCharges.toFixed(2);
            if (totalAmountEl) totalAmountEl.textContent = finalTotal.toFixed(2);
        }

        // Discount input change
        if (discountInputSidebar) {
            discountInputSidebar.addEventListener('input', function() {
                calculateTotals();
            });
        }

        // Service charges change
        const serviceChargesInput = document.getElementById('service_charges');
        if (serviceChargesInput) {
            serviceChargesInput.addEventListener('input', calculateTotals);
        }

        // Initialize events
        attachRowEvents();
        calculateTotals();

        // Prevent invalid payment type changes
        const paymentTypeSelect = document.getElementById('payment_type');
        if (paymentTypeSelect) {
            const originalType = paymentTypeSelect.getAttribute('data-original-type');

            paymentTypeSelect.addEventListener('change', function(e) {
                const newValue = e.target.value;

                // Credit Sales cannot change to anything (already disabled, but extra check)
                if (originalType === 'Credit Sales' && newValue !== 'Credit Sales') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Payment Type Change',
                        text: 'Credit Sales payment type cannot be changed.',
                        confirmButtonText: 'OK'
                    });

                    // Reset to original value
                    e.target.value = originalType;
                }
            });
        }
    });

    // Add SweetAlert2 fallback if not available
    if (typeof Swal === 'undefined') {
        window.Swal = {
            fire: function(options) {
                alert(options.text || options.title);
            }
        };
    }
</script>
@endpush
@endsection
