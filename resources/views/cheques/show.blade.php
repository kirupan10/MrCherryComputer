@extends('layouts.nexora')

@section('title', 'Cheque Details')

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
                            Cheque Details
                        </h1>
                        <p class="text-secondary" style="font-size: 0.95rem;">{{ $cheque->cheque_number }}</p>
                    </div>
                    <div class="btn-list">
                        <a href="{{ shop_route('cheques.edit', $cheque->id) }}" class="btn btn-warning btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ shop_route('cheques.index') }}" class="btn btn-secondary btn-lg px-4 py-2" style="font-weight: 600;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l14 0"/>
                                <path d="M5 12l6 -6"/>
                                <path d="M5 12l6 6"/>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status and Amount Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Status</div>
                        <div>
                            <span class="badge bg-{{ $cheque->status_color }}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                {{ ucfirst($cheque->status) }}
                            </span>
                        </div>
                        @if($cheque->status === 'pending')
                            <small class="text-muted mt-2 d-block">{{ $cheque->days_pending ?? 0 }} days old</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Amount</div>
                        <h2 class="mb-0" style="font-weight: 700;">LKR {{ number_format($cheque->amount, 2) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Cheque value
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Cheque Date</div>
                        <h2 class="mb-0" style="font-weight: 700;">{{ $cheque->cheque_date->format('d M Y') }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Date on cheque
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Related To</div>
                        <h2 class="mb-0 h4" style="font-weight: 700;">{{ ucfirst(str_replace('_', ' ', $cheque->related_to)) }}</h2>
                        <div class="text-muted mt-2" style="font-size: 0.875rem;">
                            Payment type
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Cards -->
        <div class="row mb-4">
            <!-- Cheque Information -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Cheque Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Cheque Number</div>
                                <p class="h5 mb-0">{{ $cheque->cheque_number }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Bank Name</div>
                                <p class="h5 mb-0">{{ $cheque->bank_name }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Branch Name</div>
                                <p class="h5 mb-0">{{ $cheque->branch_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Reference Number</div>
                                <p class="h5 mb-0">{{ $cheque->reference_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Party Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Party Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Drawer Name</div>
                            <p class="h5 mb-0">{{ $cheque->drawer_name }}</p>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Payee Name</div>
                            <p class="h5 mb-0">{{ $cheque->payee_name }}</p>
                        </div>
                        @if($cheque->payee_address)
                            <div>
                                <div class="text-muted mb-1" style="font-size: 0.875rem; font-weight: 500;">Payee Address</div>
                                <p class="h5 mb-0">{{ $cheque->payee_address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title" style="font-weight: 600;">Status Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Created</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ $cheque->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>

                            @if($cheque->deposit_date)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Deposited</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($cheque->deposit_date)->format('d M Y') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($cheque->clearance_date)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Cleared</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($cheque->clearance_date)->format('d M Y') }}</p>
                                    </div>
                                </div>
                            @elseif($cheque->status === 'bounced')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Bounced</h6>
                                        @if($cheque->bounce_reason)
                                            <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ $cheque->bounce_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($cheque->notes)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" style="font-weight: 600;">Notes</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-secondary mb-0">{{ $cheque->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @if($cheque->status === 'pending')
                                <form action="{{ shop_route('cheques.mark-deposited', $cheque) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-info" onclick="return confirm('Mark this cheque as deposited?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                        Mark as Deposited
                                    </button>
                                </form>

                                <form action="{{ shop_route('cheques.mark-bounced', $cheque) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Mark this cheque as bounced?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v6m0 0v0"/>
                                        </svg>
                                        Mark as Bounced
                                    </button>
                                </form>
                            @elseif($cheque->status === 'deposited')
                                <form action="{{ shop_route('cheques.mark-cleared', $cheque) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark this cheque as cleared?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                        Mark as Cleared
                                    </button>
                                </form>

                                <form action="{{ shop_route('cheques.mark-bounced', $cheque) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Mark this cheque as bounced?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v6m0 0v0"/>
                                        </svg>
                                        Mark as Bounced
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding: 0;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 40px;
        height: 20px;
        border-left: 2px solid #dee2e6;
    }

    .timeline-marker {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 15px;
        flex-shrink: 0;
        margin-top: 3px;
    }

    .timeline-content {
        flex-grow: 1;
    }
</style>
@endsection
