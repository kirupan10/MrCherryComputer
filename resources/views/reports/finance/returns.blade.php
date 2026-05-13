@extends('layouts.nexora')

@section('title', 'Returns Report')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Finance Reports</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 12l3 3l3 -3l-3 -3z"/>
                        <path d="M21 12l-3 3l-3 -3l3 -3z"/>
                        <path d="M12 3l3 3l-3 3l-3 -3z"/>
                        <path d="M12 21l3 -3l-3 -3l-3 3z"/>
                        <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                    </svg>
                    Returns Report
                </h2>
                <p class="text-muted">Monitor product return rates and identify high-return items</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/>
                                        <line x1="12" y1="12" x2="20" y2="7.5"/>
                                        <line x1="12" y1="12" x2="12" y2="21"/>
                                        <line x1="12" y1="12" x2="4" y2="7.5"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Total Returned Items</div>
                                <div class="h3 mb-0">{{ number_format((float) $results->sum('total_returned'), 0, '.', ',') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-info text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="3" y1="21" x2="21" y2="21"/>
                                        <path d="M3 10l9 -7l9 7v11h-18z"/>
                                        <polyline points="9 21 9 15 15 15 15 21"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Avg Return Rate</div>
                                <div class="h3 mb-0">{{ $results->isEmpty() ? '0.00%' : number_format(($results->avg('return_rate') ?? 0) * 100, 2) . '%' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-warning text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 9v2m0 4v.01"/>
                                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Highest Return Rate</div>
                                <div class="h3 mb-0">{{ $results->isEmpty() ? '0.00%' : number_format(($results->max('return_rate') ?? 0) * 100, 2) . '%' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Filter Returns</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product" class="form-control" placeholder="Search by product name" value="{{ $filters['product'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Minimum Return Rate (%)</label>
                        <input type="number" step="0.01" name="min_rate" class="form-control" placeholder="e.g., 5.00" value="{{ $filters['min_rate'] ?? '' }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="10" cy="10" r="7"/>
                                <line x1="21" y1="21" x2="15" y2="15"/>
                            </svg>
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Returns Data Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Return Rates</h3>
                <div class="card-actions">
                    <span class="badge bg-blue-lt">{{ count($results) }} Products</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-end">Total Sold</th>
                                <th class="text-end">Total Returns</th>
                                <th class="text-end">Return Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($results as $r)
                            <tr>
                                <td>{{ $r->product_name ?? ($r->name ?? '�') }}</td>
                                <td class="text-end">{{ number_format((float) ($r->total_sold ?? 0), 0, '.', ',') }}</td>
                                <td class="text-end">{{ number_format((float) ($r->total_returned ?? $r->total_returns ?? 0), 0, '.', ',') }}</td>
                                <td class="text-end">
                                    @php
                                        $rate = isset($r->return_rate) ? ($r->return_rate) : 0;
                                        if(is_object($r) && isset($r->total_returned) && isset($r->total_sold) && $r->total_sold > 0) {
                                            $rate = ($r->total_returned / $r->total_sold) * 100;
                                        }
                                        $badgeClass = $rate > 10 ? 'bg-danger' : ($rate > 5 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ number_format($rate, 2) }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No return data found
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
