@extends('layouts.nexora')

@section('title', 'Edit Cheque')

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
                            Edit Cheque
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">{{ $cheque->cheque_number }}</p>
                    </div>
                    <a href="{{ shop_route('cheques.show', $cheque) }}" class="btn btn-secondary btn-lg px-4 py-2" style="font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 -6"/>
                            <path d="M5 12l6 6"/>
                        </svg>
                        Back to Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ shop_route('cheques.update', $cheque) }}" method="POST" class="card">
                    @csrf
                    @method('PUT')

                    <!-- Cheque Information Section -->
                    <div class="card-body">
                        <h3 class="card-title mb-3" style="font-weight: 600;">Cheque Details</h3>

                        <div class="row">
                            <!-- Status field removed: status can only be updated from the index page -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cheque Number <span class="text-danger">*</span></label>
                                <input type="text" name="cheque_number" class="form-control @error('cheque_number') is-invalid @enderror"
                                       value="{{ old('cheque_number', $cheque->cheque_number) }}" required>
                                @error('cheque_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cheque Date <span class="text-danger">*</span></label>
                                <input type="date" name="cheque_date" class="form-control @error('cheque_date') is-invalid @enderror"
                                       value="{{ old('cheque_date', $cheque->cheque_date->format('Y-m-d')) }}" required>
                                @error('cheque_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', $cheque->amount) }}" required>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Related To <span class="text-danger">*</span></label>
                                <select name="related_to" class="form-select @error('related_to') is-invalid @enderror" required>
                                    <option value="">Select Type...</option>
                                    <option value="vendor_payment" @selected(old('related_to', $cheque->related_to) === 'vendor_payment')>Vendor Payment</option>
                                    <option value="customer_payment" @selected(old('related_to', $cheque->related_to) === 'customer_payment')>Customer Payment</option>
                                    <option value="other" @selected(old('related_to', $cheque->related_to) === 'other')>Other</option>
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
                                       value="{{ old('bank_name', $cheque->bank_name) }}" required>
                                @error('bank_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Branch Name</label>
                                <input type="text" name="branch_name" class="form-control @error('branch_name') is-invalid @enderror"
                                       value="{{ old('branch_name', $cheque->branch_name) }}">
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
                                       value="{{ old('drawer_name', $cheque->drawer_name) }}" required>
                                @error('drawer_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payee Name <span class="text-danger">*</span></label>
                                <input type="text" name="payee_name" class="form-control @error('payee_name') is-invalid @enderror"
                                       value="{{ old('payee_name', $cheque->payee_name) }}" required>
                                @error('payee_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payee Address</label>
                            <textarea class="form-control @error('payee_address') is-invalid @enderror" name="payee_address"
                                      rows="2">{{ old('payee_address', $cheque->payee_address) }}</textarea>
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
                                   value="{{ old('reference_number', $cheque->reference_number) }}">
                            @error('reference_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes"
                                      rows="3">{{ old('notes', $cheque->notes) }}</textarea>
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
                            Update Cheque
                        </button>
                        <a href="{{ shop_route('cheques.show', $cheque) }}" class="btn btn-ghost-secondary">Cancel</a>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Current Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-{{ $cheque->status_color }}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                {{ ucfirst($cheque->status) }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div class="d-flex align-items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2 mt-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                                </svg>
                                <div>
                                    <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                                        <strong>Created:</strong> {{ $cheque->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            @if($cheque->deposit_date)
                                <div class="d-flex align-items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-info me-2 mt-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    <div>
                                        <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                                            <strong>Deposited:</strong> {{ \Carbon\Carbon::parse($cheque->deposit_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($cheque->clearance_date)
                                <div class="d-flex align-items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2 mt-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    <div>
                                        <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                                            <strong>Cleared:</strong> {{ \Carbon\Carbon::parse($cheque->clearance_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($cheque->status === 'bounced' && $cheque->bounce_reason)
                                <div class="d-flex align-items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger me-2 mt-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9l0 6m0 0v0"/><path d="M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0 -18"/>
                                    </svg>
                                    <div>
                                        <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                                            <strong>Reason:</strong> {{ $cheque->bounce_reason }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Important</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary mb-2" style="font-size: 0.9rem;">
                            ℹ️ Status changes are managed through dedicated actions, not edits.
                        </p>
                        <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                            Edit this form only to update cheque details like dates and amounts.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
