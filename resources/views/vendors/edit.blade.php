@extends('layouts.nexora')

@section('title', 'Edit Supplier')

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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                            </svg>
                            Edit Supplier
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Update supplier information</p>
                    </div>
                    <div>
                        <a href="{{ shop_route('vendors.show', $vendor->id) }}" class="btn btn-secondary btn-lg px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l14 0"/>
                                <path d="M5 12l6 6"/>
                                <path d="M5 12l6 -6"/>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ shop_route('vendors.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <!-- Basic Information -->
                            <h3 class="card-title" style="font-weight: 600; font-size: 1.125rem;">Basic Information</h3>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $vendor->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                           value="{{ old('company_name', $vendor->company_name) }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div style="border-top: 1px solid #e6e7e9; margin: 1.5rem 0; padding-top: 1.5rem;">
                                <h3 class="card-title mb-3" style="font-weight: 600; font-size: 1.125rem;">Contact Information</h3>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $vendor->phone) }}" placeholder="+94 71 234 5678">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $vendor->email) }}" placeholder="supplier@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                          rows="3" placeholder="Enter supplier address">{{ old('address', $vendor->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Additional Information -->
                            <div style="border-top: 1px solid #e6e7e9; margin: 1.5rem 0; padding-top: 1.5rem;">
                                <h3 class="card-title mb-3" style="font-weight: 600; font-size: 1.125rem;">Additional Information</h3>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tax Number / VAT</label>
                                    <input type="text" name="tax_number" class="form-control @error('tax_number') is-invalid @enderror"
                                           value="{{ old('tax_number', $vendor->tax_number) }}">
                                    @error('tax_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $vendor->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $vendor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                          rows="3" placeholder="Add any additional notes about the supplier">{{ old('notes', $vendor->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ shop_route('vendors.show', $vendor->id) }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4" style="font-weight: 600;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <polyline points="9 11 12 14 20 6"/>
                                    <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9"/>
                                </svg>
                                Update Supplier
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Current Stats -->
                    <div class="card mb-3">
                        <div class="card-header" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <polyline points="12 7 12 12 15 15"/>
                            </svg>
                            Current Statistics
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted mb-1" style="font-size: 0.75rem;">Total Purchases</div>
                                <div style="font-weight: 600;">LKR {{ number_format($vendor->total_purchases, 2) }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted mb-1" style="font-size: 0.75rem;">Total Paid</div>
                                <div style="font-weight: 600; color: #28a745;">LKR {{ number_format($vendor->total_paid, 2) }}</div>
                            </div>
                            <div class="mb-0">
                                <div class="text-muted mb-1" style="font-size: 0.75rem;">Outstanding Balance</div>
                                <div style="font-weight: 600; color: #dc3545;">LKR {{ number_format($vendor->outstanding_balance, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card bg-light mb-3">
                        <div class="card-header" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                                <polyline points="11 12 12 12 12 16 13 16"/>
                            </svg>
                            Important Notes
                        </div>
                        <div class="card-body">
                            <ul class="mb-0" style="font-size: 0.875rem; padding-left: 1.25rem;">
                                <li class="mb-2">Changing status to "Inactive" won't affect existing purchases</li>
                                <li class="mb-2">Balance statistics are automatically calculated</li>
                                <li class="mb-2">Contact information is used for communication</li>
                                <li class="mb-0">All linked purchases will reflect updated supplier name</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Delete Section (if no purchases) -->
                    @if($vendor->creditPurchases->count() == 0)
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                            </svg>
                            Danger Zone
                        </div>
                        <div class="card-body">
                            <p style="font-size: 0.875rem; margin-bottom: 0.5rem;">This supplier has no purchases and can be deleted.</p>
                            <form action="{{ shop_route('vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">Delete Supplier</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
