@extends('layouts.nexora')

@section('title', 'Finance Dashboard')

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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                            </svg>
                            Finance Management
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Complete financial overview and profit/loss tracking</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ shop_route('finance.profit-loss') }}" class="btn btn-outline-primary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="2"/>
                                <line x1="9" y1="12" x2="9.01" y2="12"/>
                                <line x1="13" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="9.01" y2="16"/>
                                <line x1="13" y1="16" x2="15" y2="16"/>
                            </svg>
                            P&L Statement
                        </a>
                        <a href="{{ shop_route('finance.monthly-report') }}" class="btn btn-white btn-lg px-4 py-2" style="font-weight: 600; border: 1px solid #000;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="4" y="5" width="16" height="16" rx="2"/>
                                <line x1="16" y1="3" x2="16" y2="7"/>
                                <line x1="8" y1="3" x2="8" y2="7"/>
                                <line x1="4" y1="11" x2="20" y2="11"/>
                                <line x1="11" y1="15" x2="12" y2="15"/>
                                <line x1="12" y1="15" x2="12" y2="18"/>
                            </svg>
                            Monthly Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month Selector -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('finance.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Select Month</label>
                        <input type="month" name="month" class="form-control" value="{{ $month }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                    <div class="col-md-7 text-end">
                        <div class="text-muted">
                            Showing data for <strong>{{ $selectedDate->format('F Y') }}</strong>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row mb-4">
            <!-- Revenue Card -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-secondary mb-1" style="font-size: 0.875rem; font-weight: 500;">Monthly Turnover</div>
                                <h2 class="mb-0" style="color: #10b981; font-weight: 700;">LKR {{ number_format($revenue['total'], 2) }}</h2>
                                <div class="text-muted mt-2" style="font-size: 0.875rem;">
                                    Total Sales Revenue
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div class="text-muted" style="font-size: 0.75rem;">Cash</div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">{{ number_format($revenue['sales'], 0) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted" style="font-size: 0.75rem;">Credit</div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">{{ number_format($revenue['credit_sales'], 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Profit Card (Gross Profit) -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-secondary mb-1" style="font-size: 0.875rem; font-weight: 500;">Sales Profit</div>
                                <h2 class="mb-0" style="color: #3b82f6; font-weight: 700;">LKR {{ number_format($grossProfit, 2) }}</h2>
                                <div class="text-muted mt-2" style="font-size: 0.875rem;">
                                    Selling - Buy Price
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="text-center">
                                <div class="text-muted" style="font-size: 0.75rem;">Gross Margin</div>
                                <div style="font-weight: 600; font-size: 0.875rem;">
                                    {{ $revenue['total'] > 0 ? number_format(($grossProfit / $revenue['total']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Card -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-secondary mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Expenses</div>
                                <h2 class="mb-0" style="color: #ef4444; font-weight: 700;">LKR {{ number_format($expenses['total'], 2) }}</h2>
                                <div class="text-muted mt-2" style="font-size: 0.875rem;">
                                    Operating + Business
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div class="text-muted" style="font-size: 0.75rem;">Operating</div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">{{ number_format($expenses['operating'], 0) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted" style="font-size: 0.75rem;">Business</div>
                                    <div style="font-weight: 600; font-size: 0.875rem;">{{ number_format($expenses['business'], 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Profit Card -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-secondary mb-1" style="font-size: 0.875rem; font-weight: 500;">Net Profit / Loss</div>
                                <h2 class="mb-0" style="color: {{ $netProfit >= 0 ? '#10b981' : '#ef4444' }}; font-weight: 700;">
                                    LKR {{ number_format($netProfit, 2) }}
                                </h2>
                                <div class="text-muted mt-2" style="font-size: 0.875rem;">
                                    Sales Profit - Expenses
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="text-center">
                                <div class="text-muted" style="font-size: 0.75rem;">Profit Margin</div>
                                <div style="font-weight: 600; font-size: 0.875rem;">
                                    {{ $revenue['total'] > 0 ? number_format(($netProfit / $revenue['total']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics Row -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-white-50 mb-1" style="font-size: 0.875rem; font-weight: 500;">Outstanding Credit</div>
                                <h3 class="mb-0 text-white" style="font-weight: 700;">LKR {{ number_format($outstandingCredit, 2) }}</h3>
                                <div class="text-white-70 mt-1" style="font-size: 0.875rem;">
                                    Credit Sales Pending Collection
                                </div>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-white-50" width="56" height="56" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9"/>
                                    <polyline points="12 7 12 12 15 15"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Purchases Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0" style="font-weight: 600; color: #1a1a1a;">Purchases Management</h3>
                    <a href="{{ shop_route('purchases.index') }}" class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 -6"/>
                            <path d="M5 12l6 6"/>
                        </svg>
                        View All Purchases
                    </a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Purchases (This Month)</div>
                        <h2 class="mb-0" style="font-weight: 700;">LKR {{ number_format($creditPurchaseStats['total_purchases'] ?? 0, 2) }}</h2>
                        <small class="text-muted mt-2 d-block">This Month's Purchases</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Outstanding Due</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">LKR {{ number_format($creditPurchaseStats['outstanding_due'] ?? 0, 2) }}</h2>
                        <small class="text-muted mt-2 d-block">Total Amount Due to Vendors</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Pending Purchases</div>
                        <h2 class="mb-0" style="font-weight: 700;">{{ $creditPurchaseStats['pending_count'] ?? 0 }}</h2>
                        <small class="text-muted mt-2 d-block">Not Yet Paid</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Overdue Purchases</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">{{ $creditPurchaseStats['overdue_count'] ?? 0 }}</h2>
                        <small class="text-muted mt-2 d-block">Past Due Date</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Last 3 Months Financial Trend</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">Expenses</th>
                                        <th class="text-end">Profit/Loss</th>
                                        <th style="width: 30%">Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyTrend as $trend)
                                        <tr>
                                            <td><strong>{{ $trend['month'] }}</strong></td>
                                            <td class="text-end"><span class="text-success">LKR {{ number_format($trend['revenue'], 0) }}</span></td>
                                            <td class="text-end"><span class="text-danger">LKR {{ number_format($trend['expenses'], 0) }}</span></td>
                                            <td class="text-end">
                                                <strong class="{{ $trend['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    LKR {{ number_format($trend['profit'], 0) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @php
                                                    $maxValue = max($trend['revenue'], $trend['expenses']);
                                                    $revenuePercent = $maxValue > 0 ? ($trend['revenue'] / $maxValue) * 100 : 0;
                                                    $expensePercent = $maxValue > 0 ? ($trend['expenses'] / $maxValue) * 100 : 0;
                                                @endphp
                                                <div class="mb-1">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-success" style="width: {{ $revenuePercent }}%"></div>
                                                    </div>
                                                    <small class="text-muted">Revenue</small>
                                                </div>
                                                <div>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-danger" style="width: {{ $expensePercent }}%"></div>
                                                    </div>
                                                    <small class="text-muted">Expenses</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info Row -->
        <div class="row">
            <!-- Outstanding Credit -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Outstanding Credit Sales</h3>
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <h2 class="text-warning mb-0">LKR {{ number_format($outstandingCredit, 2) }}</h2>
                                <p class="text-muted mb-0">Pending payment collection</p>
                            </div>
                            <div>
                                <a href="{{ shop_route('credit-sales.index') }}" class="btn btn-outline-warning">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Quick Actions</h3>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ shop_route('expenses.create') }}" class="btn btn-outline-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    Add Expense
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ shop_route('business-transactions.create') }}" class="btn btn-outline-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    Add Transaction
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ shop_route('expenses.index') }}" class="btn btn-outline-secondary w-100">View All Expenses</a>
                            </div>
                            <div class="col-6">
                                <a href="{{ shop_route('business-transactions.index') }}" class="btn btn-outline-secondary w-100">View Transactions</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
