@extends('layouts.owner')

@section('title', 'Tenant Details')

@section('content')
<div class="container-fluid tenant-details-page">
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

    <!-- Tab Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="tenantTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" data-tab="overview">
                        <i class="fas fa-user"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" data-tab="financial">
                        <i class="fas fa-money-bill-wave"></i> Financial
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" data-tab="notifications">
                        <i class="fas fa-bell"></i> Notifications
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="tenantTabsContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade" id="overview" role="tabpanel">
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
                                <i class="fas fa-envelope"></i> {{ $tenant->email ?? 'N/A' }}
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

                <!-- Financial Tab -->
                <div class="tab-pane fade financial-tab" id="financial" role="tabpanel">
                    <div class="row">
            <!-- Recent Payments -->
            @if($recentPayments->count() > 0)
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card"></i> Recent Payments
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                                <div class="card-body p-0 d-flex flex-column">
                                    <div class="table-responsive flex-grow-1">
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
                                                @foreach($recentPayments->take(5) as $payment)
                                                <tr>
                                                    <td>
                                                        <small>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M j, Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>৳{{ number_format($payment->amount) }}</strong>
                                                    </td>
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
            </div>
            @endif

                        <!-- Invoice List -->
                        <div class="{{ $recentPayments->count() > 0 ? 'col-lg-6' : 'col-lg-12' }} mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-invoice"></i> Invoice List
                                        <span class="badge bg-primary ms-2">{{ $invoices->count() }}</span>
                                    </h5>
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-outline-primary">
                                            <i class="fas fa-plus"></i> New
                                        </a>
                                        <a href="#" class="btn btn-outline-secondary">
                                            <i class="fas fa-list"></i> All
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0 d-flex flex-column">
                                    @if($invoices->count() > 0)
                                    <div class="table-responsive flex-grow-1">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0">
                                                        <small class="text-muted fw-bold">INVOICE</small>
                                                    </th>
                                                    <th class="border-0">
                                                        <small class="text-muted fw-bold">DATE</small>
                                                    </th>
                                                    <th class="border-0">
                                                        <small class="text-muted fw-bold">AMOUNT</small>
                                                    </th>
                                                    <th class="border-0">
                                                        <small class="text-muted fw-bold">STATUS</small>
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <small class="text-muted fw-bold">ACTIONS</small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoices->take(5) as $invoice)
                                                <tr class="border-0">
                                                    <td class="border-0 py-2">
                                                        <div class="d-flex flex-column">
                                                            <strong class="text-dark mb-1">{{ $invoice->invoice_number }}</strong>
                                                            <small class="text-muted">{{ ucfirst($invoice->type ?? 'Rent') }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="border-0 py-2">
                                                        <div class="d-flex flex-column">
                                                            <span class="text-dark fw-medium mb-1">
                                                                {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M j, Y') }}
                                                            </span>
                                                            @if($invoice->due_date)
                                                            <small class="{{ \Carbon\Carbon::parse($invoice->due_date)->isPast() && strtolower($invoice->status) === 'unpaid' ? 'text-danger' : 'text-muted' }}">
                                                                Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M j') }}
                                                            </small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="border-0 py-2">
                                                        <strong class="text-dark">৳{{ number_format($invoice->amount) }}</strong>
                                                    </td>
                                                    <td class="border-0 py-2">
                                                        <span class="text-dark fw-medium">{{ ucfirst($invoice->status ?? 'Unknown') }}</span>
                                                    </td>
                                                    <td class="border-0 py-2 text-center">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-primary btn-sm" title="View Invoice">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if(strtolower($invoice->status) !== 'paid')
                                                            <button type="button" class="btn btn-outline-success btn-sm" title="Record Payment">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            @endif
                                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($invoices->count() > 5)
                                    <div class="card-footer bg-light text-center py-2">
                                        <small class="text-muted">
                                            Showing 5 of {{ $invoices->count() }} invoices
                                            <a href="#" class="text-primary ms-1">View all</a>
                                        </small>
                                    </div>
                                    @endif
                                    @else
                                    <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center">
                                        <div class="mb-3">
                                            <i class="fas fa-file-invoice fa-4x text-muted opacity-50"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">No Invoices Found</h5>
                                        <p class="text-muted mb-3">No invoices have been generated for this tenant yet.</p>
                                        <button type="button" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Create First Invoice
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                </div>
        </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade notifications-tab" id="notifications" role="tabpanel">
                    <div class="row">
                        <!-- Send Notification Form -->
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                                        <i class="fas fa-paper-plane"></i> Send Notification
                    </h5>
                </div>
                                <div class="card-body d-flex flex-column">
                                    <form action="{{ route('owner.tenants.send-notification', $tenant->id) }}" method="POST">
                                        @csrf
                        <div class="mb-3">
                                            <label for="notification_type" class="form-label">Notification Type</label>
                                            <select class="form-control @error('notification_type') is-invalid @enderror" 
                                                    id="notification_type" name="notification_type" required>
                                                <option value="">Select Type</option>
                                                <option value="email" {{ old('notification_type') == 'email' ? 'selected' : '' }}>
                                                    <i class="fas fa-envelope"></i> Email
                                                </option>
                                                <option value="sms" {{ old('notification_type') == 'sms' ? 'selected' : '' }}>
                                                    <i class="fas fa-sms"></i> SMS
                                                </option>
                                            </select>
                                            @error('notification_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                        </div>

                                        <div class="mb-3" id="template_selection">
                                            <label for="template_id" class="form-label">Template</label>
                                            <select class="form-control @error('template_id') is-invalid @enderror" 
                                                    id="template_id" name="template_id" required>
                                                <option value="">Select Template</option>
                                            </select>
                                            @error('template_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3" id="subject_field" style="display: none;">
                                            <label for="subject" class="form-label">Subject</label>
                                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                                   id="subject" name="subject" value="{{ old('subject') }}">
                                            @error('subject')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                        <div class="mb-3">
                                            <label for="message" class="form-label">Message</label>
                                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                                      id="message" name="message" rows="4" required 
                                                      placeholder="Enter your message here...">{{ old('message') }}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                        </div>

                                        <div class="d-grid mt-auto">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Send Notification
                                            </button>
                                        </div>
                                    </form>
                                </div>
                </div>
            </div>

                        <!-- Notification Logs -->
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header d-flex align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-history"></i> Notification History
                                    </h5>
                                </div>
                                <div class="card-body p-0 d-flex flex-column">
                                    @if($notificationLogs->count() > 0)
                                    <div class="table-responsive flex-grow-1">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Type</th>
                                                    <th>Recipient</th>
                                                    <th>Subject/Message</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($notificationLogs as $log)
                                                <tr>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($log->created_at)->format('M j, Y') }}<br>
                                                            {{ \Carbon\Carbon::parse($log->created_at)->format('g:i A') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        @if($log->type === 'email')
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-envelope"></i> Email
                                                            </span>
                        @else
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-sms"></i> SMS
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $log->recipient ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <div style="max-width: 200px;">
                                                            @if($log->type === 'email')
                                                                <strong>{{ $log->subject ?? 'No Subject' }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ Str::limit($log->content, 50) }}</small>
                            @else
                                                                <small>{{ Str::limit($log->content, 60) }}</small>
                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($log->status === 'sent')
                                                            <span class="badge bg-success">Sent</span>
                                                        @elseif($log->status === 'failed')
                                                            <span class="badge bg-danger">Failed</span>
                                                        @elseif($log->status === 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-info" 
                                                                onclick="viewNotification({{ $log->id }})" 
                                                                title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                    </div>
                                    @else
                                    <div class="text-center py-4 flex-grow-1 d-flex flex-column justify-content-center">
                                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Notifications Sent</h5>
                                        <p class="text-muted">No notifications have been sent to this tenant yet.</p>
                </div>
                                    @endif
            </div>
                </div>
                    </div>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notificationDetails">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence functionality
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Function to show a specific tab
    function showTab(tabName) {
        // Remove active class from all tabs and panes
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabPanes.forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Add active class to selected tab and pane
        const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
        const activePane = document.getElementById(tabName);
        
        if (activeTab && activePane) {
            activeTab.classList.add('active');
            activePane.classList.add('show', 'active');
        }
    }
    
    // Check URL hash on page load
    const hash = window.location.hash.substring(1);
    if (hash && ['overview', 'financial', 'notifications'].includes(hash)) {
        showTab(hash);
    } else {
        // Default to overview tab
        showTab('overview');
    }
    
    // Add click event listeners to tabs
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            if (tabName) {
                // Update URL hash
                window.location.hash = tabName;
                showTab(tabName);
            }
        });
    });
    
    // Listen for browser back/forward buttons
    window.addEventListener('hashchange', function() {
        const hash = window.location.hash.substring(1);
        if (hash && ['overview', 'financial', 'notifications'].includes(hash)) {
            showTab(hash);
        }
    });

    const notificationType = document.getElementById('notification_type');
    const templateSelect = document.getElementById('template_id');
    const subjectField = document.getElementById('subject_field');
    
    // Email templates
    const emailTemplates = @json($emailTemplates);
    // SMS templates  
    const smsTemplates = @json($smsTemplates);
    
    notificationType.addEventListener('change', function() {
        const type = this.value;
        
        // Clear template options
        templateSelect.innerHTML = '<option value="">Select Template</option>';
        
        // Show/hide subject field
        if (type === 'email') {
            subjectField.style.display = 'block';
            document.getElementById('subject').required = true;
            
            // Add email templates
            emailTemplates.forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = template.name;
                templateSelect.appendChild(option);
            });
        } else if (type === 'sms') {
            subjectField.style.display = 'none';
            document.getElementById('subject').required = false;
            
            // Add SMS templates
            smsTemplates.forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = template.name;
                templateSelect.appendChild(option);
            });
        } else {
            subjectField.style.display = 'none';
            document.getElementById('subject').required = false;
        }
    });
    
    // Template selection change
    templateSelect.addEventListener('change', function() {
        const templateId = this.value;
        const type = notificationType.value;
        
        if (templateId && type) {
            const templates = type === 'email' ? emailTemplates : smsTemplates;
            const template = templates.find(t => t.id == templateId);
            
            if (template) {
                // Pre-fill message with template content
                document.getElementById('message').value = template.content || '';
                
                // Pre-fill subject if email
                if (type === 'email' && template.subject) {
                    document.getElementById('subject').value = template.subject;
                }
            }
        }
    });
});

function viewNotification(logId) {
    // This would typically make an AJAX call to get notification details
    // For now, we'll show a placeholder
    document.getElementById('notificationDetails').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i>
            <p class="text-muted">Loading notification details...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
    modal.show();
    
    // Simulate loading (replace with actual AJAX call)
    setTimeout(() => {
        document.getElementById('notificationDetails').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Notification ID:</strong> ${logId}
            </div>
            <p>This feature will show detailed notification information including delivery status, error messages, and full content.</p>
        `;
    }, 1000);
}
</script>
@endsection

@section('styles')
<style>
/* Tenant Details Page Specific Styles */
.tenant-details-page .card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.tenant-details-page .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
    padding: 1rem 1.25rem;
    border-bottom: none;
}

.tenant-details-page .card-header .card-title {
    color: white;
    font-weight: 600;
    margin: 0;
}

.tenant-details-page .card-header .card-title i {
    margin-right: 8px;
    opacity: 0.9;
}

/* Tab Styles */
.tenant-details-page .nav-tabs .nav-link {
    border: none;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    margin-right: 5px;
}

.tenant-details-page .nav-tabs .nav-link:hover {
    border: none;
    color: white;
    background: rgba(255, 255, 255, 0.2);
}

.tenant-details-page .nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px 8px 0 0;
}

.tenant-details-page .nav-tabs .nav-link i {
    margin-right: 8px;
}


/* Button Styles */
.tenant-details-page .btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.tenant-details-page .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.tenant-details-page .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Badge Styles */
.tenant-details-page .badge {
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block !important;
    text-align: center;
    line-height: 1.2;
    white-space: nowrap;
}

.tenant-details-page .badge-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.tenant-details-page .badge-danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.tenant-details-page .badge-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.tenant-details-page .badge-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.tenant-details-page .badge-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%) !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
    font-weight: 700;
}

/* Force badge styling */
.tenant-details-page .badge {
    display: inline-block !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: baseline !important;
}


.tenant-details-page .badge-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}


/* Label Styles */
.tenant-details-page .label {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tenant-details-page .label-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    color: white;
}

.tenant-details-page .label-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.tenant-details-page .label-default {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.tenant-details-page .label-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

/* Page Header Styles */
.tenant-details-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
}

.tenant-details-page .page-title h1 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.tenant-details-page .breadcrumb {
    margin-bottom: 0;
    font-size: 0.9rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tenant-details-page .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .tenant-details-page .header-actions {
        align-self: flex-end;
    }
}

/* Form label styles */
.tenant-details-page .form-label.fw-bold {
    color: #2c3e50;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

/* Card body text */
.tenant-details-page .card-body p {
    color: #495057;
    margin-bottom: 0;
}

.tenant-details-page .card-body small {
    color: #6c757d;
}

/* Summary Cards */
.tenant-details-page .summary-card {
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.tenant-details-page .summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.tenant-details-page .summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.tenant-details-page .summary-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.tenant-details-page .summary-content {
    position: relative;
    z-index: 1;
}

.tenant-details-page .summary-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 8px;
    color: white;
}

.tenant-details-page .summary-label {
    font-size: 1rem;
    margin-bottom: 0;
    opacity: 0.9;
    color: white;
}

/* Card background colors */
.tenant-details-page .summary-card.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.tenant-details-page .summary-card.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.tenant-details-page .summary-card.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.tenant-details-page .summary-card.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.tenant-details-page .summary-card.bg-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

/* Financial Tab Table Styles */
.tenant-details-page .financial-tab .table th {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.75rem 0.5rem;
    border-bottom: 2px solid #dee2e6;
}

.tenant-details-page .financial-tab .table td {
    font-size: 0.85rem;
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.tenant-details-page .financial-tab .table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.tenant-details-page .financial-tab .card {
    min-height: 400px;
}

.tenant-details-page .financial-tab .card-body {
    min-height: 300px;
}

/* Notifications Tab Styles */
.tenant-details-page .notifications-tab .card {
    min-height: 500px;
}

.tenant-details-page .notifications-tab .card-body {
    min-height: 400px;
}

.tenant-details-page .notifications-tab .table th {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.75rem 0.5rem;
    border-bottom: 2px solid #dee2e6;
}

.tenant-details-page .notifications-tab .table td {
    font-size: 0.85rem;
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.tenant-details-page .notifications-tab .table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.tenant-details-page .notifications-tab .form-control {
    font-size: 0.9rem;
}

.tenant-details-page .notifications-tab .form-label {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Responsive summary cards */
@media (max-width: 768px) {
    .tenant-details-page .summary-card {
        padding: 20px;
    }
    
    .tenant-details-page .summary-icon {
        font-size: 2rem;
        margin-bottom: 12px;
    }
    
    .tenant-details-page .summary-value {
        font-size: 1.5rem;
    }
    
    .tenant-details-page .summary-label {
        font-size: 0.9rem;
    }
    
    .tenant-details-page .financial-tab .table th,
    .tenant-details-page .financial-tab .table td {
        font-size: 0.8rem;
        padding: 0.5rem 0.25rem;
    }
    
    .tenant-details-page .notifications-tab .table th,
    .tenant-details-page .notifications-tab .table td {
        font-size: 0.8rem;
        padding: 0.5rem 0.25rem;
    }
    
    .tenant-details-page .notifications-tab .card {
        min-height: 400px;
    }
    
    .tenant-details-page .notifications-tab .card-body {
        min-height: 300px;
    }
}

@media (max-width: 576px) {
    .tenant-details-page .summary-card {
        padding: 15px;
    }
    
    .tenant-details-page .summary-icon {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .tenant-details-page .summary-value {
        font-size: 1.3rem;
    }
    
    .tenant-details-page .summary-label {
        font-size: 0.8rem;
    }
}
</style>
@endsection 