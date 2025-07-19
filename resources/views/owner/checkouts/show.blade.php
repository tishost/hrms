@extends('layouts.owner')
@section('title', 'Check-out Details')

@section('content')
<div class="container py-4">
    <div class="page-header">
        <div class="page-title">
            <h1>🚪 Check-out Details</h1>
        </div>
        <div class="page-actions">
            <a href="{{ route('owner.checkouts.invoice', $checkout->id) }}" class="btn btn-primary">📄 Generate Invoice</a>
            <a href="{{ route('owner.checkouts.index') }}" class="btn btn-secondary">← Back to List</a>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6 mb-4">
            <div class="card" style="border-radius: 10px; box-shadow: 0 2px 12px rgba(67,97,238,0.07);">
                <div class="card-header">
                    <h5 class="mb-0">📋 Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Tenant:</strong></div>
                        <div class="col-8">{{ $checkout->tenant->first_name }} {{ $checkout->tenant->last_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Unit:</strong></div>
                        <div class="col-8">{{ $checkout->unit->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Check-out Date:</strong></div>
                        <div class="col-8">{{ $checkout->check_out_date->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Reason:</strong></div>
                        <div class="col-8">{{ $checkout->check_out_reason }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Handover Date:</strong></div>
                        <div class="col-8">{{ $checkout->handover_date ? $checkout->handover_date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="col-md-6 mb-4">
            <div class="card" style="border-radius: 10px; box-shadow: 0 2px 12px rgba(67,97,238,0.07);">
                <div class="card-header">
                    <h5 class="mb-0">💰 Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6"><strong>Security Deposit:</strong></div>
                        <div class="col-6 text-end">৳{{ number_format($checkout->security_deposit, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Outstanding Dues:</strong></div>
                        <div class="col-6 text-end text-danger">-৳{{ number_format($checkout->outstanding_dues, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Utility Bills:</strong></div>
                        <div class="col-6 text-end text-danger">-৳{{ number_format($checkout->utility_bills, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Cleaning Charges:</strong></div>
                        <div class="col-6 text-end text-danger">-৳{{ number_format($checkout->cleaning_charges, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Other Charges:</strong></div>
                        <div class="col-6 text-end text-danger">-৳{{ number_format($checkout->other_charges, 2) }}</div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Final Settlement:</strong></div>
                        <div class="col-6 text-end">
                            <strong class="{{ $checkout->final_settlement_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                ৳{{ number_format($checkout->final_settlement_amount, 2) }}
                            </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Status:</strong></div>
                        <div class="col-6 text-end">
                            <span class="status {{ strtolower($checkout->settlement_status) }}">
                                {{ ucfirst($checkout->settlement_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Condition -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card" style="border-radius: 10px; box-shadow: 0 2px 12px rgba(67,97,238,0.07);">
                <div class="card-header">
                    <h5 class="mb-0">🏠 Unit Handover Condition</h5>
                </div>
                <div class="card-body">
                    <p><strong>Condition Description:</strong></p>
                    <p class="text-muted">{{ $checkout->handover_condition ?? 'No condition details provided.' }}</p>

                    @if($checkout->notes)
                        <hr>
                        <p><strong>Additional Notes:</strong></p>
                        <p class="text-muted">{{ $checkout->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}
.status.completed {
    background: #d4ffd4;
    color: #00a000;
}
.status.partial {
    background: #fff3cd;
    color: #856404;
}
.status.pending {
    background: #ffd4d4;
    color: #d40000;
}
.page-actions {
    display: flex;
    gap: 10px;
}
.btn {
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
}
.btn-primary {
    background: #4361ee;
    color: white;
}
.btn-secondary {
    background: #6c757d;
    color: white;
}
</style>
@endsection
