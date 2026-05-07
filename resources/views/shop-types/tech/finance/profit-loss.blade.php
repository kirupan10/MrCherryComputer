@extends('shop-types.tech.layouts.nexora')

@section('title', 'Profit & Loss Statement')

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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="2"/>
                                <line x1="9" y1="12" x2="9.01" y2="12"/>
                                <line x1="13" y1="12" x2="15" y2="12"/>
                                <line x1="9" y1="16" x2="9.01" y2="16"/>
                                <line x1="13" y1="16" x2="15" y2="16"/>
                            </svg>
                            Profit & Loss Statement
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Detailed revenue and expense breakdown with profit calculations</p>
                    </div>
                    <div>
                        <a href="{{ shop_route('finance.index') }}" class="btn btn-outline-secondary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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

        <!-- Date Range Selector -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('finance.profit-loss') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Group By</label>
                        <select name="group_by" class="form-select">
                            <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Month</option>
                            <option value="year" {{ $groupBy == 'year' ? 'selected' : '' }}>Year</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Generate</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2" style="font-size: 0.875rem;">Total Turnover</div>
                        <h3 class="mb-0 text-primary" style="font-weight: 700;">LKR {{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2" style="font-size: 0.875rem;">Gross Profit</div>
                        <h3 class="mb-0 text-info" style="font-weight: 700;">LKR {{ number_format($grossProfit, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2" style="font-size: 0.875rem;">Total Expenses</div>
                        <h3 class="mb-0 text-danger" style="font-weight: 700;">LKR {{ number_format($totalExpenses, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-secondary mb-2" style="font-size: 0.875rem;">Net Profit</div>
                        <h3 class="mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 700;">
                            LKR {{ number_format($netProfit, 2) }}
                        </h3>
                        <small class="text-muted">{{ number_format($netProfitMargin, 1) }}% Margin</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed P&L Statement -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Revenue Breakdown -->
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Revenue Breakdown</h3>
                    </div>
                    <div class="card-body">
                        @if(count($revenueBreakdown) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($revenueBreakdown as $item)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span style="font-weight: 500;">{{ $item['category'] }}</span>
                                        <span class="badge" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            LKR {{ number_format($item['amount'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="font-size: 1.1rem;">Total Revenue</strong>
                                    <strong style="font-size: 1.25rem;">
                                        LKR {{ number_format($totalRevenue, 2) }}
                                    </strong>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <p>No revenue recorded for this period</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Expense Breakdown -->
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Expense Breakdown</h3>
                    </div>
                    <div class="card-body">
                        @if(count($expenseBreakdown) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($expenseBreakdown as $item)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span style="font-weight: 500;">{{ $item['category'] }}</span>
                                        <span class="badge" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            LKR {{ number_format($item['amount'], 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="font-size: 1.1rem;">Total Expenses</strong>
                                    <strong style="font-size: 1.25rem;">
                                        LKR {{ number_format($totalExpenses, 2) }}
                                    </strong>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <p>No expenses recorded for this period</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Comparison -->
        @if(count($periodComparison) > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Period-by-Period Comparison</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Expenses</th>
                                    <th class="text-end">Profit/Loss</th>
                                    <th class="text-end">Margin %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodComparison as $period)
                                    <tr>
                                        <td><strong>{{ $period['period'] }}</strong></td>
                                        <td class="text-end">
                                            <span class="text-success">LKR {{ number_format($period['revenue'], 2) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-danger">LKR {{ number_format($period['expenses'], 2) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="{{ $period['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                LKR {{ number_format($period['profit'], 2) }}
                                            </strong>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge">
                                                {{ $period['revenue'] > 0 ? number_format(($period['profit'] / $period['revenue']) * 100, 1) : 0 }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr style="font-weight: 700;">
                                    <td>TOTAL</td>
                                    <td class="text-end text-success">LKR {{ number_format($totalRevenue, 2) }}</td>
                                    <td class="text-end text-danger">LKR {{ number_format($totalExpenses, 2) }}</td>
                                    <td class="text-end {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                        LKR {{ number_format($grossProfit, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <span class="badge">
                                            {{ number_format($netProfitMargin, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
