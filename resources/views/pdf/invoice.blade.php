<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Invoice {{ $invoice['invoice_number'] }}</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f6f8;
      color: #333;
      font-size: 12px;
    }
    .invoice-box {
      max-width: 700px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
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
        <p><strong>Invoice #:</strong> {{ $invoice['invoice_number'] }}</p>
        <p><strong>Date:</strong> {{ $invoice['issue_date'] }}</p>
        <p><strong>Due Date:</strong> {{ $invoice['due_date'] }}</p>
      </div>
      <div style="text-align:right;">
        <p><strong>Status:</strong> <span class="status {{ strtolower($invoice['status']) }}">{{ ucfirst($invoice['status']) }}</span></p>
        <p><strong>Month:</strong> {{ $invoice['rent_month'] ?? 'N/A' }}</p>
        <p><strong>Unit:</strong> {{ $unit['name'] ?? '' }}</p>
      </div>
    </div>

    <div class="invoice-details">
      <div>
        <p><strong>Tenant:</strong> {{ $tenant['name'] ?? '' }}</p>
        <p><strong>Property:</strong> {{ $property['name'] ?? 'N/A' }}</p>
        <p><strong>Address:</strong> {{ $property['address'] ?? 'N/A' }}</p>
        <p><strong>Mobile:</strong> {{ $tenant['phone'] ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $tenant['email'] ?? 'N/A' }}</p>
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
        @if(!empty($invoice['breakdown']))
            @foreach($invoice['breakdown'] as $item)
                <tr>
                    <td>{{ $item['name'] ?? $item['label'] ?? 'N/A' }}</td>
                    <td>{{ number_format($item['amount'] ?? 0, 2) }}</td>
                </tr>
            @endforeach
        @endif
      </tbody>
    </table>

    <table class="totals">
      <tr>
        <td class="label">Sub Total:</td>
        <td>{{ number_format($invoice['amount'], 2) }}</td>
      </tr>
      <tr>
        <td class="label">Discount:</td>
        <td>0.00</td>
      </tr>
      <tr>
        <td class="label total">Total Amount:</td>
        <td class="total">{{ number_format($invoice['amount'], 2) }}</td>
      </tr>
      <tr>
        <td class="label">Paid Amount:</td>
        <td>{{ number_format($invoice['paid_amount'], 2) }}</td>
      </tr>
      <tr>
        <td class="label total">Remaining Amount:</td>
        <td class="total">{{ number_format($invoice['remaining_amount'], 2) }}</td>
      </tr>
    </table>

    <div class="payment-box">
      <h3>Payment Details</h3>
      <p><strong>Total Amount:</strong> ৳{{ number_format($invoice['amount'], 2) }}</p>
      <p><strong>Paid Amount:</strong> ৳{{ number_format($invoice['paid_amount'], 2) }}</p>
      <p><strong>Remaining Amount:</strong> ৳{{ number_format($invoice['remaining_amount'], 2) }}</p>
      <p><strong>Payment Status:</strong> {{ ucfirst($invoice['status']) }}</p>
      @if(isset($owner) && $owner)
        <p><strong>Generated by:</strong> {{ $owner['name'] ?? 'N/A' }}</p>
        <p><strong>Contact:</strong> {{ $owner['phone'] ?? 'N/A' }} | {{ $owner['email'] ?? 'N/A' }}</p>
      @endif
    </div>

    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 10px; border-top: 1px solid #ddd; padding-top: 15px;">
      <p>This is a computer generated invoice. No signature required.</p>
      <p>Generated on: {{ $generated_at ?? now()->format('Y-m-d H:i:s') }}</p>
    </div>
  </div>
</body>
</html>
