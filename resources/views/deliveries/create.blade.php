@extends('layouts.nexora')

@section('title', 'Record Delivery')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Record Delivery') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <x-alert />

        <div class="row row-cards">
            <form method="POST" action="{{ shop_route('deliveries.store') }}">
                @csrf

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bulb me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7" />
                                        <path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3" />
                                        <path d="M9.7 17l4.6 0" />
                                    </svg>
                                    Quick Tips
                                </h3>

                                <div class="list-group list-group-flush mb-3">
                                    <div class="list-group-item px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar bg-success text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"/></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="text-truncate">
                                                    <strong>Track Every Parcel</strong>
                                                </div>
                                                <div class="text-muted small">Record incoming & outgoing</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list-group-item px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar bg-info text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12l.01 0"/><path d="M13 12l2 0"/><path d="M9 16l.01 0"/><path d="M13 16l2 0"/></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="text-truncate">
                                                    <strong>Tracking Numbers</strong>
                                                </div>
                                                <div class="text-muted small">Keep receipts organized</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list-group-item px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar bg-warning text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="text-truncate">
                                                    <strong>Payment Type</strong>
                                                </div>
                                                <div class="text-muted small">Paid or COD tracking</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list-group-item px-0">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar bg-purple text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4"/><path d="M5 21l0 -10.15"/><path d="M19 21l0 -10.15"/><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4"/></svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="text-truncate">
                                                    <strong>Auto-Expense</strong>
                                                </div>
                                                <div class="text-muted small">Paid costs tracked automatically</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <div class="d-flex">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Pro Tip!</h4>
                                            <div class="text-muted">When marking payment as "Paid", the cost will automatically create an expense record for better financial tracking.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title">
                                        {{ __('New Delivery Record') }}
                                    </h3>
                                </div>

                                <div class="card-actions">
                                    <a href="{{ shop_route('deliveries.index') }}" class="btn-action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-12">
                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-12">

                            <div class="mb-2">
                                <label class="form-label required">Delivery Type</label>
                                <select name="direction" id="direction" class="form-select @error('direction') is-invalid @enderror" required>
                                    <option value="">Select...</option>
                                    <option value="incoming" {{ old('direction') == 'incoming' ? 'selected' : '' }}>Receiving to Our Shop</option>
                                    <option value="outgoing" {{ old('direction') == 'outgoing' ? 'selected' : '' }}>Sending from Our Shop</option>
                                    <option value="dropship" {{ old('direction') == 'dropship' ? 'selected' : '' }}>Direct Delivery (Dealer to Customer)</option>
                                </select>
                                @error('direction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">Tracking Number/Receipt</label>
                                    <input type="text" name="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" value="{{ old('tracking_number') }}" placeholder="Enter tracking number">
                                    @error('tracking_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">From Location</label>
                                    <input type="text" name="from_location" class="form-control @error('from_location') is-invalid @enderror" value="{{ old('from_location') }}" placeholder="Sender location">
                                    @error('from_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">To Location</label>
                                    <input type="text" name="to_location" class="form-control @error('to_location') is-invalid @enderror" value="{{ old('to_location') }}" placeholder="Destination location">
                                    @error('to_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label" id="receivedByLabel">Received By</label>
                                    <input type="text" id="receivedByInput" name="received_by" class="form-control @error('received_by') is-invalid @enderror" value="{{ old('received_by') }}" placeholder="Person who received">
                                    @error('received_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">Delivery Date & Time</label>
                                    <input type="datetime-local" name="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date', now()->format('Y-m-d\TH:i')) }}">
                                    @error('delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6" id="paymentTypeField">
                                <div class="mb-2">
                                    <label class="form-label">Payment Type</label>
                                    <select name="payment_type" id="paymentType" class="form-select @error('payment_type') is-invalid @enderror">
                                        <option value="">Select...</option>
                                        <option value="Paid" {{ old('payment_type') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="COD" {{ old('payment_type') == 'COD' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6" id="costField" style="display:none;">
                                <div class="mb-2">
                                    <label class="form-label">Cost (LKR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" min="0" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost') }}" placeholder="Enter cost">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">This will create an expense record automatically</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="form-label">Courier Service</label>
                                    <input type="text" name="details[courier_service]" class="form-control" value="{{ old('details.courier_service') }}" placeholder="e.g., DHL, FedEx, Pronto, Local courier">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">Package Weight</label>
                                    <input type="text" name="details[package_weight]" class="form-control" value="{{ old('details.package_weight') }}" placeholder="e.g., 2.5 kg">
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">Order Reference</label>
                                    <input type="text" name="details[order_reference]" class="form-control" value="{{ old('details.order_reference') }}" placeholder="Related order number">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Add any additional notes or details...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ shop_route('deliveries.index') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Save Delivery
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const direction = document.getElementById('direction');
        const paymentType = document.getElementById('paymentType');
        const paymentTypeField = document.getElementById('paymentTypeField');
        const costField = document.getElementById('costField');
        const fromLocation = document.querySelector('input[name="from_location"]');
        const toLocation = document.querySelector('input[name="to_location"]');
        const receivedByLabel = document.getElementById('receivedByLabel');
        const receivedByInput = document.getElementById('receivedByInput');
        const shopName = '{{ auth()->user()->shop->name ?? "Our Shop" }}';

        function updateCostFieldVisibility() {
            const directionValue = direction.value;
            const paymentValue = paymentType.value;

            // Show cost field only for:
            // - Incoming + COD (we pay)
            // - Outgoing + Paid (we pay)
            if ((directionValue === 'incoming' && paymentValue === 'COD') ||
                (directionValue === 'outgoing' && paymentValue === 'Paid')) {
                costField.style.display = 'block';
            } else {
                costField.style.display = 'none';
            }
        }

        function updatePaymentTypeVisibility() {
            const directionValue = direction.value;

            // Hide payment type for dropship (no cost involved)
            if (directionValue === 'dropship') {
                paymentTypeField.style.display = 'none';
                paymentType.value = '';
            } else {
                paymentTypeField.style.display = 'block';
            }
        }

        function updateReceivedByField() {
            const directionValue = direction.value;

            if (directionValue === 'incoming') {
                receivedByLabel.textContent = 'Received By';
                receivedByInput.placeholder = 'Person who received';
            } else if (directionValue === 'outgoing') {
                receivedByLabel.textContent = 'Sent By';
                receivedByInput.placeholder = 'Person who sent';
            } else if (directionValue === 'dropship') {
                receivedByLabel.textContent = 'Contact Person';
                receivedByInput.placeholder = 'Contact person';
            }
        }

        function updateLocationFields() {
            const directionValue = direction.value;

            if (directionValue === 'incoming') {
                // Receiving to our shop - To Location is shop name, From Location is editable
                toLocation.value = shopName;
                toLocation.readOnly = true;
                toLocation.classList.add('bg-light');
                fromLocation.readOnly = false;
                fromLocation.classList.remove('bg-light');
                // Don't auto-fill from location
                if (!fromLocation.value || fromLocation.value === shopName) {
                    fromLocation.value = '';
                }
            } else if (directionValue === 'outgoing') {
                // Sending from our shop - From Location is shop name, To Location is editable
                fromLocation.value = shopName;
                fromLocation.readOnly = true;
                fromLocation.classList.add('bg-light');
                toLocation.readOnly = false;
                toLocation.classList.remove('bg-light');
                // Don't auto-fill to location
                if (!toLocation.value || toLocation.value === shopName) {
                    toLocation.value = '';
                }
            } else if (directionValue === 'dropship') {
                // Direct delivery - both editable
                fromLocation.readOnly = false;
                toLocation.readOnly = false;
                fromLocation.classList.remove('bg-light');
                toLocation.classList.remove('bg-light');
                if (!fromLocation.value || fromLocation.value === shopName) {
                    fromLocation.value = '';
                }
                if (!toLocation.value || toLocation.value === shopName) {
                    toLocation.value = '';
                }
            }
        }

        if (direction && paymentType && costField) {
            direction.addEventListener('change', function() {
                updatePaymentTypeVisibility();
                updateCostFieldVisibility();
                updateReceivedByField();
                updateLocationFields();
            });

            paymentType.addEventListener('change', updateCostFieldVisibility);

            // Trigger on load
            updatePaymentTypeVisibility();
            updateCostFieldVisibility();
            updateReceivedByField();
            updateLocationFields();
        }
    });
</script>
@endpush
