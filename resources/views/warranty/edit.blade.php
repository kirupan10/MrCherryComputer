@extends('layouts.nexora')

@section('title', 'Edit Warranty Claim')

@push('page-styles')
<link href="{{ asset('vendor/tom-select/tom-select.bootstrap5.min.css') }}" rel="stylesheet">
<style>
/* Tom-Select styling */
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

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Service Management
                </div>
                <h2 class="page-title">
                    Edit Warranty Claim #{{ $warrantyClaim->id }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ shop_route('warranty-claims.index') }}" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <form action="{{ shop_route('warranty-claims.update', $warrantyClaim->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Claim Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Product</label>
                                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id', $warrantyClaim->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}@if($product->code) - {{ $product->code }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <script src="{{ asset('vendor/tom-select/tom-select-latest.complete.min.js') }}"></script>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Customer</label>
                                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', $warrantyClaim->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}@if($customer->phone) - {{ $customer->phone }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Serial Number</label>
                                    <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                                           value="{{ old('serial_number', $warrantyClaim->serial_number) }}" placeholder="Enter serial number" required>
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Vendor</label>
                                    <input type="text" name="vendor" class="form-control @error('vendor') is-invalid @enderror"
                                           value="{{ old('vendor', $warrantyClaim->vendor) }}" placeholder="Enter vendor name">
                                    @error('vendor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label required">Issue Description</label>
                                    <textarea name="issue_description" class="form-control @error('issue_description') is-invalid @enderror"
                                              rows="4" placeholder="Describe the issue in detail" required>{{ old('issue_description', $warrantyClaim->issue_description) }}</textarea>
                                    @error('issue_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Shipping Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Sending Method</label>
                                    <select name="sending_method" id="sending_method" class="form-select @error('sending_method') is-invalid @enderror" required>
                                        <option value="courier" {{ old('sending_method', $warrantyClaim->sending_method) == 'courier' ? 'selected' : '' }}>Courier</option>
                                        <option value="handover" {{ old('sending_method', $warrantyClaim->sending_method) == 'handover' ? 'selected' : '' }}>Handover</option>
                                        <option value="bus" {{ old('sending_method', $warrantyClaim->sending_method) == 'bus' ? 'selected' : '' }}>Bus</option>
                                    </select>
                                    @error('sending_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3" id="tracking_number_group">
                                    <label class="form-label">Tracking Number</label>
                                    <input type="text" name="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror"
                                           value="{{ old('tracking_number', $warrantyClaim->tracking_number) }}" placeholder="Enter tracking number">
                                    @error('tracking_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">For courier shipments only</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sending Date</label>
                                    <input type="date" name="sending_date" class="form-control @error('sending_date') is-invalid @enderror"
                                           value="{{ old('sending_date', $warrantyClaim->sending_date?->format('Y-m-d')) }}">
                                    @error('sending_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warranty Claim Receipt</label>
                                    <input type="file" name="claim_receipt_file" class="form-control @error('claim_receipt_file') is-invalid @enderror"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    @error('claim_receipt_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($warrantyClaim->claim_receipt_file)
                                        <small class="form-hint">
                                            Current file:
                                            <a href="{{ asset('storage/' . $warrantyClaim->claim_receipt_file) }}" target="_blank">View File</a>
                                        </small>
                                    @endif
                                    <small class="form-hint d-block">PDF, JPG, PNG (Max: 5MB)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Status & Timeline</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $warrantyClaim->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="sent" {{ old('status', $warrantyClaim->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="in_progress" {{ old('status', $warrantyClaim->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="repaired" {{ old('status', $warrantyClaim->status) == 'repaired' ? 'selected' : '' }}>Repaired</option>
                                    <option value="replaced" {{ old('status', $warrantyClaim->status) == 'replaced' ? 'selected' : '' }}>Replaced</option>
                                    <option value="rejected" {{ old('status', $warrantyClaim->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="completed" {{ old('status', $warrantyClaim->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Expected Return Date</label>
                                <input type="date" name="expected_return_date" class="form-control @error('expected_return_date') is-invalid @enderror"
                                       value="{{ old('expected_return_date', $warrantyClaim->expected_return_date?->format('Y-m-d')) }}">
                                @error('expected_return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Actual Return Date</label>
                                <input type="date" name="actual_return_date" class="form-control @error('actual_return_date') is-invalid @enderror"
                                       value="{{ old('actual_return_date', $warrantyClaim->actual_return_date?->format('Y-m-d')) }}">
                                @error('actual_return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Resolution Notes</label>
                                <textarea name="resolution_notes" class="form-control @error('resolution_notes') is-invalid @enderror"
                                          rows="4" placeholder="Enter resolution details">{{ old('resolution_notes', $warrantyClaim->resolution_notes) }}</textarea>
                                @error('resolution_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Update Warranty Claim
                            </button>
                            <a href="{{ shop_route('warranty-claims.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('page-scripts')
<script>
window.addEventListener('load', function() {
    // Initialize TomSelect for Product dropdown
    new TomSelect('#product_id', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Search product by name or code",
        searchField: ['text'],
        maxOptions: null,
        render: {
            option: function(data, escape) {
                const parts = data.text.split(' - ');
                const name = parts[0] || '';
                const code = parts[1] || '';
                return '<div class="p-2">' +
                    '<div class="fw-bold text-dark">' + escape(name) + '</div>' +
                    (code ? '<div class="small text-muted mt-1">Code: ' + escape(code) + '</div>' : '') +
                    '</div>';
            },
            item: function(data, escape) {
                const parts = data.text.split(' - ');
                const name = parts[0] || '';
                const code = parts[1] || '';
                return '<div>' +
                    '<span class="fw-bold">' + escape(name) + '</span>' +
                    (code ? ' <small class="text-muted">(' + escape(code) + ')</small>' : '') +
                    '</div>';
            }
        }
    });

    // Initialize TomSelect for Customer dropdown
    new TomSelect('#customer_id', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Search customer by name or phone",
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

    // Tracking number toggle
    const sendingMethodSelect = document.getElementById('sending_method');
    const trackingNumberGroup = document.getElementById('tracking_number_group');

    function toggleTrackingNumber() {
        if (sendingMethodSelect.value === 'courier') {
            trackingNumberGroup.style.display = 'block';
        } else {
            trackingNumberGroup.style.display = 'none';
        }
    }

    sendingMethodSelect.addEventListener('change', toggleTrackingNumber);
    toggleTrackingNumber();
});
</script>
@endpush
@endsection
