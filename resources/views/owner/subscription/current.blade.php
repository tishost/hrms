@extends('layouts.owner')

@section('title', 'Current Subscription')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">
        <div class="content-card">
                <div class="card-header">
                    <h4 class="card-title">📊 Current Subscription Plan</h4>
                </div>
                <div class="card-body">
                    @if($subscription && $plan)
                        <div class="dashboard-grid">
                            <!-- Current Plan Details -->
                            <div class="dashboard-grid-item">
                                                                <div class="plan-card primary">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $plan->name }} Plan</h5>
                                        <p class="card-text">{{ $plan->description }}</p>
                                        <div class="plan-details">
                                            <div class="plan-detail-item">
                                                <strong>Status:</strong>
                                                <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </div>
                                            <div class="plan-detail-item">
                                                <strong>Price:</strong> ৳{{ number_format($plan->price, 2) }}/year
                                            </div>
                                        </div>
                                        @if($subscription->start_date && $subscription->end_date)
                                            <div class="plan-details">
                                                <div class="plan-detail-item">
                                                    <strong>Start Date:</strong> {{ $subscription->start_date->format('M d, Y') }}
                                                </div>
                                                <div class="plan-detail-item">
                                                    <strong>End Date:</strong> {{ $subscription->end_date->format('M d, Y') }}
                                                </div>
                                            </div>
                                            @if($subscription->daysUntilExpiry() !== null)
                                                <div class="plan-details">
                                                    <div class="plan-detail-item">
                                                        <strong>Days Remaining:</strong>
                                                        <span class="badge bg-{{ $subscription->daysUntilExpiry() > 30 ? 'success' : ($subscription->daysUntilExpiry() > 7 ? 'warning' : 'danger') }}">
                                                            {{ $subscription->daysUntilExpiry() }} days
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Plan Features -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">📋 Plan Features</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-check text-success"></i> Properties: {{ $plan->properties_limit == -1 ? 'Unlimited' : $plan->properties_limit }}</li>
                                                    <li><i class="fas fa-check text-success"></i> Units: {{ $plan->units_limit == -1 ? 'Unlimited' : $plan->units_limit }}</li>
                                                    <li><i class="fas fa-check text-success"></i> Tenants: {{ $plan->tenants_limit == -1 ? 'Unlimited' : $plan->tenants_limit }}</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    <li><i class="fas fa-{{ $plan->sms_notification ? 'check text-success' : 'times text-danger' }}"></i> SMS Notifications</li>
                                                    <li><i class="fas fa-check text-success"></i> Email Support</li>
                                                    <li><i class="fas fa-check text-success"></i> 24/7 Access</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Statistics -->
                            <div class="dashboard-grid-item">
                                <div class="content-card">
                                    <div class="card-header">
                                        <h6 class="mb-0">📈 Usage Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong>Properties:</strong> {{ $propertiesCount }}
                                            @if($plan->properties_limit != -1)
                                                / {{ $plan->properties_limit }}
                                                <div class="progress mt-1">
                                                    <div class="progress-bar" style="width: {{ min(100, ($propertiesCount / $plan->properties_limit) * 100) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <strong>Units:</strong> {{ $unitsCount }}
                                            @if($plan->units_limit != -1)
                                                / {{ $plan->units_limit }}
                                                <div class="progress mt-1">
                                                    <div class="progress-bar" style="width: {{ min(100, ($unitsCount / $plan->units_limit) * 100) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <strong>Tenants:</strong> {{ $tenantsCount }}
                                            @if($plan->tenants_limit != -1)
                                                / {{ $plan->tenants_limit }}
                                                <div class="progress mt-1">
                                                    <div class="progress-bar" style="width: {{ min(100, ($tenantsCount / $plan->tenants_limit) * 100) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <h6 class="mb-3">Actions</h6>
                                        <a href="{{ route('owner.subscription.plans') }}" class="btn btn-primary btn-block mb-2">
                                            <i class="fas fa-shopping-cart"></i> Upgrade Plan
                                        </a>
                                        <a href="{{ route('owner.subscription.billing') }}" class="btn btn-info btn-block mb-2">
                                            <i class="fas fa-file-invoice"></i> Billing History
                                        </a>
                                        @if($subscription->status === 'pending')
                                            <a href="{{ route('owner.subscription.payment') }}" class="btn btn-warning btn-block">
                                                <i class="fas fa-credit-card"></i> Complete Payment
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h4>No Active Subscription</h4>
                            <p class="text-muted">You don't have an active subscription plan.</p>
                            <a href="{{ route('owner.subscription.plans') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Choose a Plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
