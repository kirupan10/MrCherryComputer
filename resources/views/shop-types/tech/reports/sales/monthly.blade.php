@extends('shop-types.tech.layouts.nexora')

@section('title', 'Monthly Report')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Reports
                </div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="4" y="5" width="16" height="16" rx="2"/>
                        <line x1="16" y1="3" x2="16" y2="7"/>
                        <line x1="8" y1="3" x2="8" y2="7"/>
                        <line x1="4" y1="11" x2="20" y2="11"/>
                        <path d="M11 15h1v4h-1z"/>
                    </svg>
                    Monthly Report
                </h2>
                <p class="text-muted">{{ $selectedMonth->format('F Y') }} - Comprehensive monthly performance analysis</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.sales.monthly.download', ['month' => $selectedMonth->format('Y-m')]) }}" class="btn btn-success d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                            <polyline points="7 11 12 16 17 11"/>
                            <line x1="12" y1="4" x2="12" y2="16"/>
                        </svg>
                        Download PDF
                    </a>
                    <a href="{{ shop_route('reports.sales.index') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row row-deck row-cards">
            <!-- Page Header -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="4" y="5" width="16" height="16" rx="2"/>
                                <line x1="16" y1="3" x2="16" y2="7"/>
                                <line x1="8" y1="3" x2="8" y2="7"/>
                                <line x1="4" y1="11" x2="20" y2="11"/>
                                <path d="M11 15h1v4h-1z"/>
                            </svg>
                            Monthly Report - {{ $selectedMonth->format('F Y') }}
                        </h3>
                        <div class="card-actions">
                            <a href="{{ shop_route('reports.sales.index') }}" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
                                </svg>
                                Back to Reports
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Select Month</label>
                                <input type="month" class="form-control" name="month" value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ shop_route('reports.sales.monthly', ['month' => $selectedMonth->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-outline-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="15,6 9,12 15,18"/>
                                        </svg>
                                        Previous Month
                                    </a>
                                    <button class="btn" style="cursor: default;">{{ $selectedMonth->format('F Y') }}</button>
                                    @if($selectedMonth->lt(now()->startOfMonth()))
                                    <a href="{{ shop_route('reports.sales.monthly', ['month' => $selectedMonth->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-outline-info">
                                        Next Month
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="9,6 15,12 9,18"/>
                                        </svg>
                                    </a>
                                    @else
                                    <button class="btn btn-outline-info" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        Next Month
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="9,6 15,12 9,18"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sales Summary Cards -->
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-right">
                            <div class="h1 m-0 text-success">LKR {{ number_format($salesData['total_sales']) }}</div>
                            <div class="text-muted mb-3">Total Sales</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card border-success">
                    <div class="card-header">
                        <h3 class="card-title mb-1">Generate & Download Monthly Report (PDF)</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ shop_route('reports.sales.monthly.download') }}" class="row g-3" id="monthlyPdfForm">
                            <input type="hidden" name="month" value="{{ $selectedMonth->format('Y-m') }}">
                            <div class="col-12 d-none" id="monthlyPdfFormError">
                                <div class="alert alert-danger mb-0" role="alert">
                                    Select at least one section before downloading the PDF.
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeSummary" name="includeSummary" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeSummary">Sales Summary</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeTopDays" name="includeTopDays" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeTopDays">Top Performing Days</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeStatistics" name="includeStatistics" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeStatistics">Monthly Statistics</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeExpenses" name="includeExpenses" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeExpenses">Expenses</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeTransactions" name="includeTransactions" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeTransactions">Transactions</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeTransactionSummary" name="includeTransactionSummary" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeTransactionSummary">Transaction Summary</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeDeliveries" name="includeDeliveries" value="1" checked>
                                            <label class="form-check-label" for="monthlyIncludeDeliveries">Deliveries</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="monthlyIncludeActivities" name="includeActivities" value="1">
                                            <label class="form-check-label" for="monthlyIncludeActivities">Activity Log</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                <div class="text-muted mb-2">Report Month: <strong>{{ $selectedMonth->format('F Y') }}</strong></div>
                                <button type="submit" class="btn btn-success">
                                    Download Monthly Report PDF
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-right">
                            <div class="h1 m-0 text-primary">{{ $salesData['total_orders'] }}</div>
                            <div class="text-muted mb-3">Total Orders</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 85%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-right">
                            <div class="h1 m-0 text-info">LKR {{ number_format($salesData['gross_profit']) }}</div>
                            <div class="text-muted mb-3">Gross Profit</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" style="width: 75%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-right">
                            <div class="h1 m-0 text-warning">LKR {{ number_format($salesData['average_order_value']) }}</div>
                            <div class="text-muted mb-3">Avg Order Value</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 60%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Trend</h3>
                    </div>
                    <div class="card-body">
                        <div id="daily-trend-chart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Weekly Summary -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Weekly Summary</h3>
                    </div>
                    <div class="card-body">
                        <div id="weekly-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Days -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Performing Days</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $topDays = $dailyData->sortByDesc('total_sales')->take(10);
                                    @endphp
                                    @foreach($topDays as $day)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($day->date)->format('M j, Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $day->order_count }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>LKR {{ number_format($day->total_sales) }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Statistics -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Statistics</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Days in Month</div>
                                    <div class="ms-auto lh-1">
                                        <div class="strong">{{ $selectedMonth->daysInMonth }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Active Sales Days</div>
                                    <div class="ms-auto lh-1">
                                        <div class="strong">{{ safe_count($dailyData) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Avg Daily Revenue</div>
                                    <div class="ms-auto lh-1">
                                        <div class="strong">LKR {{ number_format($salesData['total_sales'] / max($selectedMonth->daysInMonth, 1)) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Best Day Sales</div>
                                    <div class="ms-auto lh-1">
                                        <div class="strong">LKR {{ number_format($dailyData->max('total_sales') ?? 0) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Monthly Expenses Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Expenses</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($expensesData->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Expense Type</th>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expensesData as $expenseType => $expenses)
                                            @foreach($expenses as $expense)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $expense->type)) }}</span>
                                                    </td>
                                                    <td>{{ $expense->notes ?? '-' }}</td>
                                                    <td class="text-end">
                                                        <strong>LKR {{ number_format($expense->amount, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-info">
                                                <td colspan="2" class="text-end"><strong>{{ ucfirst(str_replace('_', ' ', $expenseType)) }} Subtotal:</strong></td>
                                                <td class="text-end">
                                                    <strong class="text-primary">LKR {{ number_format($expenses->sum('amount'), 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td colspan="2" class="text-end"><strong>Total Expenses:</strong></td>
                                            <td class="text-end">
                                                <strong class="text-warning">LKR {{ number_format($expensesData->flatMap(fn($items) => $items)->sum('amount'), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No expenses recorded for this month
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Transactions Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Business Transactions</h3>
                        <div class="card-subtitle">Showing {{ $transactionsData->count() }} of {{ $transactionsData->total() }} transactions</div>
                    </div>
                    <div class="card-body p-0">
                        @if($transactionsData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Vendor Name</th>
                                            <th>Category</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactionsData as $transaction)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $transaction->vendor_name ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ $transaction->category ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $transaction->status === 'completed' ? 'bg-success' : ($transaction->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>LKR {{ number_format($transaction->total_amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($transactionsData->hasPages())
                                <div class="card-footer d-flex align-items-center">
                                    <p class="m-0 text-muted">
                                        Showing {{ $transactionsData->firstItem() }} to {{ $transactionsData->lastItem() }} of {{ $transactionsData->total() }} entries
                                    </p>
                                    <ul class="pagination m-0 ms-auto">
                                        {{ $transactionsData->appends(['month' => request('month')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-4 text-center text-muted">
                                No transactions recorded for this month
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Transactions Summary by Category -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transactions Summary by Category</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($transactionsSummary->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-center">Count</th>
                                            <th class="text-end">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactionsSummary as $summary)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($summary->category ?? 'Uncategorized') }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <strong>{{ $summary->count }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong>LKR {{ number_format($summary->total, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td class="text-end"><strong>Total:</strong></td>
                                            <td class="text-center"><strong>{{ $transactionsSummary->sum('count') }}</strong></td>
                                            <td class="text-end">
                                                <strong class="text-info">LKR {{ number_format($transactionsSummary->sum('total'), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No transaction categories found for this month
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Deliveries Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Management</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($deliveriesData->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tracking #</th>
                                            <th>From Location</th>
                                            <th>To Location</th>
                                            <th>Date</th>
                                            <th class="text-end">Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deliveriesData as $delivery)
                                            <tr>
                                                <td>
                                                    <strong class="text-primary">{{ $delivery->tracking_number ?? 'N/A' }}</strong>
                                                    @if($delivery->notes)
                                                        <br><small class="text-muted">{{ $delivery->notes }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $delivery->from_location ?? '-' }}</td>
                                                <td>{{ $delivery->to_location ?? '-' }}</td>
                                                <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('M j, H:i') : '-' }}</td>
                                                <td class="text-end">
                                                    <strong>LKR {{ number_format($delivery->cost ?? 0, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td colspan="4" class="text-end"><strong>Total Deliveries Cost:</strong></td>
                                            <td class="text-end">
                                                <strong class="text-success">LKR {{ number_format($deliveriesData->sum('cost'), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No deliveries recorded for this month
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Activities Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activity Log</h3>
                        <div class="card-subtitle">Showing {{ $activitiesData->count() }} of {{ $activitiesData->total() }} activities</div>
                    </div>
                    <div class="card-body p-0">
                        @if($activitiesData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Model Type</th>
                                            <th>Description</th>
                                            <th>User</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activitiesData as $activity)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</span>
                                                </td>
                                                <td>{{ $activity->model_type ?? 'System' }}</td>
                                                <td>
                                                    <small>{{ $activity->description ?? 'No description' }}</small>
                                                </td>
                                                <td>{{ $activity->user?->name ?? 'System' }}</td>
                                                <td>{{ $activity->created_at->format('M j, H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($activitiesData->hasPages())
                                <div class="card-footer d-flex align-items-center">
                                    <p class="m-0 text-muted">
                                        Showing {{ $activitiesData->firstItem() }} to {{ $activitiesData->lastItem() }} of {{ $activitiesData->total() }} entries
                                    </p>
                                    <ul class="pagination m-0 ms-auto">
                                        {{ $activitiesData->appends(['month' => request('month')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-4 text-center text-muted">
                                No activities recorded for this month
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('monthlyPdfForm');
    if (form) {
        const errorBox = document.getElementById('monthlyPdfFormError');
        const checkboxSelector = 'input[name^="include"]';
        const storageKey = 'monthly-report-pdf-toggles';
        const checkboxes = Array.from(form.querySelectorAll(checkboxSelector));

        const saveToggleState = () => {
            const state = Object.fromEntries(checkboxes.map((checkbox) => [checkbox.name, checkbox.checked]));
            try {
                window.localStorage.setItem(storageKey, JSON.stringify(state));
            } catch (error) {
                // Ignore storage issues.
            }
        };

        const restoreToggleState = () => {
            try {
                const savedState = window.localStorage.getItem(storageKey);
                if (!savedState) return;
                const parsedState = JSON.parse(savedState);
                checkboxes.forEach((checkbox) => {
                    if (Object.prototype.hasOwnProperty.call(parsedState, checkbox.name)) {
                        checkbox.checked = Boolean(parsedState[checkbox.name]);
                    }
                });
            } catch (error) {
                // Ignore invalid state.
            }
        };

        const hasSelectedSection = () => checkboxes.some((checkbox) => checkbox.checked);
        const hideError = () => errorBox && errorBox.classList.add('d-none');

        restoreToggleState();

        form.addEventListener('submit', function (event) {
            if (hasSelectedSection()) {
                hideError();
                saveToggleState();
                return;
            }

            event.preventDefault();
            if (errorBox) {
                errorBox.classList.remove('d-none');
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', hideError);
            checkbox.addEventListener('change', saveToggleState);
        });
    }

    const dailyData = @json($dailyData);
    const weeklyData = @json($weeklyData);
    const selectedMonth = @json($selectedMonth->format('Y-m'));

    // Prepare daily trend data
    const dailyDates = [];
    const dailySales = [];
    const dailyOrders = [];

    // Get all days in the month
    const startDate = new Date(selectedMonth + '-01');
    const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);

    for (let day = 1; day <= endDate.getDate(); day++) {
        const dateStr = selectedMonth + '-' + String(day).padStart(2, '0');
        const dayData = dailyData[dateStr];

        dailyDates.push(day);
        dailySales.push(dayData ? parseFloat(dayData.total_sales) : 0);
        dailyOrders.push(dayData ? parseInt(dayData.order_count) : 0);
    }

    // Daily trend chart
    const dailyOptions = {
        series: [{
            name: 'Sales (LKR)',
            data: dailySales
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true
            }
        },
        colors: ['#17a2b8'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1
            }
        },
        xaxis: {
            categories: dailyDates,
            title: {
                text: 'Day of Month'
            }
        },
        yaxis: {
            title: {
                text: 'Sales (LKR)'
            },
            labels: {
                formatter: function (value) {
                    return 'LKR ' + Math.round(value).toLocaleString();
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return 'LKR ' + Math.round(value).toLocaleString();
                }
            }
        }
    };

    const dailyChart = new ApexCharts(document.querySelector("#daily-trend-chart"), dailyOptions);
    dailyChart.render();

    // Weekly breakdown chart
    const weeklyDates = [];
    const weeklySales = [];

    Object.keys(weeklyData).forEach(week => {
        weeklyDates.push('Week ' + week);
        weeklySales.push(parseFloat(weeklyData[week].total_sales));
    });

    const weeklyOptions = {
        series: [{
            data: weeklySales
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        colors: ['#fd7e14'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: weeklyDates,
            title: {
                text: 'Week'
            }
        },
        yaxis: {
            title: {
                text: 'Sales (LKR)'
            },
            labels: {
                formatter: function (value) {
                    return 'LKR ' + Math.round(value).toLocaleString();
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return 'LKR ' + Math.round(value).toLocaleString();
                }
            }
        }
    };

    const weeklyChart = new ApexCharts(document.querySelector("#weekly-chart"), weeklyOptions);
    weeklyChart.render();
});
</script>
@endpush
