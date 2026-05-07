@extends('shop-types.tech.layouts.nexora')

@section('title', 'Create Vendor Purchase')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <x-alert />

        <!-- Page Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title mb-1" style="font-weight: 700; color: #1a1a1a;">
                            Create New Vendor Purchase
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Record a new vendor purchase</p>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-lg px-4 py-2" style="font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 -6"/>
                            <path d="M5 12l6 6"/>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('purchases.store') }}" method="POST" class="card">
                    @csrf

                    <!-- Vendor Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Vendor Information</h3>

                        <div class="mb-3">
                            <label class="form-label">Select Supplier <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select id="vendor_select" class="form-select" required>
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}"
                                                data-name="{{ $vendor->name }}"
                                                data-phone="{{ $vendor->phone }}"
                                                data-email="{{ $vendor->email }}"
                                                data-address="{{ $vendor->address }}"
                                                data-company="{{ $vendor->company_name ?? '' }}">
                                            {{ $vendor->name }}{{ $vendor->company_name ? ' - ' . $vendor->company_name : '' }}{{ $vendor->phone ? ' - ' . $vendor->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary btn-icon" id="addVendorBtn" title="Add New Supplier">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14"/>
                                        <path d="M5 12l14 0"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="vendor_id" id="vendor_id" value="{{ old('vendor_id') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number') }}" placeholder="Invoice or PO number">
                            @error('reference_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-divider"></div>

                    <!-- Purchase Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Purchase Details</h3>

                        <!-- Purchase Type Selection -->
                        <div class="mb-3">
                            <label class="form-label d-block">Purchase Type <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="purchase_type" id="purchase_type_cash" value="cash"
                                       @if(old('purchase_type') == 'cash') checked @endif>
                                <label class="btn btn-outline-primary" for="purchase_type_cash">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/><path d="M5 7m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/><path d="M17 7m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/><path d="M5 14m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z"/></svg>
                                    Cash
                                </label>

                                <input type="radio" class="btn-check" name="purchase_type" id="purchase_type_cheque" value="cheque"
                                       @if(old('purchase_type') == 'cheque') checked @endif>
                                <label class="btn btn-outline-primary" for="purchase_type_cheque">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h10" stroke-dasharray="1 2"/><path d="M7 13h10" stroke-dasharray="1 2"/></svg>
                                    Cheque
                                </label>

                                <input type="radio" class="btn-check" name="purchase_type" id="purchase_type_credit" value="credit"
                                       @if(old('purchase_type', 'credit') == 'credit') checked @endif>
                                <label class="btn btn-outline-primary" for="purchase_type_credit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 15h.01"/><path d="M11 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-2v1h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-2v1a1 1 0 0 1 -1 1h-1" stroke-dasharray="1 2"/></svg>
                                    Credit
                                </label>
                            </div>
                            @error('purchase_type')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror"
                                           value="{{ old('total_amount') }}" placeholder="0.00" step="0.01" min="0.01" required>
                                </div>
                                @error('total_amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror"
                                       value="{{ old('purchase_date', now()->toDateString()) }}" required>
                                @error('purchase_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row" id="credit-days-section" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Credit Days <span class="text-danger">*</span></label>
                                <input type="number" name="credit_days" class="form-control @error('credit_days') is-invalid @enderror"
                                       value="{{ old('credit_days', 30) }}" min="1">
                                <small class="text-muted">Due date will be calculated based on this</small>
                                @error('credit_days')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="dueDate" disabled>
                                <small class="text-muted">Calculated automatically</small>
                            </div>
                        </div>

                        <!-- Cheque Details Section -->
                        <div id="cheque-details-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cheque Number <span class="text-danger">*</span></label>
                                    <input type="text" name="cheque_number" id="cheque_number" class="form-control @error('cheque_number') is-invalid @enderror"
                                           value="{{ old('cheque_number') }}" placeholder="Enter cheque number">
                                    @error('cheque_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cheque Date <span class="text-danger">*</span></label>
                                    <input type="date" name="cheque_date" id="cheque_date" class="form-control @error('cheque_date') is-invalid @enderror"
                                           value="{{ old('cheque_date') }}">
                                    @error('cheque_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                                           value="{{ old('bank_name') }}" placeholder="Enter bank name">
                                    @error('bank_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Branch Name</label>
                                    <input type="text" name="branch_name" id="branch_name" class="form-control @error('branch_name') is-invalid @enderror"
                                           value="{{ old('branch_name') }}" placeholder="Enter branch name">
                                    @error('branch_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Drawer Name (Supplier Name on Cheque)</label>
                                <input type="text" name="drawer_name" id="drawer_name" class="form-control @error('drawer_name') is-invalid @enderror"
                                       value="{{ old('drawer_name') }}" placeholder="Name on cheque">
                                @error('drawer_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-divider"></div>

                    <!-- Additional Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Additional Information</h3>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="3" placeholder="Add any additional notes about this purchase">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 18a4.6 4.6 0 0 1 0 -9a5 5 0 0 1 5 -5v-2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1v-2a4.6 4.6 0 0 1 0 -9"/>
                                <path d="M12 7v6m3 -3h-6"/>
                            </svg>
                            Create Purchase
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card bg-light-primary">
                    <div class="card-body">
                        <h4 class="card-title mb-3" style="font-weight: 600;">Help & Tips</h4>
                        <ul class="list-unstyled space-y">
                            <li>
                                <strong>Purchase Type:</strong>
                                <small class="d-block mt-1">
                                    <strong>Cash:</strong> Immediate payment - transaction completed instantly<br>
                                    <strong>Cheque:</strong> Payment by cheque - requires cheque details (number, bank, date). Details will be recorded in Cheque Management<br>
                                    <strong>Credit:</strong> Payment on credit terms with due date
                                </small>
                            </li>
                            <li class="mt-2">
                                <strong>Vendor Selection:</strong> Select the supplier from the dropdown. You can also add a new supplier using the + button.
                            </li>
                            <li class="mt-2">
                                <strong>Purchase Amount:</strong> Enter the total purchase amount in LKR currency
                            </li>
                            <li class="mt-2">
                                <strong>Cheque Details:</strong> Only required for cheque purchases. Enter cheque number, bank details, and cheque date for proper tracking
                            </li>
                            <li class="mt-2">
                                <strong>Credit Days:</strong> Only required for credit purchases. Number of days allowed to pay (e.g., 30 days). Due date will be calculated automatically
                            </li>
                            <li class="mt-2">
                                <strong>Reference Number:</strong> Optional - invoice number or PO number from vendor for tracking
                            </li>
                            <li class="mt-2">
                                <strong>Notes:</strong> Add any special terms, conditions, or important information about this purchase
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="{{ asset('vendor/tom-select/tom-select.bootstrap5.min.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/tom-select/tom-select-latest.complete.min.js') }}"></script>

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
.ts-wrapper {
    flex: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const purchaseDate = document.querySelector('input[name="purchase_date"]');
    const creditDays = document.querySelector('input[name="credit_days"]');
    const dueDate = document.getElementById('dueDate');
    const creditDaysSection = document.getElementById('credit-days-section');
    const purchaseTypeRadios = document.querySelectorAll('input[name="purchase_type"]');

    // Initialize TomSelect for vendor dropdown
    const vendorSelectEl = document.getElementById('vendor_select');
    const tomSelectInstance = new TomSelect('#vendor_select', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Search supplier by name, company, or phone",
        searchField: ['text'],
        maxOptions: null,
        render: {
            option: function(data, escape) {
                const parts = data.text.split(' - ');
                const name = parts[0] || '';
                const company = parts[1] || '';
                const phone = parts[2] || '';
                return '<div class="p-2">' +
                    '<div class="name">' + escape(name) + '</div>' +
                    (company ? '<div class="details">' + escape(company) + '</div>' : '') +
                    (phone ? '<div class="details"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/></svg> ' + escape(phone) + '</div>' : '') +
                    '</div>';
            },
            item: function(data, escape) {
                const parts = data.text.split(' - ');
                const name = parts[0] || '';
                const company = parts[1] || '';
                return '<div>' +
                    '<span class="fw-bold">' + escape(name) + '</span>' +
                    (company ? ' <small class="text-muted">(' + escape(company) + ')</small>' : '') +
                    '</div>';
            }
        }
    });

    const chequeDetailsSection = document.getElementById('cheque-details-section');
    const chequeNumber = document.getElementById('cheque_number');
    const chequeDate = document.getElementById('cheque_date');
    const bankName = document.getElementById('bank_name');

    function updateCreditDaysVisibility() {
        const selectedType = document.querySelector('input[name="purchase_type"]:checked').value;

        // Handle Credit Days Section
        if (selectedType === 'credit') {
            creditDaysSection.style.display = '';
            creditDays.setAttribute('required', 'required');
        } else {
            creditDaysSection.style.display = 'none';
            creditDays.removeAttribute('required');
            creditDays.value = 30; // Reset to default
        }

        // Handle Cheque Details Section
        if (selectedType === 'cheque') {
            chequeDetailsSection.style.display = '';
            chequeNumber.setAttribute('required', 'required');
            chequeDate.setAttribute('required', 'required');
            bankName.setAttribute('required', 'required');
        } else {
            chequeDetailsSection.style.display = 'none';
            chequeNumber.removeAttribute('required');
            chequeDate.removeAttribute('required');
            bankName.removeAttribute('required');
        }
    }

    function calculateDueDate() {
        if (purchaseDate.value && creditDays.value) {
            const date = new Date(purchaseDate.value);
            date.setDate(date.getDate() + parseInt(creditDays.value));
            dueDate.value = date.toISOString().split('T')[0];
        }
    }

    purchaseDate.addEventListener('change', calculateDueDate);
    creditDays.addEventListener('change', calculateDueDate);

    purchaseTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateCreditDaysVisibility);
    });

    // Initialize on page load
    updateCreditDaysVisibility();
    calculateDueDate();

    // Vendor Selection Handler with TomSelect
    const vendorId = document.getElementById('vendor_id');

    tomSelectInstance.on('change', function(value) {
        if (value) {
            // Set vendor ID when a vendor is selected
            vendorId.value = value;
        } else {
            // Clear vendor ID when selection is cleared
            vendorId.value = '';
        }
    });
});
</script>

<!-- Add Vendor/Supplier Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVendorModalLabel">Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVendorForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor_modal_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vendor_modal_name" name="name" required>
                            <div class="invalid-feedback" id="name_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_modal_company" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="vendor_modal_company" name="company_name">
                            <div class="invalid-feedback" id="company_name_error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor_modal_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="vendor_modal_phone" name="phone">
                            <div class="invalid-feedback" id="phone_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_modal_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="vendor_modal_email" name="email">
                            <div class="invalid-feedback" id="email_error"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="vendor_modal_address" class="form-label">Address</label>
                        <textarea class="form-control" id="vendor_modal_address" name="address" rows="2"></textarea>
                        <div class="invalid-feedback" id="address_error"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor_modal_tax" class="form-label">Tax Number</label>
                            <input type="text" class="form-control" id="vendor_modal_tax" name="tax_number">
                            <div class="invalid-feedback" id="tax_number_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_modal_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="vendor_modal_status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback" id="status_error"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="vendor_modal_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="vendor_modal_notes" name="notes" rows="2"></textarea>
                        <div class="invalid-feedback" id="notes_error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitCreateVendor()">
                    <span id="createVendorBtnText">Create Supplier</span>
                    <span id="createVendorSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Open create vendor modal
function openCreateVendorModal() {
    console.log('Opening create vendor modal...');

    try {
        // Reset form
        const form = document.getElementById('addVendorForm');
        if (!form) {
            console.error('Create vendor form not found!');
            alert('Error: Vendor form not found. Please refresh the page.');
            return;
        }
        form.reset();

        // Clear errors
        document.querySelectorAll('#addVendorModal .invalid-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('#addVendorModal .form-control').forEach(el => el.classList.remove('is-invalid'));

        // Show modal
        const modalEl = document.getElementById('addVendorModal');
        if (!modalEl) {
            console.error('Create vendor modal element not found!');
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
            backdrop.id = 'addVendorModalBackdrop';
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
                    const backdrop = document.getElementById('addVendorModalBackdrop');
                    if (backdrop) backdrop.remove();
                    document.body.classList.remove('modal-open');
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
                    const backdrop = document.getElementById('addVendorModalBackdrop');
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
        alert('Error opening vendor form: ' + error.message);
    }
}

// Submit create vendor form
function submitCreateVendor() {
    const form = document.getElementById('addVendorForm');
    const formData = new FormData(form);
    const btn = document.querySelector('#addVendorModal .btn-primary');
    const btnText = document.getElementById('createVendorBtnText');
    const spinner = document.getElementById('createVendorSpinner');

    // Clear previous errors
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

    // Show loading
    btn.disabled = true;
    btnText.classList.add('d-none');
    spinner.classList.remove('d-none');

    fetch('{{ route("vendors.store") }}', {
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
            const modalEl = document.getElementById('addVendorModal');
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
                const backdrop = document.getElementById('addVendorModalBackdrop');
                if (backdrop) backdrop.remove();
                document.body.classList.remove('modal-open');
            }

            // Add new vendor to TomSelect dropdown
            const vendorSelectEl = document.getElementById('vendor_select');
            const tomSelect = vendorSelectEl.tomselect;

            if (tomSelect) {
                // Build option text
                const optionText = data.vendor.name +
                    (data.vendor.company_name ? ' - ' + data.vendor.company_name : '') +
                    (data.vendor.phone ? ' - ' + data.vendor.phone : '');

                // Add to TomSelect
                tomSelect.addOption({
                    value: data.vendor.id,
                    text: optionText
                });

                // Add data attributes to the DOM option (for later access)
                const option = vendorSelectEl.querySelector(`option[value="${data.vendor.id}"]`);
                if (option) {
                    option.setAttribute('data-name', data.vendor.name || '');
                    option.setAttribute('data-phone', data.vendor.phone || '');
                    option.setAttribute('data-email', data.vendor.email || '');
                    option.setAttribute('data-address', data.vendor.address || '');
                    option.setAttribute('data-company', data.vendor.company_name || '');
                }

                // Select the new vendor
                tomSelect.setValue(data.vendor.id);
            }

            // Show success message
            console.log('Vendor created successfully');
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorDiv = document.getElementById(field + '_error');
                    const input = document.getElementById('vendor_modal_' + field);
                    if (errorDiv && input) {
                        errorDiv.textContent = data.errors[field][0];
                        input.classList.add('is-invalid');
                    }
                });
            } else {
                alert('Error: ' + (data.message || 'Failed to create vendor'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the vendor');
    })
    .finally(() => {
        btn.disabled = false;
        btnText.classList.remove('d-none');
        spinner.classList.add('d-none');
    });
}

// Attach event listener on page load
document.addEventListener('DOMContentLoaded', function() {
    const addVendorBtn = document.getElementById('addVendorBtn');
    if (addVendorBtn) {
        addVendorBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add vendor button clicked');
            openCreateVendorModal();
        });
        console.log('Add vendor button listener attached');
    } else {
        console.error('Add vendor button not found!');
    }
});
</script>
@endsection
