@extends('layouts.admin')

@section('title', 'Manage Subscriptions')

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
.subscriptions-table-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.subscriptions-table-section .card-header {
    background: rgba(255,255,255,0.15);
    border-bottom: 2px solid rgba(255,255,255,0.2);
    padding: 20px;
}

.subscriptions-table-section .card-header h6 {
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.subscriptions-table-section .card-body {
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
    border-bottom: 1px solid #e9ecef;
    background: white;
}

.table-modern tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.table-modern tbody tr:last-child {
    border-bottom: none;
}

.table-modern tbody td {
    padding: 15px 12px;
    border: 1px solid #e9ecef;
    vertical-align: middle;
    font-size: 0.9rem;
    color: #495057;
    background: white;
}

.table-modern tbody td:first-child {
    font-weight: 600;
    color: #667eea;
}

.badge-modern {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
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
    background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%) !important;
    color: white;
}

.badge-modern.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    color: white;
}

.badge-modern.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    color: white;
}

.btn-group-modern .btn {
    border-radius: 8px;
    margin: 2px;
    padding: 8px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group-modern .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-group-modern .btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
    color: white;
}

.btn-group-modern .btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
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

.subscriptions-table-section {
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
        <h1 class="h3 mb-0 text-gray-800">Manage Subscriptions</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.plans.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cube fa-sm text-white-50"></i> Manage Plans
            </a>
                            <a href="{{ route('admin.billing.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-file-invoice-dollar fa-sm text-white-50"></i> View Billing
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Expired Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('status', 'active')->where('end_date', '<', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($subscriptions->sum('plan.price')) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Expiring Soon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('status', 'active')->where('end_date', '>', now())->where('end_date', '<', now()->addDays(30))->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card shadow mb-4 filter-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-search"></i> Search & Filter Subscriptions
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subscriptions') }}" id="searchFilterForm">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by owner name, email, plan...">
                    </div>
                    
                    <!-- Subscription Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Plan Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="plan" class="form-label">Plan</label>
                        <select class="form-control" id="plan" name="plan">
                            <option value="">All Plans</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan }}" {{ request('plan') == $plan ? 'selected' : '' }}>
                                    {{ $plan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Auto Renew Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="auto_renew" class="form-label">Auto Renew</label>
                        <select class="form-control" id="auto_renew" name="auto_renew">
                            <option value="">All</option>
                            <option value="yes" {{ request('auto_renew') == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ request('auto_renew') == 'no' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="col-md-2 mb-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-control" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                            <option value="start_date" {{ request('sort_by') == 'start_date' ? 'selected' : '' }}>Start Date</option>
                            <option value="end_date" {{ request('sort_by') == 'end_date' ? 'selected' : '' }}>End Date</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
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
                        <a href="{{ route('admin.subscriptions') }}" class="btn btn-secondary">
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
                            Showing {{ $subscriptions->firstItem() ?? 0 }} to {{ $subscriptions->lastItem() ?? 0 }} of {{ $subscriptions->total() }} results
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card shadow mb-4 subscriptions-table-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-credit-card"></i> All Subscriptions ({{ $subscriptions->total() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern" id="subscriptionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Owner</th>
                            <th><i class="fas fa-crown"></i> Plan</th>
                            <th><i class="fas fa-toggle-on"></i> Status</th>
                            <th><i class="fas fa-calendar-plus"></i> Start Date</th>
                            <th><i class="fas fa-calendar-minus"></i> End Date</th>
                            <th><i class="fas fa-clock"></i> Days Left</th>
                            <th><i class="fas fa-sync-alt"></i> Auto Renew</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <span class="text-white font-weight-bold">{{ substr($subscription->owner->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $subscription->owner->user->name }}</div>
                                        <small class="text-muted">{{ $subscription->owner->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-modern bg-{{ $subscription->plan->name === 'Free' ? 'secondary' : ($subscription->plan->name === 'Lite' ? 'info' : 'primary') }}">
                                    {{ $subscription->plan->name }}
                                </span>
                                <div class="small text-muted">৳{{ number_format($subscription->plan->price) }}/year</div>
                            </td>
                            <td>
                                @if($subscription->isActive())
                                    <span class="badge badge-modern bg-success">Active</span>
                                @elseif($subscription->isExpired())
                                    <span class="badge badge-modern bg-danger">Expired</span>
                                @else
                                    <span class="badge badge-modern bg-warning">{{ ucfirst($subscription->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                            <td>{{ $subscription->end_date->format('M d, Y') }}</td>
                            <td>
                                @if($subscription->isActive())
                                    @if($subscription->daysUntilExpiry() <= 30)
                                        <span class="text-warning font-weight-bold">{{ $subscription->daysUntilExpiry() }} days</span>
                                    @else
                                        <span class="text-success">{{ $subscription->daysUntilExpiry() }} days</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($subscription->auto_renew)
                                    <span class="badge badge-modern bg-success">Yes</span>
                                @else
                                    <span class="badge badge-modern bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-modern" role="group">
                                    <a href="#" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-warning" title="Edit Subscription">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-success" title="Renew Subscription">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-credit-card"></i>
                                <div>No subscriptions found</div>
                                <small class="text-muted">Try adjusting your search criteria</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($subscriptions->hasPages())
            <div class="pagination-modern">
                {{ $subscriptions->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with search functionality
    var table = $('#subscriptionsTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "asc" ]],
        "dom": 'rt<"bottom"ip>', // Remove default search box since we have custom search
        "searching": false // Disable DataTable's built-in search
    });

    // Auto-submit form when search input changes (with delay)
    var searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#searchFilterForm').submit();
        }, 500); // 500ms delay
    });

    // Auto-submit form when filter dropdowns change
    $('#status, #plan, #auto_renew, #sort_by, #sort_order').on('change', function() {
        $('#searchFilterForm').submit();
    });

    // Add loading indicator
    $('#searchFilterForm').on('submit', function() {
        $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Searching...');
        $('button[type="submit"]').prop('disabled', true);
    });

    // Highlight search terms in table
    function highlightSearchTerms() {
        var searchTerm = $('#search').val().toLowerCase();
        if (searchTerm) {
            $('#subscriptionsTable tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.includes(searchTerm)) {
                    $(this).addClass('search-highlight');
                } else {
                    $(this).removeClass('search-highlight');
                }
            });
        } else {
            $('#subscriptionsTable tbody tr').removeClass('search-highlight');
        }
    }

    // Call highlight function on page load
    highlightSearchTerms();

    // Export functionality
    $('.export-btn').on('click', function(e) {
        e.preventDefault();
        var format = $(this).data('format');
        var url = '{{ route("admin.subscriptions") }}?' + $('#searchFilterForm').serialize() + '&export=' + format;
        window.open(url, '_blank');
    });
});
</script>
@endpush
@endsection
