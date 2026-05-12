@extends('layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $activeShop->name }}</div>
                <h2 class="page-title">Transactions</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('business-transactions.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        New Transaction
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Transactions</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($stats['total_transactions']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Amount</div>
                        </div>
                        <div class="h1 mb-0">LKR {{ number_format($stats['total_amount'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">This Month</div>
                        </div>
                        <div class="h1 mb-0">LKR {{ number_format($stats['this_month'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Pending</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($stats['pending_count']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Transactions</h3>
                        <div class="ms-auto">
                            <form method="GET" action="{{ shop_route('business-transactions.index') }}" class="d-flex gap-2">
                                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                    <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                                    <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>Commission</option>
                                    <option value="owner_personal" {{ request('type') == 'owner_personal' ? 'selected' : '' }}>Owner Personal Expenses</option>
                                </select>
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                @if(request()->hasAny(['type', 'status', 'search', 'start_date', 'end_date']))
                                    <a href="{{ shop_route('business-transactions.index') }}" class="btn btn-sm btn-secondary">Clear</a>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 140px;">Date</th>
                                            <th style="width: 120px;">Type</th>
                                            <th>Vendor/Supplier</th>
                                            <th>Reference #</th>
                                            <th>Paid By User</th>
                                            <th style="width: 140px;" class="text-end">Amount</th>
                                            <th style="width: 100px;">Status</th>
                                            <th style="width: 140px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td style="font-size: 0.9rem;">
                                                    <strong>{{ $transaction->transaction_date->format('d M Y') }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-blue-lt" style="font-size: 0.85rem;">{{ $transaction->formatted_type }}</span>
                                                </td>
                                                <td style="color: #2c3e50; font-weight: 500;">{{ $transaction->vendor_name ?? 'N/A' }}</td>
                                                <td style="color: #495057;">{{ $transaction->reference_number ?? '-' }}</td>
                                                <td style="color: #2c3e50;">{{ $transaction->paidByUser->name ?? '-' }}</td>
                                                <td class="text-end"><strong style="color: #198754; font-size: 1.05rem;">LKR {{ number_format($transaction->net_amount, 2) }}</strong></td>
                                                <td>
                                                    @if($transaction->status === 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($transaction->status === 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ shop_route('business-transactions.show', $transaction) }}" class="btn btn-sm" title="View">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/></svg>
                                                        </a>
                                                        <a href="{{ shop_route('business-transactions.edit', $transaction) }}" class="btn btn-sm" title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $transactions->links() }}
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2" />
                                        <rect x="9" y="3" width="6" height="4" rx="2" />
                                        <line x1="9" y1="12" x2="9.01" y2="12" />
                                        <line x1="13" y1="12" x2="15" y2="12" />
                                        <line x1="9" y1="16" x2="9.01" y2="16" />
                                        <line x1="13" y1="16" x2="15" y2="16" />
                                    </svg>
                                </div>
                                <p class="empty-title">No transactions found</p>
                                <p class="empty-subtitle text-muted">
                                    Create your first business transaction by clicking the button above.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
