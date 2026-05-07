@extends('shop-types.tech.layouts.nexora')

@section('title', 'Edit Vendor Purchase')

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
                            Edit Vendor Purchase
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">{{ $creditPurchase->vendor_name }}</p>
                    </div>
                    <a href="{{ route('purchases.show', $creditPurchase->id) }}" class="btn btn-secondary btn-lg px-4 py-2" style="font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 -6"/>
                            <path d="M5 12l6 6"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('purchases.update', $creditPurchase->id) }}" method="POST" class="card">
                    @csrf
                    @method('PUT')

                    <!-- Vendor Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Vendor Information</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror"
                                       value="{{ old('vendor_name', $creditPurchase->vendor_name) }}" placeholder="Enter vendor name" required>
                                @error('vendor_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reference Number</label>
                                <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                                       value="{{ old('reference_number', $creditPurchase->reference_number) }}" placeholder="Invoice or PO number">
                                @error('reference_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendor Email</label>
                                <input type="email" name="vendor_email" class="form-control @error('vendor_email') is-invalid @enderror"
                                       value="{{ old('vendor_email', $creditPurchase->vendor_email) }}" placeholder="vendor@example.com">
                                @error('vendor_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendor Phone</label>
                                <input type="text" name="vendor_phone" class="form-control @error('vendor_phone') is-invalid @enderror"
                                       value="{{ old('vendor_phone', $creditPurchase->vendor_phone) }}" placeholder="+94-XXX-XXX-XXXX">
                                @error('vendor_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vendor Address</label>
                            <textarea name="vendor_address" class="form-control @error('vendor_address') is-invalid @enderror"
                                      rows="2" placeholder="Enter vendor address">{{ old('vendor_address', $creditPurchase->vendor_address) }}</textarea>
                            @error('vendor_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-divider"></div>

                    <!-- Purchase Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Purchase Details</h3>

                        <!-- Purchase Type Display -->
                        <div class="mb-3">
                            <label class="form-label">Purchase Type</label>
                            @if($creditPurchase->purchase_type === 'cash')
                                <div class="form-control-plaintext">
                                    <span class="badge bg-success">Cash</span>
                                </div>
                            @elseif($creditPurchase->purchase_type === 'cheque')
                                <div class="form-control-plaintext">
                                    <span class="badge bg-info">Cheque</span>
                                </div>
                            @elseif($creditPurchase->purchase_type === 'credit')
                                <div class="form-control-plaintext">
                                    <span class="badge bg-warning">Credit</span>
                                </div>
                            @endif
                            <small class="text-muted d-block mt-2">(Cannot be changed after creation)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror"
                                           value="{{ old('total_amount', $creditPurchase->total_amount) }}" placeholder="0.00" step="0.01" min="0.01" required>
                                </div>
                                <small class="text-muted">Current: LKR {{ number_format($creditPurchase->total_amount, 2) }}</small>
                                @error('total_amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror"
                                       value="{{ old('purchase_date', $creditPurchase->purchase_date->toDateString()) }}" required>
                                @error('purchase_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row" id="credit-days-section">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Credit Days <span class="text-danger">*</span></label>
                                <input type="number" name="credit_days" class="form-control @error('credit_days') is-invalid @enderror"
                                       value="{{ old('credit_days', $creditPurchase->credit_days) }}" min="1" required>
                                <small class="text-muted">Due date will be recalculated</small>
                                @error('credit_days')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Current Due Date</label>
                                <input type="date" class="form-control" value="{{ $creditPurchase->due_date->toDateString() }}" disabled>
                                <small class="text-muted">Will be recalculated on save</small>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <strong>Payment Status:</strong>
                            <span class="badge {{ $creditPurchase->status === 'paid' ? 'bg-success' : ($creditPurchase->status === 'partial' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($creditPurchase->status) }}
                            </span><br>
                            <small class="mt-2 d-block">Paid: LKR {{ number_format($creditPurchase->paid_amount, 2) }} | Due: LKR {{ number_format($creditPurchase->due_amount, 2) }}</small>
                        </div>
                    </div>

                    <div class="card-divider"></div>

                    <!-- Additional Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Additional Information</h3>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="3" placeholder="Add any additional notes about this purchase">{{ old('notes', $creditPurchase->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <a href="{{ route('purchases.show', $creditPurchase->id) }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/>
                            </svg>
                            Update Purchase
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card bg-light-warning">
                    <div class="card-body">
                        <h4 class="card-title mb-3" style="font-weight: 600;">Important Notes</h4>
                        <ul class="list-unstyled space-y">
                            <li>
                                <strong>Editing:</strong> You can edit vendor information and purchase amount
                            </li>
                            <li class="mt-2">
                                <strong>Amount Changes:</strong> If you change the total amount, the due amount will be adjusted accordingly
                            </li>
                            <li class="mt-2">
                                <strong>Payments:</strong> Recorded payments cannot be edited here. Delete and re-record if needed
                            </li>
                            <li class="mt-2">
                                <strong>Credit Days:</strong> Changing credit days will recalculate the due date
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const creditDaysSection = document.getElementById('credit-days-section');
    const creditDays = document.querySelector('input[name="credit_days"]');
    const purchaseDate = document.querySelector('input[name="purchase_date"]');
    const purchaseType = '{{ $creditPurchase->purchase_type }}';

    // Hide credit days section if not a credit purchase
    if (purchaseType !== 'credit') {
        creditDaysSection.style.display = 'none';
    }

    // Calculate due date on purchase date or credit days change
    function calculateDueDate() {
        if (purchaseDate.value && creditDays.value) {
            const date = new Date(purchaseDate.value);
            date.setDate(date.getDate() + parseInt(creditDays.value));
            const dueDate = document.getElementById('dueDate');
            if (dueDate) {
                dueDate.value = date.toISOString().split('T')[0];
            }
        }
    }

    if (purchaseType === 'credit') {
        purchaseDate.addEventListener('change', calculateDueDate);
        creditDays.addEventListener('change', calculateDueDate);
        calculateDueDate();
    }
});
</script>
@endsection
