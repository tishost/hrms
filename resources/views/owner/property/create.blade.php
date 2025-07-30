@extends('layouts.owner')

@section('content')
<style>
.form-section {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    max-width: 800px;
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 30px;
    text-align: center;
    position: relative;
}

.form-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.form-title {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.form-title-icon {
    margin-right: 10px;
    font-size: 28px;
}

.form-body {
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.form-input, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.is-invalid, .form-select.is-invalid {
    border-color: #ef4444;
    background: #fef2f2;
}

.input-error {
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.row {
    margin: 0 -10px;
}

.col-md-4, .col-md-6 {
    padding: 0 10px;
}

.form-actions {
    background: #f8fafc;
    padding: 20px 30px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 15px;
    justify-content: center;
}

.form-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-save {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-back {
    background: #6b7280;
    color: white;
}

.btn-back:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.btn-icon {
    font-size: 16px;
}

.alert-success, .alert-danger {
    margin: 20px 30px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

@media (max-width: 768px) {
    .form-section {
        margin: 10px;
        border-radius: 8px;
    }

    .form-body {
        padding: 20px;
    }

    .form-actions {
        flex-direction: column;
        padding: 15px 20px;
    }

    .form-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="form-section">
    <div class="form-header">
        <h4 class="form-title"><span class="form-title-icon">🏢</span> Add New Building</h4>
    </div>

    <div class="form-body">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-danger">{{ session('error') }}</div>
        @endif

        <form id="property-form" action="{{ route('owner.property.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="form-label">Building Name</label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               class="form-input @error('name') is-invalid @enderror"
                               placeholder="Enter building name" required>
                        @error('name')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type" class="form-label">Property Type</label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="residential" {{ old('type') == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ old('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                        @error('type')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address"
                       value="{{ old('address') }}"
                       class="form-input @error('address') is-invalid @enderror"
                       placeholder="Enter full address" required>
                @error('address')
                    <div class="input-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city') }}"
                               class="form-input @error('city') is-invalid @enderror"
                               placeholder="Enter city" required>
                        @error('city')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="state" class="form-label">State</label>
                        <input type="text" name="state" id="state"
                               value="{{ old('state') }}"
                               class="form-input @error('state') is-invalid @enderror"
                               placeholder="Enter state" required>
                        @error('state')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="zip_code" class="form-label">Zip Code</label>
                        <input type="text" name="zip_code" id="zip_code"
                               value="{{ old('zip_code') }}"
                               class="form-input @error('zip_code') is-invalid @enderror"
                               placeholder="Enter zip code" required>
                        @error('zip_code')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="country" class="form-label">Country</label>
                        <select name="country" id="country" class="form-select @error('country') is-invalid @enderror" required>
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="total_units" class="form-label">Total Units</label>
                        <input type="number" name="total_units" id="total_units"
                               value="{{ old('total_units', 1) }}"
                               class="form-input @error('total_units') is-invalid @enderror"
                               min="1" max="100" placeholder="Enter total units" required>
                        @error('total_units')
                            <div class="input-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea name="description" id="description" rows="4"
                          class="form-input @error('description') is-invalid @enderror"
                          placeholder="Enter property description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="input-error">{{ $message }}</div>
                @enderror
            </div>
        </form>
    </div>

    <div class="form-actions">
        <button type="submit" form="property-form" class="form-btn btn-save">
            <span class="btn-icon">💾</span> Save Property
        </button>
        <a href="{{ route('owner.property.index') }}" class="form-btn btn-back">
            <span class="btn-icon">↩️</span> Back to List
        </a>
    </div>
</div>
<!-- Select2 CSS/JS for country search -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#country').select2({
        width: '100%',
        placeholder: 'Search country...'
    });
});
</script>
@endsection
