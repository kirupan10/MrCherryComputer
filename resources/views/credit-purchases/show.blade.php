@extends('layouts.nexora')

@section('title', 'Vendor Purchase Details')

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
                            Vendor Purchase Details
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">{{ $creditPurchase->vendor_name }}</p>
                    </div>
                    <div class="btn-list">
                        <a href="{{ shop_route('purchases.create') }}" class="btn btn-primary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New Purchase
                        </a>
                        <a href="{{ shop_route('purchases.edit', $creditPurchase->id) }}" class="btn btn-warning btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ shop_route('purchases.index') }}" class="btn btn-secondary btn-lg px-4 py-2" style="font-weight: 600;">
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
        </div>

        <!-- Status and Amount Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Status</div>
                        <div>
                            @if($creditPurchase->status === 'paid')
                                <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">PAID</span>
                            @elseif($creditPurchase->status === 'partial')
                                <span class="badge bg-warning" style="font-size: 1rem; padding: 0.5rem 1rem;">PARTIALLY PAID</span>
                            @else
                                <span class="badge bg-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">{{ $creditPurchase->is_overdue ? 'OVERDUE' : 'PENDING' }}</span>
                            @endif
                        </div>
                        @if($creditPurchase->is_overdue && $creditPurchase->status !== 'paid')
                            <small class="text-danger mt-2">{{ abs($creditPurchase->days_until_due) }} days overdue</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Amount</div>
                        <h2 class="mb-0" style="font-weight: 700;">LKR {{ number_format($creditPurchase->total_amount, 2) }}</h2>
                        <div class="mt-2">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $creditPurchase->payment_percentage }}%;"
                                     aria-valuenow="{{ $creditPurchase->payment_percentage }}"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">{{ number_format($creditPurchase->payment_percentage, 1) }}% paid</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Paid Amount</div>
                        <h2 class="mb-0 text-success" style="font-weight: 700;">LKR {{ number_format($creditPurchase->paid_amount, 2) }}</h2>
                        <small class="text-muted mt-2">Out of {{ number_format($creditPurchase->total_amount, 2) }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Remaining Due</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">LKR {{ number_format($creditPurchase->due_amount, 2) }}</h2>
                        <small class="text-muted mt-2">Due by {{ $creditPurchase->due_date->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Vendor Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0" style="font-weight: 600;">Vendor Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Name</label>
                                    <div style="font-weight: 600;">{{ $creditPurchase->vendor_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Reference Number</label>
                                    <div style="font-weight: 600;">{{ $creditPurchase->reference_number ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Purchase Type</label>
                                    <div>
                                        @if($creditPurchase->purchase_type === 'cash')
                                            <span class="badge bg-success">Cash</span>
                                        @elseif($creditPurchase->purchase_type === 'cheque')
                                            <span class="badge bg-info">Cheque</span>
                                        @elseif($creditPurchase->purchase_type === 'credit')
                                            <span class="badge bg-warning">Credit</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Purchase Date</label>
                                    <div>{{ $creditPurchase->purchase_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Email</label>
                                    <div>{{ $creditPurchase->vendor_email ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Phone</label>
                                    <div>{{ $creditPurchase->vendor_phone ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($creditPurchase->vendor_address)
                            <div class="mb-3">
                                <label class="form-label text-muted">Address</label>
                                <div>{{ $creditPurchase->vendor_address }}</div>
                            </div>
                        @endif

                        @if($creditPurchase->notes)
                            <div class="mb-0">
                                <label class="form-label text-muted">Notes</label>
                                <div>{{ $creditPurchase->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment History Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0" style="font-weight: 600;">Payment History</h3>
                        @if($creditPurchase->status !== 'paid')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Record Payment
                            </button>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Recorded By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($creditPurchase->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td><strong>LKR {{ number_format($payment->payment_amount, 2) }}</strong></td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>{{ $payment->payment_reference ?? '-' }}</td>
                                        <td>{{ $payment->createdBy->name ?? '-' }}</td>
                                        <td class="text-muted">{{ $payment->notes ? substr($payment->notes, 0, 40) . '...' : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No payments recorded yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Purchase Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0" style="font-weight: 600;">Purchase Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Purchase Date</label>
                            <div style="font-weight: 600;">{{ $creditPurchase->purchase_date->format('M d, Y (l)') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Due Date</label>
                            <div style="font-weight: 600;">{{ $creditPurchase->due_date->format('M d, Y (l)') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Credit Days</label>
                            <div style="font-weight: 600;">{{ $creditPurchase->credit_days }} days</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Created By</label>
                            <div>{{ $creditPurchase->createdBy->name }}</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label text-muted">Created Date</label>
                            <div class="text-muted">{{ $creditPurchase->created_at->format('M d, Y H:i A') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="card bg-light-danger">
                    <div class="card-body">
                        <h4 class="card-title mb-3" style="font-weight: 600;">Danger Zone</h4>
                        <form action="{{ shop_route('purchases.destroy', $creditPurchase->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this purchase? This action cannot be undone.')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                </svg>
                                Delete Purchase
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal modal-blur fade" id="recordPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
            </div>
            <form action="{{ shop_route('purchases.record-payment', $creditPurchase->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">LKR</span>
                            <input type="number" name="payment_amount" class="form-control"
                                   placeholder="0.00" step="0.01" min="0.01"
                                   max="{{ $creditPurchase->due_amount }}" required>
                        </div>
                        <small class="text-muted">Maximum: LKR {{ number_format($creditPurchase->due_amount, 2) }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control"
                               value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Card">Card</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Reference</label>
                        <input type="text" name="payment_reference" class="form-control"
                               placeholder="Check number, transaction ID, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Add any notes about this payment"></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-check">
                            <input type="checkbox" name="create_transaction" class="form-check-input" value="1" checked>
                            <span class="form-check-label">Create linked transaction record</span>
                        </label>
                        <small class="text-muted d-block mt-1">This will automatically create a business transaction entry for this payment</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link" data-bs-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
