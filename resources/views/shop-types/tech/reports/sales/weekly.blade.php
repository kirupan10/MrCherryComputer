@extends('shop-types.tech.layouts.nexora')

@section('title', 'Weekly Report')

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
                        <rect x="8" y="15" width="2" height="2"/>
                    </svg>
                    Weekly Report
                </h2>
                <p class="text-muted">Week of {{ $selectedWeek->format('M j, Y') }} - Analyze weekly performance and trends</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.sales.weekly.download', ['week' => $selectedWeek->format('Y-m-d')]) }}" class="btn btn-success d-none d-sm-inline-block">
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
                                <rect x="8" y="15" width="2" height="2"/>
                            </svg>
                            Weekly Report - Week of {{ $selectedWeek->format('M j, Y') }}
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
                                <label class="form-label">Select Week Starting</label>
                                <input type="date" class="form-control" name="week" value="{{ $selectedWeek->format('Y-m-d') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ shop_route('reports.sales.weekly', ['week' => $selectedWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="15,6 9,12 15,18"/>
                                        </svg>
                                        Previous Week
                                    </a>
                                    <button class="btn" style="cursor: default;">{{ $selectedWeek->format('d M') }} - {{ $selectedWeek->copy()->addDays(6)->format('d M Y') }}</button>
                                    @if($selectedWeek->lt(now()->startOfWeek()))
                                    <a href="{{ shop_route('reports.sales.weekly', ['week' => $selectedWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-success">
                                        Next Week
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="9,6 15,12 9,18"/>
                                        </svg>
                                    </a>
                                    @else
                                    <button class="btn btn-outline-success" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        Next Week
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

            <!-- Week Period Info -->
            <div class="col-12">
                <div class="alert alert-info">
                    <h4 class="alert-title">Week Period</h4>
                    <div class="text-muted">
                        <strong>{{ $selectedWeek->format('M j, Y') }}</strong> to <strong>{{ $selectedWeek->copy()->endOfWeek()->format('M j, Y') }}</strong>
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
                        <h3 class="card-title mb-1">Generate & Download Weekly Report (PDF)</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ shop_route('reports.sales.weekly.download') }}" class="row g-3" id="weeklyPdfForm">
                            <input type="hidden" name="week" value="{{ $selectedWeek->format('Y-m-d') }}">
                            <div class="col-12 d-none" id="weeklyPdfFormError">
                                <div class="alert alert-danger mb-0" role="alert">
                                    Select at least one section before downloading the PDF.
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="weeklyIncludeSummary" name="includeSummary" value="1" checked>
                                            <label class="form-check-label" for="weeklyIncludeSummary">Sales Summary</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="weeklyIncludeDailyBreakdown" name="includeDailyBreakdown" value="1" checked>
                                            <label class="form-check-label" for="weeklyIncludeDailyBreakdown">Daily Breakdown</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="weeklyIncludeExpenses" name="includeExpenses" value="1" checked>
                                            <label class="form-check-label" for="weeklyIncludeExpenses">Expenses</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="weeklyIncludeTransactions" name="includeTransactions" value="1" checked>
                                            <label class="form-check-label" for="weeklyIncludeTransactions">Transactions</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="weeklyIncludeDeliveries" name="includeDeliveries" value="1" checked>
                                            <label class="form-check-label" for="weeklyIncludeDeliveries">Deliveries</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="weeklyIncludeActivities" name="includeActivities" value="1">
                                            <label class="form-check-label" for="weeklyIncludeActivities">Activity Log</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                <div class="text-muted mb-2">Report Week: <strong>{{ $selectedWeek->format('d M Y') }} - {{ $selectedWeek->copy()->endOfWeek()->format('d M Y') }}</strong></div>
                                <button type="submit" class="btn btn-success">
                                    Download Weekly Report PDF
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
                            <div class="h1 m-0 text-warning">{{ $salesData['total_items_sold'] }}</div>
                            <div class="text-muted mb-3">Items Sold</div>
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
                        <h3 class="card-title">Daily Performance for This Week</h3>
                    </div>
                    <div class="card-body">
                        <div id="daily-chart" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Daily Table -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Breakdown</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-striped">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Date</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-end">Total Sales</th>
                                        <th class="text-end">Avg per Order</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < 7; $i++)
                                        @php
                                            $currentDate = $selectedWeek->copy()->addDays($i);
                                            $dateKey = $currentDate->format('Y-m-d');
                                            $dayData = $dailyData->get($dateKey);
                                            $sales = $dayData ? $dayData->total_sales : 0;
                                            $orders = $dayData ? $dayData->order_count : 0;
                                            $avg = $orders > 0 ? $sales / $orders : 0;
                                            $isToday = $currentDate->isToday();
                                            $isPast = $currentDate->isPast() && !$isToday;
                                        @endphp
                                        <tr class="{{ $isToday ? 'table-info' : ($isPast && $orders == 0 ? 'text-muted' : '') }}">
                                            <td>
                                                <strong>{{ $currentDate->format('l') }}</strong>
                                                @if($isToday)
                                                    <span class="badge bg-primary ms-1">Today</span>
                                                @endif
                                            </td>
                                            <td>{{ $currentDate->format('M j, Y') }}</td>
                                            <td class="text-center">
                                                @if($orders > 0)
                                                    <span class="badge bg-primary">{{ $orders }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($sales > 0)
                                                    <strong>LKR {{ number_format($sales) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($avg > 0)
                                                    LKR {{ number_format($avg) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($isToday)
                                                    <span class="badge bg-info">Today</span>
                                                @elseif($sales > 0)
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($isPast)
                                                    <span class="badge bg-secondary">No Sales</span>
                                                @else
                                                    <span class="badge bg-warning">Future</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <th colspan="2"><strong>Week Total</strong></th>
                                        <th class="text-center"><strong>{{ $salesData['total_orders'] }}</strong></th>
                                        <th class="text-end"><strong>LKR {{ number_format($salesData['total_sales']) }}</strong></th>
                                        <th class="text-end"><strong>LKR {{ number_format($salesData['average_order_value']) }}</strong></th>
                                        <th class="text-center">-</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Expenses Section -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Weekly Expenses</h3>
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
                                No expenses recorded for this week
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Weekly Transactions Section -->
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
                                        {{ $transactionsData->appends(['week' => request('week')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-4 text-center text-muted">
                                No transactions recorded for this week
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Weekly Deliveries Section -->
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
                                No deliveries recorded for this week
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Weekly Activities Section -->
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
                                        {{ $activitiesData->appends(['week' => request('week')])->links() }}
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="p-4 text-center text-muted">
                                No activities recorded for this week
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
    const form = document.getElementById('weeklyPdfForm');
    if (form) {
        const errorBox = document.getElementById('weeklyPdfFormError');
        const checkboxSelector = 'input[name^="include"]';
        const storageKey = 'weekly-report-pdf-toggles';
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

    // Prepare daily data for chart
    const dailyData = @json($dailyData);
    const selectedWeek = @json($selectedWeek->format('Y-m-d'));
    const days = [];
    const sales = [];
    const orders = [];

    // Generate 7 days starting from selected week
    const startDate = new Date(selectedWeek);
    for (let i = 0; i < 7; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);
        const dateKey = currentDate.toISOString().split('T')[0];

        days.push(currentDate.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }));

        const dayData = dailyData[dateKey];
        sales.push(dayData ? parseFloat(dayData.total_sales) : 0);
        orders.push(dayData ? parseInt(dayData.order_count) : 0);
    }

    // Create daily chart
    const options = {
        series: [{
            name: 'Sales (LKR)',
            type: 'column',
            data: sales
        }, {
            name: 'Orders',
            type: 'line',
            data: orders
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },
        colors: ['#28a745', '#206bc4'],
        stroke: {
            width: [0, 2]
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [0]
        },
        fill: {
            opacity: [0.85, 1]
        },
        xaxis: {
            categories: days,
            title: {
                text: 'Day of Week'
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
                text: 'Number of Orders'
            }
        }],
        tooltip: {
            shared: true,
            intersect: false,
            y: [{
                formatter: function (value) {
                    return 'LKR ' + Math.round(value).toLocaleString();
                }
            }, {
                formatter: function (value) {
                    return value + ' orders';
                }
            }]
        },
        grid: {
            borderColor: '#e9ecef',
            strokeDashArray: 4
        }
    };

    const chart = new ApexCharts(document.querySelector("#daily-chart"), options);
    chart.render();
});
</script>
@endpush
