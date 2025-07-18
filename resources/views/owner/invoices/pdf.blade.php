<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; }
        .invoice-box { max-width: 700px; margin: auto; padding: 30px; border: 1px solid #eee; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .company { font-size: 1.2em; font-weight: bold; color: #4361ee; }
        .invoice-title { font-size: 2em; font-weight: bold; }
        .info-table, .fee-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; }
        .fee-table th, .fee-table td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .fee-table th { background: #e8edff; color: #3f37c9; }
        .total-row td { font-weight: bold; }
        .status { font-weight: bold; color: #fff; background: #4895ef; padding: 4px 12px; border-radius: 4px; }
        .paid { background: #43aa8b; }
        .unpaid { background: #f72585; }
        .footer { margin-top: 40px; font-size: 0.95em; color: #888; }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        <div class="company">
            {{ config('app.name', 'Your Company') }}
        </div>
        <div class="invoice-title">
            INVOICE
        </div>
    </div>
    <table class="info-table">
        <tr>
            <td><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</td>
            <td><strong>Date:</strong> {{ $invoice->issue_date }}</td>
        </tr>
        <tr>
            <td><strong>Tenant:</strong> {{ $invoice->tenant->first_name ?? '' }} {{ $invoice->tenant->last_name ?? '' }}</td>
            <td><strong>Unit:</strong> {{ $invoice->unit->name ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Month:</strong> {{ $invoice->rent_month }}</td>
            <td><strong>Status:</strong>
                <span class="status {{ strtolower($invoice->status) }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </td>
        </tr>
    </table>

    <table class="fee-table" style="margin-top: 30px;">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (৳)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $breakdown = $invoice->breakdown ? json_decode($invoice->breakdown, true) : [];
            @endphp
            @if(isset($breakdown['base_rent']))
                <tr>
                    <td>Base Rent</td>
                    <td>{{ number_format($breakdown['base_rent'], 2) }}</td>
                </tr>
            @endif
            @if(isset($breakdown['charges']) && is_array($breakdown['charges']))
                @foreach($breakdown['charges'] as $charge)
                    <tr>
                        <td>{{ $charge['label'] ?? '' }}</td>
                        <td>{{ number_format($charge['amount'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            @endif
            @if(isset($breakdown['advance']))
                <tr>
                    <td>Advance Payment</td>
                    <td>{{ number_format($breakdown['advance'], 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total</td>
                <td>{{ number_format($invoice->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <strong>Notes:</strong> {{ $invoice->notes ?? 'Thank you for your payment!' }}
        <br>
        <strong>Contact:</strong> {{ config('app.email') ?? 'info@example.com' }}
    </div>
</div>
</body>
</html>
