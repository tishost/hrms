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
                            <div class="methods-grid">
                                @foreach($paymentMethods as $method)
                                    <div class="method-card" data-method-id="{{ $method->id }}" data-fee="{{ $method->transaction_fee }}">
                                        <div class="method-icon">
                                            <i class="fas fa-{{ $method->code === 'bkash' ? 'mobile-alt' : ($method->code === 'nagad' ? 'wallet' : ($method->code === 'rocket' ? 'rocket' : 'university')) }}"></i>
                                        </div>
                                        <div class="method-info">
                                            <h6>{{ $method->name }}</h6>
                                            <p>{{ $method->description }}</p>
                                            @if($method->transaction_fee > 0)
                                                <small class="fee">Fee: {{ $method->formatted_fee }}</small>
                                            @else
                                                <small class="fee">No Fee</small>
                                            @endif
                                        </div>
                                        <div class="method-radio">
                                            <input type="radio" name="payment_method_id" value="{{ $method->id }}" id="method_{{ $method->id }}">
                                            <label for="method_{{ $method->id }}"></label>
                                        </div>
                                    </div>
                                @endforeach
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

                        <div class="payment-actions">
                            <form action="{{ route('owner.subscription.initiate-gateway') }}" method="POST" id="payment-form">
                                @csrf
                                <input type="hidden" name="invoice_id" value="{{ $pendingInvoice->id }}">
                                <input type="hidden" name="payment_method_id" id="selected_method">

                                <button type="submit" class="btn btn-primary btn-lg" id="pay-button" disabled>
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </button>
                            </form>

                            <a href="{{ route('owner.subscription.billing') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Billing
                            </a>
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

.methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-top: 16px;
}

.method-card {
    border: 2px solid var(--gray-light);
    border-radius: 8px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.method-card:hover {
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.method-card.selected {
    border-color: var(--primary);
    background: rgba(67, 97, 238, 0.05);
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
    const methodCards = document.querySelectorAll('.method-card');
    const paymentForm = document.getElementById('payment-form');
    const selectedMethodInput = document.getElementById('selected_method');
    const payButton = document.getElementById('pay-button');
    const feeDisplay = document.getElementById('fee-display');
    const totalAmount = document.getElementById('total-amount');

    const invoiceAmount = {{ $pendingInvoice ? $pendingInvoice->amount : 0 }};

    methodCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            methodCards.forEach(c => c.classList.remove('selected'));

            // Add selected class to clicked card
            this.classList.add('selected');

            // Get method data
            const methodId = this.dataset.methodId;
            const fee = parseFloat(this.dataset.fee) || 0;

            // Update form
            selectedMethodInput.value = methodId;

            // Calculate and display fees
            const feeAmount = (invoiceAmount * fee) / 100;
            const total = invoiceAmount + feeAmount;

            feeDisplay.querySelector('span:last-child').textContent = '৳' + feeAmount.toFixed(2);
            totalAmount.textContent = '৳' + total.toFixed(2);

            // Enable pay button
            payButton.disabled = false;
        });
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
