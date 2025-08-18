<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px;
            padding: 10px;
            background: #fff;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }
        @page {
            size: A4;
            margin: 15mm;
        }
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .left-info, .right-info {
            flex: 1;
        }
        .info-group {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background: #f8d7da;
            color: #721c24;
        }
        .status-partial {
            background: #fff3cd;
            color: #856404;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .breakdown-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #333;
        }
        .total-row {
            margin-bottom: 10px;
        }
        .total-label {
            font-weight: bold;
            color: #666;
        }
        .total-value {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-name">BariManager</div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
        </div>

        <div class="info-section">
            <div class="left-info">
                <div class="section-title">Invoiced To:</div>
                <div class="info-group">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value">
                        {{ $invoice->tenant->first_name ?? '' }} {{ $invoice->tenant->last_name ?? '' }}
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Phone No:</div>
                    <div class="info-value">{{ $invoice->tenant->mobile ?? 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $invoice->tenant->address ?? 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Upazila:</div>
                    <div class="info-value">{{ $invoice->tenant->upazila ?? 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">District, Zipcode:</div>
                    <div class="info-value">
                        {{ $invoice->tenant->district ?? 'N/A' }}, {{ $invoice->tenant->zip ?? 'N/A' }}
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Country:</div>
                    <div class="info-value">{{ $invoice->tenant->country ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="right-info">
                <div class="section-title">Property Details:</div>
                <div class="info-group">
                    <div class="info-label">Property Name:</div>
                    <div class="info-value">{{ $invoice->property->name ?? 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Unit:</div>
                    <div class="info-value">{{ $invoice->unit->name ?? 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Issue Date:</div>
                    <div class="info-value">{{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Due Date:</div>
                    <div class="info-value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower($invoice->status ?? 'unpaid') }}">
                            {{ ucfirst($invoice->status ?? 'Unpaid') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->breakdown)
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $breakdown = json_decode($invoice->breakdown, true) ?? [];
                    @endphp
                    @foreach($breakdown as $fee)
                        <tr>
                            <td>{{ $fee['name'] ?? 'N/A' }}</td>
                            <td>${{ number_format($fee['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Total Amount:</span>
                <span class="total-value">${{ number_format($invoice->amount ?? 0, 2) }}</span>
            </div>
            @if($invoice->paid_amount && $invoice->paid_amount > 0)
                <div class="total-row">
                    <span class="total-label">Paid Amount:</span>
                    <span class="total-value">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Remaining Balance:</span>
                    <span class="total-value">${{ number_format(($invoice->amount ?? 0) - $invoice->paid_amount, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
