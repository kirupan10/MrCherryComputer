@extends('layouts.nexora')

@section('title', 'Liabilities Report')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reports</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 3v18h18"/>
                        <path d="M7 16l4 -4l4 4l4 -4"/>
                    </svg>
                    Liabilities Report
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.external-funds.index') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <line x1="5" y1="12" x2="9" y2="16"/>
                            <line x1="5" y1="12" x2="9" y2="8"/>
                        </svg>
                        Back to Funds
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                            <rect x="7" y="13" width="10" height="8" rx="2"/>
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Funds Received</div>
                        </div>
                        <div class="h1 mb-1">LKR {{ number_format($totalFundsReceived, 2) }}</div>
                        <div class="text-muted">{{ $funds->count() }} fund source(s)</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Outstanding Balance</div>
                        </div>
                        <div class="h1 mb-1 text-danger">LKR {{ number_format($totalOutstanding, 2) }}</div>
                        <div class="text-muted">Amount owed</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Repaid</div>
                        </div>
                        <div class="h1 mb-1 text-success">LKR {{ number_format($totalRepaid, 2) }}</div>
                        <div class="text-muted">Principal paid</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Interest Paid</div>
                        </div>
                        <div class="h1 mb-1 text-warning">LKR {{ number_format($totalInterestPaid, 2) }}</div>
                        <div class="text-muted">Cost of borrowing</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fund Breakdown by Type -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Fund Breakdown by Type</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Fund Type</th>
                            <th class="text-end">Count</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Outstanding</th>
                            <th class="text-end">Repayment %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fundsByType as $type => $data)
                        <tr>
                            <td>
                                @php
                                    $badgeColor = 'secondary';
                                    foreach(\App\Enums\FundType::cases() as $case) {
                                        if($case->value == $type) {
                                            $badgeColor = $case->badgeColor();
                                            break;
                                        }
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}-lt">{{ $type }}</span>
                            </td>
                            <td class="text-end">{{ $data['count'] }}</td>
                            <td class="text-end">LKR {{ number_format($data['total_amount'], 2) }}</td>
                            <td class="text-end">
                                <span class="{{ $data['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                                    LKR {{ number_format($data['outstanding'], 2) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($data['total_amount'] > 0)
                                    {{ number_format((($data['total_amount'] - $data['outstanding']) / $data['total_amount']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No fund data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($fundsByType->count() > 0)
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-end">{{ $fundsByType->sum('count') }}</th>
                            <th class="text-end">LKR {{ number_format($fundsByType->sum('total_amount'), 2) }}</th>
                            <th class="text-end">LKR {{ number_format($fundsByType->sum('outstanding'), 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Active Funds Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Liabilities</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Source Name</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Repaid</th>
                            <th class="text-end">Outstanding</th>
                            <th class="text-end">Interest Paid</th>
                            <th class="text-end">Interest Rate</th>
                            <th>Period</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funds as $fund)
                        <tr>
                            <td><strong>{{ $fund->source_name }}</strong></td>
                            <td>
                                @php
                                    $badgeColor = 'secondary';
                                    foreach(\App\Enums\FundType::cases() as $case) {
                                        if($case->value == $fund->fund_type) {
                                            $badgeColor = $case->badgeColor();
                                            break;
                                        }
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}-lt">{{ $fund->fund_type }}</span>
                            </td>
                            <td class="text-end">LKR {{ number_format($fund->amount, 2) }}</td>
                            <td class="text-end text-success">LKR {{ number_format($fund->total_repaid, 2) }}</td>
                            <td class="text-end">
                                <span class="{{ $fund->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    LKR {{ number_format($fund->outstanding_balance, 2) }}
                                </span>
                            </td>
                            <td class="text-end text-warning">LKR {{ number_format($fund->total_interest_paid, 2) }}</td>
                            <td class="text-end">{{ $fund->interest_rate ? $fund->interest_rate . '%' : '' }}</td>
                            <td>
                                <small>
                                    {{ $fund->start_date->format('d M Y') }}<br>
                                    <span class="text-muted">to {{ $fund->maturity_date ? $fund->maturity_date->format('d M Y') : '' }}</span>
                                </small>
                            </td>
                            <td>
                                @if($fund->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($fund->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-danger">Defaulted</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No liabilities recorded</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($funds->count() > 0)
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-end">LKR {{ number_format($totalFundsReceived, 2) }}</th>
                            <th class="text-end">LKR {{ number_format($totalRepaid, 2) }}</th>
                            <th class="text-end">LKR {{ number_format($totalOutstanding, 2) }}</th>
                            <th class="text-end">LKR {{ number_format($totalInterestPaid, 2) }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
