@extends('shop-types.tech.layouts.nexora')

@section('title', 'Edit Serial Number')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <a href="{{ route('tech.serial-numbers.index') }}">Serial Numbers</a>
                    </div>
                    <h2 class="page-title">
                        Edit Serial Number: {{ $serialNumber->serial_number }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('tech.serial-numbers.show', $serialNumber) }}" class="btn btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                            </svg>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <form action="{{ route('tech.serial-numbers.update', $serialNumber) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Serial Number Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Product</label>
                                        <select name="tech_product_id" class="form-select @error('tech_product_id') is-invalid @enderror" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ old('tech_product_id', $serialNumber->tech_product_id) == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} @if($product->brand)({{ $product->brand }})@endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tech_product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Serial Number</label>
                                        <input type="text" name="serial_number"
                                            class="form-control @error('serial_number') is-invalid @enderror"
                                            value="{{ old('serial_number', $serialNumber->serial_number) }}" required>
                                        @error('serial_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">IMEI Number</label>
                                        <input type="text" name="imei_number"
                                            class="form-control @error('imei_number') is-invalid @enderror"
                                            value="{{ old('imei_number', $serialNumber->imei_number) }}">
                                        @error('imei_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Batch Number</label>
                                        <input type="text" name="batch_number"
                                            class="form-control @error('batch_number') is-invalid @enderror"
                                            value="{{ old('batch_number', $serialNumber->batch_number) }}">
                                        @error('batch_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="in_stock" {{ old('status', $serialNumber->status) == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                            <option value="sold" {{ old('status', $serialNumber->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                            <option value="returned" {{ old('status', $serialNumber->status) == 'returned' ? 'selected' : '' }}>Returned</option>
                                            <option value="defective" {{ old('status', $serialNumber->status) == 'defective' ? 'selected' : '' }}>Defective</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Customer</label>
                                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                            <option value="">No Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ old('customer_id', $serialNumber->customer_id) == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} {{ $customer->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Order</label>
                                        <input type="number" name="order_id"
                                            class="form-control @error('order_id') is-invalid @enderror"
                                            value="{{ old('order_id', $serialNumber->order_id) }}">
                                        <small class="form-hint">Order ID this serial number was sold with</small>
                                        @error('order_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Pricing</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Purchase Price</label>
                                        <input type="number" name="purchase_price"
                                            class="form-control @error('purchase_price') is-invalid @enderror"
                                            value="{{ old('purchase_price', $serialNumber->purchase_price) }}" step="0.01" min="0">
                                        @error('purchase_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Selling Price</label>
                                        <input type="number" name="selling_price"
                                            class="form-control @error('selling_price') is-invalid @enderror"
                                            value="{{ old('selling_price', $serialNumber->selling_price) }}" step="0.01" min="0">
                                        @error('selling_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Warranty Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Warranty Start Date</label>
                                        <input type="date" name="warranty_start_date"
                                            class="form-control @error('warranty_start_date') is-invalid @enderror"
                                            value="{{ old('warranty_start_date', $serialNumber->warranty_start_date?->format('Y-m-d')) }}">
                                        @error('warranty_start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Warranty End Date</label>
                                        <input type="date" name="warranty_end_date"
                                            class="form-control @error('warranty_end_date') is-invalid @enderror"
                                            value="{{ old('warranty_end_date', $serialNumber->warranty_end_date?->format('Y-m-d')) }}">
                                        @error('warranty_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                            rows="3">{{ old('notes', $serialNumber->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                        Update Serial Number
                                    </button>
                                    <a href="{{ route('tech.serial-numbers.show', $serialNumber) }}" class="btn">Cancel</a>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Product Info</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <strong>Name:</strong><br>
                                        {{ $serialNumber->product->name }}
                                    </div>
                                    @if($serialNumber->product->brand)
                                        <div class="list-group-item">
                                            <strong>Brand:</strong><br>
                                            {{ $serialNumber->product->brand }}
                                        </div>
                                    @endif
                                    @if($serialNumber->product->model_number)
                                        <div class="list-group-item">
                                            <strong>Model:</strong><br>
                                            {{ $serialNumber->product->model_number }}
                                        </div>
                                    @endif
                                    <div class="list-group-item">
                                        <strong>Default Warranty:</strong><br>
                                        {{ $serialNumber->product->warranty_months }} months
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($serialNumber->warranty_start_date && $serialNumber->warranty_end_date)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Warranty Status</h3>
                                </div>
                                <div class="card-body">
                                    @if($serialNumber->isUnderWarranty())
                                        <span class="badge bg-success">Active</span>
                                        <p class="mt-2 mb-0">
                                            <small>Expires: {{ $serialNumber->warranty_end_date->format('M d, Y') }}</small>
                                        </p>
                                    @else
                                        <span class="badge bg-danger">Expired</span>
                                        <p class="mt-2 mb-0">
                                            <small>Expired: {{ $serialNumber->warranty_end_date->format('M d, Y') }}</small>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
