<!-- filepath: resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="row mb-4">
        <div class="col-12 col-md-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted small">Total Owners</div>
                <h3 class="mb-0">{{ $ownerCount }}</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
                <span class="badge bg-light text-success mt-2">Updated</span>
            </div>
        </div>
    </div>


    <div class="col-12 col-md-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted small">Market Revenue</div>
                <h3 class="mb-0">$1875.54</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-danger" style="width: 80%"></div>
                </div>
                <span class="badge bg-light text-danger mt-2">Per Week</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted small">Expenses</div>
                <h3 class="mb-0">$784.62</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-warning" style="width: 50%"></div>
                </div>
                <span class="badge bg-light text-warning mt-2">Per Month</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted small">Daily Visits</div>
                <h3 class="mb-0">1,15,187</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-success" style="width: 90%"></div>
                </div>
                <span class="badge bg-light text-success mt-2">All Time</span>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title">Last Month Sales</h6>
                <canvas id="doughnutChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title">Revenue</h6>
                <canvas id="lineChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title">Product Inventory Overview</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Wireless Headphones</td>
                            <td>Electronics</td>
                            <td>$99.99</td>
                            <td><span class="badge bg-success">In Stock</span></td>
                        </tr>
                        <!-- আরও প্রোডাক্ট -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="card-title">Top Sellers List</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>CEO</th>
                            <th>Total Sales</th>
                            <th>Revenue</th>
                            <th>Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Techlab LLC</td>
                            <td>John Doe</td>
                            <td>45k</td>
                            <td>$900k</td>
                            <td>25%</td>
                        </tr>
                        <!-- আরও কোম্পানি -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    // Doughnut Chart
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Online', 'Offline', 'Retail'],
            datasets: [{
                data: [40, 30, 30],
                backgroundColor: ['#6f42c1', '#fd7e14', '#20c997'],
            }]
        },
        options: { cutout: '70%' }
    });

    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Revenue',
                data: [30000, 50000, 40000, 60000, 80000, 70000, 90000, 110000, 100000, 120000, 140000, 130000],
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253,126,20,0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Expenses',
                data: [20000, 30000, 25000, 35000, 40000, 38000, 42000, 50000, 48000, 60000, 70000, 65000],
                borderColor: '#20c997',
                backgroundColor: 'rgba(32,201,151,0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection