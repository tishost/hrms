@extends('layouts.owner')

@section('title', 'Complete Payment')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">
        <div class="content-card">
            <div class="card-header">
                <h4 class="card-title">💸 Complete Your Payment</h4>
            </div>
            <div class="card-body">
                @if($pendingInvoice)
                    <div class="payment-info">
                        <div class="invoice-details">
                            <h5>Invoice Details</h5>
                            <div class="invoice-item">
                                <span class="label">Invoice #:</span>
                                <span class="value">{{ $pendingInvoice->invoice_number }}</span>
                            </div>
                            <div class="invoice-item">
                                <span class="label">Plan:</span>
                                <span class="value">{{ $pendingInvoice->subscription->plan->name }}</span>
                            </div>
                            <div class="invoice-item">
                                <span class="label">Amount:</span>
                                <span class="value">৳{{ number_format($pendingInvoice->amount, 2) }}</span>
                            </div>
                            <div class="invoice-item">
                                <span class="label">Due Date:</span>
                                <span class="value">{{ $pendingInvoice->due_date->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="payment-methods">
                            <h5>Select Payment Method</h5>
                            <div class="form-group">
                                <label for="payment_method" class="form-label">Select your preferred payment method</label>
                                <select class="form-control" id="payment_method" name="payment_method_id">
                                    <option value="">Choose a payment method</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}" data-fee="{{ $method->transaction_fee }}">
                                            {{ $method->name }} (Fee: {{ $method->transaction_fee }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="payment-summary">
                            <h5>Payment Summary</h5>
                            <div class="summary-item">
                                <span>Invoice Amount:</span>
                                <span>৳{{ number_format($pendingInvoice->amount, 2) }}</span>
                            </div>
                            <div class="summary-item" id="fee-display">
                                <span>Transaction Fee:</span>
                                <span>৳0.00</span>
                            </div>
                            <div class="summary-item total">
                                <span>Total Amount:</span>
                                <span id="total-amount">৳{{ number_format($pendingInvoice->amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>No Pending Payments</h4>
                        <p class="text-muted">You currently do not have any pending invoices.</p>
                        <a href="{{ route('owner.subscription.plans') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> Browse Plans
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Actions Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                @if($pendingInvoice)
                    <form action="{{ route('owner.subscription.initiate-gateway', ['invoice_id' => $pendingInvoice->id]) }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $pendingInvoice->id }}">
                        <input type="hidden" name="payment_method_id" id="selected_method">

                        <button type="submit" class="btn btn-primary btn-lg" id="pay-button">
                            <i class="fas fa-credit-card"></i> Pay Now
                        </button>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('owner.subscription.billing') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Billing
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>No Invoice Found:</strong> Please contact support if you believe this is an error.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.payment-info {
    max-width: 800px;
    margin: 0 auto;
}

.invoice-details {
    background: var(--light);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.invoice-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-light);
}

.invoice-item:last-child {
    border-bottom: none;
}

.invoice-item .label {
    font-weight: 600;
    color: var(--dark);
}

.invoice-item .value {
    color: var(--primary);
    font-weight: 600;
}

.payment-methods {
    margin-bottom: 24px;
}

.payment-methods .form-group {
    margin-bottom: 0;
}

.payment-methods .form-control {
    border: 2px solid var(--gray-light);
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--white);
}

.payment-methods .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.payment-methods .form-control option {
    padding: 8px;
    font-size: 0.95rem;
}

.method-icon {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 12px;
}

.method-info h6 {
    margin: 0 0 4px 0;
    font-weight: 600;
}

.method-info p {
    margin: 0 0 8px 0;
    color: var(--gray);
    font-size: 0.875rem;
}

.method-info .fee {
    color: var(--success);
    font-weight: 600;
}

.method-radio {
    position: absolute;
    top: 16px;
    right: 16px;
}

.method-radio input[type="radio"] {
    display: none;
}

.method-radio label {
    width: 20px;
    height: 20px;
    border: 2px solid var(--gray-light);
    border-radius: 50%;
    display: block;
    cursor: pointer;
    transition: all 0.2s ease;
}

.method-radio input[type="radio"]:checked + label {
    border-color: var(--primary);
    background: var(--primary);
    position: relative;
}

.method-radio input[type="radio"]:checked + label::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
}

.payment-summary {
    background: var(--white);
    border: 1px solid var(--gray-light);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-light);
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item.total {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--primary);
}

.payment-actions {
    text-align: center;
}

.payment-actions .btn {
    margin: 0 8px;
}

@media (max-width: 768px) {
    .methods-grid {
        grid-template-columns: 1fr;
    }

    .payment-actions .btn {
        display: block;
        margin: 8px 0;
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentForm = document.getElementById('payment-form');
    const selectedMethodInput = document.getElementById('selected_method');
    const payButton = document.getElementById('pay-button');
    const feeDisplay = document.getElementById('fee-display');
    const totalAmount = document.getElementById('total-amount');

    const invoiceAmount = {{ $pendingInvoice ? $pendingInvoice->amount : 0 }};

    // Update payment method selection
    paymentMethodSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const transactionFeePercent = parseFloat(selectedOption.getAttribute('data-fee') || 0);
        const selectedMethodInput = document.getElementById('selected_method');
        const payButton = document.getElementById('pay-button');

        // Set the payment method ID in the form
        selectedMethodInput.value = this.value;

        // Calculate and display fees
        const invoiceAmount = {{ $pendingInvoice ? $pendingInvoice->amount : 0 }};
        const transactionFeeAmount = invoiceAmount * (transactionFeePercent / 100);
        const totalAmount = invoiceAmount + transactionFeeAmount;

        document.getElementById('fee-display').textContent = '৳' + transactionFeeAmount.toFixed(2);
        document.getElementById('total-amount').textContent = '৳' + totalAmount.toFixed(2);

        // Enable/disable pay button based on selection
        if (this.value && this.value !== '') {
            payButton.disabled = false;
            payButton.classList.remove('btn-secondary');
            payButton.classList.add('btn-primary');
            payButton.innerHTML = '<i class="fas fa-credit-card"></i> Pay Now';
        } else {
            payButton.disabled = true;
            payButton.classList.remove('btn-primary');
            payButton.classList.add('btn-secondary');
            payButton.innerHTML = '<i class="fas fa-credit-card"></i> Select Payment Method';
        }
    });

    // Form submission
    paymentForm.addEventListener('submit', function(e) {
        if (!selectedMethodInput.value) {
            e.preventDefault();
            alert('Please select a payment method.');
            return;
        }

        // Show loading state
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        payButton.disabled = true;
    });
});
</script>
@endsection
