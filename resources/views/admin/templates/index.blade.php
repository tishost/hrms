@extends('layouts.admin')

@section('title', 'Template Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-layer-group text-primary"></i>
                    Template Management
                </h1>
                <p class="page-subtitle text-muted">Manage all email and SMS templates for your system</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.templates.email.create') }}" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Create Email Template
                    </a>
                    <a href="{{ route('admin.templates.sms.create') }}" class="btn btn-success">
                        <i class="fas fa-sms"></i> Create SMS Template
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Template Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $systemEmailTemplates->count() + $ownerEmailTemplates->count() + $tenantEmailTemplates->count() }}</h3>
                    <p>Email Templates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-sms"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $systemSmsTemplates->count() + $ownerSmsTemplates->count() + $tenantSmsTemplates->count() }}</h3>
                    <p>SMS Templates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ ($systemEmailTemplates->where('is_active', true)->count() + $ownerEmailTemplates->where('is_active', true)->count() + $tenantEmailTemplates->where('is_active', true)->count() + $systemSmsTemplates->where('is_active', true)->count() + $ownerSmsTemplates->where('is_active', true)->count() + $tenantSmsTemplates->where('is_active', true)->count()) }}</h3>
                    <p>Active Templates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $systemEmailTemplates->count() + $systemSmsTemplates->count() }}</h3>
                    <p>System Templates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Templates Section -->
    <div class="template-section mb-5">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-cog text-primary"></i>
                <h2>System Templates</h2>
                <span class="section-badge badge-primary">Core System</span>
            </div>
            <p class="section-description">Essential templates for system operations, authentication, and security</p>
        </div>
        
        <div class="row">
            <!-- System Email Templates -->
            <div class="col-lg-6 mb-4">
                <div class="template-category-card">
                    <div class="category-header">
                        <div class="category-icon email-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="category-info">
                            <h4>Email Templates</h4>
                            <span class="template-count">{{ $systemEmailTemplates->count() }} templates</span>
                        </div>
                        <a href="{{ route('admin.templates.email.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="template-items">
                        @forelse($systemEmailTemplates as $template)
                        <div class="template-item">
                            <div class="template-status">
                                @if($template->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="template-content">
                                <h6 class="template-name">{{ $template->name }}</h6>
                                @if($template->description)
                                    <p class="template-description">{{ $template->description }}</p>
                                @endif
                                @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                    <div class="template-tags">
                                        @foreach($template->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="template-actions">
                                <a href="{{ route('admin.templates.email.edit', $template) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.email.show', $template) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <p>No system email templates found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- System SMS Templates -->
            <div class="col-lg-6 mb-4">
                <div class="template-category-card">
                    <div class="category-header">
                        <div class="category-icon sms-icon">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div class="category-info">
                            <h4>SMS Templates</h4>
                            <span class="template-count">{{ $systemSmsTemplates->count() }} templates</span>
                        </div>
                        <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="template-items">
                        @forelse($systemSmsTemplates as $template)
                        <div class="template-item">
                            <div class="template-status">
                                @if($template->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="template-content">
                                <h6 class="template-name">{{ $template->name }}</h6>
                                @if($template->description)
                                    <p class="template-description">{{ $template->description }}</p>
                                @endif
                                @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                    <div class="template-tags">
                                        @foreach($template->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="template-actions">
                                <a href="{{ route('admin.templates.sms.edit', $template) }}" class="btn btn-sm btn-outline-success" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.sms.show', $template) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-sms"></i>
                            <p>No system SMS templates found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner Templates Section -->
    <div class="template-section mb-5">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-user-tie text-success"></i>
                <h2>Owner Templates</h2>
                <span class="section-badge badge-success">Property Owners</span>
            </div>
            <p class="section-description">Templates for property owners including welcome messages, payment confirmations, and account management</p>
        </div>
        
        <div class="row">
            <!-- Owner Email Templates -->
            <div class="col-lg-6 mb-4">
                <div class="template-category-card">
                    <div class="category-header">
                        <div class="category-icon email-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="category-info">
                            <h4>Email Templates</h4>
                            <span class="template-count">{{ $ownerEmailTemplates->count() }} templates</span>
                        </div>
                        <a href="{{ route('admin.templates.email.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="template-items">
                        @forelse($ownerEmailTemplates as $template)
                        <div class="template-item">
                            <div class="template-status">
                                @if($template->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="template-content">
                                <h6 class="template-name">{{ $template->name }}</h6>
                                @if($template->description)
                                    <p class="template-description">{{ $template->description }}</p>
                                @endif
                                @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                    <div class="template-tags">
                                        @foreach($template->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="template-actions">
                                <a href="{{ route('admin.templates.email.edit', $template) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.email.show', $template) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <p>No owner email templates found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Owner SMS Templates -->
            <div class="col-lg-6 mb-4">
                <div class="template-category-card">
                    <div class="category-header">
                        <div class="category-icon sms-icon">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div class="category-info">
                            <h4>SMS Templates</h4>
                            <span class="template-count">{{ $ownerSmsTemplates->count() }} templates</span>
                        </div>
                        <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="template-items">
                        @forelse($ownerSmsTemplates as $template)
                        <div class="template-item">
                            <div class="template-status">
                                @if($template->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="template-content">
                                <h6 class="template-name">{{ $template->name }}</h6>
                                @if($template->description)
                                    <p class="template-description">{{ $template->description }}</p>
                                @endif
                                @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                    <div class="template-tags">
                                        @foreach($template->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="template-actions">
                                <a href="{{ route('admin.templates.sms.edit', $template) }}" class="btn btn-sm btn-outline-success" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.sms.show', $template) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-sms"></i>
                            <p>No owner SMS templates found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Templates Section -->
    <div class="template-section mb-5">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-users text-info"></i>
                <h2>Tenant Templates</h2>
                <span class="section-badge badge-info">Tenants</span>
            </div>
            <p class="section-description">Templates for tenants including welcome messages, rent reminders, payment confirmations, and lease management</p>
        </div>
        
        <div class="row">
            <!-- Tenant Email Templates -->
            <div class="col-lg-6 mb-4">
                <div class="template-category-card">
                    <div class="category-header">
                        <div class="category-icon email-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="category-info">
                            <h4>Email Templates</h4>
                            <span class="template-count">{{ $tenantEmailTemplates->count() }} templates</span>
                        </div>
                        <a href="{{ route('admin.templates.email.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="template-items">
                        @forelse($tenantEmailTemplates as $template)
                        <div class="template-item">
                            <div class="template-status">
                                @if($template->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="template-content">
                                <h6 class="template-name">{{ $template->name }}</h6>
                                @if($template->description)
                                    <p class="template-description">{{ $template->description }}</p>
                                @endif
                                @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                    <div class="template-tags">
                                        @foreach($template->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="template-actions">
                                <a href="{{ route('admin.templates.email.edit', $template) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.email.show', $template) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <p>No tenant email templates found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Tenant SMS Templates -->
            <div class="col-lg-6 mb-4">
                <div class="template-category-card">
                    <div class="category-header">
                        <div class="category-icon sms-icon">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div class="category-info">
                            <h4>SMS Templates</h4>
                            <span class="template-count">{{ $tenantSmsTemplates->count() }} templates</span>
                        </div>
                        <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="template-items">
                        @forelse($tenantSmsTemplates as $template)
                        <div class="template-item">
                            <div class="template-status">
                                @if($template->is_active)
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="template-content">
                                <h6 class="template-name">{{ $template->name }}</h6>
                                @if($template->description)
                                    <p class="template-description">{{ $template->description }}</p>
                                @endif
                                @if($template->tags && is_array($template->tags) && count($template->tags) > 0)
                                    <div class="template-tags">
                                        @foreach($template->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="template-actions">
                                <a href="{{ route('admin.templates.sms.edit', $template) }}" class="btn btn-sm btn-outline-success" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.sms.show', $template) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-sms"></i>
                            <p>No tenant SMS templates found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <div class="row">
            <div class="col-md-4">
                <div class="action-card">
                    <div class="action-icon email-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="action-content">
                        <h5>Email Templates</h5>
                        <p>Manage all email templates with advanced editing features</p>
                        <a href="{{ route('admin.templates.email.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Manage Email Templates
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-card">
                    <div class="action-icon sms-icon">
                        <i class="fas fa-sms"></i>
                    </div>
                    <div class="action-content">
                        <h5>SMS Templates</h5>
                        <p>Manage all SMS templates with character counting</p>
                        <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-success">
                            <i class="fas fa-arrow-right"></i> Manage SMS Templates
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-card">
                    <div class="action-icon create-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-content">
                        <h5>Create New</h5>
                        <p>Create new templates for your system</p>
                        <div class="btn-group w-100">
                            <a href="{{ route('admin.templates.email.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                            <a href="{{ route('admin.templates.sms.create') }}" class="btn btn-outline-success">
                                <i class="fas fa-sms"></i> SMS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-subtitle {
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
}

/* Statistics Cards */
.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card-primary { border-left: 4px solid #4e73df; }
.stat-card-success { border-left: 4px solid #1cc88a; }
.stat-card-info { border-left: 4px solid #36b9cc; }
.stat-card-warning { border-left: 4px solid #f6c23e; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-card-primary .stat-icon { background: #4e73df; }
.stat-card-success .stat-icon { background: #1cc88a; }
.stat-card-info .stat-icon { background: #36b9cc; }
.stat-card-warning .stat-icon { background: #f6c23e; }

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-weight: 500;
}

/* Template Sections */
.template-section {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.section-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fc;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.section-title h2 {
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0;
    color: #2c3e50;
}

.section-title i {
    font-size: 1.5rem;
}

.section-badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 1rem;
    font-weight: 600;
}

.badge-primary { background: #4e73df; color: white; }
.badge-success { background: #1cc88a; color: white; }
.badge-info { background: #36b9cc; color: white; }

.section-description {
    color: #6c757d;
    margin: 0;
    font-size: 1rem;
}

/* Template Category Cards */
.template-category-card {
    background: #f8f9fc;
    border-radius: 1rem;
    padding: 1.5rem;
    height: 100%;
    border: 1px solid #e3e6f0;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e3e6f0;
}

.category-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
}

.email-icon { background: #4e73df; }
.sms-icon { background: #1cc88a; }

.category-info {
    flex: 1;
}

.category-info h4 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    color: #2c3e50;
}

.template-count {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

/* Template Items */
.template-items {
    max-height: 400px;
    overflow-y: auto;
}

.template-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.template-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.template-status {
    flex-shrink: 0;
}

.status-badge {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    color: white;
}

.status-active { background: #1cc88a; }
.status-inactive { background: #e74a3b; }

.template-content {
    flex: 1;
}

.template-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: #2c3e50;
}

.template-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
}

.template-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.tag {
    background: #e9ecef;
    color: #495057;
    padding: 0.2rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.template-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 1rem;
}

/* Quick Actions */
.quick-actions-section {
    margin-top: 3rem;
}

.action-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.action-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto 1.5rem auto;
}

.action-card .email-icon { background: #4e73df; }
.action-card .sms-icon { background: #1cc88a; }
.action-card .create-icon { background: #f6c23e; }

.action-content h5 {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: #2c3e50;
}

.action-content p {
    color: #6c757d;
    margin: 0 0 1.5rem 0;
    line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .template-section {
        padding: 1rem;
    }
    
    .section-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .template-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .template-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
@endsection