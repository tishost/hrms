@extends('layouts.owner')

@section('title', 'Rent Invoices')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $totalInvoices }}</h4>
                            <p class="mb-0">Total Invoices</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">৳{{ number_format($totalAmount) }}</h4>
                            <p class="mb-0">Total Amount</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">৳{{ number_format($totalPaid) }}</h4>
                            <p class="mb-0">Total Paid</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">৳{{ number_format($totalDue) }}</h4>
                            <p class="mb-0">Total Due</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice"></i> Rent Invoices
                    </h3>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Tenant</th>
                                        <th>Property</th>
                                        <th>Unit</th>
                                        <th>Amount</th>
                                        <th>Due Amount</th>
                                        <th>Payment Date</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number ?? $invoice->id }}</td>
                                        <td>{{ $invoice->tenant ? ($invoice->tenant->first_name . ' ' . $invoice->tenant->last_name) : 'N/A' }}</td>
                                        <td>{{ $invoice->unit && $invoice->unit->property ? $invoice->unit->property->name : 'N/A' }}</td>
                                        <td>{{ $invoice->unit ? $invoice->unit->name : 'N/A' }}</td>
                                        <td>৳{{ number_format($invoice->amount, 2) }}</td>
                                        <td>
                                            @php
                                                $paidAmount = $invoice->paid_amount ?? 0;
                                                $dueAmount = $invoice->amount - $paidAmount;
                                            @endphp
                                            @if($dueAmount > 0)
                                                <span class="text-danger fw-medium">৳{{ number_format($dueAmount, 2) }}</span>
                                            @else
                                                <span class="text-success fw-medium">৳0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($invoice->paid_date)
                                                {{ \Carbon\Carbon::parse($invoice->paid_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not Paid</span>
                                            @endif
                                        </td>
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
                                        <td>
                                            @if(strtolower($invoice->status) === 'paid')
                                                <span class="text-success fw-medium">Paid</span>
                                            @elseif(strtolower($invoice->status) === 'partial')
                                                <span class="text-warning fw-medium">Partial</span>
                                            @else
                                                <span class="text-danger fw-medium">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('owner.invoices.show', $invoice->id) }}"
                                               class="btn btn-sm btn-info me-1">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($dueAmount > 0)
                                                <button type="button" 
                                                        class="btn btn-sm btn-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#paymentModal{{ $invoice->id }}">
                                                    <i class="fas fa-money-bill-wave"></i> Collect Payment
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5>No Rent Invoices Found</h5>
                            <p class="text-muted">No rent invoices have been generated yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modals -->
@foreach($invoices as $invoice)
@php
    $paidAmount = $invoice->paid_amount ?? 0;
    $dueAmount = $invoice->amount - $paidAmount;
@endphp
@if($dueAmount > 0)
<!-- Payment Modal for Invoice {{ $invoice->id }} -->
<div class="modal fade" id="paymentModal{{ $invoice->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $invoice->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel{{ $invoice->id }}">Payment Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('owner.invoices.payment.process', $invoice->id) }}" method="POST" id="paymentForm{{ $invoice->id }}">
                    @csrf
                    
                    <!-- Invoice Info -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Invoice No:</small>
                            <div class="fw-bold">#{{ $invoice->invoice_number ?? $invoice->id }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Due Amount:</small>
                            <div class="fw-bold text-danger">৳{{ number_format($dueAmount, 2) }}</div>
                        </div>
                    </div>

                    <!-- Payment Fields - Compact Layout -->
                    <div class="row g-2">
                        <!-- Amount Payment -->
                        <div class="col-md-2">
                            <label for="paid_amount{{ $invoice->id }}" class="form-label">Amount Payment <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('paid_amount') is-invalid @enderror" 
                                   id="paid_amount{{ $invoice->id }}" 
                                   name="paid_amount" 
                                   value="{{ old('paid_amount', $dueAmount) }}"
                                   min="0.01" 
                                   max="{{ $dueAmount }}"
                                   step="0.01" 
                                   required>
                            <small class="text-muted">Due: ৳{{ number_format($dueAmount, 2) }}</small>
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-2">
                            <label for="payment_method{{ $invoice->id }}" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method{{ $invoice->id }}" 
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
                            <label for="transaction_id{{ $invoice->id }}" class="form-label">Transaction ID</label>
                            <input type="text" 
                                   class="form-control @error('transaction_id') is-invalid @enderror" 
                                   id="transaction_id{{ $invoice->id }}" 
                                   name="transaction_id" 
                                   value="{{ old('transaction_id') }}"
                                   placeholder="Transaction ID">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Paid Date -->
                        <div class="col-md-2">
                            <label for="paid_date{{ $invoice->id }}" class="form-label">Paid Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('paid_date') is-invalid @enderror" 
                                   id="paid_date{{ $invoice->id }}" 
                                   name="paid_date" 
                                   value="{{ old('paid_date', date('Y-m-d')) }}"
                                   required>
                            @error('paid_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-md-4">
                            <label for="notes{{ $invoice->id }}" class="form-label">Notes</label>
                            <input type="text" 
                                   class="form-control @error('notes') is-invalid @enderror" 
                                   id="notes{{ $invoice->id }}" 
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
                <button type="submit" form="paymentForm{{ $invoice->id }}" class="btn btn-success">
                    <i class="fas fa-check"></i> Process Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
// Auto-calculate remaining amount and update due display for all modals
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default for all date fields
    const dateFields = document.querySelectorAll('input[type="date"]');
    dateFields.forEach(function(field) {
        field.value = new Date().toISOString().split('T')[0];
    });

    // Add event listeners for all payment amount fields
    const paymentAmountFields = document.querySelectorAll('input[id^="paid_amount"]');
    paymentAmountFields.forEach(function(field) {
        field.addEventListener('input', function() {
            const paidAmount = parseFloat(this.value) || 0;
            const maxAmount = parseFloat(this.getAttribute('max'));
            
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
        
        // Trigger the calculation on page load
        field.dispatchEvent(new Event('input'));
    });
});
</script>

@endsection


