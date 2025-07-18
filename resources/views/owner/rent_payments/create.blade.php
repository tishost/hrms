@extends('layouts.owner')
@section('title', 'Collect Rent')
@section('content')
<div class="container py-4 assign-rent-container" >
    <div class="card" style="border-radius: 10px;  box-shadow: 0 2px 12px rgba(67,97,238,0.07);">

        <div class="form-header">
            🏦 Collect Rent
        </div>
        <div style="margin:0 20px 0 20px">
        <div class="card-body">
            <form method="POST" action="{{ route('owner.rent_payments.store') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-6 mb-3 mb-md-0">
                        <div class="mb-3">
                            <label class="form-label" style="margin-bottom:8px;">Tenant</label>
                            <input type="text" class="form-input" value="{{ $tenant->first_name ?? '' }} {{ $tenant->last_name ?? '' }}" readonly>
                            <input type="hidden" name="tenant_id" value="{{ $tenant->id ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label" style="margin-bottom:8px;">Unit</label>
                            <input type="text" class="form-input" value="{{ $unit->name ?? '' }}" readonly>
                            <input type="hidden" name="unit_id" value="{{ $unit->id ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="mb-3" >
                    <label class="form-label" style="margin-bottom:8px;">Fees & Charges</label>
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr><th>Fee Label</th><th class="text-end">Amount</th></tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach ($fees as $fee)
                                <tr>
                                    <td>{{ $fee['label'] }}</td>
                                    <td class="text-end">{{ number_format($fee['amount'], 2) }}</td>
                                </tr>
                                @php $total += $fee['amount']; @endphp
                            @endforeach
                            @if(isset($previous_due) && $previous_due > 0)
                                <tr>
                                    <td><strong>Previous Due</strong></td>
                                    <td class="text-end"><strong>{{ number_format($previous_due, 2) }}</strong></td>
                                </tr>
                                @php $total += $previous_due; @endphp
                            @endif
                            <tr>
                                <td><strong>Total Due</strong></td>
                                <td class="text-end"><strong>{{ number_format($total, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="amount_due" value="{{ $total }}">
                </div>
                <div style="margin-top:10px" class="row mb-3">
                    <div class="col-9 text-end mb-3 mb-md-0">
                        <label class="form-label" style="margin-bottom:8px;">Amount Received</label>
                    </div>
                    <div class="col-3">
                        <input type="number" name="amount_paid" class="form-input" style="margin-bottom:16px;" required>
                    </div>
                </div>
                <div style="margin-top:10px" class="row mb-3">
                    <div class="col-9 ">
                        <label class="form-label text-end" style="margin-bottom:8px;">Payment Method</label>
                    </div>

                    <div class="col-3" style="padding-right:40px;">
                        <select name="payment_method" class="form-select" style="margin-bottom:16px; width:100%;" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>

                </div>
                <div class="row mb-3">
                <div class="col-3 mb-3 mb-md-0">
                        <label class="form-label" style="margin-bottom:8px;">Payment Date</label>
                    </div>
                    <div class="col-3">
                    <input type="date" name="payment_date" class="form-input" style="margin-bottom:16px;" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-3 mb-3">
                        <label class="form-label text-end" style="margin-bottom:8px;">Payment Transaction ID</label>

                    </div>
                    <div class="col-3">
                    <input type="text" name="transaction_id" class="form-input" style="margin-bottom:16px;" placeholder="Transaction ID (if any)">
                    </div>
                    </div>

                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label" style="margin-bottom:8px;">Notes</label>
                        <input type="text" name="notes" class="form-input" style="margin-bottom:16px;" placeholder="Optional notes...">
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button class="btn btn-success px-4 py-2">💸 Collect Rent</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
@endsection
