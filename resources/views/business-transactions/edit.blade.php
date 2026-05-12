@extends('layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $activeShop->name }}</div>
                <h2 class="page-title">Edit Transaction #{{ $businessTransaction->id }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ shop_route('business-transactions.show', $businessTransaction) }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <line x1="5" y1="12" x2="9" y2="16" />
                        <line x1="5" y1="12" x2="9" y2="8" />
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <form action="{{ shop_route('business-transactions.update', $businessTransaction) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaction Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Transaction Date & Time</label>
                                    <input type="datetime-local" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', $businessTransaction->transaction_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('transaction_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Transaction Type</label>
                                    <select name="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="purchase" {{ old('transaction_type', $businessTransaction->transaction_type) == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                        <option value="expense" {{ old('transaction_type', $businessTransaction->transaction_type) == 'expense' ? 'selected' : '' }}>Expense</option>
                                        <option value="payment" {{ old('transaction_type', $businessTransaction->transaction_type) == 'payment' ? 'selected' : '' }}>Payment</option>
                                        <option value="refund" {{ old('transaction_type', $businessTransaction->transaction_type) == 'refund' ? 'selected' : '' }}>Refund</option>
                                        <option value="commission" {{ old('transaction_type', $businessTransaction->transaction_type) == 'commission' ? 'selected' : '' }}>Commission</option>
                                        <option value="owner_personal" {{ old('transaction_type', $businessTransaction->transaction_type) == 'owner_personal' ? 'selected' : '' }}>Owner Personal Expenses</option>
                                        <option value="other" {{ old('transaction_type', $businessTransaction->transaction_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('transaction_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Vendor/Supplier Name</label>
                                    <input type="text" name="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror" value="{{ old('vendor_name', $businessTransaction->vendor_name) }}" placeholder="Enter vendor name">
                                    @error('vendor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receipt Number (from Vendor)</label>
                                    <input type="text" name="receipt_number" class="form-control @error('receipt_number') is-invalid @enderror" value="{{ old('receipt_number', $businessTransaction->receipt_number) }}" placeholder="Vendor receipt number">
                                    @error('receipt_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Reference Number (Our Transaction)</label>
                                    <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror" value="{{ old('reference_number', $businessTransaction->reference_number) }}" placeholder="Bank transfer reference">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select @error('category') is-invalid @enderror">
                                        <option value="">Select Category</option>
                                        <option value="inventory" {{ old('category', $businessTransaction->category) == 'inventory' ? 'selected' : '' }}>Inventory</option>
                                        <option value="utilities" {{ old('category', $businessTransaction->category) == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                        <option value="salaries" {{ old('category', $businessTransaction->category) == 'salaries' ? 'selected' : '' }}>Salaries</option>
                                        <option value="rent" {{ old('category', $businessTransaction->category) == 'rent' ? 'selected' : '' }}>Rent</option>
                                        <option value="maintenance" {{ old('category', $businessTransaction->category) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="marketing" {{ old('category', $businessTransaction->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                        <option value="transportation" {{ old('category', $businessTransaction->category) == 'transportation' ? 'selected' : '' }}>Transportation</option>
                                        <option value="other" {{ old('category', $businessTransaction->category) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Paid By (Method)</label>
                                    <select name="paid_by" class="form-select @error('paid_by') is-invalid @enderror">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('paid_by', $businessTransaction->paid_by) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="card" {{ old('paid_by', $businessTransaction->paid_by) == 'card' ? 'selected' : '' }}>Card</option>
                                        <option value="bank_transfer" {{ old('paid_by', $businessTransaction->paid_by) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="upi" {{ old('paid_by', $businessTransaction->paid_by) == 'upi' ? 'selected' : '' }}>UPI</option>
                                        <option value="credit" {{ old('paid_by', $businessTransaction->paid_by) == 'credit' ? 'selected' : '' }}>Credit</option>
                                        <option value="cheque" {{ old('paid_by', $businessTransaction->paid_by) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                    @error('paid_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Paid By (User)</label>
                                    <select name="paid_by_user_id" class="form-select @error('paid_by_user_id') is-invalid @enderror">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('paid_by_user_id', $businessTransaction->paid_by_user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('paid_by_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Enter transaction description">{{ old('description', $businessTransaction->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Attachment (Receipt/Invoice)</label>
                                @if($businessTransaction->attachment_path)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($businessTransaction->attachment_path) }}" target="_blank" class="btn btn-sm btn-secondary">
                                            View Current Attachment
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-hint">Accepted formats: PDF, JPG, PNG (Max: 5MB). Leave empty to keep current attachment.</small>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Amount Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" step="0.01" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $businessTransaction->total_amount) }}" required min="0">
                                </div>
                                @error('total_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" step="0.01" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', $businessTransaction->discount_amount) }}" min="0">
                                </div>
                                @error('discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="completed" {{ old('status', $businessTransaction->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ old('status', $businessTransaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="cancelled" {{ old('status', $businessTransaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                    <circle cx="12" cy="14" r="2" />
                                    <polyline points="14 4 14 8 8 8 8 4" />
                                </svg>
                                Update Transaction
                            </button>
                            <button type="button" class="btn btn-danger w-100" onclick="if(confirm('Are you sure you want to delete this transaction?')) { document.getElementById('delete-form').submit(); }">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="4" y1="7" x2="20" y2="7" />
                                    <line x1="10" y1="11" x2="10" y2="17" />
                                    <line x1="14" y1="11" x2="14" y2="17" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                                Delete Transaction
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ shop_route('business-transactions.destroy', $businessTransaction) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
