@extends('layouts.nexora')

@section('title', 'Delivery Management')

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
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="3" y="7" width="18" height="13" rx="2"/>
                                <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                            Delivery Management
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Track and manage incoming and outgoing parcel deliveries</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ shop_route('expenses.index') }}" class="btn btn-outline-secondary btn-lg px-4 py-2" style="font-weight: 600; letter-spacing: 0.5px;" title="View All Expenses">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                            </svg>
                            <span>View Expenses</span>
                        </a>
                        <a href="{{ shop_route('deliveries.create') }}" class="btn btn-white btn-lg px-4 py-2" style="font-weight: 600; letter-spacing: 0.5px; border: 1px solid #000;">
                            <span>+ New Delivery</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('deliveries.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M15 15l6 6"/>
                                    <circle cx="10" cy="10" r="7"/>
                                </svg>
                            </span>
                            <input type="text" name="search" class="form-control" placeholder="Tracking, location, person..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="direction" class="form-select">
                            <option value="">All</option>
                            <option value="incoming" {{ request('direction') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                            <option value="outgoing" {{ request('direction') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                            <option value="dropship" {{ request('direction') == 'dropship' ? 'selected' : '' }}>Dropship</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ shop_route('deliveries.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Deliveries Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th style="width: 100px;">Type</th>
                                <th>Tracking</th>
                                <th>From → To</th>
                                <th>Received By</th>
                                <th>Payment</th>
                                <th>Cost</th>
                                <th>Expense</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries as $delivery)
                                <tr>
                                    <td>
                                        <div style="color: #495057; font-size: 0.9rem;">
                                            <div><strong>{{ optional($delivery->delivery_date)->format('d M Y') }}</strong></div>
                                            <div style="color: #868e96;">{{ optional($delivery->delivery_date)->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($delivery->direction == 'incoming')
                                            <span class="badge bg-info">Incoming</span>
                                        @elseif($delivery->direction == 'outgoing')
                                            <span class="badge bg-warning">Outgoing</span>
                                        @else
                                            <span class="badge bg-purple">Dropship</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate fw-600" style="max-width: 150px; color: #2c3e50;">
                                            {{ $delivery->tracking_number ?: '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color: #495057;">
                                            <div>📍 <strong>{{ $delivery->from_location ?: '-' }}</strong></div>
                                            <div style="margin-top: 3px;">→ <strong>{{ $delivery->to_location ?: '-' }}</strong></div>
                                        </div>
                                    </td>
                                    <td><strong style="color: #2c3e50;">{{ $delivery->received_by ?: '-' }}</strong></td>
                                    <td>
                                        @if($delivery->payment_type)
                                            <span class="badge {{ $delivery->payment_type == 'Paid' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $delivery->payment_type }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($delivery->cost)
                                            <strong class="text-success" style="font-size: 1.05rem;">LKR {{ number_format($delivery->cost, 2) }}</strong>
                                        @else
                                            <span style="color: #adb5bd;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($delivery->expense_id)
                                            <a href="{{ shop_route('expenses.edit', $delivery->expense_id) }}" class="badge bg-primary" target="_blank" title="View Expense" style="cursor: pointer;">
                                                #{{ $delivery->expense_id }}
                                            </a>
                                        @else
                                            <span style="color: #adb5bd;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ shop_route('deliveries.edit', $delivery) }}" class="btn btn-sm" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                    <path d="M16 5l3 3"/>
                                                </svg>
                                            </a>
                                            <form action="{{ shop_route('deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div style="color: #6c757d;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <rect x="3" y="7" width="18" height="13" rx="2"/>
                                                <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                            <p class="h5" style="color: #2c3e50; font-weight: 600;">No deliveries found</p>
                                            <p style="color: #6c757d; margin-bottom: 1rem;">Start recording deliveries to track your parcels</p>
                                            <a href="{{ shop_route('deliveries.create') }}" class="btn btn-primary btn-md px-4">+ Add First Delivery</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($deliveries->hasPages())
            <div class="mt-3">
                {{ $deliveries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
