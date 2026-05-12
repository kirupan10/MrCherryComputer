@extends('layouts.nexora')

@section('title', 'Business Transactions Report')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reports</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"/>
                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"/>
                    </svg>
                    Business Transactions Report
                </h2>
                <p class="text-muted">{{ $selectedMonth->format('F Y') }}</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.transactions.download', ['month' => $selectedMonth->format('Y-m')]) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                            <polyline points="7 11 12 16 17 11"/>
                            <line x1="12" y1="4" x2="12" y2="16"/>
                        </svg>
                        Download CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Month Selector -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('reports.transactions') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Select Month</label>
                        <input type="month" name="month" class="form-control" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                        <path d="M12 12h0"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Total Transactions</div>
                                <div class="h3 mb-0">{{ $totalTransactions }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-success text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Total Amount</div>
                                <div class="h3 mb-0">LKR {{ number_format($totalAmount, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-info text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="4" y="4" width="6" height="6" rx="1"/>
                                        <rect x="14" y="4" width="6" height="6" rx="1"/>
                                        <rect x="4" y="14" width="6" height="6" rx="1"/>
                                        <rect x="14" y="14" width="6" height="6" rx="1"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Categories</div>
                                <div class="h3 mb-0">{{ $byCategory->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-warning text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="3" y="5" width="18" height="14" rx="3"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                        <line x1="7" y1="15" x2="7.01" y2="15"/>
                                        <line x1="11" y1="15" x2="13" y2="15"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Avg Transaction</div>
                                <div class="h3 mb-0">LKR {{ $totalTransactions > 0 ? number_format($totalAmount / $totalTransactions, 2) : '0.00' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown by Category -->
        @if($byCategory->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Breakdown by Category</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Transactions</th>
                                <th class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byCategory as $category => $data)
                            <tr>
                                <td>{{ ucfirst($category ?? 'Uncategorized') }}</td>
                                <td class="text-end">{{ $data['count'] }}</td>
                                <td class="text-end">LKR {{ number_format($data['total'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Transaction List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Transactions</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Vendor</th>
                                <th>Category</th>
                                <th>Payment Method</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                <td><span class="badge bg-blue-lt">{{ $transaction->formatted_type }}</span></td>
                                <td>{{ $transaction->vendor_name ?? '-' }}</td>
                                <td>{{ ucfirst($transaction->category ?? '-') }}</td>
                                <td>{{ $transaction->formatted_paid_by }}</td>
                                <td class="text-end">LKR {{ number_format($transaction->net_amount, 2) }}</td>
                                <td>
                                    @if($transaction->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No transactions found for this month
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
