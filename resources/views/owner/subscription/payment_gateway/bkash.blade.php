@extends('layouts.owner')

@section('title', 'bKash TokenizedCheckout Payment')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">
        <div class="content-card">
            <div class="card-header">
                <h4 class="card-title">💳 bKash TokenizedCheckout Payment</h4>
            </div>
            <div class="card-body">
                <div class="payment-gateway-info">
                    <div class="gateway-details">
                        <h5>Payment Details</h5>
                        <div class="detail-item">
                            <span class="label">Amount:</span>
                            <span class="value">৳{{ number_format($paymentData['amount'], 2) }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Invoice #:</span>
                            <span class="value">{{ $paymentData['invoice_number'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Payment ID:</span>
                            <span class="value">{{ $paymentData['paymentID'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Description:</span>
                            <span class="value">{{ $paymentData['description'] }}</span>
                        </div>
                    </div>

                    <div class="gateway-instructions">
                        <h5>Complete Your bKash Payment</h5>

                        @if(isset($paymentData['manual_payment']) && $paymentData['manual_payment'])
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Manual Payment Required:</strong> {{ $paymentData['error_message'] ?? 'Unknown error' }}
                                <br><strong>Suggestion:</strong> {{ $paymentData['suggestion'] ?? 'Please use manual payment method' }}
                            </div>
                        @elseif(isset($paymentData['demo_mode']) && $paymentData['demo_mode'])
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Demo Mode:</strong> This is a test payment. No real money will be charged.
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Payment ID:</strong> {{ $paymentData['paymentID'] }}
                        </div>

                        <div class="payment-options">
                            @if($paymentData['bkashURL'] && $paymentData['bkashURL'] !== '#' && $paymentData['bkashURL'] !== 'null')
                                <div class="option-card">
                                    <h6><i class="fas fa-globe"></i> Direct bKash Payment</h6>
                                    <p>Click the button below to complete payment directly through bKash:</p>
                                    <a href="{{ $paymentData['bkashURL'] }}" class="btn btn-primary btn-lg" id="bkash-pay-button">
                                        <i class="fas fa-credit-card"></i> Pay with bKash
                                    </a>
                                    <small class="form-text text-muted">This will open bKash payment page in the same tab</small>
                                </div>
                            @else
                                <div class="option-card">
                                    <h6><i class="fas fa-mobile-alt"></i> Manual bKash Payment</h6>
                                    <ol>
                                        <li>Open your bKash app</li>
                                        <li>Go to "Send Money"</li>
                                        <li>Enter the bKash number: <strong>01XXXXXXXXX</strong></li>
                                        <li>Enter amount: <strong>৳{{ number_format($paymentData['amount'], 2) }}</strong></li>
                                        <li>Add reference: <strong>{{ $paymentData['paymentID'] }}</strong></li>
                                        <li>Enter your PIN and send money</li>
                                        <li>Copy the transaction ID from the confirmation message</li>
                                        <li>Click "Complete Payment" below and enter the transaction ID</li>
                                    </ol>
                                </div>

                                <div class="option-card">
                                    <h6><i class="fas fa-info-circle"></i> Payment Information</h6>
                                    <div class="alert alert-info">
                                        <strong>Payment ID:</strong> {{ $paymentData['paymentID'] }}<br>
                                        <strong>Amount:</strong> ৳{{ number_format($paymentData['amount'], 2) }}<br>
                                        <strong>Invoice:</strong> {{ $paymentData['invoice_number'] }}<br>
                                        <strong>Status:</strong> {{ $paymentData['statusMessage'] ?? 'Payment Created' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="payment-form">
                        <h5>Complete Payment</h5>

                        @if(isset($paymentData['manual_payment']) && $paymentData['manual_payment'])
                            <form action="{{ route('owner.subscription.payment.success') }}" method="GET" id="payment-form">
                                <input type="hidden" name="paymentID" value="{{ $paymentData['paymentID'] }}">
                                <input type="hidden" name="payerReference" value="{{ $paymentData['invoice_number'] }}">

                                <div class="form-group">
                                    <label for="trxID">bKash Transaction ID (TrxID)</label>
                                    <input type="text" class="form-control" id="trxID" name="trxID"
                                           placeholder="Enter bKash transaction ID" required>
                                    <small class="form-text text-muted">Enter the transaction ID from your bKash confirmation message</small>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> Complete Payment
                                    </button>
                                    <a href="{{ route('owner.subscription.payment.cancel') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel Payment
                                    </a>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Automatic Payment Verification:</strong> Your payment will be automatically verified once completed through bKash.
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('owner.subscription.payment.success') }}?paymentID={{ $paymentData['paymentID'] }}&payerReference={{ $paymentData['invoice_number'] }}"
                                   class="btn btn-success btn-lg" id="verify-payment-btn">
                                    <i class="fas fa-check"></i> Verify Payment
                                </a>
                                <a href="{{ route('owner.subscription.payment.cancel') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel Payment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-gateway-info {
    max-width: 600px;
    margin: 0 auto;
}

.gateway-details {
    background: var(--light);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-light);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item .label {
    font-weight: 600;
    color: var(--dark);
}

.detail-item .value {
    color: var(--primary);
    font-weight: 600;
}

.gateway-instructions {
    background: var(--white);
    border: 1px solid var(--gray-light);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
}

.gateway-instructions h5 {
    color: var(--primary);
    margin-bottom: 16px;
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.option-card {
    background: var(--light);
    border: 1px solid var(--gray-light);
    border-radius: 8px;
    padding: 20px;
}

.option-card h6 {
    color: var(--primary);
    margin-bottom: 15px;
    font-weight: 600;
}

.option-card ol {
    padding-left: 20px;
}

.option-card li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.option-card strong {
    color: var(--primary);
    background: rgba(67, 97, 238, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

.gateway-instructions ol {
    padding-left: 20px;
}

.gateway-instructions li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.gateway-instructions strong {
    color: var(--primary);
    background: rgba(67, 97, 238, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

.payment-form {
    background: var(--white);
    border: 1px solid var(--gray-light);
    border-radius: 8px;
    padding: 20px;
}

.form-actions {
    margin-top: 20px;
    text-align: center;
}

.form-actions .btn {
    margin: 0 8px;
}

@media (max-width: 768px) {
    .form-actions .btn {
        display: block;
        margin: 8px 0;
        width: 100%;
    }
}
</style>

                <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bkashPayButton = document.getElementById('bkash-pay-button');
            const verifyPaymentBtn = document.getElementById('verify-payment-btn');

            if (bkashPayButton) {
                bkashPayButton.addEventListener('click', function(e) {
                    const url = this.getAttribute('href');

                    if (url && url !== '#' && url !== '{{ $paymentData["bkashURL"] }}') {
                        // Store payment data in localStorage for redirect
                        localStorage.setItem('bkash_payment_id', '{{ $paymentData["paymentID"] }}');
                        localStorage.setItem('bkash_invoice_number', '{{ $paymentData["invoice_number"] }}');
                        localStorage.setItem('bkash_payer_reference', '{{ $paymentData["invoice_number"] }}');

                        // Redirect to bKash payment page in same tab
                        window.location.href = url;
                    } else {
                        e.preventDefault();
                        alert('bKash payment URL is not available. Please use manual payment method.');
                    }
                });
            }

            // Auto-focus on transaction ID input
            const trxIDInput = document.getElementById('trxID');
            if (trxIDInput) {
                trxIDInput.focus();
            }

            // Auto-verify payment button
            if (verifyPaymentBtn) {
                verifyPaymentBtn.addEventListener('click', function(e) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying Payment...';
                    this.disabled = true;
                });
            }

            // Check if returning from bKash payment
            const urlParams = new URLSearchParams(window.location.search);
            const paymentID = urlParams.get('paymentID');
            const status = urlParams.get('status');

            if (paymentID && status === 'success') {
                // Auto-verify payment
                const verifyUrl = '{{ route("owner.subscription.payment.success") }}?paymentID=' + paymentID + '&payerReference={{ $paymentData["invoice_number"] }}';
                window.location.href = verifyUrl;
            }
        });
        </script>
@endsection
