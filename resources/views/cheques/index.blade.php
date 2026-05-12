@extends('layouts.nexora')

@section('title', 'Cheque Management')

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
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                <path d="M9 9l0 .01"/>
                                <path d="M13 13h6"/>
                                <path d="M13 17h3"/>
                            </svg>
                            Cheque Management
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">Track and manage vendor payment cheques</p>
                    </div>
                    <div>
                        <a href="{{ shop_route('cheques.create') }}" class="btn btn-primary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            New Cheque
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Pending Cheques</div>
                        <h2 class="mb-0" style="font-weight: 700;">{{ $stats['pending']['count'] ?? 0 }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            LKR {{ number_format($stats['pending']['amount'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Cleared Cheques</div>
                        <h2 class="mb-0 text-success" style="font-weight: 700;">{{ $stats['cleared']['count'] ?? 0 }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            LKR {{ number_format($stats['cleared']['amount'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Bounced Cheques</div>
                        <h2 class="mb-0 text-danger" style="font-weight: 700;">{{ $stats['bounced']['count'] ?? 0 }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            LKR {{ number_format($stats['bounced']['amount'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Total Value</div>
                        <h2 class="mb-0" style="font-weight: 700;">LKR {{ number_format($stats['total_amount'] ?? 0, 2) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            {{ $stats['total_count'] ?? 0 }} cheques
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('cheques.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search Cheque/Bank/Payee</label>
                        <input type="text" name="search" class="form-control" placeholder="Cheque #, bank, payee..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Filter by Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="deposited" @selected(request('status') === 'deposited')>Deposited</option>
                            <option value="cleared" @selected(request('status') === 'cleared')>Cleared</option>
                            <option value="bounced" @selected(request('status') === 'bounced')>Bounced</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="10" cy="10" r="7"/>
                                <line x1="21" y1="21" x2="15" y2="15"/>
                            </svg>
                            Search
                        </button>
                        <a href="{{ shop_route('cheques.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cheques Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Cheque #</th>
                            <th>Bank Name</th>
                            <th>Drawer (From)</th>
                            <th>Payee (To)</th>
                            <th>Amount</th>
                            <th>Cheque Date</th>
                            <th>Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cheques as $cheque)
                            <tr>
                                <td>
                                    <div class="text-wrap">
                                        <strong>{{ $cheque->cheque_number }}</strong>
                                        @if($cheque->reference_number)
                                            <br><small class="text-muted">Ref: {{ $cheque->reference_number }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $cheque->bank_name }}</td>
                                <td>
                                    <div class="text-wrap">
                                        {{ $cheque->drawer_name ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-wrap">
                                        {{ $cheque->payee_name ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <strong>LKR {{ number_format($cheque->amount, 2) }}</strong>
                                </td>
                                <td>{{ $cheque->cheque_date->format('d M Y') }}</td>
                                <td>
                                    <form method="POST" action="{{ shop_route('cheques.status', $cheque) }}" style="display:inline-flex; align-items:center; gap:0.5rem;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" style="width:auto;">
                                            <option value="pending" @selected($cheque->status === 'pending')>Pending</option>
                                            <option value="deposited" @selected($cheque->status === 'deposited')>Deposited</option>
                                            <option value="cleared" @selected($cheque->status === 'cleared')>Cleared</option>
                                            <option value="bounced" @selected($cheque->status === 'bounced')>Bounced</option>
                                            <option value="cancelled" @selected($cheque->status === 'cancelled')>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ shop_route('cheques.show', $cheque) }}" class="btn btn-ghost-primary btn-sm" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="12" cy="12" r="2"/><path d="M2 12c0 -1 2.906 -6 10 -6s10 5 10 6c0 1 -2.906 6 -10 6s -10 -5 -10 -6"/>
                                            </svg>
                                        </a>
                                        @if(!Auth::user()->isEmployee())
                                        <a href="{{ shop_route('cheques.edit', $cheque) }}" class="btn btn-ghost-warning btn-sm" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ shop_route('cheques.destroy', $cheque) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-ghost-danger btn-sm" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1"/>
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-muted" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="9" y="3" width="6" height="4" rx="2"/><path d="M5 7h14v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10z"/>
                                    </svg>
                                    <p class="text-muted mb-0">No cheques found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($cheques->hasPages())
            <div class="row mt-4">
                <div class="col-12 d-flex flex-column align-items-center">
                    {{ $cheques->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
