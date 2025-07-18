@extends('layouts.owner')
@section('content')
@if(session('error'))
    <div class="input-error">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="input-error">{{ $errors->first() }}</div>
@endif
<div class="form-section">
    <div class="form-header">
        <h4 class="form-title"><span class="form-title-icon">🏢</span> Configure Units for {{ $property->name }}</h4>
    </div>

    {{-- Building Setup --}}
    <form action="{{ route('owner.units.generate', $property->id) }}" method="POST">
        @csrf
        <div class="form-group" style="display:flex; flex-wrap:wrap; gap:18px;">
            <div style="flex:1; min-width:220px;">
                <label class="form-label">Total Floors</label>
                <input type="number" name="total_floors" class="form-input" required value="{{ old('total_floors') }}">
                @error('total_floors')
                    <div class="input-error">{{ $message }}</div>
                @enderror
            </div>
            <div style="flex:1; min-width:220px;">
                <label class="form-label">Total Units</label>
                <input type="number" name="total_units" class="form-input" required value="{{ old('total_units') }}">
                @error('total_units')
                    <div class="input-error">{{ $message }}</div>
                @enderror
            </div>
            <div style="flex:1; min-width:220px;">
                <label class="form-label">Facilities</label><br>
                <div class="form-check" style="display:inline-block; margin-right:18px;">
                    <input type="checkbox" name="facilities[]" value="Lift" class="form-check-input" id="lift">
                    <label for="lift" class="form-label" style="font-weight:400; color:var(--dark)">Lift</label>
                </div>
                <div class="form-check" style="display:inline-block;">
                    <input type="checkbox" name="facilities[]" value="Garage" class="form-check-input" id="garage">
                    <label for="garage" class="form-label" style="font-weight:400; color:var(--dark)">Garage</label>
                </div>
            </div>
        </div>
        <button type="submit" class="form-btn btn-save" style="margin-top:24px; margin-bottom:32px;"><span class="btn-icon">⚙️</span> Generate Units</button>
    </form>

    @if(!empty($generated_units))
    <div style="border-bottom: 1.5px solid #e0e7ff; margin: 8px 0 0 0;"></div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h5 class="section-title mb-0"><span class="section-title-icon">🏢</span> <span class="section-title-text">Assign Rent & Fees</span></h5>
        <button type="button" class="form-btn btn-add btn-sm" onclick="addUnit()"><span class="btn-icon">➕</span> Add Unit</button>
    </div>

    <form action="{{ route('owner.units.saveFees', $property->id) }}" method="POST">
        @csrf
        <div class="table-responsive">
        <table class="table">
            <thead style="background:var(--primary-light); color:var(--primary);">
                <tr>
                    <th>Unit Name</th>
                    <th>Rent (৳)</th>
                    <th>Utility Charges</th>
                    <th>➕ Add Charge</th>
                    <th>Total (৳)</th>
                    <th>❌ Remove</th>
                </tr>
            </thead>
            <tbody id="unit_table">
                @foreach($generated_units as $unit)
                <tr class="unit-row" data-unit="{{ $unit['id'] }}">
                    <td>
                        <input type="text" name="units[{{ $unit['id'] }}][name]" value="{{ old('units.' . $unit['id'] . '.name', $unit['name']) }}" class="form-input" required>
                        @error('units.' . $unit['id'] . '.name')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="number" name="units[{{ $unit['id'] }}][rent]" class="form-input rent-input" data-unit="{{ $unit['id'] }}" required value="{{ old('units.' . $unit['id'] . '.rent', $unit['rent']) }}">
                        @error('units.' . $unit['id'] . '.rent')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <div id="charges_{{ $unit['id'] }}">
                            {{-- Charges will go here --}}
                        </div>
                    </td>
                    <td>
                        <button type="button" class="form-btn btn-add btn-sm" onclick="addCharge({{ $unit['id'] }})"><span class="btn-icon">➕</span></button>
                    </td>
                    <td>
                        <span style="font-weight:600; color:var(--primary)" id="total_{{ $unit['id'] }}">৳ 0</span>
                    </td>
                    <td>
                        <button type="button" class="form-btn btn-remove btn-sm" onclick="removeUnit(this)"><span class="btn-icon">❌</span></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <button type="submit" class="form-btn btn-save"><span class="btn-icon">💾</span> Save All Fees</button>
    </form>
    @endif
</div>

<script>
console.log('Unit setup JS loaded');
function addCharge(unitId) {
    const wrapper = document.getElementById('charges_' + unitId);
    const index = wrapper.children.length;
    const html = `
    <div class="d-flex mb-1 charge-group">
        <input type="text" name="units[${unitId}][charges][${index}][label]" class="form-input me-2" placeholder="Label">
        <input type="number" name="units[${unitId}][charges][${index}][amount]" class="form-input charge-input" data-unit="${unitId}" placeholder="৳">
        <button type="button" class="form-btn btn-remove btn-sm ms-2" onclick="removeCharge(this)"><span class="btn-icon">❌</span></button>
    </div>`;
    wrapper.insertAdjacentHTML('beforeend', html);
    attachSumEvents();
}

function removeCharge(button) {
    const parent = button.closest('.charge-group');
    const unitId = parent.querySelector('.charge-input')?.dataset.unit;
    parent.remove();
    if (unitId) calculateTotal(unitId);
}

function attachSumEvents() {
    document.querySelectorAll('.rent-input, .charge-input').forEach(input => {
        input.oninput = () => {
            const unitId = input.dataset.unit;
            calculateTotal(unitId);
        };
    });
}

function calculateTotal(unitId) {
    const rent = document.querySelector(`.rent-input[data-unit="${unitId}"]`)?.value || 0;
    const charges = document.querySelectorAll(`.charge-input[data-unit="${unitId}"]`);
    let total = parseFloat(rent);
    charges.forEach(c => total += parseFloat(c.value || 0));
    document.getElementById('total_' + unitId).textContent = '৳ ' + total;
}

function removeUnit(button) {
    const row = button.closest('tr');
    row.remove();
}

function addUnit() {
    const unitTable = document.getElementById('unit_table');
    const newId = Date.now();
    const html = `
    <tr class="unit-row" data-unit="${newId}">
        <td><input type="text" name="units[${newId}][name]" value="Unit-${newId}" class="form-input" required></td>
        <td><input type="number" name="units[${newId}][rent]" class="form-input rent-input" data-unit="${newId}" required></td>
        <td><div id="charges_${newId}"></div></td>
        <td><button type="button" class="form-btn btn-add btn-sm" onclick="addCharge(${newId})"><span class="btn-icon">➕</span></button></td>
        <td><span class="fw-bold text-success" id="total_${newId}">৳ 0</span></td>
        <td><button type="button" class="form-btn btn-remove btn-sm" onclick="removeUnit(this)"><span class="btn-icon">❌</span></button></td>
    </tr>`;
    unitTable.insertAdjacentHTML('beforeend', html);
    attachSumEvents();
}
attachSumEvents();
</script>
@endsection
