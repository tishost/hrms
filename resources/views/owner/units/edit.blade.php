@extends('layouts.owner')

@section('title', 'Edit Unit')

@section('content')
<div class="container">
    <div class="section-header" style="margin-bottom:18px;">
        <h4 class="mb-0">✏️ Edit Unit</h4>
    </div>
    <form action="{{ route('owner.units.update', $unit->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label class="form-label">Property</label>
            <input type="text" class="form-input" value="{{ $unit->property->name ?? '-' }}" readonly>
        </div>
        <div class="form-group mb-3">
            <label class="form-label">Unit Name</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $unit->name) }}" required>
            @error('name')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label class="form-label">Rent (৳)</label>
            <input type="number" name="rent" class="form-input" value="{{ old('rent', $unit->rent) }}" required>
            @error('rent')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label class="form-label">Utility Charges</label>
            <div id="charges_wrapper">
                @if($unit->charges && count($unit->charges))
                    @foreach($unit->charges as $i => $charge)
                        <div class="d-flex mb-2 charge-group" style="display:flex; align-items:center; gap:10px; width:100%; min-width:0; margin-bottom:10px">
                            <input type="text" name="charges[{{ $i }}][label]" class="form-input" value="{{ old('charges.' . $i . '.label', $charge->label) }}" placeholder="Label" required style="flex:1 1 0; min-width:0; height:38px;">
                            @error('charges.' . $i . '.label')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                            <input type="number" name="charges[{{ $i }}][amount]" class="form-input" value="{{ old('charges.' . $i . '.amount', $charge->amount) }}" placeholder="৳" required style="flex:1 1 0; min-width:0; height:38px;">
                            @error('charges.' . $i . '.amount')
                                <div class="input-error">{{ $message }}</div>
                            @enderror
                            <button type="button" class="form-btn btn-remove btn-sm custom-close-btn" onclick="removeCharge(this)" style="height:38px; width:38px; min-width:38px; flex:0 0 38px;">
                                <span class="btn-icon" style="display:flex;align-items:center;justify-content:center;">
                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                                        <rect width="22" height="22" rx="7" fill="none"/>
                                        <path d="M6 6L16 16M16 6L6 16" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="form-btn btn-add btn-sm" onclick="addCharge()" style="margin-top:8px;"><span class="btn-icon">➕</span> Add Charge</button>
        </div>
        <script>
        function addCharge() {
            const wrapper = document.getElementById('charges_wrapper');
            const index = wrapper.children.length;
            const html = `
                <div class=\"d-flex mb-2 charge-group\" style=\"display:flex; align-items:center; gap:10px; width:100%; min-width:0;\">
                    <input type=\"text\" name=\"charges[${index}][label]\" class=\"form-input\" placeholder=\"Label\" required style=\"flex:1 1 0; min-width:0; height:38px;\">
                    <input type=\"number\" name=\"charges[${index}][amount]\" class=\"form-input\" placeholder=\"৳\" required style=\"flex:1 1 0; min-width:0; height:38px;\">
                    <button type=\"button\" class=\"form-btn btn-remove btn-sm custom-close-btn\" onclick=\"removeCharge(this)\" style=\"height:38px; width:38px; min-width:38px; flex:0 0 38px;\"><span class=\"btn-icon\" style=\"display:flex;align-items:center;justify-content:center;\"><svg width=\"22\" height=\"22\" viewBox=\"0 0 22 22\" fill=\"none\"><rect width=\"22\" height=\"22\" rx=\"7\" fill=\"none\"/><path d=\"M6 6L16 16M16 6L6 16\" stroke=\"white\" stroke-width=\"2.5\" stroke-linecap=\"round\"/></svg></span></button>
                </div>`;
            wrapper.insertAdjacentHTML('beforeend', html);
        }
        function removeCharge(btn) {
            btn.closest('.charge-group').remove();
        }
        </script>
        <button type="submit" class="form-btn btn-save">💾 Save Changes</button>
        <a href="{{ route('owner.units.index') }}" class="form-btn btn-cancel" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection
