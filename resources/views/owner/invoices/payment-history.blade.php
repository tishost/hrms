@extends('layouts.owner')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Payment History - Invoice #{{ $invoice->invoice_number ?? $invoice->id }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('owner.invoices.show', $invoice->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoice
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Invoice Details</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Invoice #:</strong></td>
                                            <td>{{ $invoice->invoice_number ?? $invoice->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tenant:</strong></td>
                                            <td>{{ $invoice->tenant ? ($invoice->tenant->first_name . ' ' . $invoice->tenant->last_name) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Property:</strong></td>
                                            <td>{{ $invoice->unit && $invoice->unit->property ? $invoice->unit->property->name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Unit:</strong></td>
                                            <td>{{ $invoice->unit ? $invoice->unit->name : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Payment Summary</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td class="text-primary fw-bold">৳{{ number_format($invoice->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paid Amount:</strong></td>
                                            <td class="text-success fw-bold">৳{{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining:</strong></td>
                                            <td class="text-danger fw-bold">৳{{ number_format($invoice->amount - ($invoice->paid_amount ?? 0), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if(strtolower($invoice->status) === 'paid')
                                                    <span class="text-success fw-medium">Paid</span>
                                                @elseif(strtolower($invoice->status) === 'partial')
                                                    <span class="text-warning fw-medium">Partial</span>
                                                @else
                                                    <span class="text-danger fw-medium">Unpaid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list"></i> Payment History
                                <span class="badge badge-primary ms-2">{{ $paymentHistory->count() }} transactions</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($paymentHistory->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction Type</th>
                                                <th>Description</th>
                                                <th>Payment Method</th>
                                                <th>Transaction ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($paymentHistory as $payment)
                                            <tr>
                                                <td>
                                                    <strong>{{ \Carbon\Carbon::parse($payment->transaction_date)->format('M d, Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($payment->created_at)->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ ucwords(str_replace('_', ' ', $payment->transaction_type)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $payment->description }}</td>
                                                <td>
                                                    @if($payment->payment_method)
                                                        @php
                                                            $method = str_replace('_', ' ', $payment->payment_method);
                                                        @endphp
                                                        <span class="text-primary fw-medium">{{ ucwords($method) }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->payment_reference)
                                                        <span class="text-info fw-medium">{{ $payment->payment_reference }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->credit_amount > 0)
                                                        <span class="text-success fw-bold">+৳{{ number_format($payment->credit_amount, 2) }}</span>
                                                    @elseif($payment->debit_amount > 0)
                                                        <span class="text-danger fw-bold">-৳{{ number_format($payment->debit_amount, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">৳0.00</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->payment_status === 'completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @elseif($payment->payment_status === 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($payment->payment_status === 'failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($payment->payment_status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->notes)
                                                        <span class="text-muted">{{ Str::limit($payment->notes, 50) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Payment History Found</h5>
                                    <p class="text-muted">No payment transactions have been recorded for this invoice yet.</p>
                                    <a href="{{ route('owner.invoices.payment', $invoice->id) }}" class="btn btn-success">
                                        <i class="fas fa-money-bill-wave"></i> Collect Payment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
