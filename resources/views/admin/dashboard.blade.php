<!-- filepath: resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.owners.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-users fa-sm text-white-50"></i> Manage Owners
            </a>
            <a href="{{ route('admin.plans.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cube fa-sm text-white-50"></i> Manage Plans
            </a>
            <a href="{{ route('admin.subscriptions') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-credit-card fa-sm text-white-50"></i> View Subscriptions
            </a>
                            <a href="{{ route('admin.billing.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-file-invoice-dollar fa-sm text-white-50"></i> View Billing
            </a>
            <a href="{{ route('admin.settings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-cog fa-sm text-white-50"></i> System Settings
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Owners -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Owners</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalOwners ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeSubscriptions ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Monthly Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($monthlyRevenue ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingPayments ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expired Subscriptions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Expired Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($expiredSubscriptions ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Plan Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="planChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($planDistribution as $plan)
                        <span class="mr-2">
                            <i class="fas fa-circle text-{{ $loop->index == 0 ? 'primary' : ($loop->index == 1 ? 'success' : 'info') }}"></i> {{ $plan->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <!-- Recent Subscriptions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Subscriptions</h6>
                </div>
                <div class="card-body">
                    @foreach($recentSubscriptions as $subscription)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <span class="text-white font-weight-bold">{{ substr($subscription->owner->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ $subscription->owner->name }}</h6>
                            <small class="text-muted">{{ $subscription->plan->name }} Plan</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ $subscription->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
                </div>
                <div class="card-body">
                    @foreach($recentPayments as $payment)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ $payment->owner->name }}</h6>
                            <small class="text-muted">{{ $payment->formatted_amount }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $payment->status_badge }}">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Status -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card"></i> Payment Methods Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $paymentStatus = \App\Helpers\SettingHelper::getPaymentMethodsStatus();
                        @endphp

                        @foreach($paymentStatus as $code => $method)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-{{ $method['is_active'] ? 'success' : 'secondary' }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-{{ $code === 'bkash' ? 'mobile-alt' : ($code === 'nagad' ? 'wallet' : 'university') }} fa-2x text-{{ $method['is_active'] ? 'success' : 'secondary' }}"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $method['name'] }}</h6>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($method['is_active'])
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif

                                                @if($method['sandbox_mode'])
                                                    <span class="badge bg-warning">Sandbox</span>
                                                @else
                                                    <span class="badge bg-info">Live</span>
                                                @endif

                                                @if($method['configured'])
                                                    <span class="badge bg-success">Configured</span>
                                                @else
                                                    <span class="badge bg-danger">Not Configured</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">Fee: {{ $method['transaction_fee'] }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.settings.payment-gateway') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog"></i> Configure Payment Methods
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json(collect($monthlyRevenueData)->pluck('month')),
        datasets: [{
            label: 'Revenue',
            data: @json(collect($monthlyRevenueData)->pluck('revenue')),
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Plan Distribution Chart
const planCtx = document.getElementById('planChart').getContext('2d');
new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: @json($planDistribution->pluck('name')),
        datasets: [{
            data: @json($planDistribution->pluck('subscriptions_count')),
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
@endsection
