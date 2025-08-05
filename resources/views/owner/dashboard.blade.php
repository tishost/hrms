@extends('layouts.owner')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Dashboard')

@section('content')
<!-- Debug Info - Remove this after fixing the issue -->
@if(config('app.debug'))
<div class="alert alert-info mb-4">
    <strong>Debug Info:</strong>
    <ul class="mb-0">
        <li>Current Route: {{ request()->route()->getName() }}</li>
        <li>User ID: {{ auth()->id() }}</li>
        <li>User Name: {{ auth()->user()->name ?? 'N/A' }}</li>
        <li>User Roles: {{ auth()->user()->roles->pluck('name')->implode(', ') }}</li>
        <li>Is Owner: {{ auth()->user()->hasRole('owner') ? 'Yes' : 'No' }}</li>
        <li>Owner ID: {{ auth()->user()->owner->id ?? 'N/A' }}</li>
        <li>Layout: owner.blade.php</li>
    </ul>
</div>
@endif

<div class="page-header">
    <div class="page-title">
        <h1>Dashboard Overview</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ul>
    </div>
    <div class="chart-actions">
        <select>
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
            <option selected>Last 90 Days</option>
        </select>
    </div>
</div>
<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Tenant</div>
                <div class="stat-value">{{ $tenantCount }}</div>
            </div>
            <div class="stat-icon orders">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Property</div>
                <div class="stat-value">{{ $buildingCount }}</div>
            </div>
            <div class="stat-icon revenue">
                <i class="fas fa-building"></i>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Unit</div>
                <div class="stat-value">{{ $unitCount }}</div>
            </div>
            <div class="stat-icon sales">
                <i class="fas fa-door-open"></i>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">SMS Credits</div>
                <div class="stat-value">{{ $smsCredits - $usedSmsCredits }}</div>
                <small class="text-muted">of {{ $smsCredits }} total</small>
            </div>
            <div class="stat-icon sms">
                <i class="fas fa-sms"></i>
            </div>
        </div>
    </div>
</div>
<!-- Package Limits Widget -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie"></i> Package Usage
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $packageLimitService = new \App\Services\PackageLimitService();
                        $owner = Auth::user()->owner;
                        $stats = $packageLimitService->getUsageStats($owner);
                    @endphp

                    @foreach(['properties', 'units', 'tenants', 'sms'] as $type)
                        @if(isset($stats[$type]))
                            @php $stat = $stats[$type]; @endphp
                            <div class="col-md-3 mb-3">
                                <div class="usage-card">
                                    <div class="usage-header">
                                        <h6>{{ ucfirst($type) }}</h6>
                                        <span class="badge badge-{{ $stat['color'] }}">
                                            {{ $stat['current'] }}/{{ $stat['max'] }}
                                        </span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-{{ $stat['color'] }}"
                                             style="width: {{ $stat['percentage'] }}%">
                                            {{ round($stat['percentage']) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $stat['remaining'] }} remaining
                                        @if($stat['reset_date'])
                                            <br>Resets: {{ $stat['reset_date']->format('M d, Y') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @php
                    $suggestions = $packageLimitService->getUpgradeSuggestions($owner);
                @endphp

                @if(count($suggestions) > 0)
                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-exclamation-triangle"></i> Upgrade Suggestions</h6>
                        <ul class="mb-0">
                            @foreach($suggestions as $suggestion)
                                <li>{{ ucfirst($suggestion['type']) }}: {{ $suggestion['percentage'] }}% used</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('owner.subscription.plans') }}" class="btn btn-sm btn-warning mt-2">
                            <i class="fas fa-arrow-up"></i> Upgrade Plan
                        </a>
                    </div>
                @endif
                
                <!-- SMS Credits Management -->
                @if($subscription && $subscription->sms_credits > 0)
                    <div class="alert alert-info mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6><i class="fas fa-sms"></i> SMS Credits</h6>
                                <p class="mb-0">
                                    <strong>{{ $smsCredits - $usedSmsCredits }}</strong> credits remaining 
                                    out of <strong>{{ $smsCredits }}</strong> total credits
                                </p>
                                @if(($smsCredits - $usedSmsCredits) < 50)
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> Low SMS credits! Consider purchasing more.
                                    </small>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSmsCreditsModal">
                                    <i class="fas fa-plus"></i> Add Credits
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Charts Row -->
<div class="charts-row">
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Website Visitors</h3>
            <div class="chart-actions">
                <select>
                    <option>Daily</option>
                    <option selected>Weekly</option>
                    <option>Monthly</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="lineChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Customer Segments</h3>
            <div class="chart-actions">
                <select>
                    <option selected>By Region</option>
                    <option>By Age</option>
                    <option>By Gender</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="donutChart"></canvas>
        </div>
    </div>
</div>
<!-- Bottom Stats -->
<div class="bottom-stats">
    <div class="bottom-stat-card">
        <div class="bottom-stat-icon followers">
            <i class="fas fa-users"></i>
        </div>
        <div class="bottom-stat-content">
            <h4>Total Followers</h4>
            <p>24,589</p>
        </div>
    </div>
    <div class="bottom-stat-card">
        <div class="bottom-stat-icon subscribers">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="bottom-stat-content">
            <h4>Email Subscribers</h4>
            <p>12,847</p>
        </div>
    </div>
</div>
<!-- Add SMS Credits Modal -->
<div class="modal fade" id="addSmsCreditsModal" tabindex="-1" aria-labelledby="addSmsCreditsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSmsCreditsModalLabel">
                    <i class="fas fa-sms"></i> Add SMS Credits
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('owner.sms.add-credits') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="credits_amount" class="form-label">Number of Credits</label>
                        <select class="form-control" id="credits_amount" name="credits_amount" required>
                            <option value="">Select amount</option>
                            <option value="50">50 Credits - ৳500</option>
                            <option value="100">100 Credits - ৳900</option>
                            <option value="200">200 Credits - ৳1600</option>
                            <option value="500">500 Credits - ৳3500</option>
                            <option value="1000">1000 Credits - ৳6000</option>
                        </select>
                        <small class="form-text text-muted">
                            Current balance: <strong>{{ $smsCredits - $usedSmsCredits }}</strong> credits
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                            <option value="card">Credit/Debit Card</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Purchase Credits
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.stat-icon.sms {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.usage-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.usage-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.usage-header h6 {
    margin: 0;
    color: #495057;
}

.progress {
    height: 8px;
    border-radius: 4px;
    margin-bottom: 8px;
}

.progress-bar {
    border-radius: 4px;
    font-size: 10px;
    line-height: 8px;
}
</style>
@endsection

@section('scripts')
<script>
// Line Chart
const lineCtx = document.getElementById('lineChart').getContext('2d');
const lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [{
            label: 'Visitors',
            data: [1200, 1900, 1700, 2100, 2400, 2200, 2600],
            borderColor: '#4361ee',
            backgroundColor: 'rgba(67, 97, 238, 0.1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { drawBorder: false } },
            x: { grid: { display: false } }
        }
    }
});
// Donut Chart
const donutCtx = document.getElementById('donutChart').getContext('2d');
const donutChart = new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: ['North America', 'Europe', 'Asia', 'South America', 'Africa', 'Oceania'],
        datasets: [{
            data: [35, 25, 20, 10, 5, 5],
            backgroundColor: [
                '#4361ee', '#3f37c9', '#4895ef', '#4cc9f0', '#f72585', '#7209b7'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'right' } },
        cutout: '70%'
    }
});
</script>
@endsection
