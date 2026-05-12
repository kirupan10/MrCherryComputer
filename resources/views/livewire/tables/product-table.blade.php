<div>
    <!-- Alert for Products Without Buying Price -->
    @if($productsWithoutBuyingPrice > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-fill">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 9v4"/>
                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                    <path d="M12 16h.01"/>
                </svg>
                <strong>Profit Calculation Warning!</strong>
                <span class="ms-2">{{ $productsWithoutBuyingPrice }} {{ $productsWithoutBuyingPrice === 1 ? 'product has' : 'products have' }} no buying price set.</span>
                <p class="mb-0 mt-1 small">
                    Products without buying prices are excluded from profit calculations. Please update buying prices for accurate financial reporting.
                    <br><strong>Formula:</strong> Pure Profit = (Selling Price - Buying Price) × Quantity sold, then Net Profit = Pure Profit - Expenses
                </p>
            </div>
            <div class="ms-3">
                <a href="{{ shop_route('products.index') }}?filter=no_buying_price" class="btn btn-warning btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/>
                        <path d="M5 12l14 0"/>
                    </svg>
                    Fix {{ $productsWithoutBuyingPrice }} {{ $productsWithoutBuyingPrice === 1 ? 'Product' : 'Products' }}
                </a>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filters and Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search with Better Feedback -->
                <div class="col-md-5">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            @if($search)
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search text-success">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M15 15l6 6"/>
                                    <circle cx="10" cy="10" r="7"/>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M15 15l6 6"/>
                                    <circle cx="10" cy="10" r="7"/>
                                </svg>
                            @endif
                        </span>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               class="form-control"
                               placeholder="Search by product name, code, or category...">
                    </div>
                    <small class="text-muted d-block mt-1">
                        @if($search)
                            🔍 Searching for: <strong>{{ $search }}</strong>
                        @else
                            <span class="text-secondary">Type to search products</span>
                        @endif
                    </small>
                </div>

                <!-- Category Filter -->
                <div class="col-md-2">
                    <select wire:model.live="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        <option value="uncategorized">Uncategorized</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Unit Filter -->
                <div class="col-md-2">
                    <select wire:model.live="unitFilter" class="form-select">
                        <option value="">All Units</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock Filter -->
                <div class="col-md-2">
                    <select wire:model.live="stockFilter" class="form-select">
                        <option value="all">All Stock</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <div class="col-md-1">
                    <button type="button"
                            wire:click="clearFilters"
                            class="btn btn-outline-secondary w-100"
                            title="Clear all filters">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            <line x1="10" x2="10" y1="11" y2="17"/>
                            <line x1="14" x2="14" y1="11" y2="17"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Results Summary with Better Feedback -->
            <div class="row mt-3 align-items-center">
                <div class="col-md-6">
                    @if($products->total() > 0)
                        <small class="text-muted">
                            @if($hasActiveFilters ?? false)
                                <strong class="text-dark">{{ $products->total() }}</strong>
                                {{ $products->total() === 1 ? 'product' : 'products' }} found
                                @if($search)
                                    for "<strong>{{ $search }}</strong>"
                                @endif
                            @else
                                <span class="badge bg-info-lt">Showing latest 20 products</span>
                                <span class="text-secondary ms-2">Use search or filters to find specific products</span>
                            @endif
                        </small>
                        <br/>
                        <small class="text-secondary">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }}
                        </small>
                    @else
                        <small class="text-warning">
                            ⚠️ No products found
                            @if($search)
                                matching "<strong>{{ $search }}</strong>"
                            @endif
                        </small>
                    @endif
                </div>
                <div class="col-md-6 text-end">
                    @if($hasActiveFilters ?? false)
                        <div class="d-inline-block">
                            <label class="form-label me-2" style="display: inline; margin-bottom: 0;">Per page:</label>
                            <select wire:model.live="perPage" class="form-select form-select-sm d-inline-block" style="width: auto;">
                                <option value="10">10 products</option>
                                <option value="15">15 products</option>
                                <option value="20">20 products</option>
                                <option value="30">30 products</option>
                                <option value="50">50 products</option>
                            </select>
                        </div>
                    @else
                        <small class="text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-inline">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Limited to 20 products. Search to see more.
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <x-spinner.loading-spinner/>

    @if(safe_count($products) > 0)
        <!-- Table View -->
            <div class="card">
                <div class="table-responsive">
                    <table wire:loading.class="opacity-50" class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th class="w-1">No.</th>
                                <th>
                                    <a wire:click.prevent="sortBy('name')" href="#" role="button" class="text-decoration-none">
                                        Name
                                        @if($sortField === 'name')
                                            @if($sortAsc)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path d="m7 15 5 5 5-5"/>
                                                    <path d="m7 9 5-5 5 5"/>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path d="m17 14-5 5-5-5"/>
                                                </svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Price</th>
                                <th class="w-1 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="text-muted">
                                        {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div style="display: block;">
                                            <div class="fw-bold" style="line-height:1.2;">{{ $product->name }}</div>
                                            <div class="text-muted" style="font-size: 11px; line-height:1.4; margin-top: 2px;">
                                                {{ $product->code ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-lt">{{ optional($product->category)->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-green-lt">{{ optional($product->unit)->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $stockClass = 'text-success';
                                            if ($product->quantity <= 0) {
                                                $stockClass = 'text-danger fw-bold';
                                            } elseif ($product->quantity <= $product->quantity_alert) {
                                                $stockClass = 'text-warning fw-bold';
                                            }
                                        @endphp
                                        <span class="{{ $stockClass }}">{{ number_format($product->quantity) }}</span>
                                        <div class="text-muted small">Alert: {{ $product->quantity_alert }}</div>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $isLoss = !Auth::user()->isEmployee() && $product->buying_price > 0 && $product->selling_price < $product->buying_price;
                                        @endphp
                                        <div class="fw-bold {{ $isLoss ? 'text-danger' : 'text-success' }}">
                                            LKR {{ number_format($product->selling_price, 2) }}
                                            @if($isLoss)
                                                <span class="badge bg-danger-lt ms-1" title="Selling below cost!">LOSS</span>
                                            @endif
                                        </div>
                                        @if(!Auth::user()->isEmployee() && $product->buying_price > 0)
                                            <div class="text-muted small">Cost: LKR {{ number_format($product->buying_price, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-list flex-nowrap justify-content-center">
                                            <button type="button" class="btn btn-sm" title="Add Stock" data-bs-toggle="modal" data-bs-target="#addStockModal" onclick="window.setProductForStock('{{ $product->slug }}', '{{ $product->name }}', {{ $product->quantity }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                            </button>
                                            <a href="{{ shop_route('products.show', $product) }}" class="btn btn-white btn-sm" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" /></svg>
                                            </a>
                                            @if(!Auth::user()->isEmployee())
                                            <a href="{{ shop_route('products.edit', $product) }}" class="btn btn-sm" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 7h-1a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97l-8.415 8.385v3h3l8.385-8.415z" /><path d="M16 5l3 3" /></svg>
                                            </a>
                                            @endif
                                            <a href="{{ shop_route('barcode.print.product', $product) }}?quantity=1" target="_blank" class="btn btn-sm" title="Print Barcode">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9v-2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v2" /><path d="M6 19h12" /><path d="M8 6h8v4h-8z" /><path d="M8 10v4h8v-4" /><path d="M6 14h2" /><path d="M16 14h2" /></svg>
                                            </a>
                                            <form action="{{ shop_route('products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" title="Delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="text-center mt-4">
                <div class="text-muted mb-3">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                </div>
                <div class="d-flex justify-content-center pagination-container">
                    {{ $products->links() }}
                </div>
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div wire:loading.class="opacity-50" class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon text-muted">
                        <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-muted">
                    @if($search)
                        No products found for "{{ $search }}"
                    @else
                        No products found
                    @endif
                </h3>
                <p class="text-muted">
                    @if($search || $categoryFilter || $unitFilter || $stockFilter !== 'all')
                        No products match your current filters.
                    @else
                        Get started by creating your first product.
                    @endif
                </p>
                <div class="mt-4">
                    @if($search || $categoryFilter || $unitFilter || $stockFilter !== 'all')
                        <button wire:click="clearFilters" class="btn btn-outline-primary me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                                <path d="M3 6h18"/>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                <line x1="10" x2="10" y1="11" y2="17"/>
                                <line x1="14" x2="14" y1="11" y2="17"/>
                            </svg>
                            Clear Filters
                        </button>
                    @endif
                    <a href="{{ shop_route('products.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                            <path d="M5 12h14"/>
                            <path d="M12 5v14"/>
                        </svg>
                        Create Product
                    </a>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Hide pagination result text while keeping page navigation visible */
        .pagination-container > div > * {
            display: none;
        }

        .pagination-container nav {
            display: block !important;
        }

        .pagination-container .pagination {
            display: flex !important;
        }

        .pagination-container .page-item {
            display: list-item !important;
        }
    </style>
</div>
