@extends('layouts.nexora')

@section('title', 'Supplier Details')

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
                            {{ $vendor->name }}
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Supplier details and purchase history</p>
                    </div>
                    <div class="btn-list">
                        <a href="{{ shop_route('vendors.edit', $vendor->id) }}" class="btn btn-warning btn-lg px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                <path d="M16 5l3 3"/>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ shop_route('vendors.index') }}" class="btn btn-secondary btn-lg px-4 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l14 0"/>
                                <path d="M5 12l6 6"/>
                                <path d="M5 12l6 -6"/>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Status</div>
                        <h2 class="mb-0">
                            @if($vendor->status == 'active')
                                <span class="badge bg-success" style="font-size: 1rem;">Active</span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 1rem;">Inactive</span>
                            @endif
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Purchases</div>
                        <h2 class="mb-0" style="font-weight: 700;">LKR {{ number_format($vendor->total_purchases, 2) }}</h2>
                        <small class="text-muted">{{ $purchaseStats['total_purchases'] }} purchases</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Paid</div>
                        <h2 class="mb-0 text-success" style="font-weight: 700;">LKR {{ number_format($vendor->total_paid, 2) }}</h2>
                        <small class="text-muted">Payments received</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Outstanding</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">LKR {{ number_format($vendor->outstanding_balance, 2) }}</h2>
                        <small class="text-muted">{{ $purchaseStats['pending_purchases'] }} pending</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Supplier Information -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Supplier Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Name</div>
                            </div>
                            <div class="col-7">
                                <div style="font-weight: 600;">{{ $vendor->name }}</div>
                            </div>
                        </div>

                        @if($vendor->company_name)
                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Company</div>
                            </div>
                            <div class="col-7">
                                <div>{{ $vendor->company_name }}</div>
                            </div>
                        </div>
                        @endif

                        @if($vendor->phone)
                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Phone</div>
                            </div>
                            <div class="col-7">
                                <div>{{ $vendor->phone }}</div>
                            </div>
                        </div>
                        @endif

                        @if($vendor->email)
                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Email</div>
                            </div>
                            <div class="col-7">
                                <div>{{ $vendor->email }}</div>
                            </div>
                        </div>
                        @endif

                        @if($vendor->address)
                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Address</div>
                            </div>
                            <div class="col-7">
                                <div>{{ $vendor->address }}</div>
                            </div>
                        </div>
                        @endif

                        @if($vendor->tax_number)
                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Tax Number</div>
                            </div>
                            <div class="col-7">
                                <div>{{ $vendor->tax_number }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-5">
                                <div class="text-muted" style="font-size: 0.875rem; font-weight: 500;">Created</div>
                            </div>
                            <div class="col-7">
                                <div>{{ $vendor->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">by {{ $vendor->creator->name }}</small>
                            </div>
                        </div>

                        @if($vendor->notes)
                        <div class="row mb-0">
                            <div class="col-12">
                                <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Notes</div>
                                <div class="bg-light p-3 rounded">{{ $vendor->notes }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Purchase History -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Recent Purchases</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($vendor->creditPurchases->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vendor->creditPurchases->take(10) as $purchase)
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600;">{{ $purchase->purchase_date->format('M d, Y') }}</div>
                                                @if($purchase->reference_number)
                                                    <small class="text-muted">Ref: {{ $purchase->reference_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>LKR {{ number_format($purchase->total_amount, 2) }}</div>
                                                @if($purchase->due_amount > 0)
                                                    <small class="text-danger">Due: LKR {{ number_format($purchase->due_amount, 2) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($purchase->status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($purchase->status == 'partial')
                                                    <span class="badge bg-warning">Partial</span>
                                                @else
                                                    <span class="badge bg-danger">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ shop_route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-ghost-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($vendor->creditPurchases->count() > 10)
                                <div class="card-footer">
                                    <a href="{{ shop_route('purchases.index', ['search' => $vendor->name]) }}" class="btn btn-link">
                                        View All Purchases →
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="empty">
                                <p class="empty-title">No purchases yet</p>
                                <p class="empty-subtitle text-muted">
                                    This supplier has no purchase records.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ shop_route('purchases.create') }}" class="btn btn-primary">
                                        Create Purchase
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Payment Transactions History -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Recent Payments</h3>
                    </div>
                    <div class="card-body p-0">
                        @php $payments = $vendor->payments()->orderByDesc('payment_date')->take(10)->get(); @endphp
                        @if($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>LKR {{ number_format($payment->payment_amount, 2) }}</td>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->payment_reference }}</td>
                                            <td>{{ $payment->notes }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty">
                                <p class="empty-title">No payments yet</p>
                                <p class="empty-subtitle text-muted">
                                    This supplier has no payment records.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
