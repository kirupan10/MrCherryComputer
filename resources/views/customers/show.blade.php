@extends('layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Customer Relations
                </div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                    </svg>
                    {{ $customer->name }}
                </h2>
                <p class="text-muted">{{ $customer->phone ?? 'No phone provided' }}</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('customers.edit', $customer) }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                            <path d="M16 5l3 3"/>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ shop_route('customers.index') }}" class="btn btn-outline-secondary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <polyline points="15 6 9 12 15 18"/>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
        @include('partials._breadcrumbs', ['model' => $customer])
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Top Stats Row - Full Width -->
        <div class="row row-cards mb-3">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="h3 mb-0">{{ $customer->orders->count() }}</div>
                                <div class="text-muted">Total Orders</div>
                            </div>
                            <div class="ms-auto">
                                <div class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="9" cy="6" r="3"/>
                                        <path d="M3 9a6 6 0 1 0 12 0"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="h3 mb-0">LKR {{ number_format($customer->orders->sum('total'), 0) }}</div>
                                <div class="text-muted">Total Amount</div>
                            </div>
                            <div class="ms-auto">
                                <div class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 3l20 7l-20 7l-20 -7l20 -7"/>
                                        <polyline points="12 12 12 21"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="h3 mb-0">@if($customer->account_holder) Premium @else Regular @endif</div>
                                <div class="text-muted">Account Type</div>
                            </div>
                            <div class="ms-auto">
                                <div class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 3l7 4v7a6 6 0 0 1 -7 6a6 6 0 0 1 -7 -6v-7l7 -4"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="h3 mb-0">{{ $customer->created_at->format('M Y') }}</div>
                                <div class="text-muted">Join Date</div>
                            </div>
                            <div class="ms-auto">
                                <div class="bg-info text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/>
                                        <line x1="12" y1="12" x2="20" y2="7.5"/>
                                        <line x1="12" y1="12" x2="12" y2="21"/>
                                        <line x1="12" y1="12" x2="4" y2="7.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left: Customer Info, Right: Purchase History -->
        <div class="row row-cards">
            <!-- Left Column: Customer Information -->
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Information</h3>
                    </div>
                    <div class="card-body">
                        <!-- Customer Header with Avatar -->
                        <div class="d-flex mb-3 pb-3" style="border-bottom: 1px solid #e3e6f0;">
                            <div class="avatar avatar-lg me-3" style="background-color: {{ '#' . substr(md5($customer->name), 0, 6) }}; width: 3.5rem; height: 3.5rem; font-size: 1.5rem;">
                                {{ substr($customer->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $customer->name }}</div>
                                <div class="text-muted small">@if($customer->account_holder) Account Holder @else Regular Customer @endif</div>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="mb-3">
                            <span class="form-label">Email</span>
                            <div>{{ $customer->email ?? 'N/A' }}</div>
                        </div>

                        <div class="mb-3">
                            <span class="form-label">Phone</span>
                            <div>{{ $customer->phone ?? 'N/A' }}</div>
                        </div>

                        <div class="mb-3">
                            <span class="form-label">Address</span>
                            <div>{{ $customer->address ?? 'N/A' }}</div>
                        </div>

                        @if($customer->account_holder)
                            <hr class="my-3">
                            <div class="mb-3">
                                <span class="form-label">Account Holder</span>
                                <div>{{ $customer->account_holder }}</div>
                            </div>

                            <div class="mb-3">
                                <span class="form-label">Account Number</span>
                                <div>{{ $customer->account_number }}</div>
                            </div>

                            <div class="mb-0">
                                <span class="form-label">Bank Name</span>
                                <div>{{ $customer->bank_name }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Purchase History -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Purchase History</h3>
                    </div>
                    @if($customer->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th class="w-1">#</th>
                                        <th>Invoice No</th>
                                        <th>Order Date</th>
                                        <th>Items</th>
                                        <th>Amount</th>
                                        <th>Payment Type</th>
                                        <th class="w-1">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders as $order)
                                        <tr>
                                            <td class="text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-blue-lt">{{ $order->invoice_no ?? 'ORD' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $order->details->count() }} items</span>
                                            </td>
                                            <td class="fw-bold">LKR {{ number_format($order->total, 2) }}</td>
                                            <td>
                                                @php
                                                    $ptype = strtolower($order->payment_type ?? '');
                                                @endphp
                                                @if($ptype === 'credit sales' || $ptype === 'credit')
                                                    <span class="badge bg-warning">Credit</span>
                                                @elseif($ptype === 'card')
                                                    <span class="badge bg-purple">Card</span>
                                                @elseif($ptype === 'bank transfer')
                                                    <span class="badge bg-info text-dark">Bank</span>
                                                @else
                                                    <span class="badge bg-success">Cash</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-list flex-nowrap">
                                                    <button type="button" class="btn btn-white btn-icon btn-sm" onclick="viewOrderInModal({{ $order->id }})" data-bs-toggle="modal" data-bs-target="#orderReceiptModal" title="View Details">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="2"/>
                                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                        </svg>
                                                    </button>
                                                    <a href="{{ shop_route('orders.download-pdf-bill', $order->id) }}" class="btn btn-white btn-icon btn-sm" title="Download PDF">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                            <line x1="9" y1="9" x2="10" y2="9"/>
                                                            <line x1="9" y1="13" x2="15" y2="13"/>
                                                            <line x1="9" y1="17" x2="15" y2="17"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-img">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="64" height="64" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9"/>
                                    <line x1="9" y1="9" x2="9.01" y2="9"/>
                                    <line x1="15" y1="9" x2="15.01" y2="9"/>
                                    <path d="M8 13a4 4 0 1 0 8 0"/>
                                </svg>
                            </div>
                            <p class="empty-title">No purchases yet</p>
                            <p class="empty-subtitle text-muted">This customer hasn't made any purchases</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Receipt Modal -->
<div class="modal fade" id="orderReceiptModal" tabindex="-1" aria-labelledby="orderReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="orderReceiptModalLabel" style="font-size: 16px; font-weight: 600; color: #495057;">Order Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="order-receipt-content" class="receipt-container">
                    <div class="text-center p-4 text-muted">
                        <p>Select an order to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page-styles')
<style>
    .page-body {
        padding: 1.5rem 0;
        background-color: #f5f7fb;
    }

    .card {
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Order Receipt Modal Styles */
    #orderReceiptModal .receipt-container {
        position: relative;
        padding: 20px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.4;
        color: #333;
        background: #fff;
    }

    #orderReceiptModal .company-logo {
        width: 50px;
        height: 50px;
        background: #3b82f6;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        margin: 0 auto 10px;
    }

    #orderReceiptModal .company-name {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    #orderReceiptModal .company-address {
        font-size: 11px;
        color: #666;
        margin-bottom: 2px;
    }

    #orderReceiptModal .receipt-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
    }

    #orderReceiptModal .receipt-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #ccc;
    }

    #orderReceiptModal .customer-section {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #ccc;
    }

    #orderReceiptModal .customer-title {
        font-weight: bold;
        margin-bottom: 8px;
        font-size: 13px;
    }

    #orderReceiptModal .customer-info div {
        margin-bottom: 3px;
        font-size: 11px;
    }

    #orderReceiptModal .items-section {
        margin-bottom: 20px;
    }

    #orderReceiptModal .item-container {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px dotted #ddd;
    }

    #orderReceiptModal .item-container:last-child {
        border-bottom: none;
    }

    #orderReceiptModal .item-main-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        margin-bottom: 5px;
    }

    #orderReceiptModal .item-number {
        font-weight: bold;
        min-width: 20px;
    }

    #orderReceiptModal .item-name {
        flex: 1;
        margin-left: 5px;
        margin-right: 10px;
        font-weight: bold;
    }

    #orderReceiptModal .item-qty {
        min-width: 60px;
        text-align: center;
    }

    #orderReceiptModal .item-price {
        min-width: 80px;
        text-align: right;
    }

    #orderReceiptModal .item-total {
        min-width: 90px;
        text-align: right;
        font-weight: bold;
    }

    #orderReceiptModal .item-details-row {
        margin-left: 25px;
        font-size: 10px;
        color: #666;
        line-height: 1.3;
    }

    #orderReceiptModal .serial-info {
        margin-bottom: 2px;
    }

    #orderReceiptModal .warranty-info {
        color: #28a745;
        font-weight: bold;
    }

    #orderReceiptModal .totals-section {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px dashed #ccc;
    }

    #orderReceiptModal .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 12px;
    }

    #orderReceiptModal .total-row.final {
        font-weight: bold;
        font-size: 14px;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px solid #333;
    }

    #orderReceiptModal .print-actions {
        margin-top: 20px;
        text-align: center;
        padding-top: 15px;
        border-top: 1px dashed #ccc;
    }

    #orderReceiptModal .print-btn {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 10px;
    }

    #orderReceiptModal .print-btn:hover {
        background: #2563eb;
    }

    #orderReceiptModal .pdf-btn {
        background: #dc2626;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 10px;
    }

    #orderReceiptModal .pdf-btn:hover {
        background: #b91c1c;
    }

    @media print {
        #orderReceiptModal .print-actions {
            display: none;
        }
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e3e6f0;
        padding: 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
    }

    .card-sm {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        display: block;
    }

    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        flex-shrink: 0;
        color: white;
        font-weight: 600;
    }

    .avatar-lg {
        width: 3rem;
        height: 3rem;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table th {
        border-bottom: 2px solid #e3e6f0;
        padding: 1rem;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        padding: 0;
        border-radius: 0.25rem;
    }

    @media (max-width: 992px) {
        .col-lg-4,
        .col-lg-8 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .table th,
        .table td {
            padding: 0.75rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {
        .table th:nth-child(n+6),
        .table td:nth-child(n+6) {
            display: none;
        }
    }
</style>
@endpush

@push('page-scripts')
<script>
function printOrder(orderId) {
    try {
        const returnUrl = encodeURIComponent(window.location.href);
        const url = `/orders/${orderId}/receipt?auto=1&pos=1&return=${returnUrl}`;
        window.location.href = url;
    } catch (e) {
        console.error('printOrder navigation failed, falling back to open in new tab', e);
        window.open(`/orders/${orderId}/receipt?auto=1&pos=1`, '_blank', 'noopener');
    }
}

function viewOrderInModal(orderId) {
    try {
        window.lastFocusedElement = document.activeElement;
    } catch (e) {
        window.lastFocusedElement = null;
    }

    document.getElementById('order-receipt-content').innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    fetch(`/orders/${orderId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showOrderReceiptModal(data.order);
        } else {
            showError('Failed to load order details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to load order details');
    });
}

function showOrderReceiptModal(orderData) {
    window.currentOrderData = orderData;

    const customer = orderData.customer;
    const invoiceNo = orderData.invoice_no;
    const dateTime = orderData.order_date;
    const items = orderData.details;
    const subtotal = orderData.sub_total;
    const discount = (orderData.discount || 0);
    const serviceCharges = (orderData.service_charges || 0);
    const total = orderData.total;

    let receiptHTML = '';

    receiptHTML += `
        <div class="receipt-header">
            <div class="company-logo">{{ $customer->shop ? strtoupper(substr($customer->shop->name, 0, 1)) : 'S' }}</div>
            <div class="company-name">{{ $customer->shop ? $customer->shop->name : 'Shop Name' }}</div>
            <div class="company-address">{{ $customer->shop ? $customer->shop->address : 'Shop Address' }}</div>
            <div class="company-address">{{ $customer->shop ? $customer->shop->phone : 'Phone' }} | {{ $customer->shop ? $customer->shop->email : 'Email' }}</div>
        </div>

        <div class="receipt-info">
            <div>
                <strong>Receipt #:</strong><br>
                <strong>Date:</strong>
            </div>
            <div>
                ${invoiceNo}<br>
                ${new Date(dateTime).toLocaleDateString()}
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-title">Customer Details</div>
            <div class="customer-info">
                <div><strong>Name:</strong> ${customer.name}</div>
`;

    if (customer.phone) {
        receiptHTML += `                <div><strong>Phone:</strong> ${customer.phone}</div>\n`;
    }
    if (customer.email) {
        receiptHTML += `                <div><strong>Email:</strong> ${customer.email}</div>\n`;
    }

    receiptHTML += `            </div>
        </div>

        <div class="items-section">
`;

    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        const index = i;
        const unitPrice = Number(item.unitcost).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const lineTotal = Number(item.total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        receiptHTML += `            <div class="item-container">
                <div class="item-main-row">
                    <span class="item-number">${index + 1}.</span>
                    <span class="item-name">${item.product && item.product.name ? item.product.name : ''}</span>
                    <span class="item-qty">Qty: ${item.quantity}</span>
                    <span class="item-price">LKR ${unitPrice}</span>
                    <span class="item-total">LKR ${lineTotal}</span>
                </div>
`;

        const hasSerial = item.product && item.product.serial_number;
        const warrantyYears = item.product && item.product.warranty_years ? Number(item.product.warranty_years) : 0;
        if (hasSerial || warrantyYears > 0) {
            receiptHTML += `                <div class="item-details-row">
`;
            if (hasSerial) {
                receiptHTML += `                    <div class="serial-info">Serial No: ${item.product.serial_number}</div>
`;
            }
            if (warrantyYears > 0) {
                receiptHTML += `                    <div class="warranty-info">Warranty: ${warrantyYears} ${warrantyYears === 1 ? 'year' : 'years'}</div>
`;
            }
            receiptHTML += `                </div>
`;
        }

        receiptHTML += `            </div>
`;
    }

    receiptHTML += `        </div>

        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>LKR ${Number(subtotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
`;

    if (discount && discount > 0) {
        receiptHTML += `            <div class="total-row">
                <span>Discount:</span>
                <span>-LKR ${Number(discount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
`;
    }
    if (serviceCharges && serviceCharges > 0) {
        receiptHTML += `            <div class="total-row">
                <span>Service Charges:</span>
                <span>+LKR ${Number(serviceCharges).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
`;
    }

    receiptHTML += `            <div class="total-row final">
                <span>TOTAL:</span>
                <span>LKR ${Number(total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
        </div>
`;

    if (orderData.notes && orderData.notes.trim() !== '') {
        receiptHTML += `
        <div class="notes-section" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
            <div style="font-weight: 600; color: #333; margin-bottom: 8px; font-size: 14px;">📝 Special Notes:</div>
            <div style="color: #555; font-size: 13px; line-height: 1.6; white-space: pre-wrap;">${orderData.notes}</div>
        </div>
`;
    }

    receiptHTML += `
        <div class="print-actions">
            <button class="print-btn" onclick="(window.currentOrderData && window.currentOrderData.id) ? printOrder(window.currentOrderData.id) : alert('Order ID not found')">🖨️ Print Receipt</button>
            <button class="pdf-btn" id="modal-pdf-download-btn" data-order-id="${orderData.id}">📄 Download PDF</button>
        </div>
`;

    document.getElementById('order-receipt-content').innerHTML = receiptHTML;

    setTimeout(() => {
        const modalPdfBtn = document.getElementById('modal-pdf-download-btn');
        if (modalPdfBtn) {
            modalPdfBtn.replaceWith(modalPdfBtn.cloneNode(true));
            const newBtn = document.getElementById('modal-pdf-download-btn');

            const handleDownload = function(e) {
                e.preventDefault();
                e.stopPropagation();

                const orderId = this.getAttribute('data-order-id');
                if (orderId) {
                    const downloadUrl = `/orders/${orderId}/download-pdf-bill`;
                    window.location.href = downloadUrl;
                } else {
                    alert('Unable to download: Order ID not found');
                }
            };

            newBtn.addEventListener('touchend', handleDownload, { passive: false });
            newBtn.addEventListener('click', handleDownload);
        }
    }, 100);
}

function showError(message) {
    document.getElementById('order-receipt-content').innerHTML = `
        <div class="text-center p-4 text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <circle cx="12" cy="12" r="9"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <p class="mb-0">${message}</p>
        </div>
    `;
}
</script>
@endpush
