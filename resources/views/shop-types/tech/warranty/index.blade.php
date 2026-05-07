@extends('shop-types.tech.layouts.nexora')

@section('title', 'Warranty Claims Management')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Service Management
                </div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                        <path d="M12 12l8 -4.5"/>
                        <path d="M12 12l0 9"/>
                        <path d="M12 12l-8 -4.5"/>
                        <path d="M16 5.25l-8 4.5"/>
                    </svg>
                    Warranty Claims Management
                </h2>
                <p class="text-muted">Track warranty claims, repairs, and product returns</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('warranty-claims.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/>
                        <path d="M5 12l14 0"/>
                    </svg>
                    New Warranty Claim
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12h4l3 8l4 -16l3 8h4"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Total Claims</div>
                                <div class="text-muted">{{ $stats['total'] }} Claims</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 8v4"/>
                                        <path d="M12 16h.01"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Pending</div>
                                <div class="text-muted">{{ $stats['pending'] }} Claims</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-info text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M11 6l5 5l-1.5 1.5a3.536 3.536 0 1 1 -5 -5l1.5 -1.5z"/>
                                        <path d="M13 18l-5 -5l1.5 -1.5a3.536 3.536 0 1 1 5 5l-1.5 1.5z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">In Progress</div>
                                <div class="text-muted">{{ $stats['in_progress'] }} Claims</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Completed</div>
                                <div class="text-muted">{{ $stats['completed'] }} Claims</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if($warrantyClaims->isEmpty())
                    <x-empty title="No warranty claims found"
                        message="Try adjusting your search or filter to find what you're looking for."
                        button_label="Add your first Warranty Claim"
                        button_route="{{ route('warranty-claims.create') }}" />
                @else
                    <x-alert />

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Warranty Claims</h3>
                        </div>

                        <!-- Advanced Filters Section -->
                        <div class="card-body border-bottom py-3">
                            <form method="GET" action="{{ route('warranty-claims.index') }}" id="filterForm">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Search</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M15 15l6 6"/>
                                                    <circle cx="10" cy="10" r="7"/>
                                                </svg>
                                            </span>
                                            <input type="text" name="search" class="form-control"
                                                   placeholder="Serial No., Product, Customer, Vendor"
                                                   value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="repaired" {{ request('status') == 'repaired' ? 'selected' : '' }}>Repaired</option>
                                            <option value="replaced" {{ request('status') == 'replaced' ? 'selected' : '' }}>Replaced</option>
                                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Sending Method</label>
                                        <select name="sending_method" class="form-select">
                                            <option value="">All Methods</option>
                                            <option value="courier" {{ request('sending_method') == 'courier' ? 'selected' : '' }}>Courier</option>
                                            <option value="handover" {{ request('sending_method') == 'handover' ? 'selected' : '' }}>Handover</option>
                                            <option value="bus" {{ request('sending_method') == 'bus' ? 'selected' : '' }}>Bus</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date From</label>
                                        <input type="date" name="date_from" class="form-control"
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date To</label>
                                        <input type="date" name="date_to" class="form-control"
                                               value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="10" cy="10" r="7"/>
                                                <line x1="21" y1="21" x2="15" y2="15"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                                        <a href="{{ route('warranty-claims.index') }}" class="btn btn-outline-danger btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                            </svg>
                                            Reset Filters
                                        </a>
                                        <div class="text-muted">
                                            Show
                                            <select name="per_page" class="form-select form-select-sm d-inline-block w-auto mx-2" onchange="this.form.submit()">
                                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                            entries
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Customer</th>
                                            <th>Serial Number</th>
                                            <th>Vendor</th>
                                            <th>Sending Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($warrantyClaims as $claim)
                                        <tr>
                                            <td>{{ $claim->id }}</td>
                                            <td>
                                                <div>{{ $claim->product->name ?? 'N/A' }}</div>
                                                <div class="text-muted small">{{ $claim->product->code ?? '' }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $claim->customer->name ?? 'N/A' }}</div>
                                                <div class="text-muted small">{{ $claim->customer->phone ?? '' }}</div>
                                            </td>
                                            <td><span class="badge bg-azure">{{ $claim->serial_number }}</span></td>
                                            <td>{{ $claim->vendor ?? 'N/A' }}</td>
                                            <td>
                                                @if($claim->sending_method == 'courier')
                                                    <span class="badge bg-blue">Courier</span>
                                                    @if($claim->tracking_number)
                                                        <div class="text-muted small">{{ $claim->tracking_number }}</div>
                                                    @endif
                                                @elseif($claim->sending_method == 'handover')
                                                    <span class="badge bg-green">Handover</span>
                                                @else
                                                    <span class="badge bg-orange">Bus</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $claim->status_badge }}">
                                                    {{ $claim->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($claim->sending_date)
                                                    {{ $claim->sending_date->format('d M Y') }}
                                                @else
                                                    <span class="text-muted">Not sent</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list flex-nowrap">
                                                    <a href="{{ route('warranty-claims.show', $claim->id) }}" class="btn btn-white btn-sm" title="View">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="2"/>
                                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('warranty-claims.edit', $claim->id) }}" class="btn btn-white btn-sm" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                            <path d="M16 5l3 3"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('warranty-claims.destroy', $claim->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this warranty claim?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-white btn-sm" title="Delete">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <line x1="4" y1="7" x2="20" y2="7"/>
                                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer d-flex align-items-center">
                            <div class="ms-auto">
                                {{ $warrantyClaims->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
