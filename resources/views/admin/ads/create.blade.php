@extends('admin.layouts.app')

@section('title', 'Create New Ad')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Ad</h3>
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary float-end">
                        <i class="fas fa-arrow-left"></i> Back to Ads
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" id="createAdForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Ad Title *</label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Optional description for the ad">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="url" class="form-label">URL</label>
                                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                                   id="url" name="url" value="{{ old('url') }}" 
                                                   placeholder="https://example.com">
                                            @error('url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty if this is just an informational ad</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Upload -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Ad Image *</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Upload Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                   id="image" name="image" accept="image/*" required>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                Supported formats: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB.
                                            </div>
                                        </div>

                                        <div id="imagePreview" class="d-none">
                                            <img id="previewImg" src="" alt="Preview" 
                                                 class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Range -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Display Period *</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="start_date" class="form-label">Start Date *</label>
                                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                                    @error('start_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="end_date" class="form-label">End Date *</label>
                                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                                    @error('end_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Settings -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_active" 
                                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active
                                                </label>
                                            </div>
                                            <div class="form-text">Enable or disable this ad</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="display_order" class="form-label">Display Order</label>
                                            <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                                                   id="display_order" name="display_order" value="{{ old('display_order', 0) }}" 
                                                   min="0" step="1">
                                            @error('display_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Lower numbers appear first</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Display Locations -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Display Locations</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="show_on_owner_dashboard" 
                                                       name="show_on_owner_dashboard" value="1" {{ old('show_on_owner_dashboard') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_on_owner_dashboard">
                                                    Owner Dashboard
                                                </label>
                                            </div>
                                            <div class="form-text">Show this ad on owner dashboards</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="show_on_tenant_dashboard" 
                                                       name="show_on_tenant_dashboard" value="1" {{ old('show_on_tenant_dashboard') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_on_tenant_dashboard">
                                                    Tenant Dashboard
                                                </label>
                                            </div>
                                            <div class="form-text">Show this ad on tenant dashboards</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Quick Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="adPreview" class="text-center">
                                            <div class="text-muted">
                                                <i class="fas fa-image fa-3x mb-2"></i>
                                                <p class="mb-0">Upload an image to see preview</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create Ad
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum start date to today
    const today = new Date().toISOString().split('T')[0];
    $('#start_date').attr('min', today);
    
    // Update end date minimum when start date changes
    $('#start_date').on('change', function() {
        const startDate = $(this).val();
        if (startDate) {
            $('#end_date').attr('min', startDate);
        }
    });

    // Image preview
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').removeClass('d-none');
                
                // Update ad preview
                updateAdPreview();
            };
            reader.readAsDataURL(file);
        }
    });

    // Update ad preview when form fields change
    $('#title, #description, #url').on('input', updateAdPreview);
    $('#show_on_owner_dashboard, #show_on_tenant_dashboard').on('change', updateAdPreview);

    function updateAdPreview() {
        const title = $('#title').val() || 'Ad Title';
        const description = $('#description').val() || 'No description provided';
        const url = $('#url').val();
        const showOnOwner = $('#show_on_owner_dashboard').is(':checked');
        const showOnTenant = $('#show_on_tenant_dashboard').is(':checked');

        let previewHtml = `
            <div class="border rounded p-3">
                <h6 class="mb-2">${title}</h6>
                <p class="small text-muted mb-2">${description}</p>
        `;

        if (url) {
            previewHtml += `<p class="small text-primary mb-2">🔗 Clickable link</p>`;
        }

        if (showOnOwner || showOnTenant) {
            previewHtml += `<div class="d-flex gap-1">`;
            if (showOnOwner) {
                previewHtml += `<span class="badge bg-info">Owner</span>`;
            }
            if (showOnTenant) {
                previewHtml += `<span class="badge bg-success">Tenant</span>`;
            }
            previewHtml += `</div>`;
        } else {
            previewHtml += `<p class="small text-warning">⚠️ No display location selected</p>`;
        }

        previewHtml += `</div>`;

        $('#adPreview').html(previewHtml);
    }

    // Form validation
    $('#createAdForm').on('submit', function(e) {
        const showOnOwner = $('#show_on_owner_dashboard').is(':checked');
        const showOnTenant = $('#show_on_tenant_dashboard').is(':checked');

        if (!showOnOwner && !showOnTenant) {
            e.preventDefault();
            alert('Please select at least one display location (Owner Dashboard or Tenant Dashboard)');
            return false;
        }
    });

    // Initialize preview
    updateAdPreview();
});
</script>
@endpush
