<!-- filepath: resources/views/admin/owners/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manage Owners')

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
.owners-table-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.owners-table-section .card-header {
    background: rgba(255,255,255,0.15);
    border-bottom: 2px solid rgba(255,255,255,0.2);
    padding: 20px;
}

.owners-table-section .card-header h6 {
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.owners-table-section .card-body {
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

.table-modern tbody tr:hover td {
    color: #495057;
}

.table-modern tbody tr:hover .badge-modern {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.owners-table-section {
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
        <h1 class="h3 mb-0 text-gray-800">Owner List</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.owners.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Owner
            </a>
            <a href="{{ route('admin.subscriptions') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-credit-card fa-sm text-white-50"></i> View Subscriptions
            </a>
        </div>
    </div>



    <!-- Search and Filter Section -->
    <div class="card shadow mb-4 filter-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-search"></i> Search & Filter Owners
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.owners.index') }}" id="searchFilterForm">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name, email, phone, ID...">
                    </div>
                    
                    <!-- Subscription Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="subscription_status" class="form-label">Subscription Status</label>
                        <select class="form-control" id="subscription_status" name="subscription_status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('subscription_status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('subscription_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="expired" {{ request('subscription_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="no_subscription" {{ request('subscription_status') == 'no_subscription' ? 'selected' : '' }}>No Subscription</option>
                        </select>
                    </div>
                    
                    <!-- Country Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-control" id="country" name="country">
                            <option value="">All Countries</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Gender Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">All Genders</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="col-md-2 mb-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-control" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="country" {{ request('sort_by') == 'country' ? 'selected' : '' }}>Country</option>
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
                        <a href="{{ route('admin.owners.index') }}" class="btn btn-secondary">
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
                            Showing {{ $owners->firstItem() ?? 0 }} to {{ $owners->lastItem() ?? 0 }} of {{ $owners->total() }} results
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Owners Table -->
    <div class="card shadow mb-4 owners-table-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users"></i> All Owners ({{ $owners->total() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern" id="ownersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Name</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-phone"></i> Phone</th>
                            <th><i class="fas fa-globe"></i> Country</th>
                            <th><i class="fas fa-venus-mars"></i> Gender</th>
                            <th><i class="fas fa-crown"></i> Current Plan</th>
                            <th><i class="fas fa-toggle-on"></i> Subscription Status</th>
                            <th><i class="fas fa-calendar-alt"></i> Expiry Date</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $owner)
                        <tr>
                            <td>{{ $owner->user->name ?? $owner->name }}</td>
                            <td>{{ $owner->user->email ?? $owner->email }}</td>
                            <td>{{ $owner->phone }}</td>
                            <td>
                                @if($owner->country)
                                    <span class="badge badge-modern bg-info">{{ $owner->country }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->gender)
                                    <span class="badge badge-modern bg-secondary">{{ ucfirst($owner->gender) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->subscription && $owner->subscription->plan)
                                    <span class="badge badge-modern bg-primary">{{ $owner->subscription->plan->name }}</span>
                                    @if($owner->subscription->plan->price > 0)
                                        <small class="text-muted d-block">৳{{ number_format($owner->subscription->plan->price) }}/year</small>
                                    @else
                                        <small class="text-muted d-block">Free</small>
                                    @endif
                                @elseif($owner->subscription)
                                    <span class="badge badge-modern bg-warning">Plan Not Found</span>
                                @else
                                    <span class="badge badge-modern bg-secondary">No Plan</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->subscription)
                                    @if($owner->subscription->status === 'active')
                                        <span class="badge badge-modern bg-success">Active</span>
                                    @elseif($owner->subscription->status === 'pending')
                                        <span class="badge badge-modern bg-warning">Pending Payment</span>
                                        @php
                                            $pendingInvoice = $owner->subscription->getPendingInvoice();
                                        @endphp
                                        @if($pendingInvoice)
                                            <small class="text-muted d-block">Invoice: {{ $pendingInvoice->invoice_number }}</small>
                                        @endif
                                    @elseif($owner->subscription->status === 'expired')
                                        <span class="badge badge-modern bg-danger">Expired</span>
                                    @elseif($owner->subscription->status === 'suspended')
                                        <span class="badge badge-modern bg-warning">Suspended</span>
                                    @elseif($owner->subscription->status === 'cancelled')
                                        <span class="badge badge-modern bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge badge-modern bg-secondary">{{ ucfirst($owner->subscription->status ?? 'Unknown') }}</span>
                                    @endif
                                @else
                                    <span class="badge badge-modern bg-secondary">No Subscription</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->subscription)
                                    @if($owner->subscription->status === 'active' && $owner->subscription->end_date)
                                        {{ $owner->subscription->end_date->format('M d, Y') }}
                                        <small class="text-muted d-block">
                                            @if($owner->subscription->daysUntilExpiry() !== null)
                                                {{ $owner->subscription->daysUntilExpiry() }} days left
                                            @else
                                                Active
                                            @endif
                                        </small>
                                    @elseif($owner->subscription->status === 'pending')
                                        <span class="text-warning">Payment Required</span>
                                        @php
                                            $pendingInvoice = $owner->subscription->getPendingInvoice();
                                        @endphp
                                        @if($pendingInvoice)
                                            <small class="text-muted d-block">
                                                Due: {{ $pendingInvoice->due_date->format('M d, Y') }}
                                            </small>
                                        @endif
                                    @elseif($owner->subscription->status === 'expired' && $owner->subscription->end_date)
                                        <span class="text-danger">{{ $owner->subscription->end_date->format('M d, Y') }}</span>
                                        <small class="text-muted d-block">Expired</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-modern" role="group">
                                    <a href="{{ route('admin.owners.show', $owner->id) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($owner->subscription && $owner->subscription->status === 'pending')
                                        <a href="#" class="btn btn-danger" title="View Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="#" class="btn btn-success" title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-primary" title="Manage Subscription">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.owners.destroy', $owner->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this owner? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Remove Owner">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="fas fa-users"></i>
                                <div>No owners found</div>
                                <small class="text-muted">Try adjusting your search criteria</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($owners->hasPages())
            <div class="pagination-modern">
                {{ $owners->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with search functionality
    var table = $('#ownersTable').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
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
    $('#subscription_status, #country, #gender, #sort_by, #sort_order').on('change', function() {
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
            $('#ownersTable tbody tr').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.includes(searchTerm)) {
                    $(this).addClass('search-highlight');
                } else {
                    $(this).removeClass('search-highlight');
                }
            });
        } else {
            $('#ownersTable tbody tr').removeClass('search-highlight');
        }
    }

    // Call highlight function on page load
    highlightSearchTerms();

    // Export functionality
    $('.export-btn').on('click', function(e) {
        e.preventDefault();
        var format = $(this).data('format');
        var url = '{{ route("admin.owners.index") }}?' + $('#searchFilterForm').serialize() + '&export=' + format;
        window.open(url, '_blank');
    });
});
</script>
@endpush
@endsection
