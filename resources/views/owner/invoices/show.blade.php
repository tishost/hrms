@extends('layouts.owner')

@section('title', 'Invoice Details')

@section('content')
<style>
.invoice-details-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.invoice-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    border: none;
}

.invoice-header .card-header {
    background: transparent;
    border: none;
    padding: 25px 30px 15px;
}

.invoice-title {
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.invoice-title i {
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 32px;
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-modern {
    border-radius: 12px;
    padding: 12px 24px;
    font-weight: 600;
    text-transform: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: none;
}

.btn-modern.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
    border-radius: 8px;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.btn-info-modern {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-success-modern {
    background: linear-gradient(135deg, #48bb78, #38a169);
    color: white;
}

.btn-secondary-modern {
    background: linear-gradient(135deg, #a0aec0, #718096);
    color: white;
}

.info-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 20px;
    overflow: hidden;
}

.info-card .card-header {
    background: linear-gradient(135deg, #f7fafc, #edf2f7);
    border: none;
    padding: 20px 25px 15px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.info-card .card-title {
    font-size: 18px;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-card .card-title i {
    color: #667eea;
    font-size: 20px;
}

.info-card .card-body {
    padding: 25px;
}

.compact-table {
    margin: 0;
}

.compact-table td {
    padding: 12px 0;
    border: none;
    vertical-align: middle;
}

.compact-table td:first-child {
    font-weight: 600;
    color: #4a5568;
    width: 40%;
    font-size: 14px;
    padding-left: 20px;
}

.compact-table td:last-child {
    color: #2d3748;
    font-weight: 500;
    font-size: 14px;
}

.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-paid {
    background: linear-gradient(135deg, #48bb78, #38a169);
    color: white;
}

.status-partial {
    background: linear-gradient(135deg, #ed8936, #dd6b20);
    color: white;
}

.status-unpaid {
    background: linear-gradient(135deg, #f56565, #e53e3e);
    color: white;
}

.amount-highlight {
    font-size: 18px;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.payment-history-table {
    margin: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.payment-history-table thead th {
    background: linear-gradient(135deg, #f7fafc, #edf2f7);
    border: none;
    padding: 15px 12px;
    font-weight: 600;
    color: #4a5568;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-history-table tbody td {
    padding: 15px 12px;
    border: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    vertical-align: middle;
    font-size: 14px;
}

.payment-history-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.05);
}

.payment-amount {
    font-weight: 700;
    font-size: 15px;
}

.payment-amount.credit {
    color: #48bb78;
}

.payment-amount.debit {
    color: #f56565;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-state i {
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-state h6 {
    color: #718096;
    font-weight: 600;
    margin-bottom: 8px;
}

.empty-state p {
    color: #a0aec0;
    font-size: 14px;
}

@media (max-width: 768px) {
    .invoice-details-page {
        padding: 10px 0;
    }
    
    .invoice-header .card-header {
        padding: 20px 20px 15px;
    }
    
    .invoice-title {
        font-size: 24px;
    }
    
    .action-buttons {
        margin-top: 15px;
        justify-content: center;
    }
    
    .btn-modern {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .info-card .card-body {
        padding: 20px;
    }
    
    .compact-table td {
        padding: 10px 0;
        font-size: 13px;
    }
}
</style>

<div class="invoice-details-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card invoice-header">
                    <div class="card-header">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <h3 class="invoice-title">
                                <i class="fas fa-file-invoice"></i> Invoice Details
                            </h3>
                            <div class="action-buttons">
                                <a href="{{ route('owner.invoices.payment.history', $invoice->id) }}" class="btn btn-modern btn-info-modern">
                                    <i class="fas fa-history"></i> Payment History
                                </a>
                                @if(strtolower($invoice->status) !== 'paid')
                                    <a href="{{ route('owner.invoices.payment', $invoice->id) }}" class="btn btn-modern btn-success-modern">
                                        <i class="fas fa-money-bill-wave"></i> Collect Payment
                                    </a>
                                @endif
                                <a href="{{ route('owner.invoices.index') }}" class="btn btn-modern btn-secondary-modern">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tenant Information Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card info-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user"></i> Tenant Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table compact-table">
                                    <tr>
                                        <td>Name:</td>
                                        <td>{{ $invoice->tenant ? ($invoice->tenant->first_name . ' ' . $invoice->tenant->last_name) : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Phone:</td>
                                        <td>{{ $invoice->tenant->mobile ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email:</td>
                                        <td>{{ $invoice->tenant->email ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card info-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-building"></i> Property Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table compact-table">
                                    <tr>
                                        <td>Property:</td>
                                        <td>{{ $invoice->unit && $invoice->unit->property ? $invoice->unit->property->name : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Unit:</td>
                                        <td>{{ $invoice->unit ? $invoice->unit->name : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Created:</td>
                                        <td>{{ $invoice->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Information Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-file-invoice"></i> Invoice Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table compact-table">
                                            <tr>
                                                <td>Invoice #:</td>
                                                <td>{{ $invoice->invoice_number ?? $invoice->id }}</td>
                                            </tr>
                                            <tr>
                                                <td>Type:</td>
                                                <td>
                                                    @if($invoice->type)
                                                        <span class="text-primary fw-medium">{{ ucfirst($invoice->type) }}</span>
                                                    @elseif($invoice->invoice_type)
                                                        <span class="text-primary fw-medium">{{ ucfirst($invoice->invoice_type) }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status:</td>
                                                <td>
                                                    @if(strtolower($invoice->status) === 'paid')
                                                        <span class="status-badge status-paid">Paid</span>
                                                    @elseif(strtolower($invoice->status) === 'partial')
                                                        <span class="status-badge status-partial">Partial</span>
                                                    @else
                                                        <span class="status-badge status-unpaid">Unpaid</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Amount:</td>
                                                <td class="amount-highlight">৳{{ number_format($invoice->amount, 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table compact-table">
                                            <tr>
                                                <td>Total Amount:</td>
                                                <td class="amount-highlight">৳{{ number_format($invoice->amount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Paid Amount:</td>
                                                <td class="text-success fw-bold">৳{{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Due Amount:</td>
                                                <td>
                                                    @php
                                                        $dueAmount = $invoice->amount - ($invoice->paid_amount ?? 0);
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="text-danger fw-bold">৳{{ number_format($dueAmount, 2) }}</span>
                                                        @if($dueAmount > 0 && strtolower($invoice->status) !== 'paid')
                                                            <button type="button" class="btn btn-modern btn-success-modern btn-sm payment-btn-updated" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                                                <i class="fas fa-money-bill-wave"></i> Pay
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Payment Date:</td>
                                                <td>{{ $invoice->paid_date ? \Carbon\Carbon::parse($invoice->paid_date)->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td>Payment Method:</td>
                                                <td>
                                                    @if($invoice->payment_method)
                                                        @php
                                                            $method = str_replace('_', ' ', $invoice->payment_method);
                                                        @endphp
                                                        <span class="text-primary fw-medium">{{ ucwords($method) }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($invoice->transaction_id)
                                            <tr>
                                                <td>Transaction ID:</td>
                                                <td>
                                                    <span class="text-info fw-medium">{{ $invoice->transaction_id }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-history"></i> Payment History
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    // Get payment history from tenant ledger
                                    $paymentHistory = \App\Models\TenantLedger::where('reference_type', 'invoice')
                                        ->where('reference_id', $invoice->id)
                                        ->orderBy('transaction_date', 'desc')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp

                                @if($paymentHistory->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table payment-history-table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Payment Method</th>
                                                    <th>Transaction ID</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
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
                                                            <span class="payment-amount credit">+৳{{ number_format($payment->credit_amount, 2) }}</span>
                                                        @elseif($payment->debit_amount > 0)
                                                            <span class="payment-amount debit">-৳{{ number_format($payment->debit_amount, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">৳0.00</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($payment->payment_status === 'completed')
                                                            <span class="status-badge status-paid">Completed</span>
                                                        @elseif($payment->payment_status === 'pending')
                                                            <span class="status-badge status-partial">Pending</span>
                                                        @elseif($payment->payment_status === 'failed')
                                                            <span class="status-badge status-unpaid">Failed</span>
                                                        @else
                                                            <span class="status-badge" style="background: #a0aec0; color: white;">{{ ucfirst($payment->payment_status) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-history fa-3x"></i>
                                        <h6>No Payment History Found</h6>
                                        <p>No payment transactions have been recorded for this invoice yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($invoice->notes)
                <div class="row">
                    <div class="col-12">
                        <div class="card info-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-sticky-note"></i> Notes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-0" style="background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); color: #4a5568; border-radius: 12px;">
                                    {{ $invoice->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('owner.invoices.payment.process', $invoice->id) }}" method="POST" id="paymentForm">
                    @csrf
                    
                    <!-- Invoice Info -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Invoice No:</small>
                            <div class="fw-bold">#{{ $invoice->invoice_number ?? $invoice->id }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Due Amount:</small>
                            <div class="fw-bold text-danger">৳{{ number_format($invoice->amount - ($invoice->paid_amount ?? 0), 2) }}</div>
                        </div>
                    </div>

                    <!-- Payment Fields - Compact Layout -->
                    <div class="row g-2">
                        <!-- Amount Payment -->
                        <div class="col-md-2">
                            <label for="paid_amount" class="form-label">Amount Payment <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('paid_amount') is-invalid @enderror" 
                                   id="paid_amount" 
                                   name="paid_amount" 
                                   value="{{ old('paid_amount', $invoice->amount - ($invoice->paid_amount ?? 0)) }}"
                                   min="0.01" 
                                   max="{{ $invoice->amount - ($invoice->paid_amount ?? 0) }}"
                                   step="0.01" 
                                   required>
                            <small class="text-muted">Due: ৳{{ number_format($invoice->amount - ($invoice->paid_amount ?? 0), 2) }}</small>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-2">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" 
                                    name="payment_method" 
                                    required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_banking" {{ old('payment_method') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transaction ID -->
                        <div class="col-md-2">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" 
                                   class="form-control @error('transaction_id') is-invalid @enderror" 
                                   id="transaction_id" 
                                   name="transaction_id" 
                                   value="{{ old('transaction_id') }}"
                                   placeholder="Transaction ID">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Paid Date -->
                        <div class="col-md-2">
                            <label for="paid_date" class="form-label">Paid Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('paid_date') is-invalid @enderror" 
                                   id="paid_date" 
                                   name="paid_date" 
                                   value="{{ old('paid_date', date('Y-m-d')) }}"
                                   required>
                            @error('paid_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-md-4">
                            <label for="notes" class="form-label">Notes</label>
                            <input type="text" 
                                   class="form-control @error('notes') is-invalid @enderror" 
                                   id="notes" 
                                   name="notes" 
                                   value="{{ old('notes') }}"
                                   placeholder="Enter payment notes (optional)">
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="paymentForm" class="btn btn-success">
                    <i class="fas fa-check"></i> Process Payment
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate remaining amount and update due display
document.getElementById('paid_amount').addEventListener('input', function() {
    const paidAmount = parseFloat(this.value) || 0;
    const totalAmount = {{ $invoice->amount }};
    const alreadyPaid = {{ $invoice->paid_amount ?? 0 }};
    const maxAmount = totalAmount - alreadyPaid;
    
    // Prevent overpayment
    if (paidAmount > maxAmount) {
        this.value = maxAmount;
        paidAmount = maxAmount;
    }
    
    // Calculate remaining due amount
    const remainingDue = maxAmount - paidAmount;
    
    // Update the due amount display
    const dueDisplay = this.parentElement.querySelector('small');
    if (dueDisplay) {
        dueDisplay.textContent = `Due: ৳${remainingDue.toFixed(2)}`;
        
        // Change color based on remaining amount
        if (remainingDue <= 0) {
            dueDisplay.className = 'text-success small';
            dueDisplay.textContent = 'Fully Paid!';
        } else {
            dueDisplay.className = 'text-muted small';
        }
    }
});

// Set today's date as default
document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
document.getElementById('paid_date').value = new Date().toISOString().split('T')[0];

// Initialize due amount display on page load
document.addEventListener('DOMContentLoaded', function() {
    const paidAmountInput = document.getElementById('paid_amount');
    if (paidAmountInput) {
        // Trigger the calculation on page load
        paidAmountInput.dispatchEvent(new Event('input'));
    }
});
</script>

@endsection
