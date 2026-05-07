@extends('shop-types.tech.layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $activeShop->name }}</div>
                <h2 class="page-title">Transaction #{{ $businessTransaction->id }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('business-transactions.index') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <line x1="5" y1="12" x2="9" y2="16" />
                            <line x1="5" y1="12" x2="9" y2="8" />
                        </svg>
                        Back to List
                    </a>
                    <a href="{{ route('business-transactions.edit', $businessTransaction) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction Details</h3>
                        <div class="ms-auto">
                            @if($businessTransaction->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($businessTransaction->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Transaction Date</label>
                                    <div>{{ $businessTransaction->transaction_date->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Transaction Type</label>
                                    <div><span class="badge bg-blue-lt">{{ $businessTransaction->formatted_type }}</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Vendor/Supplier</label>
                                    <div>{{ $businessTransaction->vendor_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Receipt Number (Vendor)</label>
                                    <div>{{ $businessTransaction->receipt_number ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Reference Number (Our Transaction)</label>
                                    <div>{{ $businessTransaction->reference_number ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Category</label>
                                    <div>{{ ucfirst($businessTransaction->category ?? 'N/A') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Payment Method</label>
                                    <div>{{ $businessTransaction->formatted_paid_by }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Paid By User</label>
                                    <div>{{ $businessTransaction->paidByUser->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Created By</label>
                                    <div>{{ $businessTransaction->creator->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-uppercase" style="font-size: 0.75rem; color: #6c757d;">Created At</label>
                                    <div>{{ $businessTransaction->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        </div>

                        @if($businessTransaction->description)
                            <div class="mt-4">
                                <h4>Description</h4>
                                <p class="text-muted">{{ $businessTransaction->description }}</p>
                            </div>
                        @endif

                        @if($businessTransaction->attachment_path)
                            <div class="mt-4">
                                <h4>Attachment</h4>
                                <a href="{{ Storage::url($businessTransaction->attachment_path) }}" target="_blank" class="btn btn-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    </svg>
                                    View Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Amount Breakdown</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Amount</div>
                                <div class="datagrid-content">LKR {{ number_format($businessTransaction->total_amount, 2) }}</div>
                            </div>
                            @if($businessTransaction->discount_amount > 0)
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Discount Amount</div>
                                    <div class="datagrid-content text-danger">- LKR {{ number_format($businessTransaction->discount_amount, 2) }}</div>
                                </div>
                            @endif
                            <div class="datagrid-item">
                                <div class="datagrid-title"><strong>Net Amount</strong></div>
                                <div class="datagrid-content"><strong>LKR {{ number_format($businessTransaction->net_amount, 2) }}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('business-transactions.edit', $businessTransaction) }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Edit Transaction
                            </a>
                            @if(auth()->user()->isShopOwner() || auth()->user()->isAdmin())
                                <button type="button" class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this transaction?')) { document.getElementById('delete-form').submit(); }">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="delete-form" action="{{ route('business-transactions.destroy', $businessTransaction) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
