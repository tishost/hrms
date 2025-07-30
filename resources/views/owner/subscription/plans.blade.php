@extends('layouts.owner')

@section('title', 'Available Plans')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">
        <div class="content-card">
                <div class="card-header">
                    <h4 class="card-title">🛒 Available Subscription Plans</h4>
                </div>
                <div class="card-body">
                    @if($currentSubscription && $currentSubscription->isActive())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            You currently have an active subscription. You can upgrade to a higher plan once your current subscription expires.
                        </div>
                    @endif

                    <div class="plans-grid">
                        @foreach($plans as $plan)
                            <div class="plan-item {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'current-plan' : '' }} {{ strtolower($plan->name) }}">
                                <div class="plan-header">
                                    @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                                        <div class="plan-badge">
                                            <span class="badge badge-primary">Current Plan</span>
                                        </div>
                                    @endif
                                    <h3 class="plan-title">{{ $plan->name }}</h3>
                                    <div class="plan-price">
                                        <span class="price-amount">৳{{ number_format($plan->price, 0) }}</span>
                                        <span class="price-period">/year</span>
                                    </div>
                                </div>
                                <div class="plan-description">
                                    <p>{{ $plan->description }}</p>
                                </div>
                                <div class="plan-features">
                                    <ul class="feature-list">
                                        <li class="feature-item">
                                            <i class="fas fa-building text-success"></i>
                                            <span class="feature-label">Properties:</span>
                                            <span class="feature-value">{{ $plan->properties_limit == -1 ? 'Unlimited' : $plan->properties_limit }}</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-home text-success"></i>
                                            <span class="feature-label">Units:</span>
                                            <span class="feature-value">{{ $plan->units_limit == -1 ? 'Unlimited' : $plan->units_limit }}</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-users text-success"></i>
                                            <span class="feature-label">Tenants:</span>
                                            <span class="feature-value">{{ $plan->tenants_limit == -1 ? 'Unlimited' : $plan->tenants_limit }}</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-{{ $plan->sms_notification ? 'check' : 'times' }} {{ $plan->sms_notification ? 'text-success' : 'text-danger' }}"></i>
                                            <span class="feature-label">SMS Notifications:</span>
                                            <span class="feature-value">{{ $plan->sms_notification ? 'Included' : 'Not Included' }}</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-envelope text-success"></i>
                                            <span class="feature-label">Email Support:</span>
                                            <span class="feature-value">Included</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-clock text-success"></i>
                                            <span class="feature-label">24/7 Access:</span>
                                            <span class="feature-value">Yes</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="plan-action">
                                    @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                                        <button class="btn btn-secondary btn-block" disabled>
                                            <i class="fas fa-check"></i> Current Plan
                                        </button>
                                    @elseif($currentSubscription && $currentSubscription->isActive())
                                        <button class="btn btn-info btn-block" disabled>
                                            <i class="fas fa-clock"></i> Upgrade After Expiry
                                        </button>
                                    @else
                                        <form action="{{ route('owner.subscription.purchase') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-shopping-cart"></i>
                                                {{ $plan->price > 0 ? 'Purchase Plan' : 'Activate Free Plan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Plan Comparison -->
                    <div class="comparison-section">
                        <div class="content-card">
                                <div class="card-header">
                                    <h5 class="mb-0">📊 Plan Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Feature</th>
                                                    @foreach($plans as $plan)
                                                        <th class="text-center">{{ $plan->name }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Price (Yearly)</strong></td>
                                                    @foreach($plans as $plan)
                                                        <td class="text-center">৳{{ number_format($plan->price, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Properties</strong></td>
                                                    @foreach($plans as $plan)
                                                        <td class="text-center">{{ $plan->properties_limit == -1 ? 'Unlimited' : $plan->properties_limit }}</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Units</strong></td>
                                                    @foreach($plans as $plan)
                                                        <td class="text-center">{{ $plan->units_limit == -1 ? 'Unlimited' : $plan->units_limit }}</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Tenants</strong></td>
                                                    @foreach($plans as $plan)
                                                        <td class="text-center">{{ $plan->tenants_limit == -1 ? 'Unlimited' : $plan->tenants_limit }}</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>SMS Notifications</strong></td>
                                                    @foreach($plans as $plan)
                                                        <td class="text-center">
                                                            <i class="fas fa-{{ $plan->sms_notification ? 'check text-success' : 'times text-danger' }}"></i>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td><strong>Email Support</strong></td>
                                                    @foreach($plans as $plan)
                                                        <td class="text-center">
                                                            <i class="fas fa-check text-success"></i>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
