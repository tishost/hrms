@extends('layouts.owner')

@section('content')
<div class="form-section">
    <div class="form-header">
        <h4 class="form-title"><span class="form-title-icon">🏢</span> Add New Building</h4>
    </div>
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif
    <form action="{{ route('owner.property.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Building Name</label>
            <input type="text" name="name" id="name"
                   value="{{ old('name') }}"
                   class="form-input @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="type" class="form-label">Property Type</label>
            <select name="type" id="type" class="form-select custom-select @error('type') is-invalid @enderror">
                <option value="">Select Type</option>
                <option value="residential" {{ old('type') == 'residential' ? 'selected' : '' }}>Residential</option>
                <option value="commercial" {{ old('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
            </select>
            @error('type')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="address" class="form-label">Address</label>
            <input type="text" name="address" id="address"
                   value="{{ old('address') }}"
                   class="form-input address-input @error('address') is-invalid @enderror">
            @error('address')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="country" class="form-label">Country</label>
            <select name="country" id="country" class="form-select custom-select @error('country') is-invalid @enderror" required>
                <option value="">Select Country</option>
                @foreach ($countries as $country)
                    <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
            @error('country')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <input type="hidden" name="unit_limit" value="5">
        <div class="mt-4">
            <button type="submit" class="form-btn btn-save"><span class="btn-icon">💾</span> Save & Next</button>
            <a href="{{ route('owner.property.index') }}" class="form-btn btn-back"><span class="btn-icon">↩️</span> Back to List</a>
        </div>
    </form>
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
