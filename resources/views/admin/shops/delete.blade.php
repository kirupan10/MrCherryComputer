@extends('layouts.admin')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <div class="page-pretitle">Admin Panel</div>
                <h2 class="page-title">Delete Shop</h2>
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
                                <path d="M4 7l16 0"/>
                                <path d="M10 11l0 6"/>
                                <path d="M14 11l0 6"/>
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                            </svg>
                            Permanently Delete Shop
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
                                            <strong>Owner:</strong> {{ $shop->owner->name }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-{{ $shop->is_active ? 'success' : 'secondary' }} mb-1">
                                            {{ $shop->is_active ? 'Active' : 'Inactive' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger mb-4">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <strong>Please fix the errors below and try again.</strong>
                            </div>
                        @endif

                        <!-- Warning Messages -->
                        <div class="alert alert-danger mb-4">
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
                                    <h4 class="alert-title">⚠️ Critical Warning: Permanent Deletion!</h4>
                                    <div class="text-secondary">
                                        <strong>This action CANNOT be undone!</strong> Deleting <strong>{{ $shop->name }}</strong> will:
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Permanently remove all shop data</strong> from the system</li>
                                            @if($ownerHasOtherShops)
                                                <li><strong>Delete {{ $stats['users'] - 1 }} users</strong> associated with this shop (Owner <strong>{{ $shop->owner->name }}</strong> will be preserved as they own other shops)</li>
                                            @else
                                                <li><strong>Delete ALL {{ $stats['users'] }} users</strong> associated with this shop</li>
                                            @endif
                                            <li><strong>Delete ALL {{ $stats['products'] }} products</strong> in the shop's inventory</li>
                                            <li><strong>Delete ALL {{ $stats['customers'] }} customers</strong> and their records</li>
                                            <li><strong>Delete ALL {{ $stats['orders'] }} orders</strong> including sales history</li>
                                            <li><strong>Delete ALL payments, expenses, and financial records</strong></li>
                                            <li><strong>Delete ALL jobs, warranties, and related data</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($ownerHasOtherShops)
                        <!-- Owner Preservation Notice -->
                        <div class="alert alert-success mb-4">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                        <path d="M9 12l2 2l4 -4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">✓ Owner Account Preserved</h4>
                                    <div class="text-secondary">
                                        The shop owner <strong>{{ $shop->owner->name }}</strong> owns multiple shops.
                                        Their user account will <strong>NOT be deleted</strong> and they will continue to have access to their other shops.
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Shop Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-muted">Users to Delete</div>
                                                <div class="h3 m-0 text-danger">{{ $stats['users'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                                                    <path d="M12 12l8 -4.5"/>
                                                    <path d="M12 12l0 9"/>
                                                    <path d="M12 12l-8 -4.5"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-muted">Products to Delete</div>
                                                <div class="h3 m-0 text-danger">{{ $stats['products'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/>
                                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/>
                                                    <path d="M12 12l0 .01"/>
                                                    <path d="M3 13a20 20 0 0 0 18 0"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-muted">Orders to Delete</div>
                                                <div class="h3 m-0 text-danger">{{ $stats['orders'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                                    <path d="M16 19h6"/>
                                                    <path d="M19 16v6"/>
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-muted">Customers to Delete</div>
                                                <div class="h3 m-0 text-danger">{{ $stats['customers'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <rect x="7" y="9" width="14" height="10" rx="1"/>
                                                    <circle cx="14" cy="14" r="2"/>
                                                    <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-muted">Categories to Delete</div>
                                                <div class="h3 m-0 text-danger">{{ $stats['categories'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deletion Form -->
                        <form method="POST" action="{{ route('admin.shops.delete', $shop) }}">
                            @csrf
                            @method('DELETE')

                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h4 class="mb-3">Deletion Confirmation Requirements</h4>

                                    <div class="mb-3">
                                        <label class="form-label required">
                                            Type <strong>DELETE</strong> (all caps) to confirm:
                                        </label>
                                        <input type="text"
                                               name="confirm_delete"
                                               class="form-control @error('confirm_delete') is-invalid @enderror"
                                               placeholder="Type DELETE here"
                                               required>
                                        @error('confirm_delete')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input @error('confirm_understand') is-invalid @enderror"
                                               type="checkbox"
                                               name="confirm_understand"
                                               id="confirm_understand"
                                               value="1"
                                               required>
                                        <label class="form-check-label" for="confirm_understand">
                                            I understand that this action is <strong>PERMANENT and IRREVERSIBLE</strong>.
                                            All shop data, users, products, sales, and everything associated with
                                            <strong>{{ $shop->name }}</strong> will be permanently deleted.
                                        </label>
                                        @error('confirm_understand')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.shops.index') }}" class="btn btn-ghost-dark">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M5 12l6 6"/>
                                        <path d="M5 12l6 -6"/>
                                    </svg>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0"/>
                                        <path d="M10 11l0 6"/>
                                        <path d="M14 11l0 6"/>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                    </svg>
                                    Permanently Delete Shop
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="col-lg-4">
                <!-- Action Log -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Action Log</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-muted">
                            <p><strong>When you delete this shop:</strong></p>
                            <ol class="mb-0 ps-3">
                                <li>A comprehensive backup will be created</li>
                                <li>The deletion will be logged in admin logs</li>
                                <li>All shop data will be permanently removed</li>
                                <li>All users will be logged out and deleted</li>
                                <li>The operation cannot be reversed</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Alternative Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Consider Alternative Actions</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Instead of permanent deletion, you can:</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                </svg>
                                Deactivate Shop
                            </a>
                            <form method="POST" action="{{ route('admin.shops.suspend', $shop) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <rect x="5" y="11" width="14" height="10" rx="2"/>
                                        <circle cx="12" cy="16" r="1"/>
                                        <path d="M8 11v-5a4 4 0 0 1 8 0"/>
                                    </svg>
                                    Suspend Shop
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
