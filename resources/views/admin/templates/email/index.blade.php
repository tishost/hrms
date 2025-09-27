@extends('layouts.admin')

@section('title', 'Email Templates')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Email Templates</h1>
            <p class="text-muted">Default templates are sent for certain events. You can create custom templates and assign them to specific events.</p>
        </div>
        <div>
            <a href="{{ route('admin.templates.email.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Email Template
            </a>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-globe"></i> Manage Languages
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- System Templates Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color: #4e73df; color: white;">
            <h6 class="m-0 font-weight-bold">System Messages</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="100">Status</th>
                            <th>Template Name</th>
                            <th width="150">Trigger Event</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($systemTemplates as $template)
                        <tr>
                            <td>
                                <div>
                                    @if($template->is_active)
                                        <span class="badge badge-success text-white">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-white">
                                            <i class="fas fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="template-name-row mb-1">
                                        @if($template->is_active)
                                            <span class="badge badge-success template-status-badge text-white">Active</span>
                                        @else
                                            <span class="badge badge-danger template-status-badge text-white">Inactive</span>
                                        @endif
                                        <strong>{{ $template->name }}</strong>
                                    </div>
                                    @if($template->description)
                                        <small class="text-dark d-block" style="color: #495057 !important;">{{ $template->description }}</small>
                                    @endif
                                    @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                        <div class="template-tags">
                                            @foreach($template->tags as $tag)
                                                <span class="badge badge-light badge-sm" style="color: #495057 !important; background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($template->trigger_event)
                                    @php
                                        $triggerInfo = \App\Config\EmailTriggers::getTrigger($template->trigger_event);
                                    @endphp
                                    @if($triggerInfo)
                                        <span class="badge badge-info">
                                            <i class="fas fa-bolt"></i> {{ $triggerInfo['name'] }}
                                        </span>
                                        <small class="d-block text-muted">{{ $triggerInfo['description'] }}</small>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-bolt"></i> {{ $template->trigger_event }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-light">
                                        <i class="fas fa-hand-paper"></i> Manual Only
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.templates.email.edit', $template) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Edit Template">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.templates.email.show', $template) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Template">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.templates.email.toggle-status', $template) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-{{ $template->is_active ? 'warning' : 'success' }}"
                                                title="{{ $template->is_active ? 'Deactivate' : 'Activate' }} Template">
                                            <i class="fas fa-{{ $template->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No system templates found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Owner Templates Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color: #1cc88a; color: white;">
            <h6 class="m-0 font-weight-bold">Owner Messages</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="100">Status</th>
                            <th>Template Name</th>
                            <th width="150">Trigger Event</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ownerTemplates as $template)
                        <tr>
                            <td>
                                <div>
                                    @if($template->is_active)
                                        <span class="badge badge-success text-white">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-white">
                                            <i class="fas fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="template-name-row mb-1">
                                        @if($template->is_active)
                                            <span class="badge badge-success template-status-badge text-white">Active</span>
                                        @else
                                            <span class="badge badge-danger template-status-badge text-white">Inactive</span>
                                        @endif
                                        <strong>{{ $template->name }}</strong>
                                    </div>
                                    @if($template->description)
                                        <small class="text-dark d-block" style="color: #495057 !important;">{{ $template->description }}</small>
                                    @endif
                                    @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                        <div class="template-tags">
                                            @foreach($template->tags as $tag)
                                                <span class="badge badge-light badge-sm" style="color: #495057 !important; background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($template->trigger_event)
                                    @php
                                        $triggerInfo = \App\Config\EmailTriggers::getTrigger($template->trigger_event);
                                    @endphp
                                    @if($triggerInfo)
                                        <span class="badge badge-info">
                                            <i class="fas fa-bolt"></i> {{ $triggerInfo['name'] }}
                                        </span>
                                        <small class="d-block text-muted">{{ $triggerInfo['description'] }}</small>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-bolt"></i> {{ $template->trigger_event }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-light">
                                        <i class="fas fa-hand-paper"></i> Manual Only
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.templates.email.edit', $template) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Edit Template">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.templates.email.show', $template) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Template">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.templates.email.toggle-status', $template) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-{{ $template->is_active ? 'warning' : 'success' }}"
                                                title="{{ $template->is_active ? 'Deactivate' : 'Activate' }} Template">
                                            <i class="fas fa-{{ $template->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No owner templates found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tenant Templates Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color: #36b9cc; color: white;">
            <h6 class="m-0 font-weight-bold">Tenant Messages</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="100">Status</th>
                            <th>Template Name</th>
                            <th width="150">Trigger Event</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenantTemplates as $template)
                        <tr>
                            <td>
                                <div>
                                    @if($template->is_active)
                                        <span class="badge badge-success text-white">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-white">
                                            <i class="fas fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="template-name-row mb-1">
                                        @if($template->is_active)
                                            <span class="badge badge-success template-status-badge text-white">Active</span>
                                        @else
                                            <span class="badge badge-danger template-status-badge text-white">Inactive</span>
                                        @endif
                                        <strong>{{ $template->name }}</strong>
                                    </div>
                                    @if($template->description)
                                        <small class="text-dark d-block" style="color: #495057 !important;">{{ $template->description }}</small>
                                    @endif
                                    @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                        <div class="template-tags">
                                            @foreach($template->tags as $tag)
                                                <span class="badge badge-light badge-sm" style="color: #495057 !important; background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($template->trigger_event)
                                    @php
                                        $triggerInfo = \App\Config\EmailTriggers::getTrigger($template->trigger_event);
                                    @endphp
                                    @if($triggerInfo)
                                        <span class="badge badge-info">
                                            <i class="fas fa-bolt"></i> {{ $triggerInfo['name'] }}
                                        </span>
                                        <small class="d-block text-muted">{{ $triggerInfo['description'] }}</small>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-bolt"></i> {{ $template->trigger_event }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge badge-light">
                                        <i class="fas fa-hand-paper"></i> Manual Only
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.templates.email.edit', $template) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Edit Template">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.templates.email.show', $template) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Template">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.templates.email.toggle-status', $template) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-{{ $template->is_active ? 'warning' : 'success' }}"
                                                title="{{ $template->is_active ? 'Deactivate' : 'Activate' }} Template">
                                            <i class="fas fa-{{ $template->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No tenant templates found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Templates
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $systemTemplates->count() + $ownerTemplates->count() + $tenantTemplates->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
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
                                Active Templates
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $systemTemplates->where('is_active', true)->count() + $ownerTemplates->where('is_active', true)->count() + $tenantTemplates->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                System Templates
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $systemTemplates->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cog fa-2x text-gray-300"></i>
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
                                Owner Templates
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $ownerTemplates->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid #e3e6f0;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

/* Status badge improvements */
.badge-success {
    background-color: #28a745 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
}

.badge-danger {
    background-color: #dc3545 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
}

.badge-success.text-white,
.badge-danger.text-white {
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
    font-weight: 600 !important;
}

/* Template name styling */
.template-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.template-status-badge {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 0.25rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
    color: #ffffff !important;
}

/* Improved spacing */
.table td {
    padding: 1rem 0.75rem;
}

.template-info {
    line-height: 1.4;
}

.template-tags {
    margin-top: 0.5rem;
}

.template-tags .badge {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

/* Ensure all text is visible */
.table td {
    color: #495057 !important;
}

.table td strong {
    color: #2c3e50 !important;
    font-weight: 600 !important;
}

/* Make sure status text is always visible */
.badge {
    font-weight: 600 !important;
}

.badge i {
    margin-right: 0.25rem;
}
</style>
@endpush
