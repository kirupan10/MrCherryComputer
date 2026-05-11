<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Product List</h3>
            <div class="card-actions">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="10" cy="10" r="7"></circle>
                            <line x1="21" y1="21" x2="15" y2="15"></line>
                        </svg>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Search products..." style="width: 250px;">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th class="cursor-pointer" wire:click="sortBy('name')">
                            Product
                            @if($sortField === 'name')
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <polyline points="{{ $sortDirection === 'asc' ? '6 15 12 9 18 15' : '6 9 12 15 18 9' }}"></polyline>
                                </svg>
                            @endif
                        </th>
                        <th class="cursor-pointer" wire:click="sortBy('code')">
                            Code
                            @if($sortField === 'code')
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <polyline points="{{ $sortDirection === 'asc' ? '6 15 12 9 18 15' : '6 9 12 15 18 9' }}"></polyline>
                                </svg>
                            @endif
                        </th>
                        <th>Category</th>
                        <th class="cursor-pointer" wire:click="sortBy('quantity')">
                            Stock
                            @if($sortField === 'quantity')
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <polyline points="{{ $sortDirection === 'asc' ? '6 15 12 9 18 15' : '6 9 12 15 18 9' }}"></polyline>
                                </svg>
                            @endif
                        </th>
                        <th class="cursor-pointer" wire:click="sortBy('selling_price')">
                            Price
                            @if($sortField === 'selling_price')
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <polyline points="{{ $sortDirection === 'asc' ? '6 15 12 9 18 15' : '6 9 12 15 18 9' }}"></polyline>
                                </svg>
                            @endif
                        </th>
                        <th>Status</th>
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($product->product_image)
                                        <span class="avatar avatar-sm me-2" style="background-image: url({{ asset('storage/' . $product->product_image) }})"></span>
                                    @else
                                        <span class="avatar avatar-sm me-2">{{ substr($product->name, 0, 2) }}</span>
                                    @endif
                                    <div>
                                        <div class="font-weight-medium text-primary">
                                            <a href="{{ route('tech.products.show', $product) }}">{{ $product->name }}</a>
                                        </div>
                                        <div class="text-muted small">{{ $product->brand }} {{ $product->model_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-blue-lt">{{ $product->code }}</span>
                            </td>
                            <td>
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $product->quantity }} {{ $product->unit->name ?? '' }}</span>
                                    @if($product->isLowStock())
                                        <span class="status-dot status-dot-animated bg-red" title="Low Stock Alert"></span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $product->getFormattedSellingPrice() }}
                            </td>
                            <td>
                                @if($product->quantity > 0)
                                    <span class="badge bg-success">In Stock</span>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <button class="btn btn-white btn-icon" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#addStockModal"
                                            onclick="setProductForStock('{{ $product->slug }}', '{{ addslashes($product->name) }}', {{ $product->quantity }})"
                                            title="Add Stock">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                    <a href="{{ route('tech.products.edit', $product) }}" class="btn btn-white btn-icon" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path>
                                            <line x1="16" y1="5" x2="19" y2="8"></line>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="12" cy="12" r="9"></circle>
                                            <line x1="9" y1="10" x2="9.01" y2="10"></line>
                                            <line x1="15" y1="10" x2="15.01" y2="10"></line>
                                            <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"></path>
                                        </svg>
                                    </div>
                                    <p class="empty-title">No products found</p>
                                    <p class="empty-subtitle text-muted">
                                        Try adjusting your search or filter to find what you're looking for.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">Showing <span>{{ $products->firstItem() }}</span> to <span>{{ $products->lastItem() }}</span> of <span>{{ $products->total() }}</span> entries</p>
                <div class="ms-auto">
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .cursor-pointer:hover {
        background-color: #f8fafc;
    }
</style>
