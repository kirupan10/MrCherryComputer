@extends('shop-types.tech.layouts.nexora')

@section('title', 'Supplier Management')

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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                            </svg>
                            Supplier Management
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Manage supplier information and track purchases</p>
                    </div>
                    <div>
                        <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Add Supplier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Suppliers</div>
                        <h2 class="mb-0" style="font-weight: 700;">{{ number_format($stats['total_vendors'] ?? 0) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            All Registered
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Suppliers with Pending Credit</div>
                        <h2 class="mb-0 text-success" style="font-weight: 700;">{{ number_format($stats['pending_vendors'] ?? 0) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Currently Pending
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Outstanding Balance</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">LKR {{ number_format($stats['total_outstanding'] ?? 0, 2) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Total Amount Due
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('vendors.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 500; font-size: 0.875rem;">Search</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M15 15l6 6"/>
                                    <circle cx="10" cy="10" r="7"/>
                                </svg>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name, company, phone, email..." value="{{ $search }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" style="font-weight: 500; font-size: 0.875rem;">Outstanding Balance</label>
                        <select name="purchase_status" class="form-select">
                            <option value="pending" {{ $purchaseStatus == 'pending' ? 'selected' : '' }}>With Balance (Pending)</option>
                            <option value="completed" {{ $purchaseStatus == 'completed' ? 'selected' : '' }}>No Balance (Completed)</option>
                            <option value="all" {{ $purchaseStatus == 'all' ? 'selected' : '' }}>All Suppliers</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="10" cy="10" r="7"/>
                                <line x1="21" y1="21" x2="15" y2="15"/>
                            </svg>
                            Filter
                        </button>
                        @if($search || $purchaseStatus != 'pending')
                        <a href="{{ route('vendors.index', ['purchase_status' => 'pending']) }}" class="btn btn-secondary ms-2">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="card">
            <div class="card-body p-0">
                @if($vendors->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Supplier Name</th>
                                    <th>Contact</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendors as $vendor)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; {{ $vendor->outstanding_balance > 0 ? 'color: #dc3545;' : '' }}">
                                            {{ $vendor->name }}
                                            @if($vendor->outstanding_balance > 0)
                                                <span class="badge bg-danger ms-1">Credit Pending</span>
                                            @endif
                                        </div>
                                        @if($vendor->tax_number)
                                            <div class="text-muted" style="font-size: 0.875rem;">Tax: {{ $vendor->tax_number }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $vendor->phone ?? '-' }}</div>
                                        @if($vendor->email)
                                            <div class="text-muted" style="font-size: 0.875rem;">{{ $vendor->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vendor->outstanding_balance > 0)
                                            <span class="text-danger" style="font-weight: 600;">
                                                LKR {{ number_format($vendor->outstanding_balance, 2) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vendor->outstanding_balance > 0)
                                            <span class="badge bg-danger">Pending</span>
                                        @else
                                            <span class="badge bg-success">Completed</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-list justify-content-center">
                                            @if($vendor->outstanding_balance > 0)
                                            <button type="button"
                                                    class="btn btn-sm btn-ghost-success payment-btn"
                                                    data-vendor-id="{{ $vendor->id }}"
                                                    data-vendor-name="{{ $vendor->name }}"
                                                    data-outstanding="{{ $vendor->outstanding_balance }}"
                                                    title="Record Payment">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="14" cy="8" r="2"/>
                                                    <path d="M4 6a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/>
                                                    <path d="M18 15l-1 -1"/>
                                                    <path d="M7 15l1.5 -1.5"/>
                                                    <polyline points="7 12 9 12 11 10"/>
                                                </svg>
                                                Pay
                                            </button>
                                            @endif
                                            <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-sm btn-ghost-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="12" r="2"/>
                                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                </svg>
                                                View
                                            </a>
                                            @if(!Auth::user()->isEmployee())
                                            <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-sm btn-ghost-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                    <path d="M16 5l3 3"/>
                                                </svg>
                                                Edit
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        {{ $vendors->links() }}
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                            </svg>
                        </div>
                        <p class="empty-title">No suppliers found</p>
                        <p class="empty-subtitle text-muted">
                            Get started by adding your first supplier.
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Add Supplier
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <!-- Display validation errors -->
                    <div id="paymentErrors" class="alert alert-danger d-none mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="vendorNameDisplay" readonly>
                        <input type="hidden" name="vendor_id" id="vendorId">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Outstanding Balance</label>
                        <div class="input-group">
                            <span class="input-group-text">LKR</span>
                            <input type="text" class="form-control" id="outstandingDisplay" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">LKR</span>
                            <input type="number" class="form-control" name="amount" id="paymentAmount" step="0.01" min="0.01" required>
                        </div>
                        <small class="text-muted">Enter the amount being paid</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_method" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Card">Card</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" name="reference_number" placeholder="Transaction ID, Cheque No, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Additional payment details"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ESC and close button support for payment modal -->
<script>
function enablePaymentModalClose() {
    const modalEl = document.getElementById('paymentModal');
    if (!modalEl) return;

    // ESC key
    document.addEventListener('keydown', function(e) {
        if ((e.key === 'Escape' || e.keyCode === 27) && modalEl.classList.contains('show')) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } else {
                modalEl.style.display = 'none';
                modalEl.classList.remove('show');
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
    });

    // Close button
    const closeBtn = modalEl.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } else {
                modalEl.style.display = 'none';
                modalEl.classList.remove('show');
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', enablePaymentModalClose);
</script>

<!-- Payment modal open logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment modal open handler
    document.querySelectorAll('.payment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Set modal fields
            document.getElementById('vendorNameDisplay').value = btn.getAttribute('data-vendor-name');
            document.getElementById('vendorId').value = btn.getAttribute('data-vendor-id');
            document.getElementById('outstandingDisplay').value = btn.getAttribute('data-outstanding');

           // Set payment form action to correct vendor payment route
           var vendorId = btn.getAttribute('data-vendor-id');
           var form = document.getElementById('paymentForm');
           if (form && vendorId) {
               form.action = '/vendors/' + vendorId + '/record-payment';
           }

            // Show modal using Bootstrap Modal API
            const modalEl = document.getElementById('paymentModal');
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalEl).modal('show');
            } else {
                modalEl.style.display = 'block';
                modalEl.classList.add('show');
                document.body.classList.add('modal-open');
                // Add backdrop manually if needed
                if (!document.querySelector('.modal-backdrop')) {
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            }
        });
    });
});
</script>

@endsection
