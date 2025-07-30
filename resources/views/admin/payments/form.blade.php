@extends('layouts.admin')

@section('title', 'Payment Form - Invoice #' . $billing->invoice_number)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payment Form</h1>
        <a href="{{ route('admin.billing.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Billing
        </a>
    </div>

    <div class="row">
        <!-- Payment Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payments.process', $billing->id) }}" method="POST">
                        @csrf

                        <!-- Invoice Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Invoice Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Invoice #:</strong></td>
                                        <td>{{ $billing->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Owner:</strong></td>
                                        <td>{{ $billing->owner->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Plan:</strong></td>
                                        <td>{{ $billing->subscription->plan->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td class="text-success font-weight-bold">{{ $billing->formatted_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Due Date:</strong></td>
                                        <td>{{ $billing->due_date->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="form-group">
                            <label for="payment_method_id" class="form-label">
                                <i class="fas fa-credit-card me-2"></i>Payment Method
                            </label>
                            <select class="form-control @error('payment_method_id') is-invalid @enderror"
                                    id="payment_method_id" name="payment_method_id" required>
                                <option value="">Select Payment Method</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}"
                                            data-fee="{{ $method->transaction_fee }}"
                                            {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                        {{ $method->name }}
                                        @if($method->transaction_fee > 0)
                                            ({{ $method->formatted_fee }})
                                        @else
                                            (No Fee)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transaction Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_id" class="form-label">
                                        <i class="fas fa-receipt me-2"></i>Transaction ID
                                    </label>
                                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                                           id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}"
                                           placeholder="Enter transaction ID" required>
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_date" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Payment Date
                                    </label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                           id="payment_date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required>
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fee Calculation -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-primary">Fee Calculation</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Invoice Amount:</td>
                                                <td class="text-right">{{ $billing->formatted_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td>Transaction Fee:</td>
                                                <td class="text-right" id="transaction-fee">৳0.00</td>
                                            </tr>
                                            <tr class="font-weight-bold">
                                                <td>Total Amount:</td>
                                                <td class="text-right text-success" id="total-amount">{{ $billing->formatted_amount }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Process Payment
                            </button>
                            <a href="{{ route('admin.payments.mark-paid', $billing->id) }}"
                               class="btn btn-primary"
                               onclick="return confirm('Mark this invoice as paid without payment details?')">
                                <i class="fas fa-check-double"></i> Mark as Paid
                            </a>
                            <a href="{{ route('admin.billing.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment Methods Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Payment Methods</h6>
                </div>
                <div class="card-body">
                    @foreach($paymentMethods as $method)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-credit-card fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold mb-1">{{ $method->name }}</h6>
                                <p class="text-muted small mb-1">{{ $method->description }}</p>
                                @if($method->transaction_fee > 0)
                                    <span class="badge badge-info">{{ $method->formatted_fee }} fee</span>
                                @else
                                    <span class="badge badge-success">No fee</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method_id');
    const transactionFee = document.getElementById('transaction-fee');
    const totalAmount = document.getElementById('total-amount');
    const invoiceAmount = {{ $billing->amount }};

    paymentMethodSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const feePercentage = parseFloat(selectedOption.dataset.fee) || 0;
        const feeAmount = (invoiceAmount * feePercentage) / 100;
        const total = invoiceAmount + feeAmount;

        transactionFee.textContent = '৳' + feeAmount.toFixed(2);
        totalAmount.textContent = '৳' + total.toFixed(2);
    });
});
</script>
@endsection
