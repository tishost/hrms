<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-out Invoice</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .invoice-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .invoice-body {
            padding: 30px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .info-section h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 18px;
        }
        .info-section p {
            margin: 5px 0;
            color: #666;
        }
        .settlement-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .settlement-table th,
        .settlement-table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top;
            line-height: 1.4;
        }

        .settlement-table td small {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            line-height: 1.3;
        }
        .settlement-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .settlement-table .amount {
            text-align: right;
            font-weight: bold;
        }
        .settlement-table .deduction {
            color: #dc3545;
        }
        .settlement-table .total {
            background: #e8f5e8;
            font-weight: bold;
            font-size: 16px;
        }
        .settlement-table .total.negative {
            background: #ffe8e8;
            color: #dc3545;
        }
        .unit-condition {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .unit-condition h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .unit-condition p {
            margin: 0;
            color: #666;
            line-height: 1.6;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            border-top: 1px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .status-partial {
            background: #fff3cd;
            color: #856404;
        }
        .status-pending {
            background: #f8d7da;
            color: #721c24;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>🚪 Check-out Settlement Invoice</h1>
            <p>Final Financial Settlement</p>
        </div>

        <div class="invoice-body">
            <div class="invoice-info">
                <div class="info-section">
                    <h3>Tenant Information</h3>
                    <p><strong>Name:</strong> {{ $checkout->tenant->first_name }} {{ $checkout->tenant->last_name }}</p>
                    <p><strong>Mobile:</strong> {{ $checkout->tenant->mobile }}</p>
                    <p><strong>Email:</strong> {{ $checkout->tenant->email ?? 'N/A' }}</p>
                </div>
                <div class="info-section">
                    <h3>Check-out Details</h3>
                    <p><strong>Date:</strong> {{ $checkout->check_out_date->format('M d, Y') }}</p>
                    <p><strong>Unit:</strong> {{ $checkout->unit->name ?? 'N/A' }}</p>
                    <p><strong>Reason:</strong> {{ $checkout->check_out_reason }}</p>
                </div>
            </div>

            <table class="settlement-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Security Deposit Return</strong><br>
                            <small style="color: #666;">Original deposit amount to be returned</small>
                        </td>
                        <td class="amount">+{{ number_format($checkout->security_deposit, 2) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Outstanding Dues Deduction</strong><br>
                            <small style="color: #666;">Unpaid rent and invoice amounts</small>
                        </td>
                        <td class="amount deduction">-{{ number_format($checkout->outstanding_dues, 2) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Utility Bills Deduction</strong><br>
                            <small style="color: #666;">Pending utility charges (electricity, water, gas, etc.)</small>
                        </td>
                        <td class="amount deduction">-{{ number_format($checkout->utility_bills, 2) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Cleaning Charges Deduction</strong><br>
                            <small style="color: #666;">Unit cleaning and maintenance costs</small>
                        </td>
                        <td class="amount deduction">-{{ number_format($checkout->cleaning_charges, 2) }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Other Charges Deduction</strong><br>
                            <small style="color: #666;">Additional charges (damages, late fees, etc.)</small>
                        </td>
                        <td class="amount deduction">-{{ number_format($checkout->other_charges, 2) }}</td>
                    </tr>
                    <tr class="total {{ $checkout->final_settlement_amount < 0 ? 'negative' : '' }}">
                        <td>
                            <strong>Final Settlement Amount</strong><br>
                            <small style="color: #666;">
                                @if($checkout->final_settlement_amount >= 0)
                                    Amount to be paid to tenant
                                @else
                                    Amount tenant owes to owner
                                @endif
                            </small>
                        </td>
                        <td class="amount">
                            <strong>{{ $checkout->final_settlement_amount >= 0 ? '+' : '' }}{{ number_format($checkout->final_settlement_amount, 2) }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge status-{{ $checkout->settlement_status }}">
                    {{ ucfirst($checkout->settlement_status) }}
                </span>
            </div>

            <div class="settlement-breakdown" style="margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h4 style="margin-bottom: 15px; color: #333;">📋 Settlement Breakdown</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h5 style="color: #666; margin-bottom: 10px;">Check-out Details</h5>
                        <p><strong>Reason:</strong> {{ $checkout->check_out_reason }}</p>
                        <p><strong>Date:</strong> {{ $checkout->check_out_date->format('M d, Y') }}</p>
                        <p><strong>Unit:</strong> {{ $checkout->unit->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <h5 style="color: #666; margin-bottom: 10px;">Financial Summary</h5>
                        <p><strong>Total Deductions:</strong> ৳{{ number_format($checkout->outstanding_dues + $checkout->utility_bills + $checkout->cleaning_charges + $checkout->other_charges, 2) }}</p>
                        <p><strong>Net Return:</strong> ৳{{ number_format($checkout->final_settlement_amount, 2) }}</p>
                        <p><strong>Status:</strong>
                            <span style="color: {{ $checkout->final_settlement_amount >= 0 ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                                {{ $checkout->final_settlement_amount >= 0 ? 'Refund Due' : 'Payment Due' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            @if($checkout->handover_condition)
                <div class="unit-condition">
                    <h4>🏠 Unit Handover Condition</h4>
                    <p>{{ $checkout->handover_condition }}</p>
                    @if($checkout->handover_date)
                        <p style="margin-top: 10px; font-size: 14px;">
                            <strong>Handover Date:</strong> {{ $checkout->handover_date->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            @endif

            @if($checkout->notes)
                <div class="unit-condition">
                    <h4>📝 Additional Notes</h4>
                    <p>{{ $checkout->notes }}</p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>Generated on:</strong> {{ now()->format('M d, Y \a\t h:i A') }}</p>
            <p>This is a computer-generated document. No signature required.</p>
        </div>
    </div>
</body>
</html>
