<!-- filepath: resources/views/admin/tenants/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manage Tenants')

@section('content')
<style>
.search-highlight {
    background-color: #fff3cd !important;
    border-color: #ffeaa7 !important;
}
.filter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.filter-section .card-header {
    background: rgba(255,255,255,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.2);
}
.filter-section .form-label {
    color: white;
    font-weight: 600;
}
.filter-section .form-control {
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.9);
}
.filter-section .form-control:focus {
    border-color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
}
.export-buttons {
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
}

/* Table Design Modifications */
.tenants-table-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.tenants-table-section .card-header {
    background: rgba(255,255,255,0.15);
    border-bottom: 2px solid rgba(255,255,255,0.2);
    padding: 20px;
}

.tenants-table-section .card-header h6 {
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tenants-table-section .card-body {
    background: rgba(255,255,255,0.95);
    padding: 0;
}

.table-modern {
    margin: 0;
    border-collapse: collapse;
    width: 100%;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.table-modern thead {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
}

.table-modern thead th {
    color: #ffffff !important;
    font-weight: 700;
    padding: 15px 12px;
    border: 1px solid rgba(255,255,255,0.2);
    text-align: left;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    -webkit-text-stroke: 0.5px rgba(0,0,0,0.3);
}

.table-modern thead th:first-child {
    border-top-left-radius: 10px;
}

.table-modern thead th:last-child {
    border-top-right-radius: 10px;
}

.table-modern tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f8f9fa;
}

.table-modern tbody tr:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table-modern tbody td {
    padding: 15px 12px;
    border: 1px solid #f8f9fa;
    vertical-align: middle;
    font-size: 0.9rem;
    color: #495057;
    transition: all 0.3s ease;
}

.table-modern tbody tr:hover td {
    color: #212529;
    border-color: #dee2e6;
}

.badge-modern {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge-modern.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white;
}

.badge-modern.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    color: white;
}

.badge-modern.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    color: white;
}

.badge-modern.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
    color: white;
}

.badge-modern.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    color: white;
}

.btn-group-modern {
    display: flex;
    gap: 5px;
}

.btn-group-modern .btn {
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group-modern .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-group-modern .btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.btn-group-modern .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    color: white;
}

.btn-group-modern .btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}

.btn-group-modern .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.pagination-modern {
    background: rgba(255,255,255,0.95);
    padding: 20px;
    border-top: 1px solid rgba(102, 126, 234, 0.1);
}

.pagination-modern .pagination {
    justify-content: center;
    margin: 0;
}

.pagination-modern .page-link {
    border: none;
    color: #667eea;
    background: white;
    margin: 0 2px;
    border-radius: 8px;
    padding: 10px 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination-modern .page-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.pagination-modern .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
    font-style: italic;
}

.empty-state i {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 15px;
}

/* Additional enhancements */
.table-modern thead th i {
    margin-right: 8px;
    opacity: 1;
    color: #ffffff !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
    -webkit-text-stroke: 0.5px rgba(0,0,0,0.3);
}

/* Enhanced header visibility */
.table-modern thead th {
    font-weight: 700 !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.8) !important;
    -webkit-text-stroke: 0.5px rgba(0,0,0,0.3);
}

.table-modern tbody tr:hover td {
    color: #495057;
}

.table-modern tbody tr:hover .badge-modern {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.tenants-table-section {
    margin-bottom: 2rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .table-modern thead th {
        font-size: 0.8rem;
        padding: 12px 8px;
    }
    
    .table-modern tbody td {
        font-size: 0.8rem;
        padding: 12px 8px;
    }
    
    .btn-group-modern .btn {
        padding: 6px 8px;
        font-size: 0.7rem;
    }
    
    .badge-modern {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tenant List</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.owners.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-users fa-sm text-white-50"></i> View Owners
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card shadow mb-4 filter-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-search"></i> Search & Filter Tenants
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tenants.index') }}" id="searchFilterForm">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name, mobile, email, NID...">
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- District Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="district" class="form-label">District</label>
                        <select class="form-control" id="district" name="district">
                            <option value="">All Districts</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->name }}" {{ request('district') == $district->name ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Owner Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select class="form-control" id="owner_id" name="owner_id">
                            <option value="">All Owners</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="col-md-1 mb-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-control" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="first_name" {{ request('sort_by') == 'first_name' ? 'selected' : '' }}>Name</option>
                            <option value="mobile" {{ request('sort_by') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                            <option value="district" {{ request('sort_by') == 'district' ? 'selected' : '' }}>District</option>
                        </select>
                    </div>
                    
                    <!-- Sort Order -->
                    <div class="col-md-1 mb-3">
                        <label for="sort_order" class="form-label">Order</label>
                        <select class="form-control" id="sort_order" name="sort_order">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search & Filter
                        </button>
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                        <div class="btn-group ml-2 export-buttons" role="group">
                            <button type="button" class="btn btn-light export-btn" data-format="csv">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                            <button type="button" class="btn btn-light export-btn" data-format="pdf">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                        <span class="ml-3 text-muted">
                            Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }} results
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="card shadow mb-4 tenants-table-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users"></i> All Tenants ({{ $tenants->total() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern" id="tenantsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Name</th>
                            <th><i class="fas fa-phone"></i> Mobile No</th>
                            <th><i class="fas fa-user-tie"></i> Owner Name</th>
                            <th><i class="fas fa-map-marker-alt"></i> District</th>
                            <th><i class="fas fa-toggle-on"></i> Status</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-id-card"></i> NID Number</th>
                            <th><i class="fas fa-home"></i> Address</th>
                            <th><i class="fas fa-calendar-alt"></i> Check In Date</th>
                            <th><i class="fas fa-money-bill-wave"></i> Security Deposit</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                        <tr>
                            <td>
                                <strong>{{ $tenant->full_name }}</strong>
                                @if($tenant->gender)
                                    <small class="text-muted d-block">{{ ucfirst($tenant->gender) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $tenant->mobile }}
                                @if($tenant->alt_mobile)
                                    <small class="text-muted d-block">Alt: {{ $tenant->alt_mobile }}</small>
                                @endif
                            </td>
                            <td>
                                @if($tenant->owner)
                                    <span class="badge badge-modern bg-info">{{ $tenant->owner->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->district)
                                    <span class="badge badge-modern bg-secondary">{{ $tenant->district }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->status === 'active')
                                    <span class="badge badge-modern bg-success">Active</span>
                                @elseif($tenant->status === 'inactive')
                                    <span class="badge badge-modern bg-warning">Inactive</span>
                                @elseif($tenant->status === 'checkout')
                                    <span class="badge badge-modern bg-danger">Checkout</span>
                                @else
                                    <span class="badge badge-modern bg-secondary">{{ ucfirst($tenant->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->email)
                                    {{ $tenant->email }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->nid_number)
                                    {{ $tenant->nid_number }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->address)
                                    <small>{{ Str::limit($tenant->address, 50) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->check_in_date)
                                    {{ $tenant->check_in_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tenant->security_deposit)
                                    ৳{{ number_format($tenant->security_deposit) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-modern" role="group">
                                    <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="empty-state">
                                <i class="fas fa-users"></i>
                                <p>No tenants found matching your criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($tenants->hasPages())
    <div class="card shadow mb-4 pagination-modern">
        <div class="card-body">
            {{ $tenants->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

<script>
$(document).ready(function() {
    // Export functionality
    $('.export-btn').click(function() {
        var format = $(this).data('format');
        var url = '{{ route("admin.tenants.index") }}?' + $('#searchFilterForm').serialize() + '&export=' + format;
        window.location.href = url;
    });

    // Highlight search terms
    @if(request('search'))
        var searchTerm = '{{ request("search") }}';
        $('td').each(function() {
            var text = $(this).text();
            if (text.toLowerCase().includes(searchTerm.toLowerCase())) {
                $(this).addClass('search-highlight');
            }
        });
    @endif

    // Auto-submit form on filter change
    $('#status, #district, #owner_id, #sort_by, #sort_order').change(function() {
        $('#searchFilterForm').submit();
    });
});
</script>
@endsection
