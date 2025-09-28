@extends('layouts.owner')

@section('title', 'Payment Success')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>

                    <h2 class="text-success mb-3">Payment Successful!</h2>
                    <p class="text-muted mb-4">Your payment has been processed successfully.</p>

                    <div class="alert alert-success">
                        <h5>Payment Details:</h5>
                        <ul class="list-unstyled">
                            <li><strong>Status:</strong> <span class="badge bg-success">Paid</span></li>
                            <li><strong>Date:</strong> {{ now()->format('M d, Y H:i') }}</li>
                            <li><strong>Transaction ID:</strong> {{ session('transaction_id', 'N/A') }}</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('owner.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to Dashboard
                        </a>
                        @if(session('billing_id'))
                        <a href="{{ route('owner.subscription.invoice.download', session('billing_id')) }}" class="btn btn-success ml-2">
                            <i class="fas fa-download"></i> Download Invoice PDF
                        </a>
                        @endif
                        <a href="{{ route('owner.subscription.billing') }}" class="btn btn-outline-secondary ml-2">
                            <i class="fas fa-file-invoice"></i> View Billing History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
