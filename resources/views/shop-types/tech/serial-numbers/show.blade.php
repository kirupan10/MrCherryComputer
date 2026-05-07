@extends('shop-types.tech.layouts.nexora')

@section('title', 'Serial Number Details')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('tech.serial-numbers.index') }}">Serial Numbers</a>
                    </div>
                    <h2 class="page-title">
                        {{ $serialNumber->serial_number }}
                        @if($serialNumber->imei_number)
                            <span class="text-muted">/ {{ $serialNumber->imei_number }}</span>
                        @endif
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('tech.serial-numbers.edit', $serialNumber) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                <path d="M16 5l3 3"/>
                            </svg>
                            Edit
                        </a>
                        @can('delete', $serialNumber)
                            <form action="{{ route('tech.serial-numbers.destroy', $serialNumber) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this serial number?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0"/>
                                        <path d="M10 11l0 6"/>
                                        <path d="M14 11l0 6"/>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Serial Number Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Product</strong></label>
                                    <p>
                                        <a href="{{ route('tech.products.show', $serialNumber->product) }}">
                                            {{ $serialNumber->product->name }}
                                            @if($serialNumber->product->brand)
                                                ({{ $serialNumber->product->brand }})
                                            @endif
                                        </a>
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Status</strong></label>
                                    <p>
                                        @php
                                            $statusClasses = [
                                                'in_stock' => 'success',
                                                'sold' => 'info',
                                                'returned' => 'warning',
                                                'defective' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$serialNumber->status] ?? 'secondary' }}">
                                            {{ str_replace('_', ' ', ucfirst($serialNumber->status)) }}
                                        </span>
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Serial Number</strong></label>
                                    <p>{{ $serialNumber->serial_number }}</p>
                                </div>

                                @if($serialNumber->imei_number)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>IMEI Number</strong></label>
                                        <p>{{ $serialNumber->imei_number }}</p>
                                    </div>
                                @endif

                                @if($serialNumber->batch_number)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"><strong>Batch Number</strong></label>
                                        <p>{{ $serialNumber->batch_number }}</p>
                                    </div>
                                @endif

                                @if($serialNumber->customer)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Customer</strong></label>
                                        <p>
                                            <a href="{{ route('customer.show', $serialNumber->customer) }}">
                                                {{ $serialNumber->customer->name }}
                                            </a><br>
                                            <small class="text-muted">{{ $serialNumber->customer->phone }}</small>
                                        </p>
                                    </div>
                                @endif

                                @if($serialNumber->order)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Order</strong></label>
                                        <p>
                                            <a href="{{ route('order.show', $serialNumber->order) }}">
                                                Order #{{ $serialNumber->order->id }}
                                            </a>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Pricing</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($serialNumber->purchase_price)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Purchase Price</strong></label>
                                        <p>{{ number_format($serialNumber->purchase_price, 2) }}</p>
                                    </div>
                                @endif

                                @if($serialNumber->selling_price)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Selling Price</strong></label>
                                        <p>{{ number_format($serialNumber->selling_price, 2) }}</p>
                                    </div>
                                @endif

                                @if($serialNumber->purchase_price && $serialNumber->selling_price)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Profit Margin</strong></label>
                                        <p>
                                            @php
                                                $margin = $serialNumber->selling_price - $serialNumber->purchase_price;
                                                $marginPercent = ($margin / $serialNumber->purchase_price) * 100;
                                            @endphp
                                            {{ number_format($margin, 2) }}
                                            ({{ number_format($marginPercent, 1) }}%)
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Warranty Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($serialNumber->warranty_start_date)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Warranty Start Date</strong></label>
                                        <p>{{ $serialNumber->warranty_start_date->format('M d, Y') }}</p>
                                    </div>
                                @endif

                                @if($serialNumber->warranty_end_date)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Warranty End Date</strong></label>
                                        <p>{{ $serialNumber->warranty_end_date->format('M d, Y') }}</p>
                                    </div>
                                @endif

                                @if($serialNumber->warranty_start_date && $serialNumber->warranty_end_date)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"><strong>Warranty Status</strong></label>
                                        <p>
                                            @if($serialNumber->isUnderWarranty())
                                                <span class="badge bg-success">Active</span>
                                                @php
                                                    $daysRemaining = now()->diffInDays($serialNumber->warranty_end_date, false);
                                                @endphp
                                                <small class="text-muted">
                                                    ({{ $daysRemaining }} days remaining)
                                                </small>
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                                @php
                                                    $daysExpired = $serialNumber->warranty_end_date->diffInDays(now());
                                                @endphp
                                                <small class="text-muted">
                                                    (expired {{ $daysExpired }} days ago)
                                                </small>
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if($serialNumber->notes)
                                    <div class="col-12">
                                        <label class="form-label"><strong>Notes</strong></label>
                                        <p>{{ $serialNumber->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($serialNumber->warrantyClaims->isNotEmpty())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Warranty Claims</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Claim #</th>
                                            <th>Issue</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serialNumber->warrantyClaims as $claim)
                                            <tr>
                                                <td>{{ $claim->id }}</td>
                                                <td>{{ $claim->issue_description }}</td>
                                                <td>{{ $claim->claim_date->format('M d, Y') }}</td>
                                                <td>
                                                    @php
                                                        $claimStatusClasses = [
                                                            'pending' => 'warning',
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            'completed' => 'info'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $claimStatusClasses[$claim->status] ?? 'secondary' }}">
                                                        {{ ucfirst($claim->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('tech.warranty.show', $claim) }}" class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($serialNumber->repairJobs->isNotEmpty())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Repair History</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Job #</th>
                                            <th>Issue</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serialNumber->repairJobs as $job)
                                            <tr>
                                                <td>{{ $job->id }}</td>
                                                <td>{{ $job->issue_description }}</td>
                                                <td>{{ $job->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @php
                                                        $jobStatusClasses = [
                                                            'pending' => 'secondary',
                                                            'diagnosing' => 'info',
                                                            'approved' => 'primary',
                                                            'in_repair' => 'warning',
                                                            'completed' => 'success',
                                                            'delivered' => 'success',
                                                            'cancelled' => 'danger'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $jobStatusClasses[$job->status] ?? 'secondary' }}">
                                                        {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('tech.repairs.show', $job) }}" class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if(currentShop()->hasFeature('warranty'))
                                    <a href="{{ route('tech.warranty.create', ['serial_number_id' => $serialNumber->id]) }}"
                                        class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
                                        </svg>
                                        File Warranty Claim
                                    </a>
                                @endif

                                @if(currentShop()->hasFeature('repairs'))
                                    <a href="{{ route('tech.repairs.create', ['serial_number_id' => $serialNumber->id]) }}"
                                        class="btn btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 10h3v-3l-3.5 -3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1 -3 3l-6 -6a6 6 0 0 1 -8 -8l3.5 3.5"/>
                                        </svg>
                                        Create Repair Job
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Metadata</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <strong>Created:</strong><br>
                                    {{ $serialNumber->created_at->format('M d, Y H:i') }}
                                </div>
                                <div class="list-group-item">
                                    <strong>Updated:</strong><br>
                                    {{ $serialNumber->updated_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
