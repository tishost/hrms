@extends('layouts.owner')

@section('title', 'Collect Payment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i> Collect Payment
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
                                            <td><strong>Already Paid:</strong></td>
                                            <td class="text-success">৳{{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
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

                    <!-- Payment Form -->
                    <form action="{{ route('owner.invoices.payment.process', $invoice->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Payment Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="paid_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number" 
                                                           class="form-control @error('paid_amount') is-invalid @enderror" 
                                                           id="paid_amount" 
                                                           name="paid_amount" 
                                                           value="{{ old('paid_amount', $invoice->amount - ($invoice->paid_amount ?? 0)) }}"
                                                           min="0.01" 
                                                           max="{{ $invoice->amount - ($invoice->paid_amount ?? 0) }}"
                                                           step="0.01" 
                                                           required>
                                                </div>
                                                @error('paid_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Maximum: ৳{{ number_format($invoice->amount - ($invoice->paid_amount ?? 0), 2) }}
                                                </small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                       class="form-control @error('payment_date') is-invalid @enderror" 
                                                       id="payment_date" 
                                                       name="payment_date" 
                                                       value="{{ old('payment_date', date('Y-m-d')) }}"
                                                       max="{{ date('Y-m-d') }}"
                                                       required>
                                                @error('payment_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                        id="payment_method" 
                                                        name="payment_method" 
                                                        required>
                                                    <option value="">Select Payment Method</option>
                                                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>
                                                        <i class="fas fa-money-bill"></i> Cash
                                                    </option>
                                                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>
                                                        <i class="fas fa-credit-card"></i> Card
                                                    </option>
                                                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                                                        <i class="fas fa-university"></i> Bank Transfer
                                                    </option>
                                                    <option value="mobile_banking" {{ old('payment_method') === 'mobile_banking' ? 'selected' : '' }}>
                                                        <i class="fas fa-mobile-alt"></i> Mobile Banking
                                                    </option>
                                                </select>
                                                @error('payment_method')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                                <input type="text" 
                                                       class="form-control @error('transaction_id') is-invalid @enderror" 
                                                       id="transaction_id" 
                                                       name="transaction_id" 
                                                       value="{{ old('transaction_id') }}"
                                                       placeholder="Enter transaction ID (optional)">
                                                @error('transaction_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Required for card, bank transfer, and mobile banking payments
                                                </small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="notes" class="form-label">Notes</label>
                                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                          id="notes" 
                                                          name="notes" 
                                                          rows="3" 
                                                          placeholder="Optional payment notes...">{{ old('notes') }}</textarea>
                                                @error('notes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Payment Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="payment-summary">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total Amount:</span>
                                                <span class="fw-bold">৳{{ number_format($invoice->amount, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Already Paid:</span>
                                                <span class="text-success">৳{{ number_format($invoice->paid_amount ?? 0, 2) }}</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Remaining:</span>
                                                <span class="text-danger fw-bold">৳{{ number_format($invoice->amount - ($invoice->paid_amount ?? 0), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Payment Amount:</span>
                                                <span class="text-primary fw-bold" id="payment-amount-display">৳0.00</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <span>After Payment:</span>
                                                <span class="fw-bold" id="remaining-after-payment">৳{{ number_format($invoice->amount - ($invoice->paid_amount ?? 0), 2) }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-check"></i> Paid
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paidAmountInput = document.getElementById('paid_amount');
    const paymentAmountDisplay = document.getElementById('payment-amount-display');
    const remainingAfterPayment = document.getElementById('remaining-after-payment');
    const totalAmount = {{ $invoice->amount }};
    const alreadyPaid = {{ $invoice->paid_amount ?? 0 }};
    const remainingAmount = totalAmount - alreadyPaid;

    function updatePaymentSummary() {
        const paymentAmount = parseFloat(paidAmountInput.value) || 0;
        const newRemaining = remainingAmount - paymentAmount;
        
        paymentAmountDisplay.textContent = '৳' + paymentAmount.toFixed(2);
        remainingAfterPayment.textContent = '৳' + Math.max(0, newRemaining).toFixed(2);
        
        if (newRemaining <= 0) {
            remainingAfterPayment.className = 'fw-bold text-success';
        } else {
            remainingAfterPayment.className = 'fw-bold text-warning';
        }
    }

    paidAmountInput.addEventListener('input', updatePaymentSummary);
    updatePaymentSummary();
});
</script>
@endsection
