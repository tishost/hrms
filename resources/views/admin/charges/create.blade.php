@extends('layouts.admin')

@section('title', 'Add New Charge')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add New Charge
                        </h4>
                        <a href="{{ route('admin.charges.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Charges
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.charges.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="label" class="form-label">
                                <i class="fas fa-tag me-1"></i>
                                Charge Label <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('label') is-invalid @enderror" 
                                   id="label" 
                                   name="label" 
                                   value="{{ old('label') }}" 
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
                                       value="{{ old('amount') }}" 
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

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.charges.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Charge
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Help & Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-lightbulb me-1"></i>
                                Tips for Good Labels:
                            </h6>
                            <ul class="small text-muted">
                                <li>Use clear, descriptive names</li>
                                <li>Keep it short but informative</li>
                                <li>Examples: "Cleaning Fee", "Maintenance"</li>
                                <li>Avoid abbreviations</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Important Notes:
                            </h6>
                            <ul class="small text-muted">
                                <li>Labels must be unique</li>
                                <li>Amount cannot be negative</li>
                                <li>Charges are active by default</li>
                                <li>You can edit or deactivate later</li>
                            </ul>
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
