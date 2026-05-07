@extends('shop-types.tech.layouts.nexora')

@section('title', 'Serial Numbers Management')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Tech Shop Management
                    </div>
                    <h2 class="page-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/>
                            <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2"/>
                            <path d="M12 12h0.01"/>
                        </svg>
                        Serial Numbers Management
                    </h2>
                    <p class="text-muted">Track serial numbers, IMEI, and warranty information</p>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('tech.serial-numbers.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 5l0 14"/>
                                <path d="M5 12l14 0"/>
                            </svg>
                            Add Serial Number
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"/>
                                <path d="M9 15l3 -3l3 3"/>
                                <path d="M12 12l0 9"/>
                            </svg>
                            Bulk Import
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10"/>
                            </svg>
                        </div>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 9v4"/>
                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                <path d="M12 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Serial Numbers List</h3>
                    <div class="card-actions">
                        <form method="GET" action="{{ route('tech.serial-numbers.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search serial/IMEI..." value="{{ request('search') }}">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                <option value="defective" {{ request('status') == 'defective' ? 'selected' : '' }}>Defective</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('tech.serial-numbers.index') }}" class="btn btn-sm">Clear</a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Product</th>
                                <th>IMEI</th>
                                <th>Status</th>
                                <th>Warranty</th>
                                <th>Customer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serialNumbers as $serial)
                                <tr>
                                    <td>
                                        <strong>{{ $serial->serial_number }}</strong>
                                        @if($serial->batch_number)
                                            <br><small class="text-muted">Batch: {{ $serial->batch_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('tech.products.show', $serial->product) }}">
                                            {{ $serial->product->name }}
                                        </a>
                                        @if($serial->product->brand)
                                            <br><small class="text-muted">{{ $serial->product->brand }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $serial->imei_number ?? '-' }}</td>
                                    <td>
                                        @if($serial->status == 'in_stock')
                                            <span class="badge bg-success">In Stock</span>
                                        @elseif($serial->status == 'sold')
                                            <span class="badge bg-primary">Sold</span>
                                        @elseif($serial->status == 'returned')
                                            <span class="badge bg-warning">Returned</span>
                                        @else
                                            <span class="badge bg-danger">Defective</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($serial->isUnderWarranty())
                                            <span class="badge bg-success">Active</span>
                                            <br><small>Until {{ $serial->warranty_end_date?->format('M d, Y') }}</small>
                                        @elseif($serial->warranty_end_date)
                                            <span class="badge bg-secondary">Expired</span>
                                            <br><small>{{ $serial->warranty_end_date->format('M d, Y') }}</small>
                                        @else
                                            <span class="text-muted">No Warranty</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($serial->customer)
                                            {{ $serial->customer->name }}
                                            <br><small class="text-muted">{{ $serial->customer->phone }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tech.serial-numbers.show', $serial) }}"
                                                class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                            @can('update', $serial)
                                                <a href="{{ route('tech.serial-numbers.edit', $serial) }}"
                                                    class="btn btn-sm btn-secondary">
                                                    Edit
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No serial numbers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($serialNumbers->hasPages())
                    <div class="card-footer">
                        {{ $serialNumbers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tech.serial-numbers.bulk-import') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Import Serial Numbers</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Product</label>
                            <select name="tech_product_id" class="form-select" required>
                                <option value="">Select Product</option>
                                @foreach(App\ShopTypes\Tech\Models\TechProduct::forCurrentShop()->where('track_serial_numbers', true)->get() as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Serial Numbers (one per line)</label>
                            <textarea name="serial_numbers" class="form-control" rows="10"
                                placeholder="SN-001&#10;SN-002&#10;SN-003" required></textarea>
                            <small class="form-hint">Enter one serial number per line</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="in_stock">In Stock</option>
                                <option value="defective">Defective</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warranty (Months)</label>
                            <input type="number" name="warranty_months" class="form-control" min="0" max="120">
                            <small class="form-hint">Leave empty for no warranty</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
