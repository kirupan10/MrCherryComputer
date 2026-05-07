<div class="barcode-preview-item" style="
    display: inline-block;
    padding: 10px;
    border: 1px dashed #ccc;
    text-align: center;
    font-family: Arial, sans-serif;
    margin: 5px;
">
    @if(isset($barcodes[0]))
        @php
            $barcode = $barcodes[0];
        @endphp

        <div style="font-size: {{ $settings->font_size }}px; font-weight: bold; margin-bottom: 5px;">
            {{ Str::words($barcode['name'], 9, '...') }}
        </div>

        <div style="margin: 5px 0;">
            {!! $barcode['barcode_html'] !!}
        </div>

        <div style="font-size: {{ $settings->font_size }}px; font-weight: bold; margin-top: 5px;">
            ${{ number_format($barcode['price'], 2) }}
        </div>
    @else
        <div class="text-muted">No sample product available</div>
    @endif
</div>
