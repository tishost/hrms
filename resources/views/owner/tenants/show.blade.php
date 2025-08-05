@extends('layouts.owner')

@section('title', 'Tenant Details')

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
            <h1>👤 Tenant Details</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Tenants</li>
                <li class="breadcrumb-item active">{{ $tenant->first_name }} {{ $tenant->last_name }}</li>
            </ul>
        </div>
        <div class="header-actions">
            <a href="{{ route('owner.tenants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tenants
            </a>
            <a href="{{ route('owner.tenants.edit', $tenant->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Tenant
            </a>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="summary-card bg-success">
                <div class="summary-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="summary-content">
                    <h3 class="summary-value">৳{{ number_format($totalPaid) }}</h3>
                    <p class="summary-label">Total Paid</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="summary-card bg-danger">
                <div class="summary-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="summary-content">
                    <h3 class="summary-value">৳{{ number_format($totalDue) }}</h3>
                    <p class="summary-label">Total Due</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="summary-card {{ $advanceBalance >= 0 ? 'bg-info' : 'bg-warning' }}">
                <div class="summary-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="summary-content">
                    <h3 class="summary-value">৳{{ number_format($advanceBalance) }}</h3>
                    <p class="summary-label">{{ $advanceBalance >= 0 ? 'Advance' : 'Balance' }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="summary-card bg-primary">
                <div class="summary-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="summary-content">
                    <h3 class="summary-value">{{ $nextDueDate ? \Carbon\Carbon::parse($nextDueDate->due_date)->format('M j') : 'N/A' }}</h3>
                    <p class="summary-label">Next Due Date</p>
                </div>
            </div>
        </div>
    </div>
    
    @if($nextDueDate)
    <div class="alert alert-warning mb-4">
        <i class="fas fa-clock"></i>
        <strong>Next Due:</strong> {{ \Carbon\Carbon::parse($nextDueDate->due_date)->format('F j, Y') }} - ৳{{ number_format($nextDueDate->amount) }}
    </div>
    @endif

    <!-- Tenant Information -->
    <div class="row">
        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="mb-0">{{ $tenant->first_name }} {{ $tenant->last_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <p class="mb-0">
                                @if(strtolower($tenant->gender) === 'male')
                                    <span class="label label-primary">Male</span>
                                @elseif(strtolower($tenant->gender) === 'female')
                                    <span class="label label-info">Female</span>
                                @else
                                    <span class="label label-default">{{ $tenant->gender ?: 'Not specified' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mobile Number</label>
                            <p class="mb-0">
                                <i class="fas fa-phone"></i> {{ $tenant->mobile ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Alternative Mobile</label>
                            <p class="mb-0">
                                <i class="fas fa-phone"></i> {{ $tenant->alt_mobile ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <p class="mb-0">
                                <i class="fas fa-envelope"></i> {{ $tenant->getRawOriginal('email') ?: 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NID Number</label>
                            <p class="mb-0">{{ $tenant->nid_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <p class="mb-0">{{ $tenant->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Country</label>
                            <p class="mb-0">{{ $tenant->country ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Total Family Members</label>
                            <p class="mb-0">{{ $tenant->total_family_member ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-briefcase"></i> Professional Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Occupation</label>
                            <p class="mb-0">{{ $tenant->occupation ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Company Name</label>
                            <p class="mb-0">{{ $tenant->company_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Has Driver</label>
                            <p class="mb-0">
                                @if($tenant->is_driver)
                                    <span class="label label-success">Yes</span>
                                    @if($tenant->driver_name)
                                        <br><small class="text-muted">Driver: {{ $tenant->driver_name }}</small>
                                    @endif
                                @else
                                    <span class="label label-default">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Assignment -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-home"></i> Property Assignment
                    </h5>
                </div>
                <div class="card-body">
                    @if($tenant->unit && $tenant->unit->property)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Property</label>
                                <p class="mb-0">{{ $tenant->unit->property->name }}</p>
                                @if($tenant->unit->property->address)
                                    <small class="text-muted">{{ $tenant->unit->property->address }}</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Unit</label>
                                <p class="mb-0">{{ $tenant->unit->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Check-in Date</label>
                                <p class="mb-0">
                                    @if($tenant->check_in_date)
                                        {{ \Carbon\Carbon::parse($tenant->check_in_date)->format('F j, Y') }}
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Security Deposit</label>
                                <p class="mb-0">৳{{ number_format($tenant->security_deposit ?? 0) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Property Assigned</h5>
                            <p class="text-muted">This tenant hasn't been assigned to any property yet.</p>
                            <a href="{{ route('owner.rents.create', $tenant->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Assign Property
                            </a>
                        </div>
                    @endif
                </div>
            </div>



            <!-- Recent Payments -->
            @if($recentPayments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card"></i> Recent Payments
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M j, Y') }}</td>
                                    <td><strong>৳{{ number_format($payment->amount) }}</strong></td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($payment->type ?? 'Rent') }}</span>
                                    </td>
                                    <td>
                                        @if($payment->status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Invoice List -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice"></i> Invoice List
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($invoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices->take(10) as $invoice)
                                <tr>
                                    <td>
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                        <br><small class="text-muted">{{ ucfirst($invoice->type ?? 'Rent') }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M j, Y') }}</td>
                                    <td>
                                        @if($invoice->due_date)
                                            <span class="{{ \Carbon\Carbon::parse($invoice->due_date)->isPast() && $invoice->status === 'unpaid' ? 'text-danger' : '' }}">
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('M j, Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td><strong>৳{{ number_format($invoice->amount) }}</strong></td>
                                    <td>৳{{ number_format($invoice->paid_amount ?? 0) }}</td>
                                    <td>
                                        @if($invoice->status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($invoice->status === 'partial')
                                            <span class="badge badge-warning">Partial</span>
                                        @else
                                            <span class="badge badge-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="#" class="btn btn-outline-info" title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($invoice->status !== 'paid')
                                            <a href="#" class="btn btn-outline-success" title="Record Payment">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Invoices Found</h5>
                        <p class="text-muted">No invoices have been generated for this tenant yet.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Remarks -->
            @if($tenant->remarks)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note"></i> Remarks
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tenant->remarks }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Status
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($tenant->status === 'active')
                        <div class="mb-3">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                        </div>
                        <h4 class="text-success">Active Tenant</h4>
                        <p class="text-muted">This tenant is currently active</p>
                    @else
                        <div class="mb-3">
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                        </div>
                        <h4 class="text-danger">Inactive Tenant</h4>
                        <p class="text-muted">This tenant is currently inactive</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('owner.tenants.edit', $tenant->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Tenant
                        </a>
                        @if (empty($tenant->unit_id))
                            <a href="{{ route('owner.rents.create', $tenant->id) }}" class="btn btn-outline-success">
                                <i class="fas fa-home"></i> Assign Property
                            </a>
                        @else
                            @if($tenant->status === 'active')
                                <a href="{{ route('owner.checkouts.create', $tenant->id) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-sign-out-alt"></i> Check-out
                                </a>
                            @else
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-times"></i> Already Checked Out
                                </button>
                            @endif
                        @endif
                        <a href="#" class="btn btn-outline-info">
                            <i class="fas fa-file-pdf"></i> Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-address-book"></i> Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Primary Mobile</label>
                        <p class="mb-0">
                            <i class="fas fa-phone"></i> {{ $tenant->mobile ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alternative Mobile</label>
                        <p class="mb-0">
                            <i class="fas fa-phone"></i> {{ $tenant->alt_mobile ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <p class="mb-0">
                            <i class="fas fa-envelope"></i> {{ $tenant->email ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Address</label>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt"></i> {{ $tenant->address ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
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

.badge-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

.badge-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

/* Label Styles */
.label {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.label-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    color: white;
}

.label-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.label-default {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.label-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
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

/* Form label styles */
.form-label.fw-bold {
    color: #2c3e50;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

/* Card body text */
.card-body p {
    color: #495057;
    margin-bottom: 0;
}

.card-body small {
    color: #6c757d;
}

/* Summary Cards */
.summary-card {
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.summary-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.summary-content {
    position: relative;
    z-index: 1;
}

.summary-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 8px;
    color: white;
}

.summary-label {
    font-size: 1rem;
    margin-bottom: 0;
    opacity: 0.9;
    color: white;
}

/* Card background colors */
.summary-card.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.summary-card.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.summary-card.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.summary-card.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.summary-card.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

/* Responsive summary cards */
@media (max-width: 768px) {
    .summary-card {
        padding: 20px;
    }
    
    .summary-icon {
        font-size: 2rem;
        margin-bottom: 12px;
    }
    
    .summary-value {
        font-size: 1.5rem;
    }
    
    .summary-label {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .summary-card {
        padding: 15px;
    }
    
    .summary-icon {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .summary-value {
        font-size: 1.3rem;
    }
    
    .summary-label {
        font-size: 0.8rem;
    }
}
</style>
@endsection 