@extends('layouts.admin')

@section('title', 'Edit Plan - ' . $plan->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Plan</h1>
        <a href="{{ route('admin.plans.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Plans
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Plan: {{ $plan->name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Plan Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price (৳)</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price" value="{{ old('price', $plan->price) }}" min="0" step="0.01" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                    <select class="form-control @error('billing_cycle') is-invalid @enderror" 
                                            id="billing_cycle" name="billing_cycle" required>
                                        <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle ?? 'monthly') == 'monthly' ? 'selected' : '' }}>
                                            Monthly (30 days)
                                        </option>
                                        <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle ?? 'monthly') == 'yearly' ? 'selected' : '' }}>
                                            Yearly (365 days)
                                        </option>
                                        <option value="lifetime" {{ old('billing_cycle', $plan->billing_cycle ?? 'monthly') == 'lifetime' ? 'selected' : '' }}>
                                            Lifetime (100 years)
                                        </option>
                                    </select>
                                    @error('billing_cycle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="properties_limit" class="form-label">Properties Limit</label>
                                    <input type="number" class="form-control @error('properties_limit') is-invalid @enderror"
                                           id="properties_limit" name="properties_limit"
                                           value="{{ old('properties_limit', $plan->properties_limit) }}" required>
                                    <small class="form-text text-muted">Use -1 for unlimited</small>
                                    @error('properties_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="units_limit" class="form-label">Units Limit</label>
                                    <input type="number" class="form-control @error('units_limit') is-invalid @enderror"
                                           id="units_limit" name="units_limit"
                                           value="{{ old('units_limit', $plan->units_limit) }}" required>
                                    <small class="form-text text-muted">Use -1 for unlimited</small>
                                    @error('units_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tenants_limit" class="form-label">Tenants Limit</label>
                                    <input type="number" class="form-control @error('tenants_limit') is-invalid @enderror"
                                           id="tenants_limit" name="tenants_limit"
                                           value="{{ old('tenants_limit', $plan->tenants_limit) }}" required>
                                    <small class="form-text text-muted">Use -1 for unlimited</small>
                                    @error('tenants_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="sms_notification"
                                               name="sms_notification" value="1"
                                               {{ old('sms_notification', $plan->sms_notification) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sms_notification">
                                            SMS Notifications
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="sms_credit" class="form-label">SMS Credits</label>
                                    <input type="number" class="form-control @error('sms_credit') is-invalid @enderror"
                                           id="sms_credit" name="sms_credit"
                                           value="{{ old('sms_credit', $plan->sms_credit) }}" min="0">
                                    <small class="form-text text-muted">Yearly SMS credits (1 credit = 1 SMS)</small>
                                    @error('sms_credit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active"
                                               name="is_active" value="1"
                                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Plan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_popular"
                                               name="is_popular" value="1"
                                               {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_popular">
                                            Mark as Popular
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                                                 <div class="mb-3">
                             <label for="features" class="form-label">Features (One per line)</label>
                             <textarea class="form-control @error('features') is-invalid @enderror"
                                       id="features" name="features_text" rows="6"
                                       placeholder="Enter features, one per line...">{{ old('features_text', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                             <small class="form-text text-muted">Enter each feature on a new line</small>
                             @error('features')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>

                         <div class="mb-3">
                             <label for="features_css" class="form-label">Features CSS Classes (One per line)</label>
                             <textarea class="form-control @error('features_css') is-invalid @enderror"
                                       id="features_css" name="features_css_text" rows="4"
                                       placeholder="Enter CSS classes for features, one per line...">{{ old('features_css_text', is_array($plan->features_css) ? implode("\n", $plan->features_css) : '') }}</textarea>
                                                           <small class="form-text text-muted">Enter CSS classes for each feature (e.g., fas fa-check text-green-500 for available, fas fa-times text-red-500 for unavailable)</small>
                             @error('features_css')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Plan
                            </button>
                            <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Plan Preview</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="text-primary">{{ $plan->name }}</h3>
                        <h2 class="text-success">
                            @if($plan->price == 0)
                                Free
                            @else
                                {{ $plan->formatted_price_with_cycle ?? '৳' . number_format($plan->price) }}
                            @endif
                        </h2>
                        @if($plan->billing_cycle)
                            <small class="text-muted">{{ $plan->billing_cycle_text }}</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6>Limits:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Properties:</strong>
                                {{ $plan->properties_limit == -1 ? 'Unlimited' : $plan->properties_limit }}
                            </li>
                            <li><strong>Units:</strong>
                                {{ $plan->units_limit == -1 ? 'Unlimited' : $plan->units_limit }}
                            </li>
                            <li><strong>Tenants:</strong>
                                {{ $plan->tenants_limit == -1 ? 'Unlimited' : $plan->tenants_limit }}
                            </li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6>Features:</h6>
                        <ul class="list-unstyled">
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                                         <div class="mb-3">
                         <h6>Status:</h6>
                         <div>
                             @if($plan->is_active)
                                 <span class="badge bg-success">Active</span>
                             @else
                                 <span class="badge bg-danger">Inactive</span>
                             @endif

                             @if($plan->is_popular)
                                 <span class="badge bg-warning ms-2">Popular</span>
                             @endif

                             @if($plan->sms_notification)
                                 <span class="badge bg-info ms-2">SMS Enabled</span>
                                 @if($plan->sms_credit > 0)
                                     <span class="badge bg-primary ms-2">{{ number_format($plan->sms_credit) }} Credits</span>
                                 @endif
                             @endif
                         </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// No JavaScript needed - features are handled server-side
</script>
@endpush
@endsection
