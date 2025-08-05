@extends('layouts.owner')

@section('title', 'Tenant List')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h1>👥 Tenant List</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Tenants</li>
            </ul>
        </div>
        <div class="header-actions">
            <a href="{{ route('owner.tenants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Tenant
            </a>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search"></i> Search & Filter Tenants
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('owner.tenants.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search tenants, properties, contact info...">
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">All Gender</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="property_id" class="form-label">Property</label>
                        <select class="form-control" id="property_id" name="property_id">
                            <option value="">All Properties</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="occupation" class="form-label">Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="occupation" 
                               value="{{ request('occupation') }}" placeholder="Occupation">
                    </div>
                    <div class="col-lg-1 col-md-6 mb-3">
                        <label for="sort_order" class="form-label">Order</label>
                        <select class="form-control" id="sort_order" name="sort_order">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="check_in_from" class="form-label">Check-in From</label>
                        <input type="date" class="form-control" id="check_in_from" name="check_in_from" 
                               value="{{ request('check_in_from') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="check_in_to" class="form-label">Check-in To</label>
                        <input type="date" class="form-control" id="check_in_to" name="check_in_to" 
                               value="{{ request('check_in_to') }}">
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('owner.tenants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    @if($tenants->count())
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center" style="display: flex !important; justify-content: space-between !important; align-items: center !important;">
            <h5 class="card-title mb-0">
                <i class="fas fa-list"></i> Tenants ({{ $tenants->count() }})
            </h5>
            <div class="table-actions" style="margin-left: auto !important; display: flex !important; align-items: center !important;">
                <div class="btn-group" role="group" style="display: flex !important; gap: 5px !important;">
                    <button class="btn btn-sm btn-outline-primary" onclick="exportToCsv()" title="Export to CSV" style="border-radius: 6px !important; font-size: 0.8rem !important; padding: 6px 12px !important;">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="exportToPdf()" title="Export to PDF" style="border-radius: 6px !important; font-size: 0.8rem !important; padding: 6px 12px !important;">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tenantsTable">
                    <thead class="table-dark">
                        <tr>
                            <th data-sortable="true" data-field="name">Name <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="mobile">Contact <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="property">Property <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="occupation">Occupation <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="status">Status <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                        <tr>
                            <td>
                                <div class="tenant-info">
                                    <strong>{{ $tenant->first_name }} {{ $tenant->last_name }}</strong>
                                    <br><small class="text-muted">{{ $tenant->gender ?? 'N/A' }} • {{ $tenant->total_family_member ?? 0 }} members</small>
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <div><i class="fas fa-phone"></i> {{ $tenant->mobile ?? 'N/A' }}</div>
                                    @if($tenant->email)
                                        <small class="text-muted"><i class="fas fa-envelope"></i> {{ $tenant->email }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($tenant->unit && $tenant->unit->property)
                                    <div class="property-info">
                                        <strong>{{ $tenant->unit->property->name }}</strong>
                                        <br><small class="text-muted">{{ $tenant->unit->name ?? 'N/A' }}</small>
                                    </div>
                                @else
                                    <span class="badge badge-warning">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <div class="occupation-info">
                                    <strong>{{ $tenant->occupation ?? 'N/A' }}</strong>
                                    @if($tenant->company_name)
                                        <br><small class="text-muted">{{ $tenant->company_name }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($tenant->status === 'active')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i> Inactive
                                    </span>
                                @endif
                                @if($tenant->check_in_date)
                                    <br><small class="text-muted">Since {{ \Carbon\Carbon::parse($tenant->check_in_date)->format('M Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('owner.tenants.show', $tenant->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary" title="Edit Tenant">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if (empty($tenant->unit_id))
                                        <a href="{{ route('owner.rents.create', $tenant->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Assign Unit">
                                            <i class="fas fa-home"></i>
                                        </a>
                                    @else
                                        @if($tenant->status === 'active')
                                            <a href="{{ route('owner.checkouts.create', $tenant->id) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Check-out">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Already Checked Out">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Tenants Found</h4>
                <p class="text-muted">You haven't added any tenants yet.</p>
                <a href="{{ route('owner.tenants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Your First Tenant
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Tenant Details Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1" aria-labelledby="tenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tenantModalLabel">Tenant Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="tenantModalBody">
                <!-- Tenant details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Responsive Table Styles */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .table th,
    .table td {
        padding: 8px 4px;
    }
    
    .btn-group .btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    
    .tenant-info,
    .contact-info,
    .property-info,
    .occupation-info {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .badge {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table th,
    .table td {
        padding: 6px 2px;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 2px;
    }
    
    .btn-group .btn {
        padding: 2px 4px;
        font-size: 0.7rem;
    }
    
    .tenant-info,
    .contact-info,
    .property-info,
    .occupation-info {
        max-width: 80px;
    }
}

/* Card and Form Styles */
.card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
}

.table-dark {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.1);
}

/* Filter Form Styles */
.form-control {
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Button Styles */
.btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Badge Styles */
.badge {
    border-radius: 6px;
    padding: 6px 10px;
}

.badge-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.badge-danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.badge-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.badge-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

/* Page Header Styles */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
}

.page-title h1 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.breadcrumb {
    margin-bottom: 0;
    font-size: 0.9rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .header-actions {
        align-self: flex-end;
    }
}

/* Card header export buttons */
.card-header .table-actions {
    margin-left: auto !important;
}

.card-header .btn-group {
    gap: 5px !important;
}

.card-header .btn {
    border-radius: 6px !important;
    font-size: 0.8rem !important;
    padding: 6px 12px !important;
}

@media (max-width: 576px) {
    .card-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
    }
    
    .card-header .table-actions {
        margin-left: 0 !important;
        align-self: flex-end !important;
    }
    
    .card-header .btn-group {
        gap: 3px !important;
    }
    
    .card-header .btn {
        font-size: 0.75rem !important;
        padding: 4px 8px !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Real-time search functionality
document.getElementById('search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const tableBody = document.querySelector('#tenantsTable tbody');
    const rows = tableBody.querySelectorAll('tr');
    
    rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
            // Highlight search term
            if (searchTerm.length > 0) {
                highlightSearchTerm(row, searchTerm);
            } else {
                removeHighlight(row);
            }
        } else {
            row.style.display = 'none';
        }
    });
});

// Highlight search terms
function highlightSearchTerm(row, searchTerm) {
    const cells = row.querySelectorAll('td');
    cells.forEach(function(cell) {
        const originalText = cell.textContent;
        const highlightedText = originalText.replace(
            new RegExp(searchTerm, 'gi'),
            match => `<span class="search-highlight">${match}</span>`
        );
        cell.innerHTML = highlightedText;
    });
}

// Remove highlight
function removeHighlight(row) {
    const highlights = row.querySelectorAll('.search-highlight');
    highlights.forEach(function(highlight) {
        highlight.outerHTML = highlight.textContent;
    });
}

// Auto-submit form on filter change
document.querySelectorAll('#filterForm select').forEach(function(element) {
    element.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});

// Table sorting functionality
document.querySelectorAll('[data-sortable="true"]').forEach(function(header) {
    header.addEventListener('click', function() {
        const field = this.getAttribute('data-field');
        const currentSort = '{{ request("sort_by") }}';
        const currentOrder = '{{ request("sort_order") }}';
        
        let newOrder = 'asc';
        if (currentSort === field && currentOrder === 'asc') {
            newOrder = 'desc';
        }
        
        const url = new URL(window.location);
        url.searchParams.set('sort_by', field);
        url.searchParams.set('sort_order', newOrder);
        window.location.href = url.toString();
    });
});

// Export to CSV function
function exportToCsv() {
    const table = document.getElementById('tenantsTable');
    const rows = table.querySelectorAll('tbody tr');
    let csv = 'Name,Mobile,Email,Property,Occupation,Status\n';
    
    rows.forEach(function(row) {
        const cells = row.querySelectorAll('td');
        const rowData = [];
        
        cells.forEach(function(cell, index) {
            if (index < 5) { // Exclude actions column
                let text = cell.textContent.trim();
                text = text.replace(/"/g, '""'); // Escape quotes
                rowData.push('"' + text + '"');
            }
        });
        
        csv += rowData.join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'tenants_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Export to PDF function
function exportToPdf() {
    // Show loading message
    const loadingModal = new bootstrap.Modal(document.getElementById('tenantModal'));
    document.getElementById('tenantModalLabel').textContent = 'Generating PDF';
    document.getElementById('tenantModalBody').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-3">Generating PDF report...</p>
        </div>
    `;
    loadingModal.show();
    
    // Get current filter parameters
    const urlParams = new URLSearchParams(window.location.search);
    const searchParams = {
        search: urlParams.get('search') || '',
        status: urlParams.get('status') || 'active',
        gender: urlParams.get('gender') || '',
        property_id: urlParams.get('property_id') || '',
        occupation: urlParams.get('occupation') || '',
        check_in_from: urlParams.get('check_in_from') || '',
        check_in_to: urlParams.get('check_in_to') || '',
        sort_by: urlParams.get('sort_by') || 'id',
        sort_order: urlParams.get('sort_order') || 'desc'
    };
    
    // Create PDF download link
    const pdfUrl = '{{ route("owner.tenants.export.pdf") }}?' + new URLSearchParams(searchParams).toString();
    const a = document.createElement('a');
    a.href = pdfUrl;
    a.download = 'tenants_' + new Date().toISOString().split('T')[0] + '.pdf';
    a.click();
    
    // Hide loading modal after a short delay
    setTimeout(function() {
        loadingModal.hide();
    }, 1000);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
