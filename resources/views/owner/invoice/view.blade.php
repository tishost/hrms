@extends('layouts.owner')

@section('title', 'Invoice Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-file-invoice"></i> Invoice Details
                    </h4>
                </div>
                <div class="card-body">
                    @if($invoice)
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Invoice Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Invoice #:</strong></td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Due Date:</strong></td>
                                        <td>{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($invoice->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($invoice->status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($invoice->status == 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td>৳{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    @if($invoice->transaction_fee > 0)
                                    <tr>
                                        <td><strong>Transaction Fee:</strong></td>
                                        <td>৳{{ number_format($invoice->transaction_fee, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->net_amount)
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td>৳{{ number_format($invoice->net_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->paymentMethod)
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td>{{ $invoice->paymentMethod->name }}</td>
                                    </tr>
                                    @endif
                                    @if($invoice->paid_date)
                                    <tr>
                                        <td><strong>Paid Date:</strong></td>
                                        <td>{{ $invoice->paid_date->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($invoice->subscription && $invoice->subscription->plan)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Subscription Details</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Plan:</strong></td>
                                        <td>{{ $invoice->subscription->plan->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Plan Price:</strong></td>
                                        <td>৳{{ number_format($invoice->subscription->plan->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subscription Status:</strong></td>
                                        <td>
                                            @if($invoice->subscription->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($invoice->subscription->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($invoice->subscription->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <a href="{{ route('owner.subscription.billing') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Billing
                                </a>
                                <a href="{{ route('owner.invoice.download', $invoice->id) }}" class="btn btn-primary ml-2">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>Invoice Not Found</h5>
                            <p class="text-muted">The requested invoice could not be found.</p>
                            <a href="{{ route('owner.subscription.billing') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Billing
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
