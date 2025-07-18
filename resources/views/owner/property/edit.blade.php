@extends('layouts.owner')

@section('content')
<div class="form-section">
    <div class="form-header">
        <h4 class="form-title"><span class="form-title-icon">✏️</span> Edit Building: <span class="form-title-name">{{ $property->name }}</span></h4>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('owner.property.update', $property->id) }}" method="POST">
        @csrf

        {{-- Building Info --}}
        <div class="form-group" style="display:flex; flex-wrap:wrap; gap:18px; flex-direction:column;">
            <div style="display:flex; flex-wrap:wrap; gap:18px;">
                <div style="flex:1; min-width:220px;">
                    <label class="form-label">Building Name</label>
                    <input type="text" name="name" value="{{ $property->name }}" class="form-input" required>
                </div>
                <div style="flex:1; min-width:220px;">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select custom-select">
                        <option value="">Select</option>
                        <option value="residential" {{ $property->type === 'residential' ? 'selected' : '' }}>Residential</option>
                        <option value="commercial"  {{ $property->type === 'commercial'  ? 'selected' : '' }}>Commercial</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:18px; margin-top:10px;">
                <div style="flex:1; min-width:220px;">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ $property->address }}" class="form-input address-input">
                </div>
                <div style="flex:1; min-width:220px;">
                    <label class="form-label">Country</label>
                    <div class="custom-country-dropdown country-dropdown-fixed">
                        <input type="text" id="countrySearch" class="form-input" placeholder="Search country..." autocomplete="off" value="{{ $property->country }}">
                        <span class="dropdown-arrow" id="countryDropdownArrow">
                            <svg width="22" height="22" viewBox="0 0 20 20"><path fill="#6c63ff" d="M7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z"/></svg>
                        </span>
                        <input type="hidden" name="country" id="countryHidden" value="{{ $property->country }}">
                        <ul id="countryList" class="country-list" style="display:none;">
                            @foreach($countries as $country)
                                <li data-value="{{ $country }}" class="country-item @if($property->country === $country) selected @endif">{{ $country }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Facilities --}}
        <div class="form-group">
            <label class="form-label">Facilities</label><br>
            @php $features = json_decode($property->features ?? '[]'); @endphp
            <div class="form-check">
                <input type="checkbox" name="facilities[]" value="Lift" class="form-check-input"
                       {{ in_array('Lift', $features ?? []) ? 'checked' : '' }}>
                <label class="form-label" style="font-weight:400; color:var(--dark)">Lift</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="facilities[]" value="Garage" class="form-check-input"
                       {{ in_array('Garage', $features ?? []) ? 'checked' : '' }}>
                <label class="form-label" style="font-weight:400; color:var(--dark)">Garage</label>
            </div>
        </div>

        <hr>

        {{-- Units & Charges --}}
        <h5 class="section-title mb-3"><span class="section-title-icon">🏠</span> <span class="section-title-text">Units & Fees</span></h5>
        <table class="table">
            <thead style="background:var(--primary-light); color:var(--primary);">
                <tr>
                    <th>Unit Name</th>
                    <th>Rent (৳)</th>
                    <th>Utility Charges</th>
                    <th>➕ Add</th>
                    <th>Total (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($property->units as $unit)
                <tr>
                    <td>
                        <input type="hidden" name="units[{{ $unit->id }}][id]" value="{{ $unit->id }}">
                        <input type="text" name="units[{{ $unit->id }}][name]" value="{{ $unit->name }}" class="form-input">
                    </td>
                    <td>
                        <input type="number" name="units[{{ $unit->id }}][rent]" value="{{ $unit->rent }}"
                               class="form-input rent-input" data-unit="{{ $unit->id }}" required>
                    </td>
                    <td>
                        <div id="charges_{{ $unit->id }}">
                            @foreach($unit->charges ?? [] as $index => $charge)
                            <div style="display:flex; gap:8px; margin-bottom:6px; align-items:center;" class="charge-group">
                                <input type="text" name="units[{{ $unit->id }}][charges][{{ $index }}][label]"
                                       value="{{ $charge->label }}" class="form-input" placeholder="Label" style="max-width:120px;">
                                <input type="number" name="units[{{ $unit->id }}][charges][{{ $index }}][amount]"
                                       value="{{ $charge->amount }}" class="form-input charge-input" data-unit="{{ $unit->id }}" placeholder="৳" style="max-width:100px;">
                                <button type="button" class="form-btn btn-remove" style="background:var(--danger);padding:6px 12px;min-width:unset;" onclick="removeCharge(this)"><span class="btn-icon">❌</span></button>
                            </div>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <button type="button" class="form-btn btn-add" style="background:var(--primary);padding:6px 12px;min-width:unset;" onclick="addCharge('{{ $unit->id }}')"><span class="btn-icon">➕</span></button>
                    </td>
                    <td>
                        <span id="total_{{ $unit->id }}" style="font-weight:600; color:var(--primary)">৳ {{ $unit->rent + $unit->charges->sum('amount') }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <button type="submit" class="form-btn btn-save"><span class="btn-icon">💾</span> Save Changes</button>
            <a href="{{ route('owner.property.index') }}" class="form-btn btn-back"><span class="btn-icon">↩️</span> Back to List</a>
        </div>
    </form>
</div>

{{-- Scripts --}}
<script>
function addCharge(unitId) {
    const wrapper = document.getElementById('charges_' + unitId);
    const index = wrapper.querySelectorAll('.charge-group').length;

    const html = `
    <div style="display:flex; gap:8px; margin-bottom:6px; align-items:center;" class="charge-group">
        <input type="text" name="units[${unitId}][charges][${index}][label]" class="form-input" placeholder="Label" style="max-width:120px;">
        <input type="number" name="units[${unitId}][charges][${index}][amount]" class="form-input charge-input" data-unit="${unitId}" placeholder="৳" style="max-width:100px;">
        <button type="button" class="form-btn btn-remove" style="background:var(--danger);padding:6px 12px;min-width:unset;" onclick="removeCharge(this)"><span class="btn-icon">❌</span></button>
    </div>`;
    wrapper.insertAdjacentHTML('beforeend', html);
    attachSumEvents();
    calculateTotal(unitId);
}

function removeCharge(button) {
    const group = button.closest('.charge-group');
    const unitId = group.querySelector('.charge-input')?.dataset.unit;
    group.remove();
    if (unitId) calculateTotal(unitId);
}

function calculateTotal(unitId) {
    const rentInput = document.querySelector(`input.rent-input[data-unit="${unitId}"]`);
    const chargeInputs = document.querySelectorAll(`.charge-input[data-unit="${unitId}"]`);
    let total = parseFloat(rentInput?.value || 0);
    chargeInputs.forEach(input => {
        total += parseFloat(input.value || 0);
    });
    document.getElementById('total_' + unitId).textContent = '৳ ' + total;
}

function attachSumEvents() {
    document.querySelectorAll('.rent-input, .charge-input').forEach(input => {
        input.addEventListener('input', () => {
            const unitId = input.dataset.unit;
            calculateTotal(unitId);
        });
    });
}
attachSumEvents();

// Country dropdown search
(function() {
    const searchInput = document.getElementById('countrySearch');
    const hiddenInput = document.getElementById('countryHidden');
    const list = document.getElementById('countryList');
    const items = list.querySelectorAll('.country-item');
    const arrow = document.getElementById('countryDropdownArrow');

    function filterCountries() {
        const val = searchInput.value.toLowerCase();
        let anyVisible = false;
        items.forEach(item => {
            if(item.textContent.toLowerCase().includes(val)) {
                item.style.display = '';
                anyVisible = true;
            } else {
                item.style.display = 'none';
            }
        });
        list.style.display = anyVisible ? 'block' : 'none';
    }

    function showList() {
        filterCountries();
        list.style.display = 'block';
    }
    function hideList() {
        list.style.display = 'none';
    }
    function toggleList() {
        if(list.style.display === 'block') {
            hideList();
        } else {
            showList();
        }
    }

    searchInput.addEventListener('focus', showList);
    searchInput.addEventListener('input', filterCountries);
    arrow.addEventListener('mousedown', function(e) {
        e.preventDefault();
        toggleList();
        searchInput.focus();
    });
    document.addEventListener('click', function(e) {
        if(!e.target.closest('.custom-country-dropdown')) {
            hideList();
        }
    });
    items.forEach(item => {
        item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            searchInput.value = this.textContent;
            hiddenInput.value = this.getAttribute('data-value');
            hideList();
            items.forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
})();
</script>
@endsection
