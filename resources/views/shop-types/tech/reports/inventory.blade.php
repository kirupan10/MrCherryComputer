@extends('shop-types.tech.layouts.nexora')

@section('title', 'Inventory Report')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Reports</div>
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3"/>
                        <line x1="12" y1="12" x2="20" y2="7.5"/>
                        <line x1="12" y1="12" x2="12" y2="21"/>
                        <line x1="12" y1="12" x2="4" y2="7.5"/>
                    </svg>
                    Inventory Report
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('reports.index') }}" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <line x1="5" y1="12" x2="9" y2="16"/>
                            <line x1="5" y1="12" x2="9" y2="8"/>
                        </svg>
                        Back to Reports
                    </a>
                    <a href="{{ shop_route('reports.inventory.download', request()->query()) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                            <polyline points="7 11 12 16 17 11"/>
                            <line x1="12" y1="4" x2="12" y2="16"/>
                        </svg>
                        Download CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ shop_route('reports.inventory') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                            <option value="uncategorized" {{ request('category') == 'uncategorized' ? 'selected' : '' }}>Uncategorized</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stock Status</label>
                        <select name="stock_status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Stock Status</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search product name or code..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        @if(request()->hasAny(['category', 'search', 'stock_status']))
                            <a href="{{ shop_route('reports.inventory') }}" class="btn btn-secondary w-100">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="3" y1="21" x2="21" y2="21"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                        <polyline points="5 6 12 3 19 6"/>
                                        <line x1="4" y1="10" x2="4" y2="21"/>
                                        <line x1="20" y1="10" x2="20" y2="21"/>
                                        <line x1="8" y1="14" x2="8" y2="17"/>
                                        <line x1="12" y1="14" x2="12" y2="17"/>
                                        <line x1="16" y1="14" x2="16" y2="17"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Total Products</div>
                                <div class="h3 mb-0">{{ $totalProducts }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-success text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
                                        <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Stock Value</div>
                                <div class="h3 mb-0">LKR {{ number_format($totalStockValue, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
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
                                <div class="text-muted small">Low Stock</div>
                                <div class="h3 mb-0">{{ $lowStockProducts }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-danger text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="12" r="9"/>
                                        <line x1="5.7" y1="5.7" x2="18.3" y2="18.3"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted small">Out of Stock</div>
                                <div class="h3 mb-0">{{ $outOfStockProducts }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Inventory</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                @if(!Auth::user()->isEmployee())
                                <th class="text-end">Purchase Price</th>
                                @endif
                                <th class="text-end">Selling Price</th>
                                <th class="text-end">Stock Qty</th>
                                @if(!Auth::user()->isEmployee())
                                <th class="text-end">Stock Value</th>
                                @endif
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="{{ $product->quantity <= 0 ? 'table-danger' : ($product->quantity <= 10 ? 'table-warning' : '') }}">
                                <td><span class="badge bg-blue-lt">{{ $product->code }}</span></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                @if(!Auth::user()->isEmployee())
                                <td class="text-end">{{ $product->buying_price !== null ? 'LKR ' . number_format($product->buying_price, 2) : '-' }}</td>
                                @endif
                                <td class="text-end">LKR {{ number_format($product->selling_price ?? $product->price ?? 0, 2) }}</td>
                                <td class="text-end">
                                    <strong>{{ $product->quantity }}</strong>
                                </td>
                                @if(!Auth::user()->isEmployee())
                                <td class="text-end">{{ $product->buying_price !== null ? 'LKR ' . number_format($product->quantity * $product->buying_price, 2) : '-' }}</td>
                                @endif
                                <td>
                                    @if($product->quantity <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($product->quantity <= 10)
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No products found matching your criteria
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($products->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                    </div>
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
