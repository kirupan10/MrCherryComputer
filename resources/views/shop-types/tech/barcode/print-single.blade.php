<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $product->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .no-print h1 {
            margin: 0 0 10px 0;
            color: #1e293b;
        }

        .no-print p {
            margin: 0 0 20px 0;
            color: #64748b;
        }

        .no-print button {
            padding: 10px 20px;
            background: #0ea5e9;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin: 5px;
        }

        .no-print button:last-child {
            background: #64748b;
        }

        .barcode-preview-card {
            background: white;
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 2rem;
            min-height: 400px;
        }

        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            align-items: flex-start;
        }

        .barcode-label {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            min-width: 200px;
        }

        .product-name {
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 10px;
            color: #475569;
            word-wrap: break-word;
        }

        .barcode-image {
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-image svg,
        .barcode-image img {
            max-width: 100%;
            height: auto !important;
            width: auto !important;
        }

        /* Ensure SVG renders crisply */
        .barcode-image svg {
            shape-rendering: crispEdges;
        }

        .product-code {
            font-size: 12px;
            margin: 10px 0;
            color: #64748b;
            font-family: Arial, sans-serif;
            font-weight: 900;
        }

        .product-price {
            font-size: 16px;
            font-weight: 900;
            margin-top: 10px;
            color: #0ea5e9;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            .barcode-preview-card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                min-height: auto !important;
            }

            .barcode-container {
                margin: 0 !important;
                padding: 0 !important;
            }

            .barcode-label {
                page-break-after: always !important;
                page-break-inside: avoid !important;
                margin: 0 !important;
                padding: 2mm !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: white !important;
                box-sizing: border-box !important;
                width: 90% !important;
                height: 90vh !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                overflow: visible !important;
            }

            .barcode-label:last-child {
                page-break-after: auto !important;
            }

            .product-name {
                display: block !important;
                font-size: 11px !important;
                font-weight: 900 !important;
                margin: 0 0 1mm 0 !important;
                text-align: center !important;
                color: #000 !important;
                line-height: 1.2 !important;
                width: 100% !important;
                padding: 0 1mm !important;
                flex-shrink: 0 !important;
                word-wrap: break-word !important;
                overflow: visible !important;
            }

            .barcode-image {
                margin: 0 !important;
                padding: 2mm 3mm !important; /* Add quiet zones (white space) for scanner */
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                flex-shrink: 1 !important;
                max-height: 19mm !important; /* Adjusted for smaller barcode size */
                overflow: visible !important; /* Allow quiet zones */
            }

            .barcode-image svg,
            .barcode-image img {
                display: block !important;
                margin: 0 auto !important;
                max-width: 90% !important; /* Leave room for quiet zones */
                max-height: 100% !important;
                height: auto !important;
                width: auto !important;
                shape-rendering: crispEdges !important; /* Sharp edges for scanning */
            }

            .product-code {
                display: block !important;
                font-size: 9px !important;
                font-weight: 900 !important;
                margin: 0.5mm 0 !important;
                text-align: center !important;
                color: #000 !important;
                font-family: Arial, sans-serif !important;
                line-height: 1 !important;
                width: 100% !important;
                flex-shrink: 0 !important;
            }

            .product-price {
                display: block !important;
                font-size: 14px !important;
                font-weight: 900 !important;
                margin: 0.5mm 0 0 0 !important;
                text-align: center !important;
                color: #000 !important;
                line-height: 1 !important;
                width: 100% !important;
                flex-shrink: 0 !important;
            }

            @page {
                @if($settings->paper_size == 'A4')
                    margin: 10mm;
                    size: A4 portrait;
                @elseif($settings->paper_size == '40x30')
                    margin: 1mm;
                    size: 40mm 30mm;
                @elseif($settings->paper_size == '50x25')
                    margin: 1mm;
                    size: 50mm 25mm;
                @elseif($settings->paper_size == '60x40')
                    margin: 1mm;
                    size: 60mm 40mm;
                @else
                    margin: 10mm;
                    size: A4 portrait;
                @endif
            }
        }
    </style>
</head>
<body>
    <!-- Screen Preview Header (hidden on print) -->
    <div class="no-print">
        <h1>Print Barcode - {{ $product->name }}</h1>
        <p>Preview and print {{ $quantity }} barcode label(s)</p>
        <button onclick="window.print()">🖨️ Print Now</button>
        <button onclick="window.history.back()">← Back</button>
    </div>

    <!-- Barcode Preview -->
    <div class="barcode-preview-card">
        <div class="barcode-container">
            @foreach($barcodes as $barcode)
                <div class="barcode-label">
                    <div class="product-name">{{ Str::words($barcode['name'], 9, '...') }}</div>
                    <div class="barcode-image">
                        {!! $barcode['barcode_html'] !!}
                    </div>
                    <div class="product-price">LKR {{ number_format($barcode['price'], 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Auto-print on load (with slight delay)
        window.addEventListener('load', function() {
            console.log('Product barcode page loaded');
            console.log('Barcodes count: {{ count($barcodes ?? []) }}');
            setTimeout(function() {
                window.print();
            }, 800);
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
