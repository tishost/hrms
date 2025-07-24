@extends('layouts.owner')
@section('title', 'Add Tenant')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

<div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold">🙍 Tenant Entry Form</h2>
    <form method="POST" action="{{ route('owner.tenants.store') }}">
        @csrf
        <!-- Personal Info -->
        <div class="form-section mb-4">
            <div class="form-header">
                <h4 class="form-title"><span class="form-title-icon">🧍</span> Personal Info</h4>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">First Name *</label>
                    <input type="text" name="first_name" class="form-input" placeholder="First Name" style="width:100%;" value="{{ old('first_name') }}">
                </div>
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Last Name *</label>
                    <input type="text" name="last_name" class="form-input" placeholder="Last Name" style="width:100%;" value="{{ old('last_name') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Gender *</label>
                    <select name="gender" class="form-select custom-select" style="width:100%;">
                        <option>Male</option><option>Female</option><option>Other</option>
                    </select>
                </div>
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Mobile No *</label>
                    <input type="text" name="mobile" class="form-input" placeholder="Mobile Number" style="width:100%;" value="{{ old('mobile') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Alternative Mobile</label>
                    <input type="text" name="alt_mobile" class="form-input" placeholder="Alternative Mobile" style="width:100%;">
                </div>
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="Email Address" style="width:100%;" value="{{ old('email') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">NID Number *</label>
                    <input type="text" name="nid_number" class="form-input" placeholder="NID Number" style="width:100%;" value="{{ old('nid_number') }}">
                </div>
                <div class="col-6"></div>
            </div>
        </div>
        <!-- Address -->
        <div class="form-section mb-4">
            <div class="form-header">
                <h4 class="form-title"><span class="form-title-icon">🏠</span> Address</h4>
            </div>
            <div class="row">
                <div class="col-12" style="margin-bottom:18px;">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-input" placeholder="Address" style="min-height:38px; resize:vertical; width:100%"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label">Country *</label>
                    <div class="custom-country-dropdown country-dropdown-fixed">
                        <input type="text" id="countrySearch" class="form-input" placeholder="Search country..." autocomplete="off">
                        <span class="dropdown-arrow" id="countryDropdownArrow">
                            <svg width="22" height="22" viewBox="0 0 20 20"><path fill="#6c63ff" d="M7.293 7.293a1 1 0 011.414 0L10 8.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z"/></svg>
                        </span>
                        <input type="hidden" name="country" id="countryHidden">
                        <ul id="countryList" class="country-list" style="display:none;">
                            @foreach($countries as $country)
                                <li data-value="{{ $country }}" class="country-item">{{ $country }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label">Total Family Members *</label>
                    <input type="number" name="total_family_member" class="form-input" min="1" value="1" placeholder="Total Family Members">
                </div>
            </div>
        </div>
        <!-- Occupation & Driver -->
        <div class="form-section mb-4">
            <div class="form-header">
                <h4 class="form-title"><span class="form-title-icon">👔</span> Occupation & Driver</h4>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label">Occupation *</label>
                    <select name="occupation" id="occupationSelect" style="width:100%" data-placeholder="Select occupation..." class="form-select custom-select " required>
                        <option value="">Select Occupation</option>
                        <option value="Business">Business</option>
                        <option value="Service">Service</option>
                        <option value="Student">Student</option>
                        <option value="Other">Other</option>
                    </select>

                </div>

                <div class="col-6" id="occupationFields" style=" display:none;">
                    <div id="businessNameGroup" style="display:none;">
                        <label for="businessNameField" class="form-label">Business Name</label>
                        <input type="text" id="businessNameField" name="business_name" class="form-input" style="width:100%" placeholder="Business Name">
                    </div>
                    <div id="companyNameGroup" style="display:none;">
                        <label for="companyNameField" class="form-label">Company Name</label>
                        <input type="text" id="companyNameField" name="company_name" class="form-input" style="width:100%" placeholder="Company Name">
                    </div>
                    <div id="universityNameGroup" style="display:none;">
                        <label for="universityNameField" class="form-label">University/College Name</label>
                        <input type="text" id="universityNameField" name="university_name" class="form-input" style="width:100%" placeholder="University/College Name">
                    </div>
                    <div id="otherOccupationGroup" style="display:none;">
                        <label for="otherOccupationField" class="form-label">Other (please specify)</label>
                        <input type="text" id="otherOccupationField" name="other_occupation" class="form-input" style="width:100%" placeholder="Other (please specify)">
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label">Is Driver?</label>
                    <select class="form-select custom-select" name="is_driver" id="isDriverSelect" style="width:100%" data-placeholder="Is driver?">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="col-6" >
                    <div id="driverNameGroup" style="margin-top:10px; display:none;">
                        <label for="driverNameField" class="form-label">Driver Name</label>
                        <input type="text" id="driverNameField" name="driver_name" class="form-input" style="width:100%" placeholder="Driver Name">
                    </div>
                </div>
            </div>
        </div>

        <!-- Property & Unit Assignment -->
        <div class="form-section mb-4">
            <div class="form-header">
                <h4 class="form-title"><span class="form-title-icon">🏢</span> Property & Unit Assignment</h4>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Property *</label>
                    <select name="building_id" id="propertySelect" class="form-select custom-select" style="width:100%;" required>
                        <option value="">Select Property</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->name }} - {{ $building->address }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Unit *</label>
                    <select name="unit_id" id="unitSelect" class="form-select custom-select" style="width:100%;" required disabled>
                        <option value="">Select Unit</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Check-in Date *</label>
                    <input type="date" name="check_in_date" class="form-input" style="width:100%;" value="{{ old('check_in_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-6" style="margin-bottom:18px;">
                    <label class="form-label" style="margin-bottom:6px;">Security Deposit *</label>
                    <input type="number" name="security_deposit" class="form-input" style="width:100%;" placeholder="Security Deposit Amount" value="{{ old('security_deposit') }}" step="0.01" min="0" required>
                </div>
            </div>
            <div class="row">
                <div class="col-12" style="margin-bottom:18px;">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-input" placeholder="Additional remarks" style="min-height:38px; resize:vertical; width:100%">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-end">
            <button type="submit" class="form-btn btn-save btn-lg">💾 Save Tenant</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
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

    // Occupation dynamic field logic
    const occupationSelect = document.getElementById('occupationSelect');
    const occupationFields = document.getElementById('occupationFields');
    const businessNameGroup = document.getElementById('businessNameGroup');
    const companyNameGroup = document.getElementById('companyNameGroup');
    const universityNameGroup = document.getElementById('universityNameGroup');
    const otherOccupationGroup = document.getElementById('otherOccupationGroup');

    function showOccupationField(value) {
        occupationFields.style.display = value ? 'block' : 'none';
        businessNameGroup.style.display = value === 'Business' ? 'block' : 'none';
        companyNameGroup.style.display = value === 'Service' ? 'block' : 'none';
        universityNameGroup.style.display = value === 'Student' ? 'block' : 'none';
        otherOccupationGroup.style.display = value === 'Other' ? 'block' : 'none';
    }
    occupationSelect.addEventListener('change', function() {
        showOccupationField(this.value);
    });
    // On page load, show if already selected
    showOccupationField(occupationSelect.value);

    const isDriverSelect = document.getElementById('isDriverSelect');
    const driverNameGroup = document.getElementById('driverNameGroup');
    function showDriverNameField(value) {
        driverNameGroup.style.display = value === '1' ? 'block' : 'none';
    }
    isDriverSelect.addEventListener('change', function() {
        showDriverNameField(this.value);
    });
    // On page load, show if already selected
    showDriverNameField(isDriverSelect.value);

    // Property and Unit Selection Logic
    const propertySelect = document.getElementById('propertySelect');
    const unitSelect = document.getElementById('unitSelect');

    propertySelect.addEventListener('change', function() {
        const propertyId = this.value;

        // Reset unit select
        unitSelect.innerHTML = '<option value="">Select Unit</option>';
        unitSelect.disabled = true;

        if (propertyId) {
            // Fetch units for selected property
            fetch(`/api/properties/${propertyId}/units`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.units.length > 0) {
                        data.units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = `${unit.name} - Floor ${unit.floor} (Rent: ${unit.rent_amount})`;
                            unitSelect.appendChild(option);
                        });
                        unitSelect.disabled = false;
                    } else {
                        unitSelect.innerHTML = '<option value="">No available units</option>';
                        unitSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching units:', error);
                    unitSelect.innerHTML = '<option value="">Error loading units</option>';
                    unitSelect.disabled = true;
                });
        }
    });

    // Auto-select property if old value exists
    if (propertySelect.value) {
        propertySelect.dispatchEvent(new Event('change'));
    }
})();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

@endsection
