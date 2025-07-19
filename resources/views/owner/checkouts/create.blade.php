@extends('layouts.owner')
@section('title', 'Check-out Tenant')

@section('content')
<div class="container py-4">
    <div class="card" style="border-radius: 10px; box-shadow: 0 2px 12px rgba(67,97,238,0.07);">
        <div class="form-header">
            🚪 Check-out Tenant: {{ $tenant->first_name }} {{ $tenant->last_name }}
        </div>
        <div style="margin:0 20px 0 20px">
            <div class="card-body">
                <form method="POST" action="{{ route('owner.checkouts.process', $tenant->id) }}">
                    @csrf

                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h5 class="section-title mb-3">📋 Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tenant Name</label>
                                <input type="text" class="form-input" value="{{ $tenant->first_name }} {{ $tenant->last_name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit</label>
                                <input type="text" class="form-input" value="{{ $tenant->unit->name ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Date *</label>
                                <input type="date" name="check_out_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-out Reason *</label>
                                <select name="check_out_reason" class="form-select" required>
                                    <option value="">Select Reason</option>
                                    <option value="End of lease">End of lease</option>
                                    <option value="Personal reasons">Personal reasons</option>
                                    <option value="Job transfer">Job transfer</option>
                                    <option value="Financial issues">Financial issues</option>
                                    <option value="Property sold">Property sold</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Settlement -->
                    <div class="mb-4">
                        <h5 class="section-title mb-3">💰 Financial Settlement</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Security Deposit *</label>
                                <input type="number" name="security_deposit" class="form-input" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Outstanding Dues</label>
                                <input type="text" class="form-input" value="{{ number_format($totalOutstanding, 2) }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Utility Bills *</label>
                                <input type="number" name="utility_bills" class="form-input" step="0.01" value="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cleaning Charges *</label>
                                <input type="number" name="cleaning_charges" class="form-input" step="0.01" value="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Other Charges *</label>
                                <input type="number" name="other_charges" class="form-input" step="0.01" value="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Unit Handover -->
                    <div class="mb-4">
                        <h5 class="section-title mb-3">🏠 Unit Handover</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Handover Date *</label>
                                <input type="date" name="handover_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit Condition *</label>
                            <textarea name="handover_condition" class="form-input" rows="4" placeholder="Describe the unit condition, any damages, cleaning status, etc." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="notes" class="form-input" rows="3" placeholder="Any additional notes or comments..."></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('owner.tenants.index') }}" class="btn btn-secondary px-4 py-2 me-2">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 py-2">✅ Process Check-out</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
