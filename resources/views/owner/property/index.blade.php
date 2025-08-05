@extends('layouts.owner')

@section('title', 'My Properties')

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
            <h1>🏢 My Properties</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Properties</li>
            </ul>
        </div>
        <div class="header-actions">
            <a href="{{ route('owner.property.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Property
            </a>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search"></i> Search & Filter Properties
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('owner.property.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search properties...">
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="residential" {{ request('type') == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-3">
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
                    <div class="col-lg-2 col-md-6 mb-3">
                        <label for="sort_by" class="form-label">Sort By</label>
                        <select class="form-control" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
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
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('owner.property.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Properties Table -->
    @if($properties->count())
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center" style="display: flex !important; justify-content: space-between !important; align-items: center !important;">
            <h5 class="card-title mb-0">
                <i class="fas fa-list"></i> Properties ({{ $properties->count() }})
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
                <table class="table table-hover mb-0" id="propertiesTable">
                    <thead class="table-dark">
                        <tr>
                            <th data-sortable="true" data-field="name">Building Name <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="type">Type <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="address">Address <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="country">Country <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="units">Total Units <i class="fas fa-sort"></i></th>
                            <th data-sortable="true" data-field="status">Status <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                        <tr>
                            <td>
                                <div class="property-name">
                                    <strong>{{ $property->name }}</strong>
                                    @if($property->city)
                                        <br><small class="text-muted">{{ $property->city }}, {{ $property->state }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($property->property_type ?? 'residential') }}</span>
                            </td>
                            <td>
                                <div class="property-address">
                                    {{ $property->address }}
                                    @if($property->zip_code)
                                        <br><small class="text-muted">{{ $property->zip_code }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $property->country }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $property->units->count() }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $property->status == 'active' ? 'success' : ($property->status == 'inactive' ? 'warning' : 'info') }}">
                                    {{ ucfirst($property->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('owner.units.setup', $property->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Add Units">
                                        <i class="fas fa-plus"></i> Assigen Unit
                                    </a>
                                    <a href="{{ route('owner.property.edit', $property->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            onclick="viewProperty({{ $property->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Properties Found</h4>
                <p class="text-muted">You haven't added any properties yet.</p>
                <a href="{{ route('owner.property.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Your First Property
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Property Details Modal -->
<div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="propertyModalLabel">Property Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="propertyModalBody">
                <!-- Property details will be loaded here -->
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
    
    .property-name,
    .property-address {
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
    
    .property-name,
    .property-address {
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

.badge-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.badge-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.badge-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
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
    margin-left: auto;
}

.card-header .btn-group {
    gap: 5px;
}

.card-header .btn {
    border-radius: 6px;
    font-size: 0.8rem;
    padding: 6px 12px;
}

@media (max-width: 576px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .card-header .table-actions {
        margin-left: 0;
        align-self: flex-end;
    }
    
    .card-header .btn-group {
        gap: 3px;
    }
    
    .card-header .btn {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Auto-submit form on filter change
document.querySelectorAll('#filterForm select, #filterForm input').forEach(function(element) {
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
    const table = document.getElementById('propertiesTable');
    const rows = table.querySelectorAll('tbody tr');
    let csv = 'Building Name,Type,Address,Country,Total Units,Status\n';
    
    rows.forEach(function(row) {
        const cells = row.querySelectorAll('td');
        const rowData = [];
        
        cells.forEach(function(cell, index) {
            if (index < 6) { // Exclude actions column
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
    a.download = 'properties_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Export to PDF function
function exportToPdf() {
    // Show loading message
    const loadingModal = new bootstrap.Modal(document.getElementById('propertyModal'));
    document.getElementById('propertyModalLabel').textContent = 'Generating PDF';
    document.getElementById('propertyModalBody').innerHTML = `
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
        status: urlParams.get('status') || '',
        type: urlParams.get('type') || '',
        country: urlParams.get('country') || '',
        sort_by: urlParams.get('sort_by') || 'created_at',
        sort_order: urlParams.get('sort_order') || 'desc'
    };
    
    // Create PDF download link
    const pdfUrl = '{{ route("owner.property.export.pdf") }}?' + new URLSearchParams(searchParams).toString();
    const a = document.createElement('a');
    a.href = pdfUrl;
    a.download = 'properties_' + new Date().toISOString().split('T')[0] + '.pdf';
    a.click();
    
    // Hide loading modal after a short delay
    setTimeout(function() {
        loadingModal.hide();
    }, 1000);
}

// View property details
function viewProperty(propertyId) {
    // This would typically load property details via AJAX
    // For now, we'll show a simple message
    document.getElementById('propertyModalBody').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-3">Loading property details...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('propertyModal'));
    modal.show();
    
    // Simulate loading property details
    setTimeout(function() {
        document.getElementById('propertyModalBody').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Property details feature will be implemented soon.
            </div>
        `;
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
