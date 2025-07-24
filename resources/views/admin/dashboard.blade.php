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
                <div class="text-muted small">Super Admins</div>
                <h3 class="mb-0">{{ $superAdminCount ?? 1 }}</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
                <span class="badge bg-light text-primary mt-2">Active</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted small">Total Properties</div>
                <h3 class="mb-0">{{ $propertyCount ?? 0 }}</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-info" style="width: 75%"></div>
                </div>
                <span class="badge bg-light text-info mt-2">Registered</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted small">Total Tenants</div>
                <h3 class="mb-0">{{ $tenantCount ?? 0 }}</h3>
                <div class="progress mt-2" style="height: 5px;">
                    <div class="progress-bar bg-warning" style="width: 60%"></div>
                </div>
                <span class="badge bg-light text-warning mt-2">Active</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.owners.index') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-users"></i> Manage Owners
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-cog"></i> System Settings
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.otp-settings.index') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-mobile-alt"></i> OTP Settings
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.owners.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Add New Owner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Owners Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Owners</h5>
                <a href="{{ route('admin.owners.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOwners ?? [] as $owner)
                            <tr>
                                <td>{{ $owner->name }}</td>
                                <td>{{ $owner->email }}</td>
                                <td>{{ $owner->phone }}</td>
                                <td>
                                    <span class="badge badge-{{ $owner->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($owner->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($owner->is_super_admin)
                                        <span class="badge badge-primary">Super Admin</span>
                                    @else
                                        <span class="badge badge-secondary">Owner</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="editOwner({{ $owner->id }})" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No owners found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
