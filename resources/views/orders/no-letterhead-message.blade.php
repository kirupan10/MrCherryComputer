<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letterhead Required</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .container {
            width: 100%;
            max-width: 595px;
            margin: 0 auto;
            padding: 100px 40px;
            text-align: center;
        }
        .icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .message {
            font-size: 16px;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .instructions {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        .instructions h2 {
            font-size: 18px;
            color: #495057;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.8;
        }
        .instructions li {
            margin-bottom: 8px;
        }
        .order-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
            font-size: 12px;
            color: #adb5bd;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        .warning-box strong {
            color: #856404;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .warning-box p {
            color: #856404;
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>

        <h1>Letterhead PDF Required</h1>

        <p class="message">
            {{ $message }}
        </p>

        <div class="warning-box">
            <strong>⚡ Action Required</strong>
            <p>Your invoices cannot be generated without a letterhead template. Please upload your company letterhead PDF to continue.</p>
        </div>

        <div class="instructions">
            <h2>📋 How to Upload Letterhead:</h2>
            <ol>
                <li>Navigate to <strong>Letterhead Configuration</strong> from your dashboard menu</li>
                <li>Click on <strong>"Upload Letterhead"</strong> section</li>
                <li>Select your A4-sized PDF letterhead file (max 5MB)</li>
                <li>Click <strong>"Upload Letterhead"</strong> button</li>
                <li>Configure your customer details positioning</li>
                <li>Try downloading this invoice again</li>
            </ol>
        </div>

        <div class="order-info">
            <p><strong>Order Reference:</strong> {{ $order->invoice_no ?? 'N/A' }}</p>
            <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
            <p><strong>Date:</strong> {{ $order->sale_date ? $order->sale_date->format('d M Y') : 'N/A' }}</p>
        </div>
    </div>
</body>
</html>
