@extends('layouts.nexora')

@section('title', 'Edit Liability')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Finance</div>
                <h2 class="page-title">Edit Liability</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ shop_route('reports.external-funds.show', $externalFund) }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <line x1="5" y1="12" x2="9" y2="16"/>
                        <line x1="5" y1="12" x2="9" y2="8"/>
                    </svg>
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <form action="{{ shop_route('reports.external-funds.update', $externalFund) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Fund Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Source Name</label>
                                    <input type="text" name="source_name" class="form-control @error('source_name') is-invalid @enderror" value="{{ old('source_name', $externalFund->source_name) }}" placeholder="e.g., ABC Bank, John Doe Investment" required>
                                    @error('source_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Fund Type</label>
                                    <select name="fund_type" class="form-select @error('fund_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        @foreach($fundTypes as $type)
                                            <option value="{{ $type }}" {{ old('fund_type', $externalFund->fund_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
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
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $externalFund->amount) }}" step="0.01" min="0" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Interest Rate (%)</label>
                                    <input type="number" name="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" value="{{ old('interest_rate', $externalFund->interest_rate) }}" step="0.01" min="0" max="100" placeholder="e.g., 12.5">
                                    <small class="form-hint">Leave blank for non-loan funds</small>
                                    @error('interest_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Start Date</label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $externalFund->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Maturity Date</label>
                                    <input type="date" name="maturity_date" class="form-control @error('maturity_date') is-invalid @enderror" value="{{ old('maturity_date', $externalFund->maturity_date?->format('Y-m-d')) }}">
                                    <small class="form-hint">Optional - for loans with fixed term</small>
                                    @error('maturity_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Repayment Terms</label>
                                <textarea name="repayment_terms" class="form-control @error('repayment_terms') is-invalid @enderror" rows="3" placeholder="e.g., Monthly installments of LKR 50,000 for 24 months">{{ old('repayment_terms', $externalFund->repayment_terms) }}</textarea>
                                @error('repayment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $externalFund->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status', $externalFund->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="defaulted" {{ old('status', $externalFund->status) == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Any additional information...">{{ old('notes', $externalFund->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex justify-content-between">
                                <form action="{{ shop_route('reports.external-funds.destroy', $externalFund) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this fund?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete Fund</button>
                                </form>
                                <div class="d-flex">
                                    <a href="{{ shop_route('reports.external-funds.show', $externalFund) }}" class="btn btn-link">Cancel</a>
                                    <button type="submit" class="btn btn-primary ms-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                        Update Fund
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
