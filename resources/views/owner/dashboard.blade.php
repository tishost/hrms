@extends('layouts.owner')

@php
    use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Dashboard')

@section('content')


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

<!-- Financial Cards -->
<div class="stats-grid mb-4">
    <div class="stat-card financial-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">Monthly Received</div>
                <div class="stat-value text-success">৳{{ number_format($financialData['monthly_received']) }}</div>
                <small class="text-muted">{{ now()->format('F Y') }}</small>
            </div>
            <div class="stat-icon revenue">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="stat-card financial-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">Due Amount</div>
                <div class="stat-value text-danger">৳{{ number_format($financialData['due_amount']) }}</div>
                <small class="text-muted">{{ $financialData['unpaid_invoices'] }} unpaid invoices</small>
            </div>
            <div class="stat-icon orders">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="stat-card financial-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">All Time Paid</div>
                <div class="stat-value text-primary">৳{{ number_format($financialData['all_time_paid']) }}</div>
                <small class="text-muted">{{ $financialData['paid_invoices'] }} paid invoices</small>
            </div>
            <div class="stat-icon sales">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="stat-card financial-card">
        <div class="stat-header">
            <div>
                <div class="stat-title">Total Invoiced</div>
                <div class="stat-value text-info">৳{{ number_format($financialData['total_invoiced']) }}</div>
                <small class="text-muted">{{ $financialData['total_invoices'] }} total invoices</small>
            </div>
            <div class="stat-icon revenue">
                <i class="fas fa-file-invoice-dollar"></i>
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
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
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
/* Force responsive styles with high specificity */
@media (max-width: 768px) {
    .stats-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 15px !important;
        padding: 0 15px !important;
    }
    
    .stat-card {
        padding: 20px !important;
        margin-bottom: 15px !important;
        border-radius: 12px !important;
        background: white !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
    }
    
    .stat-header {
        flex-direction: column !important;
        text-align: center !important;
        gap: 15px !important;
    }
    
    .stat-icon {
        align-self: center !important;
        margin-top: 0 !important;
        width: 60px !important;
        height: 60px !important;
        font-size: 1.8rem !important;
    }
    
    .stat-value {
        font-size: 2.2rem !important;
        margin-bottom: 8px !important;
    }
    
    .stat-title {
        font-size: 1rem !important;
        margin-bottom: 8px !important;
    }
    
    /* Force Bootstrap columns to stack */
    .row .col-lg-3,
    .row .col-md-6,
    .row .col-sm-12 {
        width: 100% !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    .usage-card {
        padding: 15px !important;
        margin-bottom: 15px !important;
        border-radius: 10px !important;
    }
    
    .usage-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
        margin-bottom: 12px !important;
    }
    
    .usage-header h6 {
        font-size: 1.1rem !important;
        margin-bottom: 5px !important;
    }
    
    .usage-header .badge {
        align-self: flex-start !important;
        font-size: 0.85rem !important;
        padding: 6px 10px !important;
    }
    
    .progress {
        height: 10px !important;
        border-radius: 5px !important;
        margin-bottom: 10px !important;
    }
    
    .progress-bar {
        font-size: 10px !important;
        line-height: 10px !important;
        border-radius: 5px !important;
    }
    
    .charts-row {
        flex-direction: column !important;
        gap: 15px !important;
        padding: 0 15px !important;
    }
    
    .chart-card {
        width: 100% !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        border-radius: 12px !important;
    }
    
    .chart-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px !important;
        margin-bottom: 15px !important;
    }
    
    .chart-title {
        font-size: 1.3rem !important;
        margin-bottom: 0 !important;
    }
    
    .chart-actions select {
        width: 100% !important;
        max-width: 150px !important;
        padding: 8px 12px !important;
    }
    
    .bottom-stats {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
        padding: 0 15px !important;
    }
    
    .bottom-stat-card {
        padding: 20px !important;
        margin-bottom: 15px !important;
        border-radius: 12px !important;
    }
    
    .bottom-stat-content h4 {
        font-size: 1.1rem !important;
        margin-bottom: 8px !important;
    }
    
    .alert {
        padding: 15px !important;
        margin-bottom: 15px !important;
        border-radius: 10px !important;
    }
    
    .alert .d-flex {
        flex-direction: column !important;
        gap: 12px !important;
    }
    
    .alert h6 {
        font-size: 1.1rem !important;
        margin-bottom: 8px !important;
    }
    
    .modal-dialog {
        margin: 15px !important;
        max-width: calc(100% - 30px) !important;
    }
    
    .modal-body {
        padding: 20px !important;
    }
    
    .modal-footer {
        padding: 20px !important;
        flex-direction: column !important;
        gap: 12px !important;
    }
    
    .modal-footer .btn {
        width: 100% !important;
        padding: 12px !important;
        font-size: 1rem !important;
    }
    
    /* Card improvements */
    .card {
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .card-header {
        padding: 20px !important;
        border-bottom: 1px solid #e9ecef !important;
    }
    
    .card-body {
        padding: 20px !important;
    }
    
    .card-title {
        font-size: 1.2rem !important;
        margin-bottom: 0 !important;
    }
    
    .chart-container {
        height: 250px !important;
    }
    
    /* Page header responsive */
    .page-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 15px !important;
        padding: 15px !important;
    }
    
    .page-title h1 {
        font-size: 1.5rem !important;
        margin-bottom: 10px !important;
        line-height: 1.2 !important;
    }
    
    .breadcrumb {
        font-size: 0.9rem !important;
    }
    
    .chart-actions select {
        width: 100% !important;
        max-width: 200px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
    }
}

@media (max-width: 480px) {
    .page-title h1 {
        font-size: 1.3rem !important;
    }
    
    .stat-value {
        font-size: 1.5rem !important;
    }
    
    .stat-title {
        font-size: 0.9rem !important;
    }
    
    .chart-title {
        font-size: 1.1rem !important;
    }
    
    .usage-card {
        padding: 10px !important;
    }
    
    .progress {
        height: 6px !important;
    }
    
    .progress-bar {
        font-size: 8px !important;
        line-height: 6px !important;
    }
    
    .card {
        margin-bottom: 15px !important;
    }
    
    .card-body {
        padding: 15px !important;
    }
    
    .chart-container {
        height: 200px !important;
    }
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px !important;
    }
    
    .charts-row {
        flex-direction: column !important;
        gap: 20px !important;
    }
    
    .chart-card {
        width: 100% !important;
        margin-bottom: 20px !important;
    }
    
    .bottom-stats {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px !important;
    }
}
</style>
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

/* Responsive Styles */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .charts-row {
        flex-direction: column;
        gap: 20px;
    }
    
    .chart-card {
        width: 100%;
        margin-bottom: 20px;
    }
    
    .bottom-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
    }
    
    .page-title h1 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        line-height: 1.2;
    }
    
    .breadcrumb {
        font-size: 0.9rem;
    }
    
    .chart-actions select {
        width: 100%;
        max-width: 200px;
        padding: 8px 12px;
        border-radius: 6px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 0 15px;
    }
    
    .stat-card {
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 12px;
    }
    
    .stat-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .stat-icon {
        align-self: center;
        margin-top: 0;
        width: 60px;
        height: 60px;
        font-size: 1.8rem;
    }
    
    .stat-value {
        font-size: 2.2rem;
        margin-bottom: 8px;
    }
    
    .stat-title {
        font-size: 1rem;
        margin-bottom: 8px;
    }
    
    .usage-card {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 10px;
    }
    
    .usage-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
    }
    
    .usage-header h6 {
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    
    .usage-header .badge {
        align-self: flex-start;
        font-size: 0.85rem;
        padding: 6px 10px;
    }
    
    .progress {
        height: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .progress-bar {
        font-size: 10px;
        line-height: 10px;
        border-radius: 5px;
    }
    
    .charts-row {
        gap: 15px;
        padding: 0 15px;
    }
    
    .chart-card {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 12px;
    }
    
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .chart-title {
        font-size: 1.3rem;
        margin-bottom: 0;
    }
    
    .chart-actions select {
        width: 100%;
        max-width: 150px;
        padding: 8px 12px;
    }
    
    .bottom-stats {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 0 15px;
    }
    
    .bottom-stat-card {
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 12px;
    }
    
    .bottom-stat-content h4 {
        font-size: 1.1rem;
        margin-bottom: 8px;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 10px;
    }
    
    .alert .d-flex {
        flex-direction: column;
        gap: 12px;
    }
    
    .alert h6 {
        font-size: 1.1rem;
        margin-bottom: 8px;
    }
    
    .modal-dialog {
        margin: 15px;
        max-width: calc(100% - 30px);
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 20px;
        flex-direction: column;
        gap: 12px;
    }
    
    .modal-footer .btn {
        width: 100%;
        padding: 12px;
        font-size: 1rem;
    }
    
    /* Force Bootstrap columns to stack on mobile */
    .row .col-lg-3,
    .row .col-md-6,
    .row .col-sm-12 {
        width: 100% !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    /* Card improvements */
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .card-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .card-title {
        font-size: 1.2rem;
        margin-bottom: 0;
    }
}

@media (max-width: 480px) {
    .page-title h1 {
        font-size: 1.3rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-title {
        font-size: 0.9rem;
    }
    
    .chart-title {
        font-size: 1.1rem;
    }
    
    .usage-card {
        padding: 10px;
    }
    
    .progress {
        height: 6px;
    }
    
    .progress-bar {
        font-size: 8px;
        line-height: 6px;
    }
    
    .card {
        margin-bottom: 15px;
    }
    
    .card-body {
        padding: 15px;
    }
}

/* Ensure proper spacing on all devices */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

/* Ensure stat cards are responsive */
.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-title {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.orders {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.revenue {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-icon.sales {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.charts-row {
    display: flex;
    gap: 30px;
    margin-bottom: 30px;
}

.chart-card {
    flex: 1;
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.bottom-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

/* Ensure charts are responsive */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

@media (max-width: 768px) {
    .chart-container {
        height: 250px;
    }
}

@media (max-width: 480px) {
    .chart-container {
        height: 200px;
    }
}

/* Financial Cards Styling */
.financial-card {
    border-left: 4px solid #28a745;
    transition: all 0.3s ease;
}

.financial-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.financial-card .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0.5rem 0;
}

.financial-card .text-success {
    color: #28a745 !important;
}

.financial-card .text-danger {
    color: #dc3545 !important;
}

.financial-card .text-primary {
    color: #007bff !important;
}

.financial-card .text-info {
    color: #17a2b8 !important;
}

.financial-card .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.financial-card .stat-icon.revenue {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.financial-card .stat-icon.orders {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.financial-card .stat-icon.sales {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
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
