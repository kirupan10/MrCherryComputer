@extends('layouts.nexora')

@section('title', 'Credit Sales Report')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Finance Reports
                </div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"/>
                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"/>
                    </svg>
                    Credit Sales Report
                </h2>
                <p class="text-muted">View all credit sales transactions and outstanding balances</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5.5 5h13a1 1 0 0 1 .5 1.5l-5 5.5l0 7l-4 -3l0 -4l-5 -5.5a1 1 0 0 1 .5 -1.5"/>
                            </svg>
                            Filter Sales
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start" class="form-control" value="{{ old('start', $filters['start'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end" class="form-control" value="{{ old('end', $filters['end'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer" class="form-control" placeholder="Search by customer" value="{{ old('customer', $filters['customer'] ?? '') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="10" cy="10" r="7"/>
                                        <line x1="21" y1="21" x2="15" y2="15"/>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Data Card -->
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Credit Sales Transactions</h3>
                        <div class="card-actions">
                            <span class="badge bg-blue">{{ count($rows) }} Transactions</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover">
                                <thead>
                                    <tr>
                                        <th>Sale ID</th>
                                        <th>Customer</th>
                                        <th>Sale Date</th>
                                        <th class="text-end">Total Amount</th>
                                        <th class="text-end">Amount Due</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $r)
                                    <tr>
                                        <td><span class="badge bg-blue-lt">#{{ $r->credit_sale_id }}</span></td>
                                        <td>
                                            @if($r->customer_name)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2" style="background-color: {{ '#' . substr(md5($r->customer_name), 0, 6) }};">
                                                    <span class="text-white">{{ strtoupper(substr($r->customer_name, 0, 1)) }}</span>
                                                </div>
                                                <strong>{{ $r->customer_name }}</strong>
                                            </div>
                                            @else
                                            <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($r->sale_date)
                                                <span class="text-muted">{{ \Carbon\Carbon::parse($r->sale_date)->format('M d, Y') }}</span>
                                            @else
                                                <span class="text-muted">�</span>
                                            @endif
                                        </td>
                                        <td class="text-end"><strong class="text-primary">LKR {{ number_format(($r->total_cents ?? 0)/100, 2) }}</strong></td>
                                        <td class="text-end">
                                            @php
                                                $due = ($r->due_cents ?? 0)/100;
                                                $badgeClass = $due > 0 ? 'bg-red' : 'bg-green';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">LKR {{ number_format($due, 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-list flex-nowrap justify-content-center">
                                                <a href="{{ shop_route('credit-sales.show', $r->credit_sale_id) }}" class="btn btn-sm btn-white" style="border: 1px solid #000;" title="View Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                                    </svg>
                                                </a>
                                                @php
                                                    $creditSale = \App\Models\CreditSale::find($r->credit_sale_id);
                                                @endphp
                                                @if($creditSale)
                                                <a href="{{ shop_route('credit-sales.download-pdf', $creditSale) }}" class="btn btn-sm btn-white" style="border: 1px solid #000;" title="Download PDF">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                        <path d="M12 17v-6"/>
                                                        <path d="M9.5 14.5l2.5 2.5l2.5 -2.5"/>
                                                    </svg>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No credit sales found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
