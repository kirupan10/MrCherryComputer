
<header class="navbar navbar-expand-md" style="background-color: #fff;">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="container-xxl">
            <ul class="navbar-nav">
                <li class="nav-item {{ request()->is('/') || request()->is('dashboard*') ? 'active' : null }}">
                        <a class="nav-link" href="{{ shop_route('dashboard') }}" >
                            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Dashboard') }}
                            </span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->is('pos') || request()->routeIs('pos.index') ? 'active' : null }}">
                        <a class="nav-link" href="{{ shop_route('pos.index') }}" >
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-device-pos" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7l-2 0l8 -2l8 2l-2 0" /><path d="M7 9l0 10a1 1 0 0 0 1 1l8 0a1 1 0 0 0 1 -1l0 -10" /><path d="M13 17l0 .01" /><path d="M10 14l0 .01" /><path d="M10 11l0 .01" /><path d="M13 11l0 .01" /><path d="M16 11l0 .01" /><path d="M16 14l0 .01" /></svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('POS') }}
                            </span>
                        </a>
                    </li>

                    @if(Auth::user()->hasInventoryAccess())
                    <li class="nav-item {{ request()->is('products*') ? 'active' : null }}">
                        <a class="nav-link" href="{{ shop_route('products.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-packages" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" /><path d="M2 13.5v5.5l5 3" /><path d="M7 16.545l5 -3.03" /><path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" /><path d="M12 19l5 3" /><path d="M17 16.5l5 -3" /><path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" /><path d="M7 5.03v5.455" /><path d="M12 8l5 -3" /></svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Products') }}
                            </span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->is('orders*', 'sales*') && !request()->routeIs('orders.create') ? 'active' : null }}">
                        <a class="nav-link" href="{{ shop_route('orders.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-package-export" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21l-8 -4.5v-9l8 -4.5l8 4.5v4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M15 18h7" /><path d="M19 15l3 3l-3 3" /></svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Sales') }}
                            </span>
                        </a>
                    </li>

                    @if(Auth::user()->isEmployee())
                    <li class="nav-item dropdown {{ request()->is('jobs*') ? 'active' : null }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-jobs" data-bs-toggle="dropdown" data-bs-auto-close="true" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12l0 .01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Jobs') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ shop_route('jobs.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                {{ __('Create New Job') }}
                            </a>
                            <a class="dropdown-item" href="{{ shop_route('jobs.list') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12l.01 0" /><path d="M13 12l2 0" /><path d="M9 16l.01 0" /><path d="M13 16l2 0" /></svg>
                                {{ __('View All Jobs') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ shop_route('job-types.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>
                                {{ __('Job Types') }}
                            </a>
                            <a class="dropdown-item" href="{{ shop_route('job-types.create') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                {{ __('Create Job Type') }}
                            </a>
                        </div>
                    </li>
                    @endif

                    @if(Auth::user()->canAccessReports())
                    {{-- navFinanceKpis is provided by AppServiceProvider view composer; it contains returnKpi and expenseKpi objects --}}
                    <li class="nav-item dropdown {{ request()->is('reports*') && !request()->is('reports/external-funds*') ? 'active' : null }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-reports" data-bs-toggle="dropdown" data-bs-auto-close="true" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                    <path d="M12 7v5l3 3"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Reports') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <div class="dropdown-header">Special Reports</div>
                                    <a href="{{ shop_route('reports.inventory') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/>
                                            <line x1="12" y1="12" x2="20" y2="7.5"/>
                                            <line x1="12" y1="12" x2="12" y2="21"/>
                                            <line x1="12" y1="12" x2="4" y2="7.5"/>
                                        </svg>
                                        Inventory Report
                                    </a>
                                    @if(Auth::user()->canAccessFinanceDashboard())
                                    <a href="{{ shop_route('finance.monthly-report') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                                            <line x1="16" y1="3" x2="16" y2="7"/>
                                            <line x1="8" y1="3" x2="8" y2="7"/>
                                            <line x1="4" y1="11" x2="20" y2="11"/>
                                            <rect x="8" y="15" width="2" height="2"/>
                                        </svg>
                                        Monthly Report
                                    </a>
                                    <a href="{{ shop_route('reports.sales.finance.expenses') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7h18"/><path d="M7 7v-3"/><path d="M17 7v-3"/><path d="M5 7v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-14"/><path d="M9 12h6"/></svg>
                                        Expense
                                    </a>
                                    <a href="{{ shop_route('reports.sales.finance.returns') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 11v-2a9 9 0 0 1 9 -9h0"/><path d="M21 13v2a9 9 0 0 1 -9 9h0"/><path d="M21 7l-6 6"/><path d="M15 7l6 6"/></svg>
                                        Return Product
                                    </a>
                                    @endif
                                </div>
                                <div class="dropdown-menu-column">
                                    <div class="dropdown-header">Reports</div>
                                    <a href="{{ shop_route('reports.sales.daily') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                                            <line x1="16" y1="3" x2="16" y2="7"/>
                                            <line x1="8" y1="3" x2="8" y2="7"/>
                                            <line x1="4" y1="11" x2="20" y2="11"/>
                                            <line x1="11" y1="15" x2="12" y2="15"/>
                                            <line x1="12" y1="15" x2="12" y2="18"/>
                                        </svg>
                                        Daily Report
                                    </a>
                                    <a href="{{ shop_route('reports.sales.weekly') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                                            <line x1="16" y1="3" x2="16" y2="7"/>
                                            <line x1="8" y1="3" x2="8" y2="7"/>
                                            <line x1="4" y1="11" x2="20" y2="11"/>
                                            <rect x="8" y="15" width="2" height="2"/>
                                        </svg>
                                        Weekly Report
                                    </a>
                                    <a href="{{ shop_route('reports.sales.monthly') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                                            <line x1="16" y1="3" x2="16" y2="7"/>
                                            <line x1="8" y1="3" x2="8" y2="7"/>
                                            <line x1="4" y1="11" x2="20" y2="11"/>
                                            <path d="M11 15h1v4h-1z"/>
                                        </svg>
                                        Monthly Report
                                    </a>
                                    <a href="{{ shop_route('reports.sales.yearly') }}" class="dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <rect x="4" y="5" width="16" height="16" rx="2"/>
                                            <line x1="16" y1="3" x2="16" y2="7"/>
                                            <line x1="8" y1="3" x2="8" y2="7"/>
                                            <line x1="4" y1="11" x2="20" y2="11"/>
                                            <path d="M8 15h2v4H8z"/>
                                            <path d="M14 15h2v4h-2z"/>
                                        </svg>
                                        Yearly Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endif
                    @endif

                    {{-- Finance Menu --}}
                    @if(!Auth::user()->isEmployee())
                    <li class="nav-item dropdown {{ request()->is('finance*', 'expenses*', 'credit-purchases*', 'cheques*', 'business-transactions*', 'reports/external-funds*') ? 'active' : null }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-finance" data-bs-toggle="dropdown" data-bs-auto-close="true" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"/>
                                    <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                    <path d="M5 21v-2a6 6 0 0 1 6 -6h2a6 6 0 0 1 6 6v2"/>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Finance') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            @if(Auth::user()->canAccessTransactions())
                            <a class="dropdown-item" href="{{ shop_route('business-transactions.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 7h14v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12z"/>
                                    <path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v2h-6v-2z"/>
                                    <path d="M5 10h14"/>
                                    <path d="M9 14h.01"/>
                                    <path d="M12 14h.01"/>
                                    <path d="M15 14h.01"/>
                                </svg>
                                {{ __('Transactions') }}
                            </a>
                            @endif
                            <a class="dropdown-item" href="{{ shop_route('purchases.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="2"/>
                                    <path d="M9 12h6"/>
                                    <path d="M9 16h6"/>
                                </svg>
                                {{ __('Purchases Management') }}
                            </a>
                            <a class="dropdown-item" href="{{ shop_route('vendors.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/>
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                                </svg>
                                {{ __('Supplier Management') }}
                            </a>
                            <a class="dropdown-item" href="{{ shop_route('cheques.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                    <path d="M9 9l0 .01"/>
                                    <path d="M13 13h6"/>
                                    <path d="M13 17h3"/>
                                </svg>
                                {{ __('Cheque Management') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ shop_route('reports.external-funds.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                </svg>
                                {{ __('Liabilities') }}
                            </a>
                        </div>
                    </li>
                    @endif

                    <li class="nav-item dropdown {{ request()->is('categories*', 'units*', 'warranty-claims*', 'business-transactions*') || (Auth::user()->canManageUsers() && request()->is('users*')) ? 'active' : null }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="true" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="icon icon-nexora icon-nexora-settings"
                                     width="24"
                                     height="24"
                                     viewBox="0 0 24 24"
                                     stroke-width="2"
                                     stroke="currentColor"
                                     fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                            </span>
                                <span class="nav-link-title">
                                {{ __('Settings') }}
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <div class="dropdown-header">Master Data</div>
                                    <a class="dropdown-item" href="{{ shop_route('customers.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
                                        {{ __('Customers') }}
                                    </a>
                                    @if(Auth::user()->hasInventoryAccess())
                                    <a class="dropdown-item" href="{{ shop_route('categories.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>
                                        {{ __('Categories') }}
                                    </a>
                                    @endif
                                    @if(in_array(Auth::user()->role, ['shop_owner', 'shop_manager']))
                                    <a class="dropdown-item" href="{{ shop_route('warranties.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/><path d="M9 12l2 2l4 -4"/></svg>
                                        {{ __('Warranties') }}
                                    </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ shop_route('user.profile') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.832 2.849"/></svg>
                                        {{ __('Profile') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <div class="dropdown-header">Service & Management</div>
                                    <a class="dropdown-item" href="{{ shop_route('warranty-claims.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                                            <path d="M12 12l8 -4.5"/>
                                            <path d="M12 12l0 9"/>
                                            <path d="M12 12l-8 -4.5"/>
                                            <path d="M16 5.25l-8 4.5"/>
                                        </svg>
                                        {{ __('Warranty Claims') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ shop_route('deliveries.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                            <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"/>
                                        </svg>
                                        {{ __('Delivery Management') }}
                                    </a>
                                    @if(!Auth::user()->isEmployee())
                                    <a class="dropdown-item" href="{{ shop_route('jobs.list') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" /><path d="M12 12l0 .01" /><path d="M3 13a20 20 0 0 0 18 0" /></svg>
                                        {{ __('Jobs') }}
                                    </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ shop_route('expenses.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 7h18"/>
                                            <path d="M7 7v-3"/>
                                            <path d="M17 7v-3"/>
                                            <path d="M5 7v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-14"/>
                                            <path d="M9 12h6"/>
                                        </svg>
                                        {{ __('Expense Management') }}
                                    </a>
                                </div>
                                <div class="dropdown-menu-column">
                                    @if(Auth::user()->canAccessDataImport())
                                    <div class="dropdown-header">Data Import</div>
                                    <a class="dropdown-item" href="{{ shop_route('orders.import.manual') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>
                                        {{ __('Import Single Order') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ shop_route('orders.import.bulk') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M12 11v6"/><path d="M9.5 13.5l2.5 -2.5l2.5 2.5"/></svg>
                                        {{ __('Import Bulk Orders (CSV)') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    @endif
                                    @if(Auth::user()->canAccessAuditLogs() || Auth::user()->canAccessLetterhead())
                                    <div class="dropdown-header">Configuration</div>
                                    @if(Auth::user()->canAccessAuditLogs())
                                    <a class="dropdown-item" href="{{ shop_route('logs.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>
                                        {{ __('Logs') }}
                                    </a>
                                    @endif
                                    @if(Auth::user()->canAccessLetterhead())
                                    <a class="dropdown-item" href="{{ shop_route('letterhead.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 9h1"/><path d="M9 13h6"/><path d="M9 17h6"/></svg>
                                        {{ __('Letterhead') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ shop_route('barcode.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7v-1a2 2 0 0 1 2 -2h2"/><path d="M4 17v1a2 2 0 0 0 2 2h2"/><path d="M16 4h2a2 2 0 0 1 2 2v1"/><path d="M16 20h2a2 2 0 0 0 2 -2v-1"/><path d="M5 11h1v2h-1z"/><path d="M10 11l0 2"/><path d="M14 11h1v2h-1z"/><path d="M19 11l0 2"/></svg>
                                        {{ __('Barcode Settings') }}
                                    </a>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Shop Switcher --}}
                    @if(Auth::check() && Auth::user()->isShopOwner() && Auth::user()->ownsMultipleShops())
                    <li class="nav-item dropdown ms-auto">
                        <a class="nav-link dropdown-toggle" href="#navbar-shop" data-bs-toggle="dropdown" data-bs-auto-close="true" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 21l18 0" />
                                    <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
                                    <path d="M5 21l0 -10.5" />
                                    <path d="M19 21l0 -10.5" />
                                    <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                {{ Auth::user()->getActiveShop() ? Auth::user()->getActiveShop()->name : 'Select Shop' }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-header">Your Shops</div>
                            @php
                                $currentShopId = Auth::user()->getActiveShop()?->id;
                                $userShops = Auth::user()->getOwnedShops();
                            @endphp
                            @foreach($userShops as $shop)
                                <a href="#" class="dropdown-item shop-switch-item {{ $shop->id === $currentShopId ? 'active' : '' }}" data-shop-id="{{ $shop->id }}">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2 flex-shrink-0" style="background-image: url({{ asset('static/avatars/shop-default.png') }})"></span>
                                        <div style="min-width: 0; flex: 1;">
                                            <div class="fw-bold text-truncate">{{ $shop->name }}</div>
                                            <div class="text-muted small text-truncate">{{ $shop->email }}</div>
                                        </div>
                                        @if($shop->id === $currentShopId)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success flex-shrink-0 ms-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                            <div class="dropdown-divider"></div>
                            <a href="{{ shop_route('shop.select') }}" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M8 12h8" />
                                </svg>
                                View All Shops
                            </a>
                        </div>
                    </li>
                    @endif

            </ul>

        </div>
    </div>
</header>
