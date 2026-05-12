@extends('layouts.nexora')

@section('title', 'Fund Details - ' . $externalFund->source_name)

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Liabilities</div>
                <h2 class="page-title">{{ $externalFund->source_name }}</h2>
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
                        Back to List
                    </a>
                    <a href="{{ shop_route('reports.external-funds.edit', $externalFund) }}" class="btn btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Edit
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="4" y1="7" x2="20" y2="7"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                        </svg>
                        Delete
                    </button>
                    @if($externalFund->isActive())
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRepaymentModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Repayment
                    </button>
                    @endif
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

        <div class="row mb-4">
            <!-- Fund Details Card -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fund Information</h3>
                        <div class="card-actions">
                            @if($externalFund->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($externalFund->status == 'completed')
                                <span class="badge bg-info">Completed</span>
                            @else
                                <span class="badge bg-danger">Defaulted</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Source Name</label>
                                <div><strong>{{ $externalFund->source_name }}</strong></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fund Type</label>
                                <div>
                                    @php
                                        $badgeColor = 'secondary';
                                        foreach(\App\Enums\FundType::cases() as $case) {
                                            if($case->value == $externalFund->fund_type) {
                                                $badgeColor = $case->badgeColor();
                                                break;
                                            }
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}-lt">{{ $externalFund->fund_type }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <div class="h3 mb-0">LKR {{ number_format($externalFund->amount, 2) }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Interest Rate</label>
                                <div class="h3 mb-0">{{ $externalFund->interest_rate ? $externalFund->interest_rate . '%' : '' }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <div>{{ $externalFund->start_date->format('d M Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maturity Date</label>
                                <div>{{ $externalFund->maturity_date ? $externalFund->maturity_date->format('d M Y') : 'Not specified' }}</div>
                            </div>
                        </div>

                        @if($externalFund->repayment_terms)
                        <div class="mb-3">
                            <label class="form-label">Repayment Terms</label>
                            <div class="text-secondary">{{ $externalFund->repayment_terms }}</div>
                        </div>
                        @endif

                        @if($externalFund->notes)
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <div class="text-secondary">{{ $externalFund->notes }}</div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Created By</label>
                                <div>{{ $externalFund->creator->name ?? 'System' }} on {{ $externalFund->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="subheader">Outstanding Balance</div>
                                <div class="h1 mb-0 {{ $externalFund->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    LKR {{ number_format($externalFund->outstanding_balance, 2) }}
                                </div>
                                @if($externalFund->amount > 0)
                                <div class="text-muted small">
                                    {{ number_format(($externalFund->total_principal_paid / $externalFund->amount) * 100, 1) }}% repaid
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="subheader">Total Repaid</div>
                                <div class="h2 mb-0 text-success">
                                    LKR {{ number_format($externalFund->total_repaid, 2) }}
                                </div>
                                <div class="text-muted small">
                                    Principal: LKR {{ number_format($externalFund->total_principal_paid, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="subheader">Interest Paid</div>
                                <div class="h2 mb-0 text-warning">
                                    LKR {{ number_format($externalFund->total_interest_paid, 2) }}
                                </div>
                                <div class="text-muted small">
                                    {{ $repayments->count() }} payment(s) made
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repayments History -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Repayment History</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th class="text-end">Principal</th>
                            <th class="text-end">Interest</th>
                            <th class="text-end">Total Amount</th>
                            <th>Payment Method</th>
                            <th>Reference</th>
                            <th>Recorded By</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repayments as $repayment)
                        <tr>
                            <td>{{ $repayment->payment_date->format('d M Y') }}</td>
                            <td class="text-end">LKR {{ number_format($repayment->principal_amount, 2) }}</td>
                            <td class="text-end">LKR {{ number_format($repayment->interest_amount, 2) }}</td>
                            <td class="text-end"><strong>LKR {{ number_format($repayment->total_amount, 2) }}</strong></td>
                            <td>{{ $repayment->payment_method ?? '' }}</td>
                            <td>{{ $repayment->reference_number ?? '-' }}</td>
                            <td>{{ $repayment->recorder->name ?? 'System' }}</td>
                            <td>
                                <form action="{{ shop_route('reports.external-funds.repayments.delete', $repayment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this repayment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No repayments recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Repayment Modal -->
<div class="modal fade" id="addRepaymentModal" tabindex="-1" aria-labelledby="addRepaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ shop_route('reports.external-funds.repayments.add', $externalFund) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRepaymentModalLabel">Add Repayment</h5>
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
                        <textarea name="notes" class="form-control" rows="2"></textarea>
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

<!-- Delete Confirmation Modal -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <h3>Are you sure?</h3>
                <div class="text-muted">Do you really want to delete this liability record? This will also delete all associated repayment records. This action cannot be undone.</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                        </div>
                        <div class="col">
                            <form action="{{ shop_route('reports.external-funds.destroy', $externalFund) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
