@extends('shop-types.tech.layouts.nexora')

@section('title', 'Daily Report')

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
                    </svg>
                    Daily Report
                </h2>
                <p class="text-muted">{{ $selectedDate->format('F j, Y') }} - View detailed daily business performance</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.sales.daily.download', ['date' => $selectedDate->format('Y-m-d')]) }}" class="btn btn-success d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                            <polyline points="7 11 12 16 17 11"/>
                            <line x1="12" y1="4" x2="12" y2="16"/>
                        </svg>
                        Download PDF
                    </a>
                    <a href="{{ shop_route('reports.sales.index') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
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
        <div class="row row-deck row-cards g-4">
            <!-- Date Selection Card -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Select Date</label>
                                <input type="date" class="form-control" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ shop_route('reports.sales.daily', ['date' => $selectedDate->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="15,6 9,12 15,18"/>
                                        </svg>
                                        Previous Day
                                    </a>
                                    <button class="btn btn-primary" style="cursor: default;">{{ $selectedDate->format('d M Y') }}</button>
                                    @if($selectedDate->lt(now()->startOfDay()))
                                    <a href="{{ shop_route('reports.sales.daily', ['date' => $selectedDate->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                                        Next Day
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="9,6 15,12 9,18"/>
                                        </svg>
                                    </a>
                                    @else
                                    <button class="btn btn-outline-primary" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        Next Day
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
                            <div class="text-muted small mb-2">Gross Profit: LKR {{ number_format($salesData['gross_profit'], 2) }}</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 100%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="text-right">
                            <div class="h1 m-0 text-primary">LKR {{ number_format($dailySummary['total_purchases']) }}</div>
                            <div class="text-muted mb-3">Daily Purchases</div>
                            <div class="text-muted small mb-2">{{ $dailySummary['purchase_count'] }} purchases today</div>
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
                            <div class="h1 m-0 text-info">{{ $dailySummary['transaction_count'] }}</div>
                            <div class="text-muted mb-3">Daily Transactions</div>
                            <div class="text-muted small mb-2">LKR {{ number_format($dailySummary['transaction_amount']) }} total amount</div>
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
                            <div class="h1 m-0 text-warning">{{ $dailySummary['delivery_count'] }}</div>
                            <div class="text-muted mb-3">Daily Deliveries</div>
                            <div class="text-muted small mb-2">LKR {{ number_format($dailySummary['delivery_cost']) }} total cost</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 60%" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card border-primary">
                    <div class="card-header">
                        <h3 class="card-title mb-1">Generate & Download Daily Report (PDF)</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ shop_route('reports.sales.daily.download') }}" class="row g-3" id="dailyPdfForm">
                            <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                            <div class="col-12 d-none" id="dailyPdfFormError">
                                <div class="alert alert-danger mb-0" role="alert">
                                    Select at least one section before downloading the PDF.
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includeSummary" name="includeSummary" value="1" checked>
                                            <label class="form-check-label" for="includeSummary">Sales Summary</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includePayments" name="includePayments" value="1" checked>
                                            <label class="form-check-label" for="includePayments">Payment Methods</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includeExpenses" name="includeExpenses" value="1" checked>
                                            <label class="form-check-label" for="includeExpenses">Expenses</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includePurchases" name="includePurchases" value="1" checked>
                                            <label class="form-check-label" for="includePurchases">Purchases</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includeTransactions" name="includeTransactions" value="1" checked>
                                            <label class="form-check-label" for="includeTransactions">Transactions</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includeDeliveries" name="includeDeliveries" value="1" checked>
                                            <label class="form-check-label" for="includeDeliveries">Deliveries</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="includeActivities" name="includeActivities" value="1">
                                            <label class="form-check-label" for="includeActivities">Activity Log</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center">
                                <div class="text-muted mb-2">Report Date: <strong>{{ $selectedDate->format('d M Y') }}</strong></div>
                                <button type="submit" class="btn btn-primary">
                                    Download Daily Report PDF
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sales Details Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sales Details</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($salesDetailsData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Invoice</th>
                                            <th>Customer</th>
                                            <th>Payment Type</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end">Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salesDetailsData as $sale)
                                            @php
                                                $paymentType = strtolower((string) ($sale->payment_type ?? ''));
                                                $isCredit = in_array($paymentType, ['credit', 'due'], true);
                                                $status = $isCredit
                                                    ? (($sale->due ?? 0) > 0 ? 'Due' : 'Completed')
                                                    : 'Completed';
                                            @endphp
                                            <tr>
                                                <td>{{ $sale->order_date ? $sale->order_date->format('Y-m-d') : '-' }}</td>
                                                <td><strong>{{ $sale->invoice_no ?? '-' }}</strong></td>
                                                <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_type ?? '-')) }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $status === 'Completed' ? 'bg-success' : 'bg-warning' }}">{{ $status }}</span>
                                                </td>
                                                <td class="text-end"><strong>LKR {{ number_format($sale->total ?? 0, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-muted">
                                    Showing {{ $salesDetailsData->firstItem() }} to {{ $salesDetailsData->lastItem() }} of {{ $salesDetailsData->total() }} entries
                                </p>
                                <ul class="pagination m-0 ms-auto">
                                    @if($salesDetailsData->hasPages())
                                        {{ $salesDetailsData->appends(['date' => request('date')])->links() }}
                                    @else
                                        <li class="page-item active"><span class="page-link">1</span></li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No sales recorded for this date
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Methods Breakdown -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title mb-1">Payment Methods Breakdown</h3>
                            <div class="text-muted">Quick visual summary of how customers paid today</div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($paymentMethodData->isNotEmpty())
                            @php
                                $totalSales = $salesData['total_sales'] ?? 0;
                                $totalOrders = $paymentMethodData->sum('count');
                                $topMethodKey = $paymentMethodData->sortByDesc('total_amount')->keys()->first();
                                $topMethodLabel = $topMethodKey ? ucfirst(str_replace('_', ' ', $topMethodKey)) : '-';
                            @endphp

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted small">Total Collected</div>
                                        <div class="h3 mb-0">LKR {{ number_format($totalSales, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted small">Total Paid Orders</div>
                                        <div class="h3 mb-0">{{ $totalOrders }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 h-100">
                                        <div class="text-muted small">Top Method</div>
                                        <div class="h3 mb-0">{{ $topMethodLabel }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                @foreach($paymentMethodData->sortByDesc('total_amount') as $method => $data)
                                    @php
                                        $percentage = $totalSales > 0 ? ($data->total_amount / $totalSales) * 100 : 0;
                                        $avg = $data->count > 0 ? $data->total_amount / $data->count : 0;
                                        $methodName = ucfirst(str_replace('_', ' ', $method));
                                        $methodColor =
                                            $method === 'cash' ? '#198754' :
                                            ($method === 'cheque' ? '#0dcaf0' :
                                            ($method === 'due' ? '#ffc107' :
                                            ($method === 'gift' ? '#e83e8c' : '#6c757d')));
                                    @endphp

                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <div>
                                                <span class="badge" style="background-color: {{ $methodColor }}">{{ $methodName }}</span>
                                                <span class="text-muted ms-2">{{ $data->count }} orders</span>
                                            </div>
                                            <div class="text-md-end">
                                                <div><strong>LKR {{ number_format($data->total_amount, 2) }}</strong></div>
                                                <div class="text-muted small">Avg: LKR {{ number_format($avg, 2) }}</div>
                                            </div>
                                        </div>

                                        <div class="progress" style="height: 10px;">
                                            <div
                                                class="progress-bar"
                                                role="progressbar"
                                                style="width: {{ min($percentage, 100) }}%; background-color: {{ $methodColor }};"
                                                aria-valuenow="{{ $percentage }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                            ></div>
                                        </div>
                                        <div class="text-muted small mt-1">{{ number_format($percentage, 2) }}% of daily sales</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                No orders found for this date
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Expenses Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Expenses</h3>
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
                                No expenses recorded for this date
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Purchases Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Purchases</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($purchasesData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>Vendor Name</th>
                                            <th>Category</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchasesData as $purchase)
                                            <tr>
                                                <td><strong>{{ $purchase->vendor_name ?? 'N/A' }}</strong></td>
                                                <td>{{ $purchase->category ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $purchase->status === 'completed' ? 'bg-success' : ($purchase->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ ucfirst($purchase->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>LKR {{ number_format($purchase->total_amount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-muted">
                                    Showing {{ $purchasesData->firstItem() }} to {{ $purchasesData->lastItem() }} of {{ $purchasesData->total() }} entries
                                </p>
                                <ul class="pagination m-0 ms-auto">
                                    @if($purchasesData->hasPages())
                                        {{ $purchasesData->appends(['date' => request('date')])->links() }}
                                    @else
                                        <li class="page-item active"><span class="page-link">1</span></li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No purchases recorded for this date
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Transactions Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Business Transactions</h3>
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
                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-muted">
                                    Showing {{ $transactionsData->firstItem() }} to {{ $transactionsData->lastItem() }} of {{ $transactionsData->total() }} entries
                                </p>
                                <ul class="pagination m-0 ms-auto">
                                    @if($transactionsData->hasPages())
                                        {{ $transactionsData->appends(['date' => request('date')])->links() }}
                                    @else
                                        <li class="page-item active"><span class="page-link">1</span></li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No transactions recorded for this date
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Deliveries Section -->
            <div class="col-12 mb-4">
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
                                                <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('H:i') : '-' }}</td>
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
                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-muted">
                                    Showing {{ $deliveriesData->firstItem() }} to {{ $deliveriesData->lastItem() }} of {{ $deliveriesData->total() }} entries
                                </p>
                                <ul class="pagination m-0 ms-auto">
                                    @if($deliveriesData->hasPages())
                                        {{ $deliveriesData->appends(['date' => request('date')])->links() }}
                                    @else
                                        <li class="page-item active"><span class="page-link">1</span></li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No deliveries recorded for this date
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Activities Section -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activity Log</h3>
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
                                                <td>{{ $activity->created_at->format('H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                <p class="m-0 text-muted">
                                    Showing {{ $activitiesData->firstItem() }} to {{ $activitiesData->lastItem() }} of {{ $activitiesData->total() }} entries
                                </p>
                                <ul class="pagination m-0 ms-auto">
                                    @if($activitiesData->hasPages())
                                        {{ $activitiesData->appends(['date' => request('date')])->links() }}
                                    @else
                                        <li class="page-item active"><span class="page-link">1</span></li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                No activities recorded for this date
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('dailyPdfForm');
    if (!form) return;

    const errorBox = document.getElementById('dailyPdfFormError');
    const checkboxSelector = 'input[name^="include"]';
    const storageKey = 'daily-report-pdf-toggles';
    const checkboxes = Array.from(form.querySelectorAll(checkboxSelector));

    function saveToggleState() {
        const state = Object.fromEntries(
            checkboxes.map((checkbox) => [checkbox.name, checkbox.checked])
        );

        try {
            window.localStorage.setItem(storageKey, JSON.stringify(state));
        } catch (error) {
            // Ignore storage issues and continue with in-memory form state.
        }
    }

    function restoreToggleState() {
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
            // Ignore invalid saved state and keep server-rendered defaults.
        }
    }

    function hasSelectedSection() {
        return checkboxes.some((checkbox) => checkbox.checked);
    }

    function hideError() {
        if (errorBox) {
            errorBox.classList.add('d-none');
        }
    }

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
});
</script>
@endpush

