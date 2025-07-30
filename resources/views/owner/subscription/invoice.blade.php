<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Noto Sans Bengali';
            src: url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;700&display=swap');
        }
        body {
            font-family: 'Noto Sans Bengali', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .invoice-details {
            margin: 30px 0;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .company-info h3{
            margin-bottom: 0px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ $company_name }}</div>
        <div style="margin-top: 10px; color: #666;">
            {{ $company_address }}<br>
            Phone: {{ $company_phone }} | Email: {{ $company_email }}
        </div>
    </div>

    <div class="clear"></div>
    <h2 style="text-align: center; margin-bottom: 10px;">SUBSCRIPTION INVOICE</h2>
    <div class="company-info">
        <h3>Bill To:</h3>
        <strong>{{ $owner->name }}</strong><br>
        Email: {{ $owner->email }}<br>
        Phone: {{ $owner->phone ?? 'N/A' }}<br>
        Address: {{ $owner->address ?? 'N/A' }}
    </div>

    <div class="invoice-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td><strong>Invoice Number:</strong></td>
                <td>{{ $invoice_number }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ $created_at->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Due Date:</strong></td>
                <td>{{ $created_at->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td class="{{ $status === 'paid' ? 'status-paid' : 'status-pending' }}">
                    {{ ucfirst($status) }}
                </td>
            </tr>

        </table>
    </div>

    <div class="clear"></div>

    <div class="invoice-details">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $plan->name }} Subscription</strong><br>
                        ({{ $billing->subscription ? $billing->subscription->start_date->format('M d, Y') . ' to ' . $billing->subscription->end_date->format('M d, Y') : $plan->duration . ' ' . $plan->duration_type }})<br>
                        <small>
                            @if($plan->features && is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    • {{ $feature }}<br>
                                @endforeach
                            @else
                                Properties: {{ $plan->properties_limit_text }},
                                Units: {{ $plan->units_limit_text }},
                                Tenants: {{ $plan->tenants_limit_text }}
                                @if($plan->sms_notification)
                                    <br>• SMS Notifications
                                @endif
                            @endif
                        </small>
                    </td>
                    <td style="text-align: right;">&#2547;{{ number_format($amount, 2) }}</td>
                </tr>
                @if($payment_method && $payment_method->name)
                <tr>
                    <td>Payment Method</td>
                    <td style="text-align: right;">{{ $payment_method->name }}</td>
                </tr>
                @elseif($billing->paymentMethod && $billing->paymentMethod->name)
                <tr>
                    <td>Payment Method</td>
                    <td style="text-align: right;">{{ $billing->paymentMethod->name }}</td>
                </tr>
                @elseif($billing->payment_method_id)
                <tr>
                    <td>Payment Method</td>
                    <td style="text-align: right;">Payment Method ID: {{ $billing->payment_method_id }}</td>
                </tr>
                @endif

            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td><strong>Total Amount</strong></td>
                    <td style="text-align: right;"><strong>&#2547;{{ number_format($amount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Transactions Section -->
    <div class="transactions-section" style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px; color: #333;">Transactions</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Transaction Date</th>
                    <th>Gateway</th>
                    <th>Transaction ID</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($status === 'paid' && $paid_date && $transaction_id)
                    <tr>
                        <td>{{ $paid_date->format('M d, Y') }}</td>
                        <td>{{ $payment_method ? $payment_method->name : 'N/A' }}</td>
                        <td>{{ $transaction_id }}</td>
                        <td style="text-align: right;">&#2547;{{ number_format($amount, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666; padding: 20px;">
                            No Related Transactions Found
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your subscription!</p>
        <p>This is a computer generated invoice. No signature required.</p>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</body>
</html>
