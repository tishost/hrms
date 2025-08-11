@extends('layouts.admin')

@section('title', 'Edit Charge')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Charge: {{ $charge->label }}
                        </h4>
                        <a href="{{ route('admin.charges.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Charges
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.charges.update', $charge) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="label" class="form-label">
                                <i class="fas fa-tag me-1"></i>
                                Charge Label <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('label') is-invalid @enderror" 
                                   id="label" 
                                   name="label" 
                                   value="{{ old('label', $charge->label) }}" 
                                   placeholder="e.g., Cleaning Fee, Maintenance Charge, etc."
                                   required 
                                   maxlength="255">
                            @error('label')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter a descriptive name for this charge that tenants will see.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="form-label">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Amount (৳) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount', $charge->amount) }}" 
                                       placeholder="0.00"
                                       step="0.01" 
                                       min="0" 
                                       max="999999.99"
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Enter the amount in Bangladeshi Taka (৳). Maximum: ৳999,999.99
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Current Status
                            </label>
                            <div class="d-flex align-items-center">
                                @if($charge->is_active)
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Active
                                    </span>
                                    <small class="text-muted">
                                        This charge is currently active and visible to tenants.
                                    </small>
                                @else
                                    <span class="badge bg-secondary me-2">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Inactive
                                    </span>
                                    <small class="text-muted">
                                        This charge is currently inactive and hidden from tenants.
                                    </small>
                                @endif
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                You can change the status from the charges list using the toggle button.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.charges.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Charge
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Charge Information Card -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Charge Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Created:</strong>
                                <span class="text-muted">{{ $charge->formatted_created_at_display }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>Last Updated:</strong>
                                <span class="text-muted">{{ $charge->formatted_updated_at }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Charge ID:</strong>
                                <span class="text-muted">#{{ $charge->id }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>Current Amount:</strong>
                                <span class="badge bg-success">{{ $charge->formatted_amount }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.charges.toggle-status', $charge) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" 
                                    class="btn btn-{{ $charge->is_active ? 'outline-warning' : 'outline-success' }} w-100"
                                    onclick="return confirm('Are you sure you want to {{ $charge->is_active ? 'deactivate' : 'activate' }} this charge?')">
                                <i class="fas fa-{{ $charge->is_active ? 'pause' : 'play' }} me-1"></i>
                                {{ $charge->is_active ? 'Deactivate' : 'Activate' }} Charge
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.charges.destroy', $charge) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this charge? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i>
                                Delete Charge
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-focus on label field
    document.getElementById('label').focus();

    // Real-time validation feedback
    document.getElementById('label').addEventListener('input', function() {
        const label = this.value.trim();
        const submitBtn = document.querySelector('button[type="submit"]');
        
        if (label.length > 0 && label.length <= 255) {
            this.classList.remove('is-invalid');
            submitBtn.disabled = false;
        } else {
            this.classList.add('is-invalid');
            submitBtn.disabled = true;
        }
    });

    document.getElementById('amount').addEventListener('input', function() {
        const amount = parseFloat(this.value);
        const submitBtn = document.querySelector('button[type="submit"]');
        
        if (amount >= 0 && amount <= 999999.99) {
            this.classList.remove('is-invalid');
            submitBtn.disabled = false;
        } else {
            this.classList.add('is-invalid');
            submitBtn.disabled = true;
        }
    });
</script>
@endpush
