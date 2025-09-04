@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar"></i> Analytics Dashboard
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" onclick="refreshAnalytics()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" class="btn btn-info" onclick="showCustomDateRange()">
                <i class="fas fa-calendar"></i> Custom Range
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($systemPerformance['total_users']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Properties
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($systemPerformance['total_properties']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Helpers\SystemHelper::formatCurrency($revenueAnalytics['total_revenue']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                System Uptime
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $systemPerformance['uptime'] }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- User Growth Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Growth Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Property Analytics -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Property Analytics</h6>
                </div>
                <div class="card-body">
                    <canvas id="propertyAnalyticsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue and Notifications Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Notifications Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Notifications</h6>
                </div>
                <div class="card-body">
                    <canvas id="notificationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- System Performance and Geographic Analytics -->
    <div class="row">
        <!-- System Performance -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Performance</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Disk Usage</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $systemPerformance['disk_usage']['used'] }}%"
                                     aria-valuenow="{{ $systemPerformance['disk_usage']['used'] }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $systemPerformance['disk_usage']['used'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $systemPerformance['disk_usage']['used_gb'] }} / {{ $systemPerformance['disk_usage']['total'] }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Memory Usage</h6>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $systemPerformance['memory_usage']['used'] }}%"
                                     aria-valuenow="{{ $systemPerformance['memory_usage']['used'] }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $systemPerformance['memory_usage']['used'] }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $systemPerformance['memory_usage']['used_gb'] }} / {{ $systemPerformance['memory_usage']['total'] }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h5 class="text-success">{{ $systemPerformance['user_activity_rate'] }}%</h5>
                            <small class="text-muted">User Activity Rate</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h5 class="text-info">{{ $systemPerformance['avg_response_time'] }}ms</h5>
                            <small class="text-muted">Avg Response Time</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h5 class="text-warning">{{ $propertyAnalytics['occupancy_rate'] }}%</h5>
                            <small class="text-muted">Property Occupancy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Analytics -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Geographic Distribution</h6>
                </div>
                <div class="card-body">
                    <h6>Top Property Locations</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Properties</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($geographicAnalytics['property_locations'] as $location)
                                <tr>
                                    <td>{{ $location->city ?? 'Unknown' }}</td>
                                    <td>{{ $location->count }}</td>
                                    <td>
                                        @php
                                            $percentage = $systemPerformance['total_properties'] > 0 
                                                ? round(($location->count / $systemPerformance['total_properties']) * 100, 1)
                                                : 0;
                                        @endphp
                                        {{ $percentage }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notification Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ number_format($notificationAnalytics['total_notifications']) }}</h4>
                                <p class="text-muted mb-0">Total Notifications</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-success">{{ number_format($notificationAnalytics['successful_notifications']) }}</h4>
                                <p class="text-muted mb-0">Successful</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">{{ number_format($notificationAnalytics['failed_notifications']) }}</h4>
                                <p class="text-muted mb-0">Failed</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h4 class="text-info">{{ $notificationAnalytics['success_rate'] }}%</h4>
                                <p class="text-muted mb-0">Success Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile App Analytics -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-mobile-alt"></i> Mobile App Analytics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-primary">{{ number_format($mobileAppAnalytics['total_installations']) }}</div>
                                <div class="text-muted">Total Installations</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-success">{{ number_format($mobileAppAnalytics['active_users_this_month']) }}</div>
                                <div class="text-muted">Active Users (This Month)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-info">{{ $mobileAppAnalytics['active_rate'] }}%</div>
                                <div class="text-muted">Active Rate</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-warning">{{ number_format($mobileAppAnalytics['current_month_installations']) }}</div>
                                <div class="text-muted">This Month Installations</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Platform Distribution</h6>
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="platformDistributionChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Monthly Installations</h6>
                            <div class="chart-bar pt-4 pb-2">
                                <canvas id="monthlyInstallationsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Analytics -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-laptop"></i> Device Analytics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">Device Types</h6>
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="deviceTypesChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">OS Versions</h6>
                            <div class="chart-bar pt-4 pb-2">
                                <canvas id="osVersionsChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="font-weight-bold">App Versions</h6>
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="appVersionsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Device Manufacturers</h6>
                            <div class="chart-bar pt-4 pb-2">
                                <canvas id="manufacturersChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Performance Metrics</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong>Average Load Time</strong></td>
                                            <td>{{ $deviceAnalytics['performance_metrics']['avg_load_time'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Crash Rate</strong></td>
                                            <td>{{ $deviceAnalytics['performance_metrics']['crash_rate'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Memory Usage</strong></td>
                                            <td>{{ $deviceAnalytics['performance_metrics']['memory_usage'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Battery Impact</strong></td>
                                            <td>{{ $deviceAnalytics['performance_metrics']['battery_impact'] }}</td>
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

<!-- Custom Date Range Modal -->
<div class="modal fade" id="customDateRangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom Date Range Analytics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customDateRangeForm">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="getCustomAnalytics()">Get Analytics</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
const userGrowthChart = new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: @json($userGrowth->pluck('month')),
        datasets: [{
            label: 'New Users',
            data: @json($userGrowth->pluck('new_users')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Active Users',
            data: @json($userGrowth->pluck('active_users')),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'User Growth Trend'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Property Analytics Chart
const propertyAnalyticsCtx = document.getElementById('propertyAnalyticsChart').getContext('2d');
const propertyAnalyticsChart = new Chart(propertyAnalyticsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Occupied', 'Vacant'],
        datasets: [{
            data: [{{ $propertyAnalytics['occupied_properties'] }}, {{ $propertyAnalytics['vacant_properties'] }}],
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: true,
                text: 'Property Occupancy'
            }
        }
    }
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: @json($revenueAnalytics['monthly_revenue']->pluck('month')),
        datasets: [{
            label: 'Revenue',
            data: @json($revenueAnalytics['monthly_revenue']->pluck('revenue')),
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Monthly Revenue'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Notifications Chart
const notificationsCtx = document.getElementById('notificationsChart').getContext('2d');
const notificationsChart = new Chart(notificationsCtx, {
    type: 'line',
    data: {
        labels: @json($notificationAnalytics['daily_notifications']->pluck('date')),
        datasets: [{
            label: 'Notifications',
            data: @json($notificationAnalytics['daily_notifications']->pluck('count')),
            borderColor: 'rgb(255, 159, 64)',
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Daily Notifications'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Platform Distribution Chart
const platformCtx = document.getElementById('platformDistributionChart').getContext('2d');
const platformChart = new Chart(platformCtx, {
    type: 'doughnut',
    data: {
        labels: ['Android', 'iOS', 'Web'],
        datasets: [{
            data: [
                {{ $mobileAppAnalytics['platform_distribution']['android'] }},
                {{ $mobileAppAnalytics['platform_distribution']['ios'] }},
                {{ $mobileAppAnalytics['platform_distribution']['web'] }}
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 205, 86, 0.8)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 205, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Monthly Installations Chart
const monthlyInstallCtx = document.getElementById('monthlyInstallationsChart').getContext('2d');
const monthlyInstallChart = new Chart(monthlyInstallCtx, {
    type: 'bar',
    data: {
        labels: @json($mobileAppAnalytics['monthly_installations']->pluck('month')),
        datasets: [{
            label: 'Installations',
            data: @json($mobileAppAnalytics['monthly_installations']->pluck('installations')),
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Device Types Chart
const deviceTypesCtx = document.getElementById('deviceTypesChart').getContext('2d');
const deviceTypesChart = new Chart(deviceTypesCtx, {
    type: 'doughnut',
    data: {
        labels: ['Smartphone', 'Tablet', 'Desktop'],
        datasets: [{
            data: [75, 20, 5],
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 205, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// OS Versions Chart
const osVersionsCtx = document.getElementById('osVersionsChart').getContext('2d');
const osVersionsChart = new Chart(osVersionsCtx, {
    type: 'bar',
    data: {
        labels: ['Android 13', 'Android 12', 'Android 11', 'iOS 17', 'iOS 16'],
        datasets: [{
            label: 'Users',
            data: [35, 25, 20, 15, 5],
            backgroundColor: 'rgba(153, 102, 255, 0.8)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// App Versions Chart
const appVersionsCtx = document.getElementById('appVersionsChart').getContext('2d');
const appVersionsChart = new Chart(appVersionsCtx, {
    type: 'doughnut',
    data: {
        labels: ['1.0.0', '0.9.0', '0.8.0', 'Other'],
        datasets: [{
            data: [60, 25, 10, 5],
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(201, 203, 207, 0.8)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(201, 203, 207, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Manufacturers Chart
const manufacturersCtx = document.getElementById('manufacturersChart').getContext('2d');
const manufacturersChart = new Chart(manufacturersCtx, {
    type: 'bar',
    data: {
        labels: ['Samsung', 'Xiaomi', 'Apple', 'OnePlus', 'Other'],
        datasets: [{
            label: 'Users',
            data: [30, 20, 15, 10, 25],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Functions
function refreshAnalytics() {
    location.reload();
}

function showCustomDateRange() {
    const modal = new bootstrap.Modal(document.getElementById('customDateRangeModal'));
    modal.show();
}

function getCustomAnalytics() {
    const form = document.getElementById('customDateRangeForm');
    const formData = new FormData(form);
    
    fetch('/admin/analytics/custom', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Custom Analytics Results:\nRevenue: ${data.revenue}\nNew Users: ${data.new_users}\nNotifications: ${data.notifications}`);
        } else {
            alert('Failed to get custom analytics.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error getting custom analytics.');
    });
}

// Real-time updates (every 30 seconds)
setInterval(function() {
    fetch('/admin/analytics/real-time')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update real-time data if needed
                console.log('Real-time data updated');
            }
        })
        .catch(error => {
            console.error('Error updating real-time data:', error);
        });
}, 30000);

// Real-time device statistics updates (every 60 seconds)
setInterval(function() {
    fetch('/admin/analytics/device-stats', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Device stats updated:', data.current_device_status);
                // You can update UI elements here with real-time device data
            }
        })
        .catch(error => {
            console.error('Error updating device stats:', error);
        });
}, 60000);

// Function to get device installation trends
function getDeviceInstallationTrends(days = 30) {
    fetch('/admin/analytics/device-trends', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ days: days })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Device trends:', data);
                // You can update charts or display data here
            }
        })
        .catch(error => {
            console.error('Error getting device trends:', error);
        });
}
</script>
@endsection 