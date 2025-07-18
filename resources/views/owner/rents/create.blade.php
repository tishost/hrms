@extends('layouts.owner')
@section('title', 'Assign Rent')

@section('head')
<style>
    .assign-rent-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(67,97,238,0.07);
        padding: 32px 24px;

        margin: 0 auto;
    }
    .assign-rent-container h4 {
        font-weight: 600;
        color: #3f37c9;
        margin-bottom: 24px;
    }
    .assign-rent-container label {
        font-weight: 500;
        color: #3f37c9;
        margin-bottom: 6px;
    }
    .assign-rent-container .form-control, .assign-rent-container .form-select {
        border-radius: 5px;
        border: 1px solid #dbeafe;
        font-size: 16px;
        padding: 8px 12px;
        background: #f8faff;
        margin-bottom: 8px;
    }
    .assign-rent-container .form-control:focus, .assign-rent-container .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 2px #e8edff;
        background: #fff;
    }
    .assign-rent-container .card {
        border-radius: 8px;
        border: 1px solid #e8edff;
        box-shadow: 0 1px 4px rgba(67,97,238,0.04);
        margin-bottom: 18px;
    }
    .assign-rent-container .card-header {
        background: #e8edff;
        border-bottom: 1px solid #e8edff;
        font-weight: 600;
        color: #3f37c9;
    }
    .assign-rent-container .card-footer {
        background: #f8faff;
        border-top: 1px solid #e8edff;
    }
    .assign-rent-container .btn {
        border-radius: 5px;
        font-weight: 500;
        font-size: 15px;
        padding: 7px 18px;
    }
    .assign-rent-container .btn-success {
        background: #43aa8b;
        border: none;
    }
    .assign-rent-container .btn-success:hover {
        background: #277c5d;
    }
    .assign-rent-container .btn-outline-primary {
        border: 1px solid #4895ef;
        color: #4895ef;
        background: #fff;
    }
    .assign-rent-container .btn-outline-primary:hover {
        background: #4895ef;
        color: #fff;
    }
    .assign-rent-container .btn-outline-danger {
        border: 1px solid #f72585;
        color: #f72585;
        background: #fff;
    }
    .assign-rent-container .btn-outline-danger:hover {
        background: #f72585;
        color: #fff;
    }
    .assign-rent-container .table {
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 0;
    }
    .assign-rent-container .table th {
        background: #e8edff;
        color: #3f37c9;
        font-weight: 600;
        border-bottom: 1px solid #e8edff;
    }
    .assign-rent-container .table td {
        vertical-align: middle;
    }
    .assign-rent-container .form-group.row {
        margin-bottom: 10px;
    }
    .assign-rent-container .row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -8px;
        margin-right: -8px;
    }
    .assign-rent-container .row > [class^='col-'] {
        padding-left: 8px;
        padding-right: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .assign-rent-container label {
        margin-bottom: 4px;
        font-size: 15px;
    }
    .assign-rent-container .form-control,
    .assign-rent-container .form-select {
        min-height: 38px;
        font-size: 15px;
        margin-bottom: 0;
    }
    .assign-rent-container .row.mb-3, .assign-rent-container .row.mb-4 {
        margin-bottom: 18px !important;
    }
    @media (max-width: 768px) {
        .assign-rent-container {
            padding: 16px 4px;
        }
        .assign-rent-container .row {
            flex-direction: column;
        }
        .assign-rent-container .row > [class^='col-'] {
            margin-bottom: 10px;
        }
    }
    .assign-rent-container .card-header.d-flex {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="container py-4 assign-rent-container">
    <h4>🧾 Assign Rent to Tenant: {{ $tenant->first_name }}</h4>

    <form method="POST" action="{{ route('owner.rents.store', $tenant->id) }}">
        @csrf

        <!-- 🔹 Building and Unit Selection -->
        <div class="row mb-4">
            <div class="col-6">
                <label>Building</label>
                <select id="buildingSelect" class="form-select">
                    <option value="">Select Building</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label>Unit</label>
                <select name="unit_id" id="unitSelect" class="form-select" required>
                    <option value="">Select Unit</option>
                </select>
            </div>
        </div>





        <!-- 📆 Start Month & Due Day -->
        <div class="row mb-3">
            <div class="col-6">
                <label>Start Month</label>
                <input type="month" name="start_month" class="form-control" required>
            </div>
            <div class="col-6">
                <label>Due Day</label>
                <input type="number" name="due_day" class="form-control" min="1" max="31" required>
            </div>
        </div>

        <!-- 💵 Advance & Frequency -->
        <div class="row mb-3">
            <div class="col-6">
                <label>Advance</label>
                <input type="number" name="advance_amount" class="form-control" step="0.01">
            </div>
            <div class="col-6">
                <label>Frequency</label>
                <select name="frequency" class="form-select" required>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
        </div>

 <!-- 💰 Fees Section (Above Remarks) -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center w-100">
                <strong>Fees</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addFeeBtn">➕ Add Fee</button>
            </div>
            <div class="card-body p-0 pb-4">
                <div id="feeTableContainer" class="table-responsive"></div>
            </div>
          <div class="card-footer">
            <div class="pb-4 row">
                <div  class="col-8 text-right">
                    <label for="inputEmail3" class="col-sm-8 col-form-label ">Subtotal</label>
                </div>

                <div class="col-4">
                   <input type="text" id="totalRent" class="form-control bg-light" readonly>
                </div>
            </div>

            <div class="pb-4 row">
                <label for="subtatal" class="col-8 col-form-label text-end">Discount</label>
                <div class="col-4">
                     <input type="number" name="discount" id="discountInput" class="form-control" step="0.01" value="0">
                </div>
            </div>

              <div class="md-2 row">
                <label for="subtatal" class="col-8 col-form-label text-end">Total Rent</label>
                <div class="col-4">
                    <input type="text" id="netRent" class="form-control bg-light" readonly>
                </div>
            </div>

            </div>
        </div>

          <!-- 📝 Remarks -->
        <div class="mb-3 col-12">
            <label>Remarks</label>
            <textarea name="remarks" style="width:100% ; margin:10px 0 10px 0" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
        </div>

        <!-- ✅ Submit -->
        <div class="text-end">
            <button class="btn btn-success">✅ Assign Rent</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buildingSelect = document.getElementById('buildingSelect');
    const unitSelect = document.getElementById('unitSelect');
    const feeTableContainer = document.getElementById('feeTableContainer');
    const addFeeBtn = document.getElementById('addFeeBtn');
    const totalRentInput = document.getElementById('totalRent');
    const discountInput = document.getElementById('discountInput');
    const netRentInput = document.getElementById('netRent');

    buildingSelect.addEventListener('change', () => {
        const buildingId = buildingSelect.value;
        unitSelect.innerHTML = '<option>Loading...</option>';
        feeTableContainer.innerHTML = '';
        fetch(`/owner/units-by-building/${buildingId}`)
            .then(res => res.json())
            .then(units => {
                unitSelect.innerHTML = '<option value="">Select Unit</option>';
                for (const [uid, name] of Object.entries(units)) {
                    unitSelect.innerHTML += `<option value="${uid}">${name}</option>`;
                }
            });
    });

    unitSelect.addEventListener('change', () => {
        const unitId = unitSelect.value;
        if (!unitId) return;
        feeTableContainer.innerHTML = '<p class="text-muted px-3 py-2">Loading fees...</p>';
        fetch(`/owner/unit-fees/${unitId}`)
            .then(res => res.json())
            .then(fees => renderFeeTable(fees));
    });

    addFeeBtn.addEventListener('click', () => appendFeeRow('', ''));

    discountInput.addEventListener('input', updateNetRent);

    function renderFeeTable(fees) {
        let html = `<table class="table table-bordered mb-0">
            <thead><tr><th>Fee Label</th><th>Amount</th><th>Action</th></tr></thead><tbody>`;

        let baseAmount = fees['rent'] ?? '';
        if (baseAmount !== '') {
            html += feeRow('Base Fare', baseAmount);
            delete fees['rent'];
        }

        for (const [label, amount] of Object.entries(fees)) {
            html += feeRow(label, amount);
        }

        if (!baseAmount && Object.keys(fees).length === 0) {
            html += feeRow('', '');
        }

        html += '</tbody></table>';
        feeTableContainer.innerHTML = html;
        updateNetRent();
    }

    function appendFeeRow(label = '', amount = '') {
        let tbody = feeTableContainer.querySelector('tbody');
        if (!tbody) {
            renderFeeTable({});
            tbody = feeTableContainer.querySelector('tbody');
        }
        tbody.insertAdjacentHTML('beforeend', feeRow(label, amount));
        updateNetRent();
    }

    function feeRow(label = '', amount = '') {
        return `<tr>
            <td><input type="text" name="fee_labels[]" value="${label}" class="form-control" required></td>
            <td><input type="number" name="fee_amounts[]" value="${amount}" class="form-control amount-field" step="0.01" required></td>
            <td><button type="button" class="form-btn btn-remove btn-sm" onclick="this.closest('tr').remove(); updateNetRent();"><span class="btn-icon">❌</span></button></td>
        </tr>`;
    }

    function updateNetRent() {
        let total = 0;
        feeTableContainer.querySelectorAll('.amount-field').forEach(field => {
            total += parseFloat(field.value) || 0;
        });
        const discount = parseFloat(discountInput.value) || 0;
        totalRentInput.value = total.toFixed(2);
        netRentInput.value = Math.max(total - discount, 0).toFixed(2);
    }
});
</script>
@endsection
