@extends('layouts.admin')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <div class="page-pretitle">Admin Panel</div>
                <h2 class="page-title">Suspend Shop</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('admin.shops.index') }}" class="btn btn-ghost-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l14 0"/>
                            <path d="M5 12l6 6"/>
                            <path d="M5 12l6 -6"/>
                        </svg>
                        Back to Shops
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Main Form Column -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-status-top bg-danger"></div>
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                            </svg>
                            Suspend Shop Account
                        </h3>
                    </div>

                    <div class="card-body">
                        <!-- Shop Information Card -->
                        <div class="card mb-4 bg-red-lt">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg me-3 bg-danger text-white">
                                        {{ strtoupper(substr($shop->name, 0, 2)) }}
                                    </span>
                                    <div class="flex-fill">
                                        <div class="h2 mb-1">{{ $shop->name }}</div>
                                        <div class="text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/>
                                                <path d="M3 7l9 6l9 -6"/>
                                            </svg>
                                            {{ $shop->email }}
                                        </div>
                                        <div class="text-muted mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
                                            </svg>
                                            {{ $shop->phone }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-blue mb-1">{{ $shop->users_count ?? 0 }} Users</div><br>
                                        <div class="badge bg-green">{{ $shop->products_count ?? 0 }} Products</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Suspension Form -->
                        <form action="{{ route('admin.shops.suspend.store', $shop) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label required">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                        <path d="M12 8v4"/>
                                        <path d="M12 16h.01"/>
                                    </svg>
                                    Suspension Reason
                                </label>
                                <textarea class="form-control @error('reason') is-invalid @enderror"
                                          name="reason"
                                          rows="4"
                                          required
                                          placeholder="Enter detailed reason for suspending this shop...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">This reason will be displayed to shop users when they try to access the system.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="suspend_users"
                                           value="1"
                                           {{ old('suspend_users') ? 'checked' : '' }}>
                                    <span class="form-check-label">
                                        <strong>Also suspend all shop users</strong>
                                        <span class="form-check-description">
                                            This will suspend the shop owner and all employees associated with this shop
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <!-- Warning Messages -->
                            <div class="alert alert-danger">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 9v4"/>
                                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                            <path d="M12 16h.01"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">⚠️ Shop Suspension Warning!</h4>
                                        <div class="text-secondary">
                                            Suspending <strong>{{ $shop->name }}</strong> will:
                                            <ul class="mb-0 mt-2">
                                                <li>Immediately <strong>block access</strong> to all shop operations</li>
                                                <li><strong>Prevent all login attempts</strong> from shop users</li>
                                                <li>Display the suspension reason to anyone attempting to access</li>
                                                <li>Affect all {{ $shop->users_count ?? 0 }} users if "suspend users" is checked</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                    </svg>
                                    Suspend Shop
                                </button>
                                <a href="{{ route('admin.shops.index') }}" class="btn btn-link">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Information Sidebar -->
            <div class="col-lg-4">
                <!-- Shop Statistics -->
                <div class="card mb-3">
                    <div class="card-status-top bg-info"></div>
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            Shop Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Users</div>
                                <div class="datagrid-content">{{ $shop->users_count ?? 0 }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Products</div>
                                <div class="datagrid-content">{{ $shop->products_count ?? 0 }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Orders</div>
                                <div class="datagrid-content">{{ $shop->orders_count ?? 0 }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Created</div>
                                <div class="datagrid-content">{{ $shop->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Owner</div>
                                <div class="datagrid-content">{{ $shop->owner->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="card">
                    <div class="card-status-top bg-warning"></div>
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 9v4"/>
                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                <path d="M12 16h.01"/>
                            </svg>
                            Important Notes
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Shop can be unsuspended at any time
                            </li>
                            <li class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                All shop data will be preserved
                            </li>
                            <li class="mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Users will be logged out immediately
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Suspension is reversible, deletion is not
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
