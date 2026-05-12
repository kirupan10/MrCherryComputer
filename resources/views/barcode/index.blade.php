@extends('layouts.nexora')

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7v-1a2 2 0 0 1 2 -2h2"/>
                        <path d="M4 17v1a2 2 0 0 0 2 2h2"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v1"/>
                        <path d="M16 20h2a2 2 0 0 0 2 -2v-1"/>
                        <path d="M5 11h1v2h-1z"/>
                        <path d="M10 11l0 2"/>
                        <path d="M14 11h1v2h-1z"/>
                        <path d="M19 11l0 2"/>
                    </svg>
                    {{ __('Barcode Configuration') }}
                </h2>
                <div class="text-muted mt-1">Configure barcode printing settings for your products</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ shop_route('products.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"/></svg>
                        Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="row row-cards">
            <!-- Settings Card -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Barcode Settings</h3>
                    </div>
                    <div class="card-body">
                        <form id="barcodeSettingsForm" method="POST" action="{{ shop_route('barcode.settings.update') }}">
                            @csrf

                            <!-- Info Alert -->
                            <div class="alert alert-info mb-4">
                                <h4 class="alert-title">📋 Label Configuration</h4>
                                <p class="mb-2">All labels will display: <strong>Product Name</strong>, <strong>Barcode</strong>, <strong>Barcode Number</strong>, and <strong>Price</strong></p>
                                <p class="mb-0"><strong>Format:</strong> EAN-13 (Industry Standard) | <strong>Size:</strong> 40mm x 30mm</p>
                            </div>

                            <!-- Hidden Fields for Fixed Settings -->
                            <input type="hidden" name="barcode_type" value="EAN13">
                            <input type="hidden" name="barcode_width" value="2">
                            <input type="hidden" name="barcode_height" value="50">
                            <input type="hidden" name="paper_size" value="40x30">
                            <input type="hidden" name="labels_per_row" value="3">

                            <!-- Font Size -->
                            <div class="mb-3">
                                <label class="form-label">Text Font Size</label>
                                <select name="font_size" class="form-select">
                                    <option value="10" {{ $settings->font_size == '10' ? 'selected' : '' }}>Small (10px)</option>
                                    <option value="12" {{ $settings->font_size == '12' ? 'selected' : '' }}>Normal (12px)</option>
                                    <option value="14" {{ $settings->font_size == '14' ? 'selected' : '' }}>Medium (14px)</option>
                                    <option value="16" {{ $settings->font_size == '16' ? 'selected' : '' }}>Large (16px)</option>
                                    <option value="18" {{ $settings->font_size == '18' ? 'selected' : '' }}>Extra Large (18px)</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M14 4l0 4l-6 0l0 -4"/></svg>
                                    Save Settings
                                </button>
                                <button type="button" class="btn btn-info" id="previewBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                                    Preview
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Info Card -->
                <div class="card mt-3">
                    <div class="card-header bg-info-lt">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            Label Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Format:</strong> EAN-13 (Industry Standard)</li>
                            <li><strong>Size:</strong> 40mm × 30mm</li>
                            <li><strong>Content:</strong> Product name, barcode, price</li>
                            <li><strong>Font:</strong> Adjustable (10-18px above)</li>
                        </ul>
                    </div>
                </div>

                <!-- Where to Print From -->
                <div class="card mt-3">
                    <div class="card-header bg-success-lt">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                            </svg>
                            Where to Print
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Individual Product:</strong> Go to Products → Select product → Click "Print Barcode" button</li>
                            <li><strong>Bulk Print:</strong> Products page → "Print All Barcodes" for multiple items</li>
                            <li><strong>Custom Quantity:</strong> Specify number of labels per product when printing</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Preview & Quick Actions Card -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview & Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div id="previewContainer" class="border rounded p-3 mb-4 text-center bg-light" style="min-height: 200px;">
                            <div class="text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 7v-1a2 2 0 0 1 2 -2h2"/>
                                    <path d="M4 17v1a2 2 0 0 0 2 2h2"/>
                                    <path d="M16 4h2a2 2 0 0 1 2 2v1"/>
                                    <path d="M16 20h2a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M5 11h1v2h-1z"/>
                                    <path d="M10 11l0 2"/>
                                    <path d="M14 11h1v2h-1z"/>
                                    <path d="M19 11l0 2"/>
                                </svg>
                                <p>Click "Preview" to see how your barcodes will look</p>
                            </div>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <strong>Go to Products</strong>
                                        <div class="text-muted small">View individual products and print barcodes one by one</div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ shop_route('products.index') }}" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M12 12l0 9"/><path d="M12 12l-8 -4.5"/></svg>
                                            View Products
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Card - Right Side -->
                <div class="card mt-3">
                    <div class="card-header bg-info-lt">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                <path d="M12 9h.01"/>
                                <path d="M11 12h1v4h1"/>
                            </svg>
                            How Printing Works
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-2">🖨️ Print Process</h5>
                        <ol class="mb-3">
                            <li>Click "Print Barcode" from product page</li>
                            <li>New window opens with labels</li>
                            <li>Print dialog appears automatically</li>
                            <li>If not, click <strong class="text-primary">"PRINT NOW"</strong> button or press <kbd>Ctrl+P</kbd></li>
                        </ol>

                        <h5 class="mb-2">⚙️ Print Settings</h5>
                        <ul class="mb-3">
                            <li><strong>Scale:</strong> 100% (don't shrink)</li>
                            <li><strong>Margins:</strong> Minimum or None</li>
                            <li><strong>Background:</strong> Enable graphics</li>
                        </ul>

                        <h5 class="mb-2">🔧 Quick Troubleshooting</h5>
                        <ul class="mb-0">
                            <li><strong>Popup blocked?</strong> Allow popups in browser</li>
                            <li><strong>Won't scan?</strong> Enable background graphics, use high quality print</li>
                            <li><strong>Misaligned?</strong> Set margins to none, check label size in printer</li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Barcode settings JavaScript loaded');

    const form = document.getElementById('barcodeSettingsForm');

    if (!form) {
        console.error('Form #barcodeSettingsForm not found!');
        return;
    }

    console.log('Form found:', form);

    const previewBtn = document.getElementById('previewBtn');
    const previewContainer = document.getElementById('previewContainer');

    // Save settings
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered!');
        e.preventDefault();
        console.log('Default form submission prevented');

        const formData = new FormData(form);
        const data = {};

        // Get form values (fixed values from hidden fields)
        data.barcode_type = 'EAN13';
        data.barcode_width = 2;
        data.barcode_height = 50;
        data.paper_size = '40x30';
        data.labels_per_row = 3;
        data.font_size = formData.get('font_size');

        console.log('Submitting barcode settings:', data);

        fetch('{{ shop_route("barcode.settings.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Settings save response:', data);
            if (data.success) {
                showToast(data.message, 'success');
                console.log('Settings updated successfully, reloading in 1 second...');
                // Reload page after 1 second to show updated settings
                setTimeout(function() {
                    console.log('Reloading page...');
                    window.location.reload();
                }, 1000);
            } else {
                console.error('Save failed:', data);
                showToast(data.message || 'Failed to save settings', 'error');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error saving settings:', error);
            showToast('An error occurred while saving settings', 'error');
        });
    });

    console.log('Form submit event listener attached successfully');

    // Preview functionality
    previewBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        const data = {};

        // Get form values (fixed values)
        data.barcode_type = 'EAN13';
        data.barcode_width = 2;
        data.barcode_height = 50;
        data.paper_size = '40x30';
        data.labels_per_row = 3;
        data.font_size = formData.get('font_size');

        previewContainer.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

        fetch('{{ shop_route("barcode.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                previewContainer.innerHTML = data.html;
            } else {
                previewContainer.innerHTML = '<div class="text-danger">Failed to generate preview</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            previewContainer.innerHTML = '<div class="text-danger">An error occurred</div>';
        });
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }
});
</script>
@endpush
@endsection
