@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Settings</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="default_building_limit">
                                        <i class="fas fa-building"></i> Default Building Limit (per Owner)
                                    </label>
                                    <input type="number" class="form-control" id="default_building_limit"
                                           name="default_building_limit"
                                           value="{{ $settings['default_building_limit'] ?? 5 }}"
                                           min="1" max="50" required>
                                    <small class="form-text text-muted">Maximum number of buildings an owner can create</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="default_unit_limit">
                                        <i class="fas fa-home"></i> Default Unit Limit (per Building)
                                    </label>
                                    <input type="number" class="form-control" id="default_unit_limit"
                                           name="default_unit_limit"
                                           value="{{ $settings['default_unit_limit'] ?? 10 }}"
                                           min="1" max="100" required>
                                    <small class="form-text text-muted">Maximum number of units per building</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.otp-settings.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-mobile-alt"></i> OTP Settings
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.owners.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-users"></i> Owner Management
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-success btn-block">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('form').submit(function(e) {
        const buildingLimit = parseInt($('#default_building_limit').val());
        const unitLimit = parseInt($('#default_unit_limit').val());

        if (buildingLimit < 1 || buildingLimit > 50) {
            alert('Building limit must be between 1 and 50');
            e.preventDefault();
            return false;
        }

        if (unitLimit < 1 || unitLimit > 100) {
            alert('Unit limit must be between 1 and 100');
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
