<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Sheet - {{ $job->reference_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm 20mm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #000;
            background: white;
            padding: 0;
            margin: 0;
        }

        .page {
            width: 100%;
            max-width: 100%;
            background: white;
            padding: 20px 25px;
            margin: 0 auto;
        }

        /* Section Headers */
        .section-header {
            font-size: 22px;
            font-weight: 700;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 0;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            margin-top: 18px;
            margin-left: -25px;
            margin-right: -25px;
            padding-left: 25px;
            padding-right: 25px;
        }

        /* Subsection Headers */
        .subsection-header {
            font-size: 18px;
            font-weight: 600;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 12px;
            margin-top: 15px;
        }

        /* Field Label */
        .field-label {
            font-size: 14px;
            font-weight: 600;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        /* Field Value */
        .field-value {
            font-size: 16px;
            color: #000;
            font-weight: 400;
            padding: 6px 0;
            border-bottom: 1px solid #ddd;
        }

        /* Modern Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }

        .data-table thead {
            background: #f5f5f5;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .data-table th {
            padding: 12px 15px;
            text-align: left;
            font-size: 15px;
            font-weight: 700;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table tbody tr:last-child {
            border-bottom: 2px solid #000;
        }

        .data-table td {
            padding: 12px 15px;
            font-size: 15px;
            color: #000;
        }

        /* Numbered Tips */
        .tip-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 6px;
            font-size: 10px;
            line-height: 1.5;
        }

        .tip-number {
            min-width: 22px;
            height: 22px;
            background: #000;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 11px;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .tip-content {
            flex: 1;
        }

        .tip-title {
            font-weight: 700;
            color: #000;
            margin-bottom: 2px;
        }
        .tip-description {
            color: #000;
        }

        /* Terms List */
        .terms-list {
            font-size: 15px;
            line-height: 1.7;
            color: #000;
        }

        .terms-list div {
            margin-bottom: 6px;
            padding-left: 16px;
            position: relative;
        }

        .terms-list div::before {
            content: '•';
            position: absolute;
            left: 0;
            font-weight: 700;
        }

        /* Alert Box */
        .alert-notice {
            padding: 10px 15px;
            background: #fff;
            border: 2px solid #000;
            border-left: 5px solid #000;
            margin: 12px -25px;
            padding-left: 40px;
            padding-right: 40px;
        }

        .alert-title {
            font-size: 15px;
            font-weight: 700;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
        }

        .alert-content {
            font-size: 15px;
            line-height: 1.6;
            color: #000;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            background: #000;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: 700;
        }

        .divider {
            border: none;
            border-top: 1px solid #ddd;
            margin: 5px -25px;
        }
    </style>
</head>
<body>
    <div class="page">

        <!-- Company Header -->
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 18px; margin-left: -25px; margin-right: -25px; padding-left: 25px; padding-right: 25px;">
            <div style="font-size: 36px; font-weight: 700; color: #000; margin-bottom: 3px; letter-spacing: 1.5px;">
                {{ strtoupper($shop->name ?? 'COMPANY NAME') }}
            </div>
            <div style="font-size: 20px; color: #000; line-height: 1.6;">
                {{ $shop->address ?? 'Company Address' }}<br>
                Tel: {{ $shop->phone ?? 'Contact Number' }} | Email: {{ $shop->email ?? 'email@company.com' }}
            </div>
        </div>

        <!-- Document Title -->
        <div style="text-align: center; margin-bottom: 18px;">
            <div style="font-size: 32px; font-weight: 700; color: #000; letter-spacing: 2px;">JOB SHEET</div>
            <div style="font-size: 15px; color: #000; margin-top: 6px;">Professional Service Request Document</div>
        </div>

        <!-- JOB DETAILS SECTION -->

                    <!-- Job Information -->
                    <div class="section-header">JOB INFORMATION</div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                        <tr>
                            <td style="width: 65%; vertical-align: top; padding-right: 15px;">
                                <div class="field-label">Job Reference Number *</div>
                                <div class="field-value text-bold" style="font-size: 18px;">{{ $job->reference_number }}</div>
                            </td>
                            <td style="width: 35%; vertical-align: top;">
                                <div class="field-label">Date *</div>
                                <div class="field-value">{{ $job->created_at ? $job->created_at->format('d/m/Y') : 'N/A' }}</div>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-bottom: 8px;">
                        <div class="field-label">Status *</div>
                        <div style="padding: 3px 0;">
                            <span class="status-badge">{{ strtoupper($job->status ?? 'PENDING') }}</span>
                        </div>
                    </div>

                    <hr class="divider">

                    <!-- Client Information -->
                    <div class="subsection-header">CLIENT INFO</div>

                    <div style="margin-bottom: 8px;">
                        <div class="field-label">Customer Name *</div>
                        <div class="field-value">{{ $job->customer->name ?? 'N/A' }}</div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                                <div class="field-label">Phone *</div>
                                <div class="field-value">{{ $job->customer->phone ?? 'N/A' }}</div>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Address</div>
                                <div class="field-value" style="font-size: 15px;">{{ $job->customer->address ?? 'N/A' }}</div>
                            </td>
                        </tr>
                    </table>

                    <hr class="divider">

                    <!-- Job Details -->
                    <div class="subsection-header">JOB DETAILS</div>

                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                                <div class="field-label">Job Type *</div>
                                <div class="field-value">{{ $job->jobType->name ?? $job->type ?? 'N/A' }}</div>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="field-label">Est. Duration</div>
                                <div class="field-value">{{ $job->estimated_duration ?? $job->estimated_days ?? 'N/A' }} Days</div>
                            </td>
                        </tr>
                    </table>

                    <!-- Special Notes -->
                    @if(isset($job->notes) && !empty($job->notes))
                    <hr class="divider">
                    <div class="subsection-header">SPECIAL NOTES</div>
                    <div style="padding: 12px 15px; background: #f9f9f9; border-left: 3px solid #000; font-size: 15px; color: #000; line-height: 1.6;">
                        {{ $job->notes }}
                    </div>
                    @endif

        <!-- Important Notice -->
        <div class="alert-notice" style="margin-top: 20px;">
            <div class="alert-title" style="font-size: 15px;">COLLECTION NOTICE</div>
            <div class="alert-content" style="font-size: 15px;">
                Items must be collected within <strong>15 days</strong> from completion date. After this period, the shop will not be responsible for any items left at the premises.
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="section-header">TERMS & CONDITIONS</div>

        <div class="terms-list">
            <div><strong>Collection Policy:</strong> 15-day pickup period mandatory from completion date. Uncollected items will be deemed abandoned after this period.</div>
            <div><strong>Payment Terms:</strong> Full payment required before collection of items. We accept cash, card payments, and bank transfers.</div>
            <div><strong>Warranty Coverage:</strong> Warranty applies to our workmanship only. Does not cover customer-supplied materials or normal wear and tear.</div>
            <div><strong>Liability Limitations:</strong> We are not liable for pre-existing damage, hidden defects, or items left beyond the collection period.</div>
            <div><strong>Service Changes:</strong> Any modifications to the agreed work require written request and may incur additional charges.</div>
            <div><strong>Dispute Resolution:</strong> All disputes must be raised within 7 days of collection. After this period, work is considered accepted.</div>
            <div><strong>Force Majeure:</strong> We are not liable for delays caused by circumstances beyond our reasonable control including natural disasters or supply chain issues.</div>
            <div><strong>Privacy & Data:</strong> Customer information is kept confidential and used solely for service provision and communication purposes.</div>
        </div>

        <!-- Signature Section -->
        <div style="margin-top: 20px; padding-top: 12px; border-top: 2px solid #000; margin-left: -25px; margin-right: -25px; padding-left: 25px; padding-right: 25px;">
            <div style="font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 12px;">SIGNATURES & ACKNOWLEDGMENT</div>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 48%; vertical-align: top; padding: 18px;">
                        <div style="text-align: center;">
                            <div style="min-height: 40px; border-bottom: 1px solid #000; margin-bottom: 12px;"></div>
                            <div style="font-size: 15px; font-weight: 700; color: #000; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">Customer Signature</div>
                            <div style="font-size: 14px; color: #000;">Date: {{ date('d/m/Y') }}</div>
                        </div>
                    </td>
                    <td style="width: 4%;"></td>
                    <td style="width: 48%; vertical-align: top; padding: 18px;">
                        <div style="text-align: center;">
                            <div style="min-height: 40px; border-bottom: 1px solid #000; margin-bottom: 12px;"></div>
                            <div style="font-size: 15px; font-weight: 700; color: #000; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px;">Authorized Signature</div>
                            <div style="font-size: 14px; color: #000;">Date: {{ date('d/m/Y') }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 12px; font-size: 14px; color: #000; font-style: italic;">
                By signing above, customer acknowledges receipt of this job sheet and acceptance of all terms and conditions stated herein.
            </div>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #ddd; padding-top: 12px; margin-top: 12px; margin-left: -25px; margin-right: -25px; padding-left: 25px; padding-right: 25px;">
            <div style="text-align: center; font-size: 15px; color: #000; line-height: 1.6;">
                <div style="font-weight: 700; margin-bottom: 6px;">Thank You For Your Business</div>
                <div>For queries or support, please contact us at {{ $shop->phone ?? 'Contact Number' }} or {{ $shop->email ?? 'email@company.com' }}</div>
                <div style="margin-top: 7px; font-size: 13px; color: #000;">This is a computer-generated document. Please retain for your records.</div>
            </div>
        </div>

    </div>
</body>
</html>
