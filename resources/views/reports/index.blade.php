@extends('layouts.nexora')

@section('title', 'Reports')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Analytics</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2"/>
                        <rectangle x="9" y="3" width="6" height="4" rx="2"/>
                        <line x1="9" y1="12" x2="9.01" y2="12"/>
                        <line x1="13" y1="12" x2="15" y2="12"/>
                        <line x1="9" y1="16" x2="9.01" y2="16"/>
                        <line x1="13" y1="16" x2="15" y2="16"/>
                    </svg>
                    Business Reports
                </h2>
                <p class="text-muted">Comprehensive reports and analytics for your business</p>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row g-3">
            <!-- Sales Reports -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.daily') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="4" y1="19" x2="20" y2="19"/>
                                        <polyline points="4 15 8 9 12 11 16 6 20 10"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Daily Report</h3>
                                <div class="text-muted small">View daily performance with charts</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.weekly') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-info text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                        <path d="M12 7v5l3 3"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Weekly Report</h3>
                                <div class="text-muted small">Analyze weekly trends</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.monthly') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-success text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="4" y="5" width="16" height="16" rx="2"/>
                                        <line x1="16" y1="3" x2="16" y2="7"/>
                                        <line x1="8" y1="3" x2="8" y2="7"/>
                                        <line x1="4" y1="11" x2="20" y2="11"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Monthly Report</h3>
                                <div class="text-muted small">Monthly performance overview</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.yearly') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-warning text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                        <path d="M12 7v5l3 3"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Yearly Report</h3>
                                <div class="text-muted small">Annual performance analysis</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Transactions Report -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.transactions') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-purple text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"/>
                                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Business Transactions</h3>
                                <div class="text-muted small">Expenses, purchases and payments</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Report -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.inventory') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-teal text-white">
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
                                <h3 class="card-title mb-1">Inventory Report</h3>
                                <div class="text-muted small">Stock levels and low stock alerts</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liabilities Report -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.external-funds.index') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-purple text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Liabilities</h3>
                                <div class="text-muted small">Loans, investments, and repayments</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Sales Report -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.finance.credit-sales') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-orange text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="3" y="5" width="18" height="14" rx="3"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                        <line x1="7" y1="15" x2="7.01" y2="15"/>
                                        <line x1="11" y1="15" x2="13" y2="15"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Credit Sales Report</h3>
                                <div class="text-muted small">Track credit sales and payments</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Returns Report -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.finance.returns') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-red text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Returns Report</h3>
                                <div class="text-muted small">Track product returns and refunds</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Report -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-link card-link-pop" onclick="window.location.href='{{ shop_route('reports.sales.finance.expenses') }}'">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-pink text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <h3 class="card-title mb-1">Expenses Report</h3>
                                <div class="text-muted small">Monitor business expenses</div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <line x1="13" y1="18" x2="19" y2="12"/>
                                    <line x1="13" y1="6" x2="19" y2="12"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-link-pop {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-link-pop:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,.1);
}
</style>
@endsection
