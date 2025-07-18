@extends('layouts.owner')
@section('title', 'Unpaid Rent Invoices')
@section('head')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
.btn-outline-primary {
    border: 1px solid #4895ef;
    color: #4895ef;
    background: #fff;
    transition: all 0.2s;
}
.btn-outline-primary:hover {
    background: #4895ef;
    color: #fff;
}
.btn-success {
    background: #43aa8b;
    border: none;
    color: #fff;
    transition: all 0.2s;
}
.btn-success:hover {
    background: #277c5d;
    color: #fff;
}
.btn-sm {
    font-size: 15px;
    padding: 6px 16px;
    border-radius: 5px;
    font-weight: 500;
}
.d-inline-flex {
    display: inline-flex !important;
    align-items: center;
}
.ms-2 { margin-left: 0.5rem !important; }
.me-1 { margin-right: 0.25rem !important; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tenantSelect = new TomSelect('#tenantSelect', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Search tenant..."
    });
    var unitSelect = new TomSelect('#unitSelect', {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Search unit..."
    });

    document.getElementById('tenantSelect').addEventListener('change', function() {
        unitSelect.setValue('');
        this.form.submit();
    });
    document.getElementById('unitSelect').addEventListener('change', function() {
        tenantSelect.setValue('');
        this.form.submit();
    });
});
</script>
@endsection
@section('content')
<div class="container py-4">
<div class="form-section mb-4">

    <div class="form-header">
     <h4 class="mb-4">🧾 Unpaid Rent Invoices</h4>
    </div>
    <form method="GET" action=""  >
    <div class="row mb-4">

        <div class="col-12">
            <label class="form-label">Invoice Number</label>
        </div>
        <div class="col-6 row">
            <input type="text" name="invoice" style="margin-left:10px" class="form-input " value="{{ request('invoice') }}" placeholder="Search by Invoice Number">
            <button class="form-btn btn-lg" style="margin-left:10px">Search</button>
        </div    >

    </div>

    <div class="row mb-4">
            <div class="col-6">
                <label class="form-label">Tenant</label>
                <select name="tenant_id" id="tenantSelect" class="form-select">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" @if(request('tenant_id') == $tenant->id) selected @endif>
                            {{ $tenant->first_name }} {{ $tenant->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">Unit</label>
                <select name="unit_id" id="unitSelect" class="form-select">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" @if(request('unit_id') == $unit->id) selected @endif>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
    </div>
    </form>
</div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Invoice #</th>
                    <th>Tenant</th>
                    <th>Unit</th>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Issue Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->tenant->first_name ?? '' }} {{ $invoice->tenant->last_name ?? '' }}</td>
                    <td>{{ $invoice->unit->name ?? '' }}</td>
                    <td>{{ $invoice->rent_month }}</td>
                    <td>{{ $invoice->amount }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->issue_date }}</td>
                    <td>
                        <a href="{{ route('owner.invoices.pdf', $invoice->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" target="_blank" title="View Invoice">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View
                        </a>
                        <a href="{{ route('owner.rent_payments.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-success d-inline-flex align-items-center ms-2" title="Make Payment">
                            <i class="bi bi-cash-coin me-1"></i> Payment
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No unpaid invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


