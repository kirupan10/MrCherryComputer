@extends('shop-types.tech.layouts.nexora')

@section('title', 'External Funds')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Finance</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 2 0 0 1 -2 -2v-12"/>
                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                    </svg>
                    External Funds
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.external-funds.report') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="3" y1="3" x2="21" y2="21"/>
                            <path d="M8 7v11"/>
                            <path d="M12 3v18"/>
                            <path d="M16 8v9"/>
                            <path d="M20 11v4"/>
                        </svg>
                        View Report
                    </a>
                    <a href="{{ shop_route('reports.external-funds.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add External Fund
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Funds Received</div>
                        </div>
                        <div class="h1 mb-1">LKR {{ number_format($totalFundsReceived, 2) }}</div>
                        <div class="text-muted">All time</div>
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
                        <div class="text-muted">Current debt</div>
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
                            <div class="subheader">Interest Paid</div>
                        </div>
                        <div class="h1 mb-1 text-warning">LKR {{ number_format($totalInterestPaid, 2) }}</div>
                        <div class="text-muted">All time</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('reports.external-funds.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="defaulted" {{ request('status') == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fund Type</label>
                        <select name="fund_type" class="form-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach(\App\Enums\FundType::values() as $type)
                                <option value="{{ $type }}" {{ request('fund_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by source name..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        @if(request()->hasAny(['status', 'fund_type', 'search']))
                            <a href="{{ shop_route('reports.external-funds.index') }}" class="btn btn-secondary w-100">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Funds List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">External Funds</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Source Name</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Outstanding</th>
                            <th class="text-end">Interest Rate</th>
                            <th>Start Date</th>
                            <th>Maturity Date</th>
                            <th>Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funds as $fund)
                        <tr>
                            <td>
                                <strong>{{ $fund->source_name }}</strong>
                            </td>
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
                            <td class="text-end">
                                <strong>LKR {{ number_format($fund->amount, 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="{{ $fund->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    LKR {{ number_format($fund->outstanding_balance, 2) }}
                                </span>
                            </td>
                            <td class="text-end">
                                {{ $fund->interest_rate ? $fund->interest_rate . '%' : '' }}
                            </td>
                            <td>{{ $fund->start_date->format('d M Y') }}</td>
                            <td>{{ $fund->maturity_date ? $fund->maturity_date->format('d M Y') : '' }}</td>
                            <td>
                                @if($fund->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($fund->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-danger">Defaulted</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ shop_route('reports.external-funds.show', $fund) }}" class="btn btn-ghost-primary btn-sm" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <circle cx="12" cy="12" r="2"/>
                                            <path d="M2 12c0 -1 2.906 -6 10 -6s10 5 10 6c0 1 -2.906 6 -10 6s -10 -5 -10 -6"/>
                                        </svg>
                                    </a>
                                    @if($fund->status == 'active')
                                    <button type="button" class="btn btn-ghost-success btn-sm" title="Add Repayment"
                                            data-bs-toggle="modal" data-bs-target="#addRepaymentModal"
                                            data-fund-id="{{ $fund->id }}"
                                            data-fund-name="{{ $fund->source_name }}"
                                            onclick="setRepaymentFund(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                            <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                        </svg>
                                    </button>
                                    @endif
                                    <a href="{{ shop_route('reports.external-funds.edit', $fund) }}" class="btn btn-ghost-warning btn-sm" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            <path d="M16 5l3 3"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No external funds found. <a href="{{ shop_route('reports.external-funds.create') }}">Add your first external fund</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($funds->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $funds->firstItem() }} to {{ $funds->lastItem() }} of {{ $funds->total() }} funds
                    </div>
                    <div>
                        {{ $funds->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Repayment Modal -->
<div class="modal fade" id="addRepaymentModal" tabindex="-1" aria-labelledby="addRepaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="repaymentForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRepaymentModalLabel">Add Repayment - <span id="fundNameDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Principal Amount (LKR)</label>
                        <input type="number" name="principal_amount" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Interest Amount (LKR)</label>
                        <input type="number" name="interest_amount" class="form-control" step="0.01" min="0" placeholder="0.00" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Online Payment">Online Payment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Transaction reference">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Repayment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function setRepaymentFund(button) {
    const fundId = button.getAttribute('data-fund-id');
    const fundName = button.getAttribute('data-fund-name');

    // Update modal title
    document.getElementById('fundNameDisplay').textContent = fundName;

    // Update form action
    const form = document.getElementById('repaymentForm');
    form.action = '{{ url('reports/external-funds') }}/' + fundId + '/repayments';

    // Reset form fields
    form.reset();
    form.querySelector('[name="payment_date"]').value = '{{ date('Y-m-d') }}';
    form.querySelector('[name="interest_amount"]').value = '0';
}
</script>
@endsection
