@extends('admin.layouts.app')

@section('title', 'Ad Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Ad Details: {{ $ad->title }}</h3>
                    <div>
                        <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Ad
                        </a>
                        <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Ads
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Ad Image -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Ad Image</h5>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" 
                                         class="img-fluid rounded" style="max-height: 400px;">
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Title</label>
                                                <p class="form-control-plaintext">{{ $ad->title }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge bg-{{ $ad->status_color }}">{{ $ad->status_text }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($ad->description)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <p class="form-control-plaintext">{{ $ad->description }}</p>
                                    </div>
                                    @endif

                                    @if($ad->url)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">URL</label>
                                        <p class="form-control-plaintext">
                                            <a href="{{ $ad->url }}" target="_blank" class="text-decoration-none">
                                                {{ $ad->url }} <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Display Settings -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Display Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Display Order</label>
                                                <p class="form-control-plaintext">{{ $ad->display_order }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Active Status</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge bg-{{ $ad->is_active ? 'success' : 'danger' }}">
                                                        {{ $ad->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Display Locations</label>
                                        <div class="d-flex gap-2">
                                            @if($ad->show_on_owner_dashboard)
                                                <span class="badge bg-info">Owner Dashboard</span>
                                            @endif
                                            @if($ad->show_on_tenant_dashboard)
                                                <span class="badge bg-success">Tenant Dashboard</span>
                                            @endif
                                            @if(!$ad->show_on_owner_dashboard && !$ad->show_on_tenant_dashboard)
                                                <span class="text-muted">No display locations selected</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Range -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Display Period</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Start Date</label>
                                                <p class="form-control-plaintext">{{ $ad->start_date->format('F d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">End Date</label>
                                                <p class="form-control-plaintext">{{ $ad->end_date->format('F d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $daysRemaining = $today->diffInDays($ad->end_date, false);
                                    @endphp

                                    @if($daysRemaining > 0)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            This ad will expire in {{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }}.
                                        </div>
                                    @elseif($daysRemaining == 0)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            This ad expires today.
                                        </div>
                                    @else
                                        <div class="alert alert-secondary">
                                            <i class="fas fa-clock"></i>
                                            This ad expired {{ abs($daysRemaining) }} day{{ abs($daysRemaining) != 1 ? 's' : '' }} ago.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Performance Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="text-success">
                                                <h3 class="mb-1">{{ number_format($ad->clicks_count) }}</h3>
                                                <small class="text-muted">Total Clicks</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-info">
                                                <h3 class="mb-1">{{ number_format($ad->impressions_count) }}</h3>
                                                <small class="text-muted">Total Impressions</small>
                                            </div>
                                        </div>
                                    </div>

                                    @if($ad->impressions_count > 0)
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">Click Rate</small>
                                            <small class="text-muted">
                                                {{ number_format(($ad->clicks_count / $ad->impressions_count) * 100, 2) }}%
                                            </small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ min(($ad->clicks_count / $ad->impressions_count) * 100, 100) }}%"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm toggle-status-btn" 
                                                data-ad-id="{{ $ad->id }}" 
                                                data-current-status="{{ $ad->is_active ? '1' : '0' }}">
                                            <i class="fas fa-toggle-{{ $ad->is_active ? 'on' : 'off' }}"></i>
                                            {{ $ad->is_active ? 'Deactivate' : 'Activate' }} Ad
                                        </button>
                                        
                                        @if($ad->url)
                                        <a href="{{ $ad->url }}" target="_blank" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Visit URL
                                        </a>
                                        @endif
                                        
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="copyAdUrl()">
                                            <i class="fas fa-copy"></i> Copy Image URL
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">Timestamps</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <small class="text-muted">Created</small>
                                        <p class="mb-1">{{ $ad->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Last Updated</small>
                                        <p class="mb-1">{{ $ad->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                    @if($ad->created_at != $ad->updated_at)
                                    <div class="mb-0">
                                        <small class="text-muted">Age</small>
                                        <p class="mb-0">{{ $ad->created_at->diffForHumans() }}</p>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle ad status
    $('.toggle-status-btn').on('click', function() {
        const adId = $(this).data('ad-id');
        const currentStatus = $(this).data('current-status');
        const newStatus = currentStatus === '1' ? '0' : '1';
        
        $.ajax({
            url: `/admin/ads/${adId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                    
                    // Reload page to update status
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Failed to update ad status');
            }
        });
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.card-body').first().prepend(alertHtml);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});

// Copy ad image URL to clipboard
function copyAdUrl() {
    const imageUrl = '{{ $ad->image_url }}';
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(imageUrl).then(function() {
            showCopySuccess();
        }, function() {
            fallbackCopyTextToClipboard(imageUrl);
        });
    } else {
        fallbackCopyTextToClipboard(imageUrl);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showCopySuccess();
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }
    
    document.body.removeChild(textArea);
}

function showCopySuccess() {
    // Show a temporary success message
    const btn = document.querySelector('.btn-outline-warning');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
    btn.classList.remove('btn-outline-warning');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-warning');
    }, 2000);
}
</script>
@endpush
