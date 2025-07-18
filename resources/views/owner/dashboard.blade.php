@extends('layouts.owner')

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
