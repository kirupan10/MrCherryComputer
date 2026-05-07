@extends('shop-types.tech.layouts.nexora')

@section('title', 'Monthly Financial Report')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Finance Management
                    </div>
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                            <line x1="16" y1="3" x2="16" y2="7"/>
                            <line x1="8" y1="3" x2="8" y2="7"/>
                            <line x1="4" y1="11" x2="20" y2="11"/>
                        </svg>
                        Monthly Financial Report
                    </h2>
                    <p class="text-muted">Comprehensive monthly breakdown of all financial activities</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ shop_route('finance.index') }}" class="btn d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <x-alert />

            <!-- Month Selector -->
            <div class="card mb-3">
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ shop_route('finance.monthly-report') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Select Month</label>
                            <input type="month" name="month" class="form-control" value="{{ $month }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                        <div class="col-md-7 text-end">
                            <div class="text-muted">
                                Report for <strong>{{ $startDate->format('F Y') }}</strong>
                                <br><small>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Monthly Turnover
                                    </div>
                                    <div class="h2 mb-0">LKR {{ number_format($revenue['total'], 0) }}</div>
                                    <div class="text-muted mt-1">
                                        <small>Cash: LKR {{ number_format($revenue['sales'], 0) }}</small> •
                                        <small>Credit: LKR {{ number_format($revenue['credit_sales'], 0) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Gross Profit
                                    </div>
                                    <div class="h2 mb-0">LKR {{ number_format($grossProfit, 0) }}</div>
                                    <div class="text-muted mt-1">
                                        <small>Selling - Buying Price</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Total Expenses
                                    </div>
                                    <div class="h2 mb-0">LKR {{ number_format($expenses['total'], 0) }}</div>
                                    <div class="text-muted mt-1">
                                        <small>Operating + Business</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}
                                    </div>
                                    <div class="h2 mb-0">LKR {{ number_format($netProfit, 0) }}</div>
                                    <div class="text-muted mt-1">
                                        <small>Margin: {{ $revenue['total'] > 0 ? number_format(($netProfit / $revenue['total']) * 100, 1) : 0 }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Management Tools -->
            <div class="row mb-4">
                <!-- Bulk Update Profit Section -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="2"/>
                                    <path d="M9 14l2 2l4 -4"/>
                                </svg>
                                Bulk Update Monthly Profit
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Select Month</label>
                                <input type="month" id="bulkUpdateMonth" class="form-control" value="{{ $month }}">
                            </div>
                            <div class="d-flex gap-2 align-items-center mb-3">
                                <button type="button" id="bulkPreviewButton" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                    </svg>
                                    Preview Changes
                                </button>
                                <span id="bulkUpdateMessage" class="badge" style="display: none;"></span>
                            </div>

                    <!-- Preview Results Table -->
                    <div id="bulkPreviewResults" class="mt-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Orders Ready to Update</h4>
                            <div>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="selectAllOrders">
                                    <span class="form-check-label">Select All</span>
                                </label>
                                <button type="button" id="updateSelectedButton" class="btn btn-success ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    Update Selected
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th class="w-1">
                                            <input class="form-check-input" type="checkbox" disabled>
                                        </th>
                                        <th>Invoice#</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th class="text-end">Sale Amount</th>
                                        <th class="text-end">Current Profit</th>
                                        <th class="text-end">New Profit</th>
                                        <th class="text-end">Difference</th>
                                        <th class="text-end">Items to Update</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                    <!-- Preview rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Bulk Update Summary -->
                    <div id="bulkUpdateSummary" class="mt-4" style="display: none;">
                        <div class="alert alert-success">
                            <h4 class="alert-title">Update Complete!</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Orders Updated:</strong> <span id="summaryOrdersUpdated">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total Items Updated:</strong> <span id="summaryItemsUpdated">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Processing Time:</strong> <span id="summaryProcessingTime">0s</span>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>

                <!-- Verify Sales Profit Section -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 11l3 3l8 -8"/>
                                    <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"/>
                                </svg>
                                Verify Sales Profit
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="verifyProfitForm" class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" name="invoice_no" id="invoiceNoInput" class="form-control" placeholder="Enter invoice number" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="10" cy="10" r="7"/>
                                        <line x1="21" y1="21" x2="15" y2="15"/>
                                    </svg>
                                    Verify Profit
                                </button>
                            </form>

                            <!-- Loading State -->
                            <div id="verifyLoading" class="text-center py-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2">Verifying profit...</p>
                            </div>

                            <!-- Error State -->
                            <div id="verifyError" class="alert alert-danger mt-3" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verify Results Section (Full Width) -->
            <div id="verifyResults" style="display: none;">
                <div class="card mb-4">
                    <div class="card-body">

                        <!-- Summary -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h4 class="mb-3">Verification Results for <span id="resultInvoiceNo" class="text-primary"></span></h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-muted small">Order Date</div>
                                        <div class="fw-bold" id="resultOrderDate"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-muted small">Customer</div>
                                        <div class="fw-bold" id="resultCustomer"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-muted small">Items Updated</div>
                                        <div class="fw-bold text-success" id="resultItemsUpdated"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="text-muted small">Sale Amount</div>
                                        <div class="h3 mb-0" id="resultSaleAmount"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <div class="text-muted small">Total Cost</div>
                                        <div class="h3 mb-0" id="resultTotalCost"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success-lt">
                                    <div class="card-body p-3">
                                        <div class="text-muted small">Total Profit</div>
                                        <div class="h3 mb-0 text-success" id="resultTotalProfit"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info-lt">
                                    <div class="card-body p-3">
                                        <div class="text-muted small">Profit Margin</div>
                                        <div class="h3 mb-0 text-info" id="resultProfitMargin"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Details Table -->
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Stored Price</th>
                                        <th class="text-end">Current Price</th>
                                        <th class="text-end">Profit (Stored)</th>
                                        <th class="text-end">Profit (Current)</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="verifyDetailsTable"></tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row g-2 mt-4">
                            <div class="col-auto">
                                <button type="button" id="updateStoredProfitButton" class="btn btn-warning" style="display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 12a8 8 0 0 1 8 -8"/>
                                        <path d="M4 12a8 8 0 0 0 8 8"/>
                                        <path d="M4 12h16"/>
                                    </svg>
                                    Update Stored Profit
                                </button>
                            </div>
                            <div class="col-auto">
                                <span id="updateStoredProfitMessage" class="badge" style="display: none;"></span>
                            </div>
                            <div class="col-auto">
                                <button type="button" id="updateKpiButton" class="btn btn-success" style="display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    Update KPI Calculations
                                </button>
                            </div>
                            <div class="col-auto">
                                <span id="updateKpiMessage" class="badge" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Breakdown -->
            @if(count($dailyBreakdown) > 0)
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="card-title">
                                    Daily Financial Breakdown
                                </h3>
                                <p class="text-muted mb-0">Track your daily performance</p>
                            </div>
                            <div class="col-auto ms-auto">
                                @if(!$showFullMonth)
                                    <a href="{{ shop_route('finance.monthly-report', ['month' => $month, 'show_full_month' => 1]) }}" class="btn btn-sm d-none d-sm-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/>
                                            <path d="M16 3v4"/>
                                            <path d="M8 3v4"/>
                                            <path d="M4 11h16"/>
                                        </svg>
                                        View Full Month
                                    </a>
                                @else
                                    <a href="{{ shop_route('finance.monthly-report', ['month' => $month]) }}" class="btn btn-sm d-none d-sm-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
                                        </svg>
                                        Last 7 Days
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Expenses</th>
                                    <th class="text-end">Net Profit</th>
                                    <th style="width: 25%;">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyBreakdown as $day)
                                    <tr>
                                        <td>
                                            <div class="font-weight-medium">{{ \Carbon\Carbon::parse($day['day'])->format('D, M d') }}</div>
                                            <div class="text-muted"><small>{{ \Carbon\Carbon::parse($day['day'])->format('Y') }}</small></div>
                                        </td>
                                        <td class="text-end">
                                            <strong>LKR {{ number_format($day['revenue'], 0) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>LKR {{ number_format($day['expenses'], 0) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ $day['profit'] >= 0 ? '+' : '' }}LKR {{ number_format($day['profit'], 0) }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $maxVal = max($day['revenue'], $day['expenses'], 1);
                                                $revWidth = ($day['revenue'] / $maxVal) * 100;
                                                $expWidth = ($day['expenses'] / $maxVal) * 100;
                                            @endphp
                                            <div class="d-flex flex-column gap-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted" style="width: 55px;">Revenue</small>
                                                    <div class="flex-fill">
                                                        <div class="progress" style="height: 4px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $revWidth }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted" style="width: 55px;">Expense</small>
                                                    <div class="flex-fill">
                                                        <div class="progress" style="height: 4px;">
                                                            <div class="progress-bar bg-danger" style="width: {{ $expWidth }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
<tfoot class="border-top">
                                <tr>
                                    <td>
                                        <strong>Period Total</strong>
                                        <div class="text-muted"><small>{{ count($dailyBreakdown) }} days</small></div>
                                    </td>
                                    <td class="text-end">
                                        <strong>LKR {{ number_format(array_sum(array_column($dailyBreakdown, 'revenue')), 0) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>LKR {{ number_format(array_sum(array_column($dailyBreakdown, 'expenses')), 0) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $totalProfit = array_sum(array_column($dailyBreakdown, 'profit'));
                                        @endphp
                                        <strong>{{ $totalProfit >= 0 ? '+' : '' }}LKR {{ number_format($totalProfit, 0) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $avgDaily = $totalProfit / max(count($dailyBreakdown), 1);
                                        @endphp
                                        <div class="text-muted">
                                            <small><strong>Avg/Day:</strong> {{ $avgDaily >= 0 ? '+' : '' }}LKR {{ number_format($avgDaily, 0) }}</small>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Detailed Sales Breakdown -->
            @if(count($ordersWithProfit) > 0)
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="card-title">
                                    Detailed Sales Breakdown
                                </h3>
                                <p class="text-muted mb-0">
                                    @if($sortBy === 'profit')
                                        Individual orders sorted by profit (highest to lowest)
                                    @else
                                        Individual orders sorted by date (latest first)
                                    @endif
                                </p>
                            </div>
                            <div class="col-auto ms-auto d-print-none">
                                <div class="btn-group" role="group">
                                    <a href="{{ shop_route('finance.monthly-report', ['month' => $month, 'sort_sales' => 'date', 'show_full_month' => request('show_full_month')]) }}" class="btn btn-sm {{ $sortBy === 'date' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-4"/><path d="M14 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-4"/><path d="M4 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-4"/><path d="M14 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-4"/></svg>
                                        By Date
                                    </a>
                                    <a href="{{ shop_route('finance.monthly-report', ['month' => $month, 'sort_sales' => 'profit', 'show_full_month' => request('show_full_month')]) }}" class="btn btn-sm {{ $sortBy === 'profit' ? 'btn-primary' : 'btn-outline-primary' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/><polyline points="12 12 20 7.5"/><polyline points="12 12 12 21"/><polyline points="12 12 4 7.5"/></svg>
                                        By Profit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table" id="salesTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice#</th>
                                    <th>Customer</th>
                                    <th class="text-end">Sale Amount</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Profit</th>
                                    <th class="text-end">Margin %</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                @foreach($ordersWithProfit as $index => $item)
                                    @php
                                        $order = $item['order'];
                                        $profit = $item['profit'];
                                        $margin = $item['margin'];
                                        $cost = max(0, $order->total - $profit);
                                        $isHidden = $index >= 15;
                                    @endphp
                                    <tr class="{{ $isHidden ? 'sales-row-hidden' : '' }}" data-row-index="{{ $index }}" data-invoice="{{ $order->invoice_no }}">
                                        <td>
                                            <span class="text-muted">{{ $order->created_at->format('M d, y') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="text-reset text-decoration-none">
                                                <strong>{{ $order->invoice_no }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            @if($order->customer && $order->customer->name)
                                                {{ substr($order->customer->name, 0, 25) }}
                                            @else
                                                <span class="text-muted">Walk-in</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>LKR {{ number_format($order->total, 0) }}</strong>
                                        </td>
                                        <td class="text-end" data-cost="{{ $cost }}">
                                            <span class="text-muted">LKR <span class="cost-value">{{ number_format($cost, 0) }}</span></span>
                                        </td>
                                        <td class="text-end" data-profit="{{ $profit }}">
                                            <strong class="profit-value {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $profit >= 0 ? '+' : '' }}LKR {{ number_format($profit, 0) }}
                                            </strong>
                                        </td>
                                        <td class="text-end" data-margin="{{ $margin }}">
                                            <span class="badge margin-badge {{ $margin >= 0 ? 'bg-success-lt' : 'bg-danger-lt' }}">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3">
                                        <div class="font-weight-medium">Total (Sorted by Profit)</div>
                                        <div class="text-muted"><small>{{ count($ordersWithProfit) }} sales</small></div>
                                    </td>
                                    <td class="text-end">
                                        <strong>LKR {{ number_format($ordersWithProfit->sum(function($item) { return $item['order']->total; }), 0) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>LKR {{ number_format($ordersWithProfit->sum(function($item) { return max(0, $item['order']->total - $item['profit']); }), 0) }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $totalProfit = $ordersWithProfit->sum(function($item) { return $item['profit']; });
                                            $totalAmount = $ordersWithProfit->sum(function($item) { return $item['order']->total; });
                                        @endphp
                                        <strong class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $totalProfit >= 0 ? '+' : '' }}LKR {{ number_format($totalProfit, 0) }}
                                        </strong>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ $totalAmount > 0 ? number_format(($totalProfit / $totalAmount) * 100, 1) : 0 }}%</strong>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if(count($ordersWithProfit) > 15)
                        <div class="card-footer text-center py-3 d-print-none">
                            <button type="button" class="btn btn-primary" id="loadMoreBtn" onclick="loadMoreSales()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="12 5 12 19"/><polyline points="5 12 19 12"/></svg>
                                Load More (showing 15 of {{ count($ordersWithProfit) }})
                            </button>
                        </div>
                    @endif
                </div>
            @endif

        <!-- Detailed Transactions -->
        <div class="row">
            <!-- Sales Orders -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">Sales Orders ({{ count($salesOrders) }})</h3>
                    </div>
                    <div class="card-body p-0">
                        @if(count($salesOrders) > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Invoice#</th>
                                            <th>Date</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salesOrders->take(10) as $order)
                                            <tr>
                                                <td><a href="{{ route('orders.show', $order) }}">{{ $order->invoice_no }}</a></td>
                                                <td>{{ $order->created_at->format('M d') }}</td>
                                                <td class="text-end"><strong>{{ number_format($order->total, 0) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(count($salesOrders) > 10)
                                <div class="card-footer text-center">
                                    <a href="{{ route('orders.index') }}" class="text-muted">View all {{ count($salesOrders) }} orders ?</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-muted">No sales this month</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Job Orders -->
            <!-- Expenses -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">Expenses ({{ count($expenseRecords) }})</h3>
                    </div>
                    <div class="card-body p-0">
                        @if(count($expenseRecords) > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenseRecords->take(10) as $expense)
                                            <tr>
                                                <td><a href="{{ route('expenses.edit', $expense) }}">{{ $expense->type }}</a></td>
                                                <td>{{ $expense->expense_date->format('M d') }}</td>
                                                <td class="text-end"><strong>{{ number_format($expense->amount, 0) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(count($expenseRecords) > 10)
                                <div class="card-footer text-center">
                                    <a href="{{ route('expenses.index') }}" class="text-muted">View all {{ count($expenseRecords) }} expenses ?</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-muted">No expenses this month</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Business Transactions -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">Business Transactions ({{ count($businessTransactions) }})</h3>
                    </div>
                    <div class="card-body p-0">
                        @if(count($businessTransactions) > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($businessTransactions->take(10) as $transaction)
                                            <tr>
                                                <td><a href="{{ route('business-transactions.show', $transaction) }}">{{ ucfirst($transaction->transaction_type) }}</a></td>
                                                <td>{{ $transaction->transaction_date->format('M d') }}</td>
                                                <td class="text-end"><strong>{{ number_format($transaction->net_amount, 0) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if(count($businessTransactions) > 10)
                                <div class="card-footer text-center">
                                    <a href="{{ route('business-transactions.index') }}" class="text-muted">View all {{ count($businessTransactions) }} transactions ?</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-muted">No business transactions this month</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .sales-row-hidden {
        display: none;
    }
</style>

<script>
    function loadMoreSales() {
        const tableBody = document.getElementById('salesTableBody');
        const hiddenRows = tableBody.querySelectorAll('.sales-row-hidden');
        const loadMoreBtn = document.getElementById('loadMoreBtn');

        // Show all hidden rows
        hiddenRows.forEach(row => {
            row.classList.remove('sales-row-hidden');
        });

        // Hide the button
        loadMoreBtn.style.display = 'none';
    }

    // Verify Profit Functionality
    document.getElementById('verifyProfitForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const invoiceNo = document.getElementById('invoiceNoInput').value;
        const loading = document.getElementById('verifyLoading');
        const error = document.getElementById('verifyError');
        const results = document.getElementById('verifyResults');

        // Hide previous results
        results.style.display = 'none';
        error.style.display = 'none';
        loading.style.display = 'block';

        // Make AJAX request
        fetch('{{ shop_route('finance.verify-profit') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ invoice_no: invoiceNo })
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';

            if (data.success) {
                // Populate summary
                document.getElementById('resultInvoiceNo').textContent = data.invoice_no;
                document.getElementById('resultOrderDate').textContent = data.order_date;
                document.getElementById('resultCustomer').textContent = data.customer;
                document.getElementById('resultItemsUpdated').textContent = data.items_updated + ' item(s)';

                // Populate financial summary
                document.getElementById('resultSaleAmount').textContent = 'LKR ' + data.sale_amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('resultTotalCost').textContent = 'LKR ' + data.total_cost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('resultTotalProfit').textContent = 'LKR ' + data.total_profit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('resultProfitMargin').textContent = data.profit_margin.toFixed(1) + '%';

                // Populate details table
                const tableBody = document.getElementById('verifyDetailsTable');
                tableBody.innerHTML = '';

                data.details.forEach(item => {
                    const row = document.createElement('tr');

                    const statusBadge = item.was_updated
                        ? '<span class="badge bg-success">Updated</span>'
                        : '<span class="badge bg-secondary">No Change</span>';

                    const profitStoredClass = item.profit_with_stored !== null
                        ? (item.profit_with_stored >= 0 ? 'text-success' : 'text-danger')
                        : 'text-muted';

                    const profitCurrentClass = item.profit_with_current >= 0 ? 'text-success' : 'text-danger';

                    const profitStoredText = item.profit_with_stored !== null
                        ? 'LKR ' + item.profit_with_stored.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                        : '-';

                    row.innerHTML = `
                        <td>${item.product_name}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-end">LKR ${item.unit_cost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-end">${item.stored_buying_price ? 'LKR ' + item.stored_buying_price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-'}</td>
                        <td class="text-end">LKR ${item.current_buying_price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-end ${profitStoredClass}">${profitStoredText}</td>
                        <td class="text-end ${profitCurrentClass}">LKR ${item.profit_with_current.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-center">${statusBadge}</td>
                    `;

                    tableBody.appendChild(row);
                });

                results.style.display = 'block';

                // Show update KPI button if items were updated
                const updateKpiBtn = document.getElementById('updateKpiButton');
                if (data.items_updated > 0) {
                    updateKpiBtn.style.display = 'inline-block';
                    updateKpiBtn.setAttribute('data-invoice', data.invoice_no);
                } else {
                    updateKpiBtn.style.display = 'none';
                }

                // Update the Detailed Sales Breakdown table if items were updated
                if (data.items_updated > 0) {
                    updateSalesBreakdownRow(data.invoice_no, data.total_cost, data.total_profit, data.profit_margin);
                }

                // Always validate and check for profit discrepancies
                validateAndUpdateTotalProfit(data.total_profit, data.invoice_no);
            } else {
                error.textContent = data.message || 'An error occurred';
                error.style.display = 'block';
            }
        })
        .catch(err => {
            loading.style.display = 'none';
            error.textContent = 'Error: ' + err.message;
            error.style.display = 'block';
        });
    });

    // Function to update the Detailed Sales Breakdown row
    function updateSalesBreakdownRow(invoiceNo, newCost, newProfit, newMargin) {
        const salesTableBody = document.getElementById('salesTableBody');
        if (!salesTableBody) return; // Table not on page

        const rows = salesTableBody.querySelectorAll('tr[data-invoice]');
        rows.forEach(row => {
            if (row.getAttribute('data-invoice') === invoiceNo) {
                // Update cost
                const costCell = row.querySelector('td[data-cost]');
                if (costCell) {
                    costCell.setAttribute('data-cost', newCost);
                    const costValue = costCell.querySelector('.cost-value');
                    if (costValue) {
                        costValue.textContent = newCost.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                    }
                }

                // Update profit
                const profitCell = row.querySelector('td[data-profit]');
                if (profitCell) {
                    profitCell.setAttribute('data-profit', newProfit);
                    const profitValue = profitCell.querySelector('.profit-value');
                    if (profitValue) {
                        // Update class based on positive/negative
                        profitValue.className = 'profit-value ' + (newProfit >= 0 ? 'text-success' : 'text-danger');
                        profitValue.textContent = (newProfit >= 0 ? '+' : '') + 'LKR ' + newProfit.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                    }
                }

                // Update margin
                const marginCell = row.querySelector('td[data-margin]');
                if (marginCell) {
                    marginCell.setAttribute('data-margin', newMargin);
                    const marginBadge = marginCell.querySelector('.margin-badge');
                    if (marginBadge) {
                        // Update badge class based on positive/negative
                        marginBadge.className = 'badge margin-badge ' + (newMargin >= 0 ? 'bg-success-lt' : 'bg-danger-lt');
                        marginBadge.textContent = newMargin.toFixed(1) + '%';
                    }
                }

                // Add a subtle flash effect to indicate update
                row.style.transition = 'background-color 0.3s';
                row.style.backgroundColor = '#fffbea';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 2000);
            }
        });
    }

    // Function to validate and update the total profit in the sales breakdown table footer
    function validateAndUpdateTotalProfit(verifiedTotalProfit, invoiceNo) {
        const salesTableBody = document.getElementById('salesTableBody');
        const salesTable = document.getElementById('salesTable');
        if (!salesTableBody || !salesTable) {
            console.log('Sales table not found on page');
            return;
        }

        // Find the row with the matching invoice and get its current profit
        let currentRowProfit = null;
        const targetRow = salesTableBody.querySelector(`tr[data-invoice="${invoiceNo}"]`);

        if (targetRow) {
            const profitCell = targetRow.querySelector('td[data-profit]');
            if (profitCell) {
                currentRowProfit = parseFloat(profitCell.getAttribute('data-profit')) || 0;
            }
        }

        console.log(`Comparing: Current Row Profit=${currentRowProfit}, Verified Profit=${verifiedTotalProfit}`);

        // Show update button if profit differs (allowing 1 unit for rounding)
        const updateStoredProfitBtn = document.getElementById('updateStoredProfitButton');
        if (currentRowProfit !== null) {
            const difference = Math.abs(currentRowProfit - verifiedTotalProfit);
            console.log(`Profit Difference: ${difference}`);

            if (difference > 1) {
                console.log('Showing update button - profit mismatch detected');
                updateStoredProfitBtn.style.display = 'inline-block';
                updateStoredProfitBtn.setAttribute('data-invoice', invoiceNo);
                updateStoredProfitBtn.setAttribute('data-verified-profit', verifiedTotalProfit);
            } else {
                updateStoredProfitBtn.style.display = 'none';
            }
        }

        // Update the footer total profit by recalculating from all rows
        let totalProfit = 0;
        let totalAmount = 0;
        const allRows = salesTableBody.querySelectorAll('tr[data-invoice]');

        allRows.forEach(row => {
            const profitCell = row.querySelector('td[data-profit]');
            const amountCell = row.querySelector('td:nth-child(4)');

            if (profitCell) {
                totalProfit += parseFloat(profitCell.getAttribute('data-profit')) || 0;
            }
            if (amountCell) {
                const amountText = amountCell.textContent.replace(/[^\d.-]/g, '');
                totalAmount += parseFloat(amountText) || 0;
            }
        });

        // Update the footer with recalculated totals
        const tfoot = salesTable.querySelector('tfoot');
        if (tfoot) {
            const footerRow = tfoot.querySelector('tr');
            if (footerRow) {
                const footerCells = footerRow.querySelectorAll('td');

                if (footerCells.length >= 6) {
                    // Update profit column (6th column)
                    const footerProfitCell = footerCells[5];
                    if (footerProfitCell) {
                        const profitValueSpan = footerProfitCell.querySelector('strong');
                        if (profitValueSpan) {
                            profitValueSpan.className = totalProfit >= 0 ? 'text-success' : 'text-danger';
                            profitValueSpan.textContent = (totalProfit >= 0 ? '+' : '') + 'LKR ' + totalProfit.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                        }
                    }

                    // Update margin column (7th column)
                    if (footerCells.length >= 7) {
                        const footerMarginCell = footerCells[6];
                        if (footerMarginCell && totalAmount > 0) {
                            const marginValue = (totalProfit / totalAmount) * 100;
                            const marginStrong = footerMarginCell.querySelector('strong');
                            if (marginStrong) {
                                marginStrong.textContent = marginValue.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        }
    }

    // Update KPI Calculations
    document.getElementById('updateKpiButton').addEventListener('click', function() {
        const invoiceNo = this.getAttribute('data-invoice');
        const updateKpiBtn = document.getElementById('updateKpiButton');
        const updateKpiMsg = document.getElementById('updateKpiMessage');

        // Disable button and show loading
        updateKpiBtn.disabled = true;
        updateKpiMsg.className = 'badge bg-info';
        updateKpiMsg.textContent = 'Updating KPI...';
        updateKpiMsg.style.display = 'inline-block';

        // Make AJAX request to update KPI
        fetch('{{ shop_route('finance.update-kpi-calculations') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ invoice_no: invoiceNo })
        })
        .then(response => response.json())
        .then(data => {
            updateKpiBtn.disabled = false;

            if (data.success) {
                updateKpiMsg.className = 'badge bg-success';
                updateKpiMsg.textContent = 'KPI updated successfully!';
                updateKpiBtn.style.display = 'none';

                // Show success notification
                setTimeout(() => {
                    updateKpiMsg.style.display = 'none';
                }, 3000);
            } else {
                updateKpiMsg.className = 'badge bg-danger';
                updateKpiMsg.textContent = data.message || 'Failed to update KPI';
            }
        })
        .catch(err => {
            updateKpiBtn.disabled = false;
            updateKpiMsg.className = 'badge bg-danger';
            updateKpiMsg.textContent = 'Error: ' + err.message;
        });
    });

    // Update Stored Profit Button
    document.getElementById('updateStoredProfitButton').addEventListener('click', function() {
        const invoiceNo = this.getAttribute('data-invoice');
        const verifiedProfit = parseFloat(this.getAttribute('data-verified-profit'));
        const updateStoredProfitBtn = document.getElementById('updateStoredProfitButton');
        const updateStoredProfitMsg = document.getElementById('updateStoredProfitMessage');

        console.log('Update Stored Profit button clicked');
        console.log('Invoice:', invoiceNo, 'Verified Profit:', verifiedProfit);

        // Disable button and show loading
        updateStoredProfitBtn.disabled = true;
        updateStoredProfitMsg.className = 'badge bg-info';
        updateStoredProfitMsg.textContent = 'Updating stored profit...';
        updateStoredProfitMsg.style.display = 'inline-block';

        // Make AJAX request to update stored profit
        fetch('{{ shop_route('finance.update-stored-profit') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ invoice_no: invoiceNo, verified_profit: verifiedProfit })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            updateStoredProfitBtn.disabled = false;

            if (data.success) {
                updateStoredProfitMsg.className = 'badge bg-success';
                updateStoredProfitMsg.textContent = 'Stored profit updated successfully! Reloading...';
                updateStoredProfitBtn.style.display = 'none';

                console.log('Update successful, reloading page...');

                // Reload the page with cache buster to show updated values
                setTimeout(() => {
                    // Add timestamp to force cache refresh
                    const url = new URL(window.location.href);
                    url.searchParams.set('_t', Date.now());
                    window.location.href = url.toString();
                }, 1000);
            } else {
                console.error('Update failed:', data.message);
                updateStoredProfitMsg.className = 'badge bg-danger';
                updateStoredProfitMsg.textContent = data.message || 'Failed to update stored profit';
            }
        })
        .catch(err => {
            console.error('Error updating stored profit:', err);
            updateStoredProfitBtn.disabled = false;
            updateStoredProfitMsg.className = 'badge bg-danger';
            updateStoredProfitMsg.textContent = 'Error: ' + err.message;
        });
    });

    // Bulk Preview Button
    const bulkPreviewButton = document.getElementById('bulkPreviewButton');
    if (bulkPreviewButton) {
        bulkPreviewButton.addEventListener('click', function() {
            const month = document.getElementById('bulkUpdateMonth').value;
            const bulkPreviewBtn = document.getElementById('bulkPreviewButton');
            const bulkUpdateMsg = document.getElementById('bulkUpdateMessage');
            const bulkPreviewResults = document.getElementById('bulkPreviewResults');
            const bulkUpdateSummary = document.getElementById('bulkUpdateSummary');

            if (!month) {
                bulkUpdateMsg.className = 'badge bg-danger';
                bulkUpdateMsg.textContent = 'Please select a month';
                bulkUpdateMsg.style.display = 'inline-block';
                return;
            }

            console.log('Bulk preview initiated for month:', month);

            // Clear previous results
            bulkPreviewResults.style.display = 'none';
            bulkUpdateSummary.style.display = 'none';
            document.getElementById('previewTableBody').innerHTML = '';

            // Disable button and show loading
            bulkPreviewBtn.disabled = true;
            bulkUpdateMsg.className = 'badge bg-info';
            bulkUpdateMsg.textContent = 'Loading preview...';
            bulkUpdateMsg.style.display = 'inline-block';

            // Make AJAX request to preview
            fetch('{{ shop_route('finance.bulk-preview-profit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ month: month })
            })
            .then(response => {
                console.log('Bulk preview response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Bulk preview response:', data);
                bulkPreviewBtn.disabled = false;

                if (data.success) {
                    if (data.orders.length === 0) {
                        bulkUpdateMsg.className = 'badge bg-warning';
                        bulkUpdateMsg.textContent = 'No orders found for this month or all profits are up to date';
                    } else {
                        bulkUpdateMsg.className = 'badge bg-success';
                        bulkUpdateMsg.textContent = `Found ${data.orders.length} order${data.orders.length > 1 ? 's' : ''} with changes`;

                        // Show preview table
                        bulkPreviewResults.style.display = 'block';

                        // Populate preview table
                        const tableBody = document.getElementById('previewTableBody');
                        data.orders.forEach(order => {
                            const row = document.createElement('tr');
                            row.dataset.orderId = order.id;

                            const profitDiff = order.new_profit - order.current_profit;
                            const diffClass = profitDiff > 0 ? 'text-success' : 'text-danger';

                            row.innerHTML = `
                                <td>
                                    <input class="form-check-input order-checkbox" type="checkbox" value="${order.id}">
                                </td>
                                <td>${order.invoice_number}</td>
                                <td>${order.date}</td>
                                <td>${order.customer_name}</td>
                                <td class="text-end">?${parseFloat(order.sale_amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-end">?${parseFloat(order.current_profit).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-end">?${parseFloat(order.new_profit).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-end ${diffClass}">
                                    ${profitDiff > 0 ? '+' : ''}?${parseFloat(profitDiff).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-blue">${order.items_to_update}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success update-single-order" data-order-id="${order.id}">
                                        Update
                                    </button>
                                </td>
                            `;

                            tableBody.appendChild(row);
                        });
                    }
                } else {
                    console.error('Bulk preview failed:', data.message);
                    bulkUpdateMsg.className = 'badge bg-danger';
                    bulkUpdateMsg.textContent = data.message || 'Failed to load preview';
                }
            })
            .catch(err => {
                console.error('Error in bulk preview:', err);
                bulkPreviewBtn.disabled = false;
                bulkUpdateMsg.className = 'badge bg-danger';
                bulkUpdateMsg.textContent = 'Error: ' + err.message;
            });
        });
    }

    // Select All Orders Checkbox
    const selectAllCheckbox = document.getElementById('selectAllOrders');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    // Update Selected Button
    const updateSelectedButton = document.getElementById('updateSelectedButton');
    if (updateSelectedButton) {
        updateSelectedButton.addEventListener('click', function() {
            const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
                .map(cb => cb.value);

            if (selectedOrders.length === 0) {
                alert('Please select at least one order to update');
                return;
            }

            if (!confirm(`Are you sure you want to update ${selectedOrders.length} order${selectedOrders.length > 1 ? 's' : ''}?`)) {
                return;
            }

            const bulkUpdateMsg = document.getElementById('bulkUpdateMessage');
            bulkUpdateMsg.className = 'badge bg-info';
            bulkUpdateMsg.textContent = `Updating ${selectedOrders.length} order${selectedOrders.length > 1 ? 's' : ''}...`;
            bulkUpdateMsg.style.display = 'inline-block';

            updateSelectedButton.disabled = true;
            const startTime = Date.now();

            fetch('{{ shop_route('finance.update-selected-profit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ order_ids: selectedOrders })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Update selected response:', data);

                if (data.success) {
                    const processingTime = ((Date.now() - startTime) / 1000).toFixed(2);

                    bulkUpdateMsg.className = 'badge bg-success';
                    bulkUpdateMsg.textContent = 'Update completed! Reloading...';

                    // Show summary
                    document.getElementById('summaryOrdersUpdated').textContent = data.orders_updated;
                    document.getElementById('summaryItemsUpdated').textContent = data.total_items_updated;
                    document.getElementById('summaryProcessingTime').textContent = processingTime + 's';
                    document.getElementById('bulkUpdateSummary').style.display = 'block';

                    // Hide preview table
                    document.getElementById('bulkPreviewResults').style.display = 'none';

                    // Reload page after 3 seconds
                    setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('_t', Date.now());
                        window.location.href = url.toString();
                    }, 3000);
                } else {
                    console.error('Update failed:', data.message);
                    updateSelectedButton.disabled = false;
                    bulkUpdateMsg.className = 'badge bg-danger';
                    bulkUpdateMsg.textContent = data.message || 'Error updating orders';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                updateSelectedButton.disabled = false;
                bulkUpdateMsg.className = 'badge bg-danger';
                bulkUpdateMsg.textContent = 'Error: ' + err.message;
            });
        });
    }

    // Update Single Order Button (Event Delegation)
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('update-single-order')) {
            const orderId = e.target.dataset.orderId;
            const button = e.target;

            if (!confirm('Update this order?')) {
                return;
            }

            button.disabled = true;
            button.textContent = 'Updating...';

            fetch('{{ shop_route('finance.update-selected-profit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ order_ids: [orderId] })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    button.textContent = '? Updated';
                    button.className = 'btn btn-sm btn-success';

                    // Remove row after 1 second
                    setTimeout(() => {
                        const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                        if (row) {
                            row.style.opacity = '0.5';
                            setTimeout(() => row.remove(), 300);
                        }

                        // Check if all rows are removed
                        const remainingRows = document.querySelectorAll('#previewTableBody tr');
                        if (remainingRows.length === 0) {
                            const bulkUpdateMsg = document.getElementById('bulkUpdateMessage');
                            bulkUpdateMsg.className = 'badge bg-success';
                            bulkUpdateMsg.textContent = 'All updates completed! Reloading...';
                            document.getElementById('bulkPreviewResults').style.display = 'none';

                            // Reload page after 2 seconds
                            setTimeout(() => {
                                const url = new URL(window.location.href);
                                url.searchParams.set('_t', Date.now());
                                window.location.href = url.toString();
                            }, 2000);
                        }
                    }, 1000);
                } else {
                    button.disabled = false;
                    button.textContent = 'Failed';
                    button.className = 'btn btn-sm btn-danger';
                    alert(data.message || 'Error updating order');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                button.disabled = false;
                button.textContent = 'Error';
                button.className = 'btn btn-sm btn-danger';
                alert('Error: ' + err.message);
            });
        }
    });

</script>
@endsection
