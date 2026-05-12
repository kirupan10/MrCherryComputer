@extends('layouts.nexora')

@section('title', 'Warranty Claim Details')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Service Management
                </div>
                <h2 class="page-title">
                    Warranty Claim #{{ $warrantyClaim->id }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('warranty-claims.edit', $warrantyClaim->id) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ shop_route('warranty-claims.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Claim Information</h3>
                        <div class="card-actions">
                            <span class="badge {{ $warrantyClaim->status_badge }}">
                                {{ $warrantyClaim->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Product</label>
                                <div class="fw-bold">{{ $warrantyClaim->product->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $warrantyClaim->product->code ?? '' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Customer</label>
                                <div class="fw-bold">{{ $warrantyClaim->customer->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $warrantyClaim->customer->phone ?? '' }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Serial Number</label>
                                <div class="fw-bold">
                                    <span class="badge bg-azure-lt">{{ $warrantyClaim->serial_number }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Vendor</label>
                                <div class="fw-bold">{{ $warrantyClaim->vendor ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Issue Description</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        {{ $warrantyClaim->issue_description }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($warrantyClaim->resolution_notes)
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label text-muted">Resolution Notes</label>
                                <div class="card bg-success-lt">
                                    <div class="card-body">
                                        {{ $warrantyClaim->resolution_notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Shipping Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Sending Method</label>
                                <div class="fw-bold">
                                    @if($warrantyClaim->sending_method == 'courier')
                                        <span class="badge bg-blue">Courier</span>
                                    @elseif($warrantyClaim->sending_method == 'handover')
                                        <span class="badge bg-green">Handover</span>
                                    @else
                                        <span class="badge bg-orange">Bus</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Tracking Number</label>
                                <div class="fw-bold">{{ $warrantyClaim->tracking_number ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Sending Date</label>
                                <div class="fw-bold">
                                    @if($warrantyClaim->sending_date)
                                        {{ $warrantyClaim->sending_date->format('d M Y') }}
                                    @else
                                        <span class="text-muted">Not sent yet</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($warrantyClaim->claim_receipt_file)
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label text-muted">Warranty Claim Receipt</label>
                                <div>
                                    <a href="{{ asset('storage/' . $warrantyClaim->claim_receipt_file) }}"
                                       target="_blank" class="btn btn-outline-primary btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                        </svg>
                                        View Receipt File
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Expected Return Date</label>
                                <div class="fw-bold">
                                    @if($warrantyClaim->expected_return_date)
                                        {{ $warrantyClaim->expected_return_date->format('d M Y') }}
                                        @if($warrantyClaim->expected_return_date->isPast() && !$warrantyClaim->actual_return_date)
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Actual Return Date</label>
                                <div class="fw-bold">
                                    @if($warrantyClaim->actual_return_date)
                                        {{ $warrantyClaim->actual_return_date->format('d M Y') }}
                                        <span class="badge bg-success ms-2">Returned</span>
                                    @else
                                        <span class="text-muted">Not returned yet</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-2">
                            <div class="col-12">
                                <label class="form-label text-muted">Created At</label>
                                <div>{{ $warrantyClaim->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <label class="form-label text-muted">Created By</label>
                                <div>{{ $warrantyClaim->creator->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <label class="form-label text-muted">Last Updated</label>
                                <div>{{ $warrantyClaim->updated_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($warrantyClaim->order_id)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Related Order</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label text-muted">Order Invoice</label>
                                <div class="fw-bold">{{ $warrantyClaim->order->invoice_no ?? 'N/A' }}</div>
                                <a href="{{ shop_route('orders.show', $warrantyClaim->order_id) }}" class="btn btn-outline-primary btn-sm mt-2">
                                    View Order Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card mt-3">
                    <div class="card-body">
                        <form action="{{ shop_route('warranty-claims.destroy', $warrantyClaim->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this warranty claim? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="4" y1="7" x2="20" y2="7"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                </svg>
                                Delete Warranty Claim
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
