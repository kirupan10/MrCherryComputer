@extends('layouts.nexora')

@section('title', 'Edit Delivery')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">Edit Delivery #{{ $delivery->id }}</h2>
                <div class="text-muted mt-1">Update delivery details and tracking information</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ shop_route('deliveries.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 6l-6 6l6 6"/>
                            <path d="M3 12h18"/>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <x-alert />

        <form method="POST" action="{{ shop_route('deliveries.update', $delivery) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <rect x="3" y="7" width="18" height="13" rx="2"/>
                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                Delivery Overview
                            </h3>

                            <div class="mb-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M3 21l18 0" />
                                                        <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    {{ ucfirst($delivery->direction) }}
                                                </div>
                                                <div class="text-muted small">Delivery Type</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($delivery->cost)
                            <div class="mb-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-success text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    LKR {{ number_format($delivery->cost, 2) }}
                                                </div>
                                                <div class="text-muted small">Delivery Cost</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($delivery->delivery_date)
                            <div class="mb-3">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-info text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <rect x="4" y="5" width="16" height="16" rx="2"/>
                                                        <line x1="16" y1="3" x2="16" y2="7"/>
                                                        <line x1="8" y1="3" x2="8" y2="7"/>
                                                        <line x1="4" y1="11" x2="20" y2="11"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium">
                                                    {{ $delivery->delivery_date->format('d M Y') }}
                                                </div>
                                                <div class="text-muted small">{{ $delivery->delivery_date->format('H:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="alert alert-info mb-0">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <circle cx="12" cy="12" r="9"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Quick Tips</h4>
                                        <div class="text-muted">Update tracking details to keep accurate records. Cost entries will auto-create expense records.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Delivery Details</h3>

                            <div class="mb-3">
                                <label class="form-label required">Delivery Type</label>
                                <select name="direction" id="direction" class="form-select @error('direction') is-invalid @enderror" required>
                                    <option value="">Select...</option>
                                    <option value="incoming" {{ old('direction', $delivery->direction) == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                    <option value="outgoing" {{ old('direction', $delivery->direction) == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                    <option value="dropship" {{ old('direction', $delivery->direction) == 'dropship' ? 'selected' : '' }}>Dropship</option>
                                </select>
                                @error('direction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Tracking Number/Receipt</label>
                                    <input type="text" name="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror" value="{{ old('tracking_number', $delivery->tracking_number) }}" placeholder="Enter tracking number">
                                    @error('tracking_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">From Location</label>
                                    <input type="text" name="from_location" class="form-control @error('from_location') is-invalid @enderror" value="{{ old('from_location', $delivery->from_location) }}" placeholder="Sender location">
                                    @error('from_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">To Location</label>
                                    <input type="text" name="to_location" class="form-control @error('to_location') is-invalid @enderror" value="{{ old('to_location', $delivery->to_location) }}" placeholder="Destination location">
                                    @error('to_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Received By</label>
                                    <input type="text" name="received_by" class="form-control @error('received_by') is-invalid @enderror" value="{{ old('received_by', $delivery->received_by) }}" placeholder="Person who received">
                                    @error('received_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Delivery Date & Time</label>
                                    <input type="datetime-local" name="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" value="{{ old('delivery_date', optional($delivery->delivery_date)->format('Y-m-d\TH:i')) }}">
                                    @error('delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Payment Type</label>
                                    <select name="payment_type" id="paymentType" class="form-select @error('payment_type') is-invalid @enderror">
                                        <option value="">Select...</option>
                                        <option value="Paid" {{ old('payment_type', $delivery->payment_type) == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="COD" {{ old('payment_type', $delivery->payment_type) == 'COD' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2" id="costField" style="display:none;">
                                    <label class="form-label">Cost (LKR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">LKR</span>
                                        <input type="number" step="0.01" min="0" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', $delivery->cost) }}" placeholder="Enter cost">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if($delivery->expense_id)
                                        <small class="text-muted">Linked to <a href="{{ shop_route('expenses.edit', $delivery->expense_id) }}" target="_blank">Expense #{{ $delivery->expense_id }}</a></small>
                                    @else
                                        <small class="text-muted">Will create an expense record automatically</small>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Details Section -->
                            <div class="mb-2">
                                <label class="form-label">Courier Service</label>
                                <input type="text" name="details[courier_service]" class="form-control" value="{{ old('details.courier_service', $delivery->details['courier_service'] ?? '') }}" placeholder="e.g., DHL, FedEx, Pronto, Local courier">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Package Weight</label>
                                    <input type="text" name="details[package_weight]" class="form-control" value="{{ old('details.package_weight', $delivery->details['package_weight'] ?? '') }}" placeholder="e.g., 2.5 kg">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Order Reference</label>
                                    <input type="text" name="details[order_reference]" class="form-control" value="{{ old('details.order_reference', $delivery->details['order_reference'] ?? '') }}" placeholder="Related order number">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Add any additional notes or details...">{{ old('notes', $delivery->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ shop_route('deliveries.index') }}" class="btn btn-outline-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
                                    </svg>
                                    Cancel
                                </a>
                                <button class="btn btn-primary" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    Update Delivery
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
        const paymentType = document.getElementById('paymentType');
        const costField = document.getElementById('costField');

        if (paymentType && costField) {
            paymentType.addEventListener('change', function() {
                if (this.value === 'Paid') {
                    costField.style.display = 'block';
                } else {
                    costField.style.display = 'none';
                }
            });

            // Trigger on load
            if (paymentType.value === 'Paid') {
                costField.style.display = 'block';
            }
        }
    });
</script>
@endpush
