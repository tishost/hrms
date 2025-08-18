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
      font-size: 10px;
      line-height: 1.2;
    }
    @page {
      size: A4;
      margin: 10mm;
    }
    .invoice-container {
      max-width: 100%;
      margin: 0 auto;
      background: #fff;
      border: 1px solid #ddd;
    }
    .header {
      background: #fff;
      color: #333;
      padding: 10px;
      position: relative;
      border-bottom: 1px solid #eee;
    }
    .status-banner {
      position: absolute;
      top: -10px;
      right: -30px;
      background: #dc3545;
      color: white;
      padding: 8px 40px;
      transform: rotate(45deg);
      font-weight: bold;
      font-size: 14px;
      text-transform: uppercase;
      box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .status-banner.paid {
      background: #28a745;
    }
    .status-banner.partial {
      background: #ffc107;
      color: #333;
    }

  .status-badge {
    padding: 4px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 12px;
    display: inline-block;
  }
  .status-badge.paid {
    background: #d4edda;
    color: #155724;
  }
  .status-badge.unpaid {
    background: #f8d7da;
    color: #721c24;
  }
  .status-badge.partial {
    background: #fff3cd;
    color: #856404;
  }
  .company-contact p {
    margin: 2px 0;
    font-size: 13px;
  }
.company-info {
    display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;

      flex-wrap: nowrap;
    }
    .status-text {
    flex: 0 0 auto;
    align-self: flex-end; /* নিচে বসে */
    max-width: 100px;
    margin-top: 35px;
    margin-left: 30px;
    }

    .company-contact {
    flex: 1;
    align-self: flex-start; /* উপরে বসে */
      text-align: right;
      font-size: 11px;
    }

    .status-text .status-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-weight: bold;
      font-size: 12px;
      text-transform: uppercase;
      color: white;
    }
    .status-text .status-badge.paid {
      background: #28a745;
    }
    .status-text .status-badge.unpaid {
      background: #dc3545;
    }
    .status-text .status-badge.partial {
      background: #ffc107;
      color: #333;
    }

    .company-contact p {
      margin: 2px 0;
    }
    .invoice-info {
      padding: 10px;
      background: #fff;
    }
    .invoice-details {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 10px;
    }
    .invoice-details > div {
      flex: 1;
      min-width: 200px;
      margin: 5px;
    }
    .invoice-details h3 {
      margin: 0 0 5px 0;
      color: #333;
      font-size: 12px;
      font-weight: bold;
    }
    .invoice-details p {
      margin: 3px 0;
      font-size: 10px;
    }
    .content {
      padding: 10px;
    }


    .table-container {
      overflow-x: auto;
      width: 100%;
    }
    table {
      min-width: 400px;
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #ccc;
      font-size: 13px;
      overflow-x: auto;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      vertical-align: top;
    }
    th.description, td.description {
      text-align: left;
      width: 70%;
    }
    th.total, td.total {
      text-align: right;
      width: 30%;
      white-space: nowrap;
    }
    th {
      background-color: #eee;
      text-align: center;
    }
    td:last-child, th:last-child {
      text-align: center;
    }
    tr.summary {
      background-color: #eee;
    }


    .summary-totals {
      text-align: right;
      margin: 2px 0;
    }
    .summary-totals p {
      margin: 2px 0;
      font-size: 11px;
    }
    .summary-totals .total {
      font-weight: bold;
      font-size: 10px;
      margin-top: 3px;
    }
    .transactions {
      margin: 3px 0;
    }
    .transactions h3 {
      margin: 0 0 5px 0;
      color: #333;
      font-size: 12px;
      font-weight: bold;
    }
    .transaction-table {
      width: 100%;
      border-collapse: collapse;
    }
    .transaction-table th {
      background: #f8f9fa;
      padding: 8px;
      text-align: left;
      font-weight: bold;
      font-size: 11px;
      border-bottom: 1px solid #ddd;
    }
    .transaction-table td {
      padding: 8px;
      border-bottom: 1px solid #eee;
      font-size: 11px;
    }
    .footer {
      text-align: center;
      padding: 20px;
      color: #666;
      font-size: 10px;
      border-top: 1px solid #eee;
    }
  </style>
</head>
<body>
  <div class="invoice-container">
    <div class="header">
        <div class="company-info">
            <div class="status-text">
                <span class="status-badge {{ strtolower($invoice->status) }}">
                {{ strtoupper($invoice->status) }}
                </span>
            </div>
            <div class="company-contact">
                <p><strong>{{ $invoice->property->name ?? 'N/A' }}</strong></p>
                <p>{{ $invoice->property->address ?? 'N/A' }}</p>
                <p>Email: {{ $invoice->property->email ?? 'sales@samitpark.com' }}</p>
                <p>Mobile: {{ $invoice->property->mobile ?? '9611 677170' }}</p>
            </div>
    </div>
    </div>

    <div class="invoice-info">
      <div class="invoice-details">
        <div>
          <h3>Invoice Information</h3>
          <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
          <p><strong>Invoice Date:</strong> {{ $invoice->issue_date }}</p>
          <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
        </div>
        <div>
          <h3>Invoiced To</h3>
          <p> {{ $invoice->tenant->first_name ?? '' }} {{ $invoice->tenant->last_name ?? '' }}</p>
          <p> {{ $invoice->tenant->mobile ?? 'N/A' }}</p>
          <p> {{ $invoice->tenant->address ?? 'N/A' }}</p>
          <p> {{ $invoice->tenant->upazila ?? 'N/A' }}</p>
          <p>{{ $invoice->tenant->district ?? 'N/A' }}, {{ $invoice->tenant->zip ?? 'N/A' }}</p>
          <p>{{ $invoice->tenant->country ?? 'N/A' }}</p>
        </div>
        <div>
          <h3>Property Details</h3>
          <p> {{ $invoice->property->name ?? 'N/A' }}</p>
          <p>{{ $invoice->unit->name ?? 'N/A' }}</p>
        </div>
      </div>
    </div>

    <div class="table-container">
        <table>
        <thead>
            <tr>
            <th >Description</th>
            <th >Total</th>
            </tr>
        </thead>
        <tbody>
         @php
            $breakdown = $invoice->breakdown ? json_decode($invoice->breakdown, true) : [];
            $hasBreakdown = !empty($breakdown) && is_array($breakdown) && count($breakdown) > 0;
          @endphp

          @if($hasBreakdown)
            @foreach($breakdown as $item)
              <tr>
                <td class="description">
                  <strong>{{ $item['name'] ?? 'N/A' }}</strong>
                </td>
                <td class="total">{{ number_format($item['amount'] ?? 0, 2) }}BDT</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td>
                <strong>Rent Payment</strong>
              </td>
              <td>{{ number_format($invoice->amount, 2) }}BDT</td>
            </tr>
          @endif


        <tr class="summary">
          <td style="text-align: right;"><strong>Sub Total</strong></td>
          <td>{{ number_format($invoice->amount, 2) }}BDT</td>
        </tr>
        <tr class="summary">
          <td style="text-align: right;"><strong>Discount</strong></td>
          <td>0.00BDT</td>
        </tr>
        <tr class="summary">
          <td style="text-align: right;"><strong>Total</strong></td>
          <td><strong>{{ number_format($invoice->amount, 2) }}BDT</strong></td>
        </tr>
      </tbody>
    </table>
  </div>


      <!-- Transactions Section -->
      <div class="transactions">
        <h3>Transactions</h3>
        <table class="transaction-table">
          <thead>
            <tr>
              <th>Transaction Date</th>
              <th>Gateway</th>
              <th>Transaction ID</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @php
              $txnDate = isset($lastPayment) ? ($lastPayment->payment_date ? $lastPayment->payment_date->format('Y-m-d') : null) : ($invoice->paid_date ?? null);
              $gateway = isset($lastPayment) ? ($lastPayment->payment_method ?? null) : ($invoice->payment_method ?? null);
              $txnId = isset($lastPayment) ? ($lastPayment->reference_number ?? null) : ($invoice->transaction_id ?? null);
              $paidAmt = isset($lastPayment) ? ($lastPayment->amount_paid ?? $lastPayment->amount ?? null) : ($invoice->paid_amount ?? null);
            @endphp
            @if(($invoice->status === 'paid') || ($paidAmt && $paidAmt > 0))
              <tr>
                <td>{{ $txnDate ?? 'N/A' }}</td>
                <td>{{ $gateway ?? 'N/A' }}</td>
                <td>{{ $txnId ?? 'N/A' }}</td>
                <td>{{ number_format($paidAmt ?? 0, 2) }}BDT</td>
              </tr>
            @else
              <tr>
                <td colspan="4" style="text-align: center; color: #666;">No Related Transactions Found</td>
              </tr>
            @endif
          </tbody>
        </table>

        <div class="summary-totals">
          <p><strong>Balance:</strong> {{ number_format(($invoice->amount - ($invoice->paid_amount ?? 0)), 2) }}BDT</p>
        </div>
      </div>
    </div>

    <div class="footer">
      <p>PDF Generated on {{ now()->format('d/m/Y') }}</p>
    </div>
  </div>
</body>
</html>
