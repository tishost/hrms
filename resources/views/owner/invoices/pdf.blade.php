<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Invoice {{ $invoice->invoice_number }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #fff;
      color: #333;
      font-size: 10px;
    }
    .invoice-box {
      max-width: 600px;
      margin: 10px auto;
      background: #fff;
      padding: 15px;
      border: 1px solid #ddd;
    }
    h1 {
      font-size: 20px;
      color: #2d2d2d;
      margin-bottom: 8px;
    }
    .invoice-header,
    .invoice-details {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 15px;
    }
    .invoice-header div,
    .invoice-details div {
      flex: 1;
      min-width: 200px;
    }
    .invoice-header p,
    .invoice-details p {
      margin: 3px 0;
      font-size: 11px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table thead {
      background: #f0f0f0;
    }
    table th {
      padding: 8px 10px;
      text-align: left;
      font-size: 11px;
    }
    table th:last-child {
      text-align: right;
    }
    table td {
      padding: 8px 10px;
      text-align: right;
      border-bottom: 1px solid #eee;
      font-size: 11px;
    }
    table td:first-child {
      text-align: left;
    }
    .totals {
      margin-top: 15px;
      width: 100%;
    }
    .totals td {
      padding: 5px 10px;
      text-align: right;
      font-size: 11px;
    }
    .totals td:first-child {
      text-align: left;
    }
    .totals .label {
      font-weight: bold;
    }
    .totals .total {
      font-size: 14px;
      font-weight: bold;
    }
    .status {
      padding: 3px 8px;
      background: #ffd4d4;
      color: #d40000;
      border-radius: 3px;
      font-weight: bold;
      display: inline-block;
      font-size: 10px;
    }
    .status.paid {
      background: #d4ffd4;
      color: #00a000;
    }
    .status.partial {
      background: #fff3cd;
      color: #856404;
    }
    .payment-box {
      margin-top: 20px;
      background: #f9f9f9;
      padding: 12px;
      border-radius: 6px;
    }
    .payment-box h3 {
      margin: 0 0 8px 0;
      font-size: 14px;
    }
    .payment-box p {
      margin: 3px 0;
      font-size: 11px;
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="invoice-header">
      <div>
        <h1>Rent Invoice</h1>
        <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Date:</strong> {{ $invoice->issue_date }}</p>
        <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
      </div>
      <div style="text-align:right;">
        <p><strong>Status:</strong> <span class="status {{ strtolower($invoice->status) }}">{{ ucfirst($invoice->status) }}</span></p>
        <p><strong>Month:</strong> {{ $invoice->rent_month }}</p>
        <p><strong>Unit:</strong> {{ $invoice->unit->name ?? '' }}</p>
      </div>
    </div>

    <div class="invoice-details">
      <div>
        <p><strong>Tenant:</strong> {{ $invoice->tenant->first_name ?? '' }} {{ $invoice->tenant->last_name ?? '' }}</p>
        <p><strong>Address:</strong> {{ $invoice->tenant->address ?? 'N/A' }}</p>
        <p><strong>Mobile:</strong> {{ $invoice->tenant->mobile ?? 'N/A' }}</p>
      </div>
    </div>

    <table>
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
      </tbody>
    </table>

    <table class="totals">
      <tr>
        <td class="label">Sub Total:</td>
        <td>{{ number_format($invoice->amount, 2) }}</td>
      </tr>
      <tr>
        <td class="label">Discount:</td>
        <td>0.00</td>
      </tr>
      <tr>
        <td class="label total">Total:</td>
        <td class="total">{{ number_format($invoice->amount, 2) }}</td>
      </tr>
    </table>

    <div class="payment-box">
      <h3>Payment Details</h3>
      <p><strong>Payment Method:</strong> {{ $invoice->payment_method ?? 'N/A' }}</p>
      <p><strong>Transaction ID:</strong> {{ $invoice->transaction_id ?? 'N/A' }}</p>
      <p><strong>Paid On:</strong> {{ $invoice->paid_date ?? 'N/A' }}</p>
      <p><strong>Payment Status:</strong> {{ ucfirst($invoice->status) }}</p>
    </div>
  </div>
</body>
</html>
