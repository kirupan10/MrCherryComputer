@extends('layouts.nexora')

@section('title', 'Monthly Financial Report')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Finance Management
                    </div>
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                            <line x1="16" y1="3" x2="16" y2="7"/>
                            <line x1="8" y1="3" x2="8" y2="7"/>
                            <line x1="4" y1="11" x2="20" y2="11"/>
                        </svg>
                        Monthly Financial Report
                    </h2>
                    <p class="text-muted">Comprehensive monthly breakdown of all financial activities</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ shop_route('finance.index') }}" class="btn d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <x-alert />

            <!-- Month Selector -->
            <div class="card mb-3">
                <div class="card-body border-bottom py-3">
                    <form method="GET" action="{{ shop_route('finance.monthly-report') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Select Month</label>
                            <input type="month" name="month" class="form-control" value="{{ $month }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                        </div>
                        <div class="col-md-7 text-end">
                            <div class="text-muted">
                                Report for <strong>{{ $startDate->format('F Y') }}</strong>
                                <br><small>{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="font-weight-medium">
                                        Monthly Turnover
                                    </div>
                                    <div class="h2 mb-0">LKR {{ number_format($revenue['total'], 0) }}</div>
                                    <div class="text-muted mt-1">
