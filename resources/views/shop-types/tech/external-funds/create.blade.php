@extends('shop-types.tech.layouts.nexora')

@section('title', 'Add External Fund')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Finance</div>
                <h2 class="page-title">Add External Fund</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ shop_route('reports.external-funds.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <line x1="5" y1="12" x2="9" y2="16"/>
                        <line x1="5" y1="12" x2="9" y2="8"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ shop_route('reports.external-funds.store') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Fund Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Source Name</label>
                                    <input type="text" name="source_name" class="form-control @error('source_name') is-invalid @enderror" value="{{ old('source_name') }}" placeholder="e.g., ABC Bank, John Doe Investment" required>
                                    @error('source_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Fund Type</label>
                                    <select name="fund_type" class="form-select @error('fund_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        @foreach($fundTypes as $type)
                                            <option value="{{ $type }}" {{ old('fund_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('fund_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Amount (LKR)</label>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Interest Rate (%)</label>
                                    <input type="number" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate') }}" step="0.01" min="0" max="100" placeholder="e.g., 12.5">
                                    <small class="form-hint">Leave blank for non-loan funds</small>
                                    @error('interest_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Start Date</label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Maturity Date</label>
                                    <input type="date" name="maturity_date" class="form-control @error('maturity_date') is-invalid @enderror" value="{{ old('maturity_date') }}">
                                    <small class="form-hint">Optional - for loans with fixed term</small>
                                    @error('maturity_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Repayment Terms</label>
                                <textarea name="repayment_terms" class="form-control @error('repayment_terms') is-invalid @enderror" rows="3" placeholder="e.g., Monthly installments of LKR 50,000 for 24 months">{{ old('repayment_terms') }}</textarea>
                                @error('repayment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="defaulted" {{ old('status') == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <a href="{{ shop_route('reports.external-funds.index') }}" class="btn btn-link">Cancel</a>
                                <button type="submit" class="btn btn-primary ms-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    Add Fund
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                                <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"/>
                            </svg>
                            Tips & Guidelines
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary me-2">1</span>
                                    <div>
                                        <div class="fw-bold">Source Name</div>
                                        <div class="text-muted">Enter the bank name, investor name, or organization providing the fund</div>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-info me-2">2</span>
                                    <div>
                                        <div class="fw-bold">Fund Type</div>
                                        <div class="text-muted">Choose between Bank Loan, Angel Investor, Venture Capital, Personal Loan, Government Grant, Line of Credit, Crowdfunding, or Other</div>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-warning me-2">3</span>
                                    <div>
                                        <div class="fw-bold">Interest Rate</div>
                                        <div class="text-muted">Enter the annual interest rate (if applicable). Leave blank for equity investments or grants</div>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-success me-2">4</span>
                                    <div>
                                        <div class="fw-bold">Repayment Terms</div>
                                        <div class="text-muted">Describe the repayment schedule, frequency, and any special conditions</div>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-0">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-purple me-2">5</span>
                                    <div>
                                        <div class="fw-bold">Track Repayments</div>
                                        <div class="text-muted">After creating, you can add repayment records to track principal and interest payments</div>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <hr class="my-3">

                        <div class="alert alert-info mb-0">
                            <div class="d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 9v2m0 4v.01"/>
                                    <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                                </svg>
                                <div>
                                    <strong>Important:</strong> All external funds are tracked separately from your regular business transactions. They will appear in your financial reports under External Funds section.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
