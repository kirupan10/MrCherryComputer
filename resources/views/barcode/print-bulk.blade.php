<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcodes - All In-Stock Products</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 10mm;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
                background: white;
            }
            .no-print {
                display: none;
            }
            @page {
                @if($settings->paper_size == 'A4')
                    margin: 10mm;
                    size: A4;
                @elseif($settings->paper_size == '40x30')
                    margin: 0;
                    size: 40mm 30mm;
                @elseif($settings->paper_size == '50x25')
                    margin: 0;
                    size: 50mm 25mm;
                @elseif($settings->paper_size == '60x40')
                    margin: 0;
                    size: 60mm 40mm;
                @endif
            }
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .barcode-container {
            @if($settings->paper_size == 'A4')
                display: flex;
                flex-wrap: wrap;
                gap: 5mm;
                justify-content: flex-start;
            @else
                display: block;
            @endif
        }

        .barcode-label {
            @if($settings->paper_size == 'A4')
                width: calc((100% - ({{ $settings->labels_per_row - 1 }} * 5mm)) / {{ $settings->labels_per_row }});
                border: 1px solid #ddd;
                margin-bottom: 5mm;
                padding: 3mm;
            @else
                width: 100%;
                height: 100%;
                padding: 2mm;
                page-break-after: always;
            @endif

            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            background: white;
        }

        .barcode-label:last-child {
            page-break-after: auto;
        }

        .product-name {
            font-size: 11px;
            font-weight: 900;
            margin: 0 0 0.5mm 0;
            padding: 0 1mm;
            word-wrap: break-word;
            max-width: 100%;
            line-height: 1.2;
            color: #000;
            text-rendering: optimizeLegibility;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            letter-spacing: 0.2px;
            overflow: visible;
        }

        @media print {
            .barcode-label {
                margin: 0;
                padding: 1mm 2mm;
            }

            .barcode-image {
                margin: 0.5mm 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .barcode-image svg,
            .barcode-image img {
                @if($settings->paper_size == '60x40')
                    width: 48mm !important;
                    height: 30mm !important;
                    max-width: 48mm !important;
                    max-height: 30mm !important;
                @elseif($settings->paper_size == '50x25')
                    width: 40mm !important;
                    height: 19mm !important;
                    max-width: 40mm !important;
                    max-height: 19mm !important;
                @elseif($settings->paper_size == '40x30')
                    width: 30mm !important;
                    height: 19mm !important;
                    max-width: 30mm !important;
                    max-height: 19mm !important;
                @else
                    width: 70% !important;
                    height: auto !important;
                    max-width: 70% !important;
                @endif
                display: block !important;
                margin: 0 auto !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                shape-rendering: crispEdges !important; /* Sharp edges for scanning */
            }

            .barcode-image svg rect {
                fill: #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .barcode-image svg path {
                fill: #000 !important;
                stroke: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        .barcode-image {
            margin: 0;
            max-width: 100%;
            background: white;
        }

        .barcode-image svg,
        .barcode-image img {
            width: 48mm;
            max-width: 48mm;
            height: 30mm;
            max-height: 30mm;
            background: white;
            display: block;
            margin: 0;
            shape-rendering: crispEdges; /* Sharp edges for scanning */
        }

        .barcode-image svg rect {
            fill: #000 !important;
        }

        .product-code {
            font-size: 11px;
            font-weight: 900;
            margin: 1mm 0;
            line-height: 1.2;
            color: #000;
            font-family: Arial, sans-serif;
            letter-spacing: 0.5px;
            text-rendering: optimizeLegibility;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .product-price {
            font-size: 13px;
            font-weight: 900;
            margin: 1mm 0 0 0;
            color: #000;
            line-height: 1.2;
            text-rendering: optimizeLegibility;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            letter-spacing: 0.3px;
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .btn-print:hover {
            background-color: #0052a3;
        }

        .total-labels {
            position: fixed;
            top: 70px;
            right: 20px;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-note {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 15px 20px;
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
            max-width: 350px;
        }

        .print-note strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="print-note">
            <strong>⏳ Print dialog should open automatically...</strong>
            <p style="margin: 10px 0 0 0;">If it doesn't open, click the "🖨️ Print Barcodes" button or press <strong>Ctrl+P</strong> (Windows) or <strong>Cmd+P</strong> (Mac)</p>
        </div>

        <button class="btn-print" onclick="window.print()">
            🖨️ Print Barcodes
        </button>
        <div class="total-labels">
            <strong>Total Labels:</strong> {{ count($allBarcodes) }}
        </div>
    </div>

    <div class="print-header no-print">
        <h1>Product Barcodes - All In-Stock Products</h1>
        <p>Generated: {{ date('F d, Y h:i A') }}</p>
    </div>

    <div class="barcode-container">
        @foreach($allBarcodes as $barcode)
            <div class="barcode-label">
                <div class="product-name">{{ Str::words($barcode['name'], 9, '...') }}</div>

                <div class="barcode-image">
                    {!! $barcode['barcode_html'] !!}
                </div>

                <div class="product-price">LKR {{ number_format($barcode['price'], 2) }}</div>
            </div>
        @endforeach
    </div>

    @if(count($allBarcodes) == 0)
        <div class="no-print" style="text-align: center; margin-top: 50px;">
            <h2>No in-stock products found</h2>
            <p>Please add products with quantity greater than 0 to generate barcodes.</p>
        </div>
    @endif

    <!-- Manual Print Button (Fallback) -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <button onclick="window.print()" style="
            background-color: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        ">
            🖨️ PRINT NOW
        </button>
    </div>

    <script>
        // Auto-print with proper timing
        window.addEventListener('load', function() {
            console.log('Bulk print page fully loaded, preparing to print...');

            const labels = document.querySelectorAll('.barcode-label');
            console.log('Found ' + labels.length + ' barcode labels');

            if (labels.length > 0) {
                // Give browser time to fully render
                setTimeout(function() {
                    console.log('Triggering print dialog...');
                    window.print();
                }, 800);
            } else {
                console.warn('No barcode labels found to print');
            }
        });

        // Auto-close after printing
        window.addEventListener('afterprint', function() {
            console.log('Print dialog closed');
            setTimeout(function() {
                window.close();
            }, 500);
        });
    </script>
</body>
</html>
