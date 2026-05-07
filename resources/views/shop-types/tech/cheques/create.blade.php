@extends('shop-types.tech.layouts.nexora')

@section('title', 'Create Cheque')

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
                            Create New Cheque
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Register a new payment cheque</p>
                    </div>
                    <a href="{{ route('cheques.index') }}" class="btn btn-secondary btn-lg px-4 py-2" style="font-weight: 600;">
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
                <form action="{{ route('cheques.store') }}" method="POST" class="card">
                    @csrf

                    <!-- Cheque Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Cheque Details</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cheque Number <span class="text-danger">*</span></label>
                                <input type="text" name="cheque_number" class="form-control @error('cheque_number') is-invalid @enderror"
                                       value="{{ old('cheque_number') }}" placeholder="Enter cheque number" required>
                                @error('cheque_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cheque Date <span class="text-danger">*</span></label>
                                <input type="date" name="cheque_date" class="form-control @error('cheque_date') is-invalid @enderror"
                                       value="{{ old('cheque_date') }}" required>
                                @error('cheque_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount') }}" placeholder="0.00" required>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Related To <span class="text-danger">*</span></label>
                                <select name="related_to" class="form-select @error('related_to') is-invalid @enderror" required>
                                    <option value="">Select Type...</option>
                                    <option value="vendor_payment" @selected(old('related_to') === 'vendor_payment')>Vendor Payment</option>
                                    <option value="customer_payment" @selected(old('related_to') === 'customer_payment')>Customer Payment</option>
                                    <option value="other" @selected(old('related_to') === 'other')>Other</option>
                                </select>
                                @error('related_to')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bank Information Section -->
                    <div class="card-body border-top">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Bank Information</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                                       value="{{ old('bank_name') }}" placeholder="e.g., State Bank of India" required>
                                @error('bank_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch Name</label>
                                <input type="text" name="branch_name" class="form-control @error('branch_name') is-invalid @enderror"
                                       value="{{ old('branch_name') }}" placeholder="Branch name">
                                @error('branch_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Party Information Section -->
                    <div class="card-body border-top">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Party Information</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Drawer Name <span class="text-danger">*</span></label>
                                <input type="text" name="drawer_name" class="form-control @error('drawer_name') is-invalid @enderror"
                                       value="{{ old('drawer_name') }}" placeholder="Who is drawing the cheque" required>
                                @error('drawer_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payee Name <span class="text-danger">*</span></label>
                                <input type="text" name="payee_name" class="form-control @error('payee_name') is-invalid @enderror"
                                       value="{{ old('payee_name') }}" placeholder="Who is receiving the payment" required>
                                @error('payee_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payee Address</label>
                            <textarea class="form-control @error('payee_address') is-invalid @enderror" name="payee_address"
                                      rows="2" placeholder="Payee's address">{{ old('payee_address') }}</textarea>
                            @error('payee_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="card-body border-top">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Additional Information</h3>

                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number') }}" placeholder="e.g., Invoice #, PO #">
                            @error('reference_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes"
                                      rows="3" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 18a4.6 4.6 0 0 1 0 -9a5 5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"/>
                            </svg>
                            Create Cheque
                        </button>
                        <a href="{{ route('cheques.index') }}" class="btn btn-ghost-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Status Flow</h3>
                    </div>
                    <div class="card-body">
                        <div class="space-y-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">1</span>
                                <span class="text-secondary">Pending - Initial state</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2">2</span>
                                <span class="text-secondary">Deposited - In bank</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2">3</span>
                                <span class="text-secondary">Cleared - Funds received</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">4</span>
                                <span class="text-secondary">Bounced - Failed payment</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Important</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary mb-2" style="font-size: 0.9rem;">
                            All marked fields are required. Make sure to enter accurate cheque details for proper tracking.
                        </p>
                        <ul class="list-unstyled text-secondary" style="font-size: 0.9rem;">
                            <li>✓ Cheque number should be unique</li>
                            <li>✓ Amount must be greater than zero</li>
                            <li>✓ Bank name is mandatory</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
