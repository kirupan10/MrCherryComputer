@extends('shop-types.tech.layouts.nexora')

@section('title', 'Yearly Report')

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
                        <path d="M8 15h2v4H8z"/>
                        <path d="M14 15h2v4h-2z"/>
                    </svg>
                    Yearly Report
                </h2>
                <p class="text-muted">{{ $selectedYear }} - Complete annual financial overview and performance metrics</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
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
        <div class="row row-deck row-cards g-2">
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
                                <path d="M8 15h2v4H8z"/>
                                <path d="M14 15h2v4h-2z"/>
                            </svg>
                            Yearly Report - {{ $selectedYear }}
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
                                <label class="form-label">Select Year</label>
                                <select class="form-select" name="year" onchange="this.form.submit()">
                                    @for($year = now()->year; $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ shop_route('reports.sales.yearly', ['year' => $selectedYear - 1]) }}" class="btn btn-outline-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="15,6 9,12 15,18"/>
                                        </svg>
                                        {{ $selectedYear - 1 }}
                                    </a>
                                    <button class="btn" style="cursor: default;">{{ $selectedYear }}</button>
                                    @if($selectedYear < now()->year)
                                    <a href="{{ shop_route('reports.sales.yearly', ['year' => $selectedYear + 1]) }}" class="btn btn-outline-info">
                                        {{ $selectedYear + 1 }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="9,6 15,12 9,18"/>
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Executive Financial Summary -->
            <div class="col-12">
                <div class="card bg-primary-lt">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Executive Financial Summary</h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center p-2 bg-white rounded small">
                                    <div class="h4 m-0 text-success" style="margin-bottom: 2px;">LKR {{ number_format($financialMetrics['total_revenue']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Total Revenue</div>
                                    <small class="text-muted">{{ number_format($salesData['total_orders']) }} orders</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center p-2 bg-white rounded small">
                                    <div class="h4 m-0 {{ $financialMetrics['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}" style="margin-bottom: 2px;">
                                        LKR {{ number_format($financialMetrics['net_profit']) }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1; margin-bottom: 2px;\">Net Profit</div>
                                    <small class="badge {{ $financialMetrics['is_profitable'] ? 'bg-success' : 'bg-danger' }}">
                                        {{ $financialMetrics['profit_loss_status'] }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center p-2 bg-white rounded small">
                                    <div class="h4 m-0 text-info" style="margin-bottom: 2px;\">{{ number_format($financialMetrics['net_margin'], 1) }}%</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;\">Net Profit Margin</div>
                                    <small class="text-muted">After all expenses</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="text-center p-2 bg-white rounded small">
                                    <div class="h4 m-0 text-primary" style="margin-bottom: 2px;\">LKR {{ number_format($financialMetrics['avg_revenue_per_order']) }}</div>
                                    <div class="text-muted\" style="font-size: 0.75rem; line-height: 1;\">Avg Order Value</div>
                                    <small class="text-muted">Per transaction</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit & Loss Statement -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Profit & Loss - {{ $selectedYear }}</h4>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr class="bg-light">
                                        <td class="fw-bold small">REVENUE</td>
                                        <td class="text-end fw-bold text-success small">LKR {{ number_format($financialMetrics['total_revenue']) }}</td>
                                    </tr>
                                    <tr class="bg-light small">
                                        <td class="fw-bold">COGS</td>
                                        <td class="text-end text-danger">{{ number_format($financialMetrics['cogs_percentage'], 1) }}%</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td class="fw-bold small">GROSS PROFIT</td>
                                        <td class="text-end fw-bold small">{{ number_format($financialMetrics['gross_margin'], 1) }}%</td>
                                    </tr>
                                    <tr class="bg-light small">
                                        <td class="fw-bold">EXPENSES</td>
                                        <td class="text-end text-warning">{{ number_format($financialMetrics['expense_ratio'], 1) }}%</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td class="fw-bold small">OPERATING PROFIT</td>
                                        <td class="text-end fw-bold small">{{ number_format($financialMetrics['operating_margin'], 1) }}%</td>
                                    </tr>
                                    <tr class="{{ $financialMetrics['is_profitable'] ? 'table-success' : 'table-danger' }}">
                                        <td class="fw-bold small">NET PROFIT</td>
                                        <td class="text-end fw-bold">LKR {{ number_format($financialMetrics['net_profit']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Ratios & Performance -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Financial Ratios & KPIs</h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-6 col-sm-3">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h4 m-0 text-success" style="margin-bottom: 2px;">{{ number_format($financialMetrics['gross_margin'], 1) }}%</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Gross Margin</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h4 m-0 text-info" style="margin-bottom: 2px;">{{ number_format($financialMetrics['net_margin'], 1) }}%</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Net Margin</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h4 m-0 text-warning" style="margin-bottom: 2px;">{{ number_format($financialMetrics['operating_margin'], 1) }}%</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Op. Margin</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h4 m-0 text-primary" style="margin-bottom: 2px;">{{ number_format($financialMetrics['return_on_sales'], 1) }}%</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">ROS</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-center p-2 bg-light rounded small">
                                    <div class="h5 m-0" style="margin-bottom: 2px;">{{ number_format($financialMetrics['contribution_margin_ratio'], 1) }}%</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Contribution Margin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Structure & Per Order Metrics - Side by Side -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Cost Structure Analysis</h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="mb-1">
                            <div class="d-flex justify-content-between mb-0" style="font-size: 0.85rem;">
                                <span>COGS</span>
                                <span class="fw-bold">{{ number_format($financialMetrics['cogs_percentage'], 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 6px; margin-top: 2px; margin-bottom: 2px;">
                                <div class="progress-bar bg-danger" style="width: {{ $financialMetrics['cogs_percentage'] }}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem; line-height: 1;">LKR {{ number_format($financialMetrics['cogs']) }}</small>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between mb-0" style="font-size: 0.85rem;">
                                <span>Expenses</span>
                                <span class="fw-bold">{{ number_format($financialMetrics['expense_ratio'], 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 6px; margin-top: 2px; margin-bottom: 2px;">
                                <div class="progress-bar bg-warning" style="width: {{ $financialMetrics['expense_ratio'] }}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem; line-height: 1;">LKR {{ number_format($financialMetrics['operating_expenses']) }}</small>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-0" style="font-size: 0.85rem;">
                                <span>Net Profit</span>
                                <span class="fw-bold {{ $financialMetrics['is_profitable'] ? 'text-success' : 'text-danger' }}">{{ number_format($financialMetrics['net_margin'], 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 6px; margin-top: 2px; margin-bottom: 2px;">
                                <div class="progress-bar {{ $financialMetrics['is_profitable'] ? 'bg-success' : 'bg-danger' }}" style="width: {{ abs($financialMetrics['net_margin']) }}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem; line-height: 1;">LKR {{ number_format($financialMetrics['net_profit']) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Per Order Metrics -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Performance Metrics</h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h5 m-0 text-primary" style="margin-bottom: 2px;">LKR {{ number_format($financialMetrics['avg_revenue_per_order']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Avg/Order</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h5 m-0 {{ $financialMetrics['avg_profit_per_order'] >= 0 ? 'text-success' : 'text-danger' }}" style="margin-bottom: 2px;">LKR {{ number_format($financialMetrics['avg_profit_per_order']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Profit/Order</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h5 m-0 text-warning" style="margin-bottom: 2px;">LKR {{ number_format($financialMetrics['avg_cost_per_order']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Cost/Order</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 border rounded small">
                                    <div class="h5 m-0 text-info" style="margin-bottom: 2px;">{{ number_format($salesData['total_items_sold']) }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1;">Items Sold</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded small">
                                            <div style="font-size: 0.9rem; font-weight: bold; margin-bottom: 2px;">{{ number_format($financialMetrics['avg_monthly_orders'], 0) }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem; line-height: 1;">Avg Orders/Month</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded small">
                                            <div style="font-size: 0.9rem; font-weight: bold; margin-bottom: 2px;">{{ number_format($salesData['total_orders']) }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem; line-height: 1;">Total Orders</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Monthly Performance</h4>
                        <div class="card-actions">
                            <div class="dropdown">
                                <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="1"/>
                                        <circle cx="12" cy="19" r="1"/>
                                        <circle cx="12" cy="5" r="1"/>
                                    </svg>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item" onclick="changeChartType('area')">Area Chart</a>
                                    <a href="#" class="dropdown-item" onclick="changeChartType('bar')">Bar Chart</a>
                                    <a href="#" class="dropdown-item" onclick="changeChartType('line')">Line Chart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="monthly-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Months -->
            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Top Months</h4>
                    </div>
                    <div class="card-body p-2" style="overflow-y: auto; max-height: 340px;">
                        @php
                            $topMonths = collect($monthlyData)->sortByDesc('total_sales')->take(6);
                        @endphp
                        @foreach($topMonths as $month => $data)
                        <div class="d-flex align-items-center mb-2 p-2 border-bottom">
                            <div class="me-2">
                                <span class="badge bg-success" style="font-size: 0.7rem;">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</span>
                            </div>
                            <div class="flex-fill small">
                                <div style="font-weight: 500;">{{ number_format($data['order_count']) }} orders</div>
                                <div class="text-muted" style="font-size: 0.8rem;">LKR {{ number_format($data['total_sales']) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quarterly Analysis -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Quarterly Breakdown</h4>
                    </div>
                    <div class="card-body">
                        <div id="quarterly-chart" style="height: 280px;"></div>
                    </div>
                </div>
            </div>

            <!-- Year-over-Year Comparison -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">YoY Comparison ({{ $selectedYear - 1 }} vs {{ $selectedYear }})</h4>
                    </div>
                    <div class="card-body p-2">
                        @if($previousYearData)
                            @php
                                $growth = $previousYearData['total_sales'] > 0
                                    ? (($salesData['total_sales'] - $previousYearData['total_sales']) / $previousYearData['total_sales']) * 100
                                    : 0;
                                $orderGrowth = $previousYearData['total_orders'] > 0
                                    ? (($salesData['total_orders'] - $previousYearData['total_orders']) / $previousYearData['total_orders']) * 100
                                    : 0;
                            @endphp
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 border rounded small">
                                        <div class="text-muted" style="font-size: 0.75rem;">Sales Growth</div>
                                        <div class="h5 m-0 {{ $growth >= 0 ? 'text-success' : 'text-danger' }}">{{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded small">
                                        <div class="text-muted" style="font-size: 0.75rem;">Order Growth</div>
                                        <div class="h5 m-0 {{ $orderGrowth >= 0 ? 'text-success' : 'text-danger' }}">{{ $orderGrowth >= 0 ? '+' : '' }}{{ number_format($orderGrowth, 1) }}%</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr class="my-1">
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded small text-center">
                                        <div class="text-muted" style="font-size: 0.7rem;">Previous Year Sales</div>
                                        <div style="font-size: 0.85rem; font-weight: bold;">LKR {{ number_format($previousYearData['total_sales']) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded small text-center">
                                        <div class="text-muted" style="font-size: 0.7rem;">Previous Orders</div>
                                        <div style="font-size: 0.85rem; font-weight: bold;">{{ number_format($previousYearData['total_orders']) }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <p class="m-0">No previous year data</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Key Metrics Summary -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Year Overview</h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 border rounded small text-center">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">Active Months</div>
                                    <div class="h5 m-0">{{ count($monthlyData) }}/12</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded small text-center">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">Avg Monthly Revenue</div>
                                    <div style="font-size: 0.85rem; font-weight: bold; line-height: 1;">LKR {{ number_format($salesData['total_sales'] / 12) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded small text-center">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">Best Month</div>
                                    @php
                                        $bestMonth = collect($monthlyData)->sortByDesc('total_sales')->first();
                                        $bestMonthKey = collect($monthlyData)->sortByDesc('total_sales')->keys()->first();
                                    @endphp
                                    <div class="h5 m-0">{{ $bestMonth ? date('M', mktime(0, 0, 0, $bestMonthKey, 1)) : 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded small text-center">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">Peak Sales</div>
                                    <div style="font-size: 0.85rem; font-weight: bold; line-height: 1;">LKR {{ number_format(collect($monthlyData)->max('total_sales') ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Revenue per Day</div>
                                    <div class="ms-auto lh-1">
                                        <div class="strong">LKR {{ number_format($salesData['total_sales'] / 365) }}</div>
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

            <!-- Expense & Revenue Summary Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Expense Breakdown</h4>
                    </div>
                    <div class="card-body p-2">
                        @if(!empty($financialMetrics['expenses_by_type']))
                            @foreach($financialMetrics['expenses_by_type'] as $type => $data)
                                <div class="mb-1">
                                    <div class="d-flex justify-content-between mb-0" style="font-size: 0.85rem;">
                                        <span>{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                        <span class="text-muted">{{ number_format($data['percentage'], 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; margin-top: 2px; margin-bottom: 2px;">
                                        <div class="progress-bar" style="width: {{ $data['percentage'] }}%"></div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem; line-height: 1;">LKR {{ number_format($data['amount']) }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted p-2">
                                <small>No expenses recorded</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Revenue vs Costs -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Revenue vs Costs</h4>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2 text-center align-items-stretch">
                            <div class="col-12">
                                <div class="p-2 bg-success-lt rounded small" style="margin-bottom: 0;">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">Total Revenue</div>
                                    <div class="h5 m-0 text-success">LKR {{ number_format($financialMetrics['total_revenue']) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-danger-lt rounded small" style="margin-bottom: 0;">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">COGS</div>
                                    <div style="font-size: 0.9rem; font-weight: bold; margin-bottom: 2px;" class="text-danger">LKR {{ number_format($financialMetrics['cogs']) }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ number_format($financialMetrics['cogs_percentage'], 1) }}%</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-warning-lt rounded small" style="margin-bottom: 0;">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">Expenses</div>
                                    <div style="font-size: 0.9rem; font-weight: bold; margin-bottom: 2px;" class="text-warning">LKR {{ number_format($financialMetrics['operating_expenses']) }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ number_format($financialMetrics['expense_ratio'], 1) }}%</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-2 {{ $financialMetrics['is_profitable'] ? 'bg-info-lt' : 'bg-danger-lt' }} rounded small" style="margin-bottom: 0;">
                                    <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">{{ $financialMetrics['profit_loss_status'] }}</div>
                                    <div class="h5 m-0 {{ $financialMetrics['is_profitable'] ? 'text-success' : 'text-danger' }}" style="margin-bottom: 2px;">LKR {{ number_format($financialMetrics['net_profit']) }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ number_format($financialMetrics['net_margin'], 1) }}% margin</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yearly Expenses Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Yearly Expenses</h4>
                        <div class="card-subtitle small text-muted">Showing {{ $expensesData->count() }} of {{ $expensesData->total() }} expenses</div>
                    </div>
                    <div class="card-body p-0">
                        @if($expensesData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped table-sm" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f8f9fa;">
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Date</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Expense Type</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Description</th>
                                            <th class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expensesData as $expense)
                                            <tr style="height: auto;">
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('M j, Y') : '-' }}</td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <span class="badge badge-sm bg-warning">{{ ucfirst(str_replace('_', ' ', $expense->type)) }}</span>
                                                </td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $expense->notes ?? '-' }}</td>
                                                <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong>LKR {{ number_format($expense->amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td colspan="3" class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;"><strong>Total Expenses (All):</strong></td>
                                            <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                <strong class="text-warning">LKR {{ number_format($financialMetrics['total_expenses'], 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if($expensesData->hasPages())
                                <div class="card-footer p-2 d-flex align-items-center gap-2">
                                    <p class="m-0 text-muted small">
                                        Showing {{ $expensesData->firstItem() }} to {{ $expensesData->lastItem() }} of {{ $expensesData->total() }} entries
                                    </p>
                                    <ul class="pagination pagination-sm m-0 ms-auto">
                                        {{ $expensesData->appends(['year' => request('year')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-2 text-center text-muted small">
                                No expenses recorded for this year
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Yearly Transactions Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Business Transactions</h4>
                        <div class="card-subtitle small text-muted">Showing {{ $transactionsData->count() }} of {{ $transactionsData->total() }} transactions</div>
                    </div>
                    <div class="card-body p-0">
                        @if($transactionsData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped table-sm" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f8f9fa;">
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Type</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Vendor Name</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Category</th>
                                            <th class="text-center" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Status</th>
                                            <th class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactionsData as $transaction)
                                            <tr style="height: auto;">
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <span class="badge badge-sm bg-info">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                                                </td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong>{{ $transaction->vendor_name ?? 'N/A' }}</strong>
                                                </td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $transaction->category ?? '-' }}</td>
                                                <td class="text-center" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <span class="badge badge-sm {{ $transaction->status === 'completed' ? 'bg-success' : ($transaction->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong>LKR {{ number_format($transaction->total_amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($transactionsData->hasPages())
                                <div class="card-footer p-2 d-flex align-items-center gap-2">
                                    <p class="m-0 text-muted small">
                                        Showing {{ $transactionsData->firstItem() }} to {{ $transactionsData->lastItem() }} of {{ $transactionsData->total() }} entries
                                    </p>
                                    <ul class="pagination pagination-sm m-0 ms-auto">
                                        {{ $transactionsData->appends(['year' => request('year')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-2 text-center text-muted small">
                                No transactions recorded for this year
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Yearly Transactions Summary by Category -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Transactions Summary by Category</h4>
                    </div>
                    <div class="card-body p-0">
                        @if($transactionsSummary->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped table-sm" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f8f9fa;">
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Category</th>
                                            <th class="text-center" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Count</th>
                                            <th class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactionsSummary as $summary)
                                            <tr style="height: auto;">
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <span class="badge badge-sm bg-secondary">{{ ucfirst($summary->category ?? 'Uncategorized') }}</span>
                                                </td>
                                                <td class="text-center" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong>{{ $summary->count }}</strong>
                                                </td>
                                                <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong>LKR {{ number_format($summary->total, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-center" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;"><strong>{{ $transactionsSummary->sum('count') }}</strong></td>
                                            <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                <strong class="text-info">LKR {{ number_format($transactionsSummary->sum('total'), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-2 text-center text-muted small">
                                No transaction categories found for this year
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Yearly Deliveries Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Delivery Management</h4>
                        <div class="card-subtitle small text-muted">Showing {{ $deliveriesData->count() }} of {{ $deliveriesData->total() }} deliveries</div>
                    </div>
                    <div class="card-body p-0">
                        @if($deliveriesData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped table-sm" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Tracking #</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">From Location</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">To Location</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Date</th>
                                            <th class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deliveriesData as $delivery)
                                            <tr style="height: auto;">
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong class="text-primary">{{ $delivery->tracking_number ?? 'N/A' }}</strong>
                                                    @if($delivery->notes)
                                                        <br><small class="text-muted" style="line-height: 1;">{{ $delivery->notes }}</small>
                                                    @endif
                                                </td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $delivery->from_location ?? '-' }}</td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $delivery->to_location ?? '-' }}</td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $delivery->delivery_date ? $delivery->delivery_date->format('M j, H:i') : '-' }}</td>
                                                <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <strong>LKR {{ number_format($delivery->cost ?? 0, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td colspan="4" class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;"><strong>Total Deliveries Cost:</strong></td>
                                            <td class="text-end" style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                <strong class="text-success">LKR {{ number_format($totalDeliveryCost, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if($deliveriesData->hasPages())
                                <div class="card-footer p-2 d-flex align-items-center gap-2">
                                    <p class="m-0 text-muted small">
                                        Showing {{ $deliveriesData->firstItem() }} to {{ $deliveriesData->lastItem() }} of {{ $deliveriesData->total() }} entries
                                    </p>
                                    <ul class="pagination pagination-sm m-0 ms-auto">
                                        {{ $deliveriesData->appends(['year' => request('year')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-2 text-center text-muted small">
                                No deliveries recorded for this year
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Yearly Activities Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Activity Log</h4>
                        <div class="card-subtitle small text-muted">Showing {{ $activitiesData->count() }} of {{ $activitiesData->total() }} activities</div>
                    </div>
                    <div class="card-body p-0">
                        @if($activitiesData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped table-sm" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f8f9fa;">
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Action</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Model Type</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Description</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">User</th>
                                            <th style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activitiesData as $activity)
                                            <tr style="height: auto;">
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <span class="badge badge-sm bg-primary">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</span>
                                                </td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $activity->model_type ?? 'System' }}</td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">
                                                    <small>{{ $activity->description ?? 'No description' }}</small>
                                                </td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $activity->user?->name ?? 'System' }}</td>
                                                <td style="font-size: 0.85rem; padding: 0.4rem 0.5rem;">{{ $activity->created_at->format('M j, H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($activitiesData->hasPages())
                                <div class="card-footer p-2 d-flex align-items-center gap-2">
                                    <p class="m-0 text-muted small">
                                        Showing {{ $activitiesData->firstItem() }} to {{ $activitiesData->lastItem() }} of {{ $activitiesData->total() }} entries
                                    </p>
                                    <ul class="pagination pagination-sm m-0 ms-auto">
                                        {{ $activitiesData->appends(['year' => request('year')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-3 text-center text-muted small">
                                No activities recorded for this year
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
    const monthlyData = @json($monthlyData);
    const quarterlyData = @json($quarterlyData);

    let monthlyChart;
    let quarterlyChart;

    // Prepare monthly data
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthlySales = [];
    const monthlyOrders = [];

    for (let i = 1; i <= 12; i++) {
        const monthData = monthlyData[i];
        monthlySales.push(monthData ? parseFloat(monthData.total_sales) : 0);
        monthlyOrders.push(monthData ? parseInt(monthData.order_count) : 0);
    }

    // Monthly chart
    function renderMonthlyChart(type = 'area') {
        if (monthlyChart) {
            monthlyChart.destroy();
        }

        const monthlyOptions = {
            series: [{
                name: 'Sales (LKR)',
                data: monthlySales
            }, {
                name: 'Orders',
                data: monthlyOrders
            }],
            chart: {
                type: type,
                height: 400,
                toolbar: {
                    show: true
                }
            },
            colors: ['#28a745', '#17a2b8'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: type === 'area' ? 'gradient' : 'solid',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1
                }
            },
            xaxis: {
                categories: months,
                title: {
                    text: 'Month'
                }
            },
            yaxis: [{
                title: {
                    text: 'Sales (LKR)'
                },
                labels: {
                    formatter: function (value) {
                        return 'LKR ' + Math.round(value).toLocaleString();
                    }
                }
            }, {
                opposite: true,
                title: {
                    text: 'Orders'
                },
                labels: {
                    formatter: function (value) {
                        return Math.round(value);
                    }
                }
            }],
            tooltip: {
                y: [{
                    formatter: function (value) {
                        return 'LKR ' + Math.round(value).toLocaleString();
                    }
                }, {
                    formatter: function (value) {
                        return Math.round(value) + ' orders';
                    }
                }]
            },
            legend: {
                position: 'top'
            }
        };

        monthlyChart = new ApexCharts(document.querySelector("#monthly-chart"), monthlyOptions);
        monthlyChart.render();
    }

    // Initial render
    renderMonthlyChart('area');

    // Chart type changer
    window.changeChartType = function(type) {
        renderMonthlyChart(type);
    };

    // Quarterly chart
    const quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];
    const quarterlySales = [];

    quarterNames.forEach((quarter, index) => {
        const quarterNum = index + 1;
        const quarterData = quarterlyData[quarterNum];
        quarterlySales.push(quarterData ? parseFloat(quarterData.total_sales) : 0);
    });

    const quarterlyOptions = {
        series: [{
            data: quarterlySales
        }],
        chart: {
            type: 'donut',
            height: 300
        },
        colors: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
        labels: quarterNames,
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return Math.round(val) + '%';
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Sales',
                            formatter: function (w) {
                                const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return 'LKR ' + Math.round(total).toLocaleString();
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return 'LKR ' + Math.round(value).toLocaleString();
                }
            }
        },
        legend: {
            position: 'bottom'
        }
    };

    quarterlyChart = new ApexCharts(document.querySelector("#quarterly-chart"), quarterlyOptions);
    quarterlyChart.render();
});
</script>
@endpush
