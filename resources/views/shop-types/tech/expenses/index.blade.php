@extends('shop-types.tech.layouts.nexora')

@section('title', 'All Expenses')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title" style="font-weight: 700; color: #1a1a1a;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-danger" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                    </svg>
                    Expenses Management
                </h2>
                <p class="text-secondary" style="font-size: 0.95rem; margin-bottom: 0;">View and track all expense records organized by month</p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('expenses.create') }}" class="btn btn-white btn-lg px-4 py-2" style="font-weight: 600; letter-spacing: 0.5px; border: 2px solid #000;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        <span>New Expense</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">

        <x-alert />

        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach(($expenseCategories ?? []) as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Category Spending Breakdown -->
        @php
            $activeCategoryTotals = collect($expenseCategories ?? [])->mapWithKeys(fn($cat) => [$cat => $categoryTotals[$cat] ?? 0])->filter(fn($v) => $v > 0);
        @endphp
        @if($activeCategoryTotals->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header" style="padding: 0.65rem 1rem;">
                <h4 class="card-title mb-0" style="font-size: 0.875rem; font-weight: 600; color: #2c3e50;">
                    Spending by Category
                    <span class="text-muted fw-normal ms-2" style="font-size: 0.78rem;">{{ request('start_date') || request('end_date') ? (request('start_date') ?? '—') . ' → ' . (request('end_date') ?? '—') : 'Current Month' }}</span>
                </h4>
            </div>
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($activeCategoryTotals as $cat => $total)
                    <a href="{{ request()->fullUrlWithQuery(['category' => $cat]) }}"
                       class="d-flex align-items-center gap-1 px-2 py-1 rounded text-decoration-none {{ request('category') == $cat ? 'bg-danger text-white' : 'bg-light text-dark' }}"
                       style="border: 1px solid {{ request('category') == $cat ? '#dc3545' : '#dee2e6' }}; font-size: 0.82rem; white-space: nowrap;">
                        <span class="fw-semibold">{{ $cat }}</span>
                        <span class="ms-1 {{ request('category') == $cat ? 'opacity-75' : 'text-muted' }}">LKR&nbsp;{{ number_format($total, 0) }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Stats -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Expenses</div>
                        </div>
                        <div class="h1 mb-0 text-danger">LKR {{ number_format($totalExpenses, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Records</div>
                        </div>
                        <div class="h1 mb-0">{{ $totalRecords }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Months with Data</div>
                        </div>
                        <div class="h1 mb-0">{{ count($expensesByMonth) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Average per Month</div>
                        </div>
                        <div class="h1 mb-0">LKR {{ count($expensesByMonth) > 0 ? number_format($totalExpenses / count($expensesByMonth), 2) : '0.00' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses by Month -->
        @forelse($expensesByMonth as $monthYear => $expenses)
            @php
                $monthTotal = $expenses->sum('amount');
            @endphp
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title" style="font-weight: 600; color: #2c3e50;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-dark" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                            <line x1="16" y1="3" x2="16" y2="7"/>
                            <line x1="8" y1="3" x2="8" y2="7"/>
                            <line x1="4" y1="11" x2="20" y2="11"/>
                        </svg>
                        {{ $monthYear }}
                    </h3>
                    <div class="ms-auto">
                        <span class="badge bg-danger text-white" style="font-size: 1rem; padding: 0.5rem 1rem;">LKR {{ number_format($monthTotal, 2) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">Date</th>
                                    <th style="width: 120px;">Type</th>
                                    <th>Details</th>
                                    <th style="width: 140px;" class="text-end">Amount</th>
                                    <th style="width: 120px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td style="font-size: 0.9rem;">
                                        <strong>{{ $expense->expense_date->format('d M Y') }}</strong><br>
                                        <span class="text-muted" style="font-size: 0.85rem;">{{ $expense->expense_date->format('l') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-white text-dark" style="font-size: 0.85rem; cursor: default; border: 1px solid #dee2e6;">{{ $expense->type }}</span>
                                    </td>
                                    <td style="color: #495057;">
                                        @if($expense->delivery)
                                            @php $d = $expense->delivery; @endphp
                                            <div>
                                                <span class="badge me-1 {{ $d->direction === 'incoming' ? 'bg-success-lt text-success' : ($d->direction === 'outgoing' ? 'bg-danger-lt text-danger' : 'bg-info-lt text-info') }}" style="border: 1px solid #dee2e6;">{{ ucfirst($d->direction) }}</span>
                                                @if($d->tracking_number)
                                                    <span class="badge bg-light text-dark me-1" style="border: 1px solid #dee2e6;">Tracking: {{ $d->tracking_number }}</span>
                                                @endif
                                                @if($d->from_location)
                                                    <span class="badge bg-light text-dark me-1" style="border: 1px solid #dee2e6;">From: {{ $d->from_location }}</span>
                                                @endif
                                                @if($d->to_location)
                                                    <span class="badge bg-light text-dark me-1" style="border: 1px solid #dee2e6;">To: {{ $d->to_location }}</span>
                                                @endif
                                            </div>
                                        @elseif($expense->details && !empty(array_filter($expense->details)))
                                            <div>
                                                @foreach(array_slice(array_filter($expense->details), 0, 2) as $key => $value)
                                                    <span class="badge bg-light text-dark me-1" style="border: 1px solid #dee2e6;">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                                @endforeach
                                            </div>
                                        @elseif($expense->notes)
                                            <div class="text-truncate" style="max-width: 300px;">{{ $expense->notes }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong style="color: #dc3545; font-size: 1.05rem;">LKR {{ number_format($expense->amount, 2) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-white" style="border: 1px solid #000;" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                                                </svg>
                                            </a>
                                            @if(!Auth::user()->isEmployee())
                                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-white" style="border: 1px solid #000;" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                                    <path d="M16 5l3 3"/>
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background-color: #f8f9fa;">
                                <tr>
                                    <td colspan="3" class="text-end" style="font-weight: 700; color: #2c3e50; font-size: 1rem; padding: 1rem;">Month Total:</td>
                                    <td class="text-end" style="font-weight: 700; font-size: 1.1rem; padding: 1rem;"><span style="color: #dc3545;">LKR {{ number_format($monthTotal, 2) }}</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-3" style="color: #6c757d;" width="64" height="64" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                    </svg>
                    <h3 style="color: #2c3e50; font-weight: 600;">No expenses found</h3>
                    <p style="color: #6c757d; margin-bottom: 1.5rem;">Start by creating your first expense record to track your spending</p>
                    <a href="{{ route('expenses.create') }}" class="btn btn-white btn-md px-4" style="border: 2px solid #000;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        + Create First Expense
                    </a>
                </div>
            </div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {{ $paginatedExpenses->links() }}
        </div>
    </div>
</div>
@endsection
