@extends('shop-types.tech.layouts.nexora')

@section('title', 'Purchases Management')

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
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="2"/>
                                <line x1="9" y1="12" x2="9.01" y2="12"/>
                                <line x1="13" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="9.01" y2="16"/>
                                <line x1="13" y1="16" x2="15" y2="16"/>
                            </svg>
                            Purchases Management
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Manage purchases and payment tracking</p>
                    </div>
                    <div>
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New Purchase
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Purchases</div>
                        <h2 class="mb-0" style="font-weight: 700;">{{ number_format($totals->total_count ?? 0) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            LKR {{ number_format($totals->total_amount ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Paid</div>
                        <h2 class="mb-0 text-success" style="font-weight: 700;">LKR {{ number_format($totals->paid_amount ?? 0, 2) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            {{ $totals->total_amount > 0 ? number_format(($totals->paid_amount / $totals->total_amount) * 100, 1) : 0 }}% Complete
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Outstanding Due</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">LKR {{ number_format($totals->due_amount ?? 0, 2) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Awaiting Payment
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Suppliers with Pending Credit</div>
                        <h2 class="mb-0" style="font-weight: 700;">
                            {{ $suppliersPendingCount ?? 0 }}
                        </h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Currently Pending
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('purchases.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search Vendor or Reference</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M15 15l6 6"/>
                                    <circle cx="10" cy="10" r="7"/>
                                </svg>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Vendor name or reference..." value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Filter by Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $status === 'all' || !$status ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Filter by Type</label>
                        <select name="purchase_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="cash" @selected($purchaseType === 'cash')>Cash</option>
                            <option value="cheque" @selected($purchaseType === 'cheque')>Cheque</option>
                            <option value="credit" @selected($purchaseType === 'credit')>Credit</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}">
                    </div>

                    <div class="col-md-1 d-flex flex-column align-items-end">
                        <button type="submit" class="btn btn-primary mb-2">Search</button>
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Purchases Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Purchase Date</th>
                            <th>Vendor Name</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Due</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th class="w-1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="text-muted">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td>
                                    <div class="text-wrap">
                                        <strong>{{ $purchase->vendor_name }}</strong>
                                        @if($purchase->vendor_email)
                                            <br><small class="text-muted">{{ $purchase->vendor_email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $purchase->reference_number ?? '-' }}</td>
                                <td>
                                    @if($purchase->purchase_type === 'cash')
                                        <span class="badge bg-success">Cash</span>
                                    @elseif($purchase->purchase_type === 'cheque')
                                        <span class="badge bg-info">Cheque</span>
                                    @elseif($purchase->purchase_type === 'credit')
                                        <span class="badge bg-warning">Credit</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>LKR {{ number_format($purchase->total_amount, 2) }}</strong>
                                </td>
                                <td class="text-danger">
                                    @if($purchase->due_amount > 0)
                                        <strong>LKR {{ number_format($purchase->due_amount, 2) }}</strong>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 100px;">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $purchase->payment_percentage }}%; background-color: #10b981;"
                                             aria-valuenow="{{ $purchase->payment_percentage }}"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($purchase->payment_percentage, 1) }}%</small>
                                </td>
                                <td>
                                    @if($purchase->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($purchase->status === 'partial')
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-danger">{{ $purchase->is_overdue ? 'Overdue' : 'Pending' }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td class="text-muted">
                                    {{ $purchase->due_date->format('M d, Y') }}
                                    @if($purchase->is_overdue && $purchase->status !== 'paid')
                                        <br><small class="text-danger">{{ abs($purchase->days_until_due) }} days overdue</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-ghost-primary btn-icon" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="2"/><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/></svg>
                                        </a>
                                        @if(!Auth::user()->isEmployee())
                                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-ghost-warning btn-icon" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </a>
                                        @endif
                                        @if(in_array(Auth::user()->role, ['shop_owner', 'manager']))
                                        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase? This will remove all related payments and transactions.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost-danger btn-icon" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <p>No purchases found</p>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">Create First Purchase</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($purchases->hasPages())
                <div class="card-footer d-flex justify-content-center align-items-center">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
