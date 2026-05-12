@extends('layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid mb-3">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Edit Product') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row row-cards">

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 9h.01" />
                                    <path d="M11 12h1v4h1" />
                                </svg>
                                Quick Stats
                            </h3>

                            <!-- Stock Status Card -->
                            @php
                                $stockStatus = 'success';
                                $stockBg = 'bg-success-lt';
                                $stockMessage = 'Well Stocked';
                                $stockIcon = '<path d="M5 12l5 5l10 -10" />';

                                if ($product->quantity <= 0) {
                                    $stockStatus = 'danger';
                                    $stockBg = 'bg-danger-lt';
                                    $stockMessage = 'Out of Stock';
                                    $stockIcon = '<path d="M18 6l-12 12" /><path d="M6 6l12 12" />';
                                } elseif ($product->quantity <= $product->quantity_alert) {
                                    $stockStatus = 'warning';
                                    $stockBg = 'bg-warning-lt';
                                    $stockMessage = 'Low Stock';
                                    $stockIcon = '<path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" />';
                                }

                                $profitMargin = !auth()->user()->isEmployee() && $product->selling_price > 0 && $product->buying_price > 0
                                    ? (($product->selling_price - $product->buying_price) / $product->selling_price) * 100
                                    : 0;

                                $isLoss = $product->buying_price > $product->selling_price;
                                $profitOrLoss = $isLoss ? 'Loss' : 'Profit';
                            @endphp

                            <div class="card {{ $stockBg }} mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-{{ $stockStatus }} text-white avatar me-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                {!! $stockIcon !!}
                                            </svg>
                                        </span>
                                        <div>
                                            <h3 class="mb-0">{{ $stockMessage }}</h3>
                                            <div class="text-{{ $stockStatus }} fw-bold">{{ number_format($product->quantity) }} {{ $product->unit->short_code ?? 'units' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Key Metrics -->
                            <div class="row g-2 mb-3">
                                @if(!Auth::user()->isEmployee())
                                <div class="col-6">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1">Buying Price</div>
                                            <h3 class="text-danger mb-0">{{ $product->buying_price !== null ? number_format($product->buying_price, 2) : '-' }}</h3>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="{{ Auth::user()->isEmployee() ? 'col-12' : 'col-6' }}">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1">Selling Price</div>
                                            <h3 class="text-success mb-0">{{ number_format($product->selling_price, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                @if(!Auth::user()->isEmployee())
                                <div class="col-12">
                                    <div class="card card-sm {{ $isLoss ? 'bg-danger-lt' : '' }}">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1">{{ $profitOrLoss }} Margin</div>
                                            <h3 class="{{ $isLoss ? 'text-danger' : 'text-success' }} mb-0">
                                                {{ $profitMargin >= 0 ? '+' : '' }}{{ number_format($profitMargin, 1) }}%
                                            </h3>
                                            @if($isLoss)
                                                <small class="text-danger fw-bold">⚠️ Selling below cost!</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Stock Level Progress Bar -->
                            @if($product->quantity > 0)
                                @php
                                    $maxStock = max($product->quantity_alert * 3, $product->quantity);
                                    $stockPercentage = min(100, ($product->quantity / $maxStock) * 100);
                                    $progressClass = $stockPercentage > 50 ? 'bg-success' : ($stockPercentage > 25 ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Stock Level</span>
                                        <span class="text-muted small">Alert: {{ $product->quantity_alert }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $stockPercentage }}%" aria-valuenow="{{ $stockPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Barcode -->
                            <div class="card mb-0">
                                <div class="card-body text-center">
                                    <div class="text-muted mb-2 small">Product Barcode</div>
                                    {!! $barcode !!}
                                    <div class="text-muted small mt-1">{{ $product->code }}</div>

                                    <!-- Print Barcode Button -->
                                    <div class="mt-3">
                                        <button type="button" onclick="printBarcodeLabels('{{ shop_route('barcode.print.product', $product) }}?quantity={{ $product->quantity > 0 ? $product->quantity : 1 }}')" class="btn btn-primary btn-sm w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"/>
                                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"/>
                                                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"/>
                                            </svg>
                                            Print Barcode Labels
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ __('Product Details') }}
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-muted" style="width: 160px;">Name</th>
                                        <td class="fw-bold">{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Slug</th>
                                        <td class="text-secondary">{{ $product->slug }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Code</th>
                                        <td><span class="badge bg-light text-dark border">{{ $product->code }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Barcode</th>
                                        <td>{!! $barcode !!}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Category</th>
                                        <td>
                                            @if($product->category)
                                                <a href="{{ shop_route('categories.show', $product->category) }}" class="badge bg-blue-lt text-uppercase fw-normal">
                                                    {{ $product->category->name }}
                                                </a>
                                            @else
                                                <span class="badge bg-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Unit</th>
                                        <td>
                                            @if($product->unit)
                                                <a href="{{ shop_route('units.show', $product->unit) }}" class="badge bg-green-lt text-uppercase fw-normal">
                                                    {{ $product->unit->short_code }}
                                                </a>
                                            @else
                                                <span class="badge bg-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Quantity</th>
                                        <td class="fw-bold">{{ $product->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Quantity Alert</th>
                                        <td><span class="badge bg-red-lt">{{ $product->quantity_alert }}</span></td>
                                    </tr>
                                    @if(!Auth::user()->isEmployee())
                                    <tr>
                                        <th class="text-muted">Buying Price</th>
                                        <td class="fw-bold text-danger">LKR {{ number_format($product->buying_price, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th class="text-muted">Selling Price</th>
                                        <td class="fw-bold text-success">LKR {{ number_format($product->selling_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Notes</th>
                                        <td class="text-secondary">{{ $product->notes }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer text-end">
                            @if(!Auth::user()->isEmployee())
                            <x-button.edit route="{{ shop_route('products.edit', $product) }}">
                                {{ __('Edit') }}
                            </x-button.edit>
                            @endif

                            <x-button.back route="{{ shop_route('products.index') }}">
                                {{ __('Cancel') }}
                            </x-button.back>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printBarcodeLabels(url) {
    // Show loading indicator
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';

    // Fetch the barcode page content
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Create a new window with the barcode content
            const printWindow = window.open('', '_blank');
            printWindow.document.write(html);
            printWindow.document.close();

            // Wait for content to load, then trigger print
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();

                    // Close the window after printing
                    printWindow.onafterprint = function() {
                        printWindow.close();
                    };
                }, 500);
            };

            // Restore button
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        })
        .catch(error => {
            console.error('Error loading barcode:', error);
            alert('Failed to load barcode. Please try again.');

            // Restore button
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
}
</script>


@endsection
