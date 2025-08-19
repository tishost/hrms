@extends('layouts.admin')

@section('title', 'Ads Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Ads Management</h3>
                    <a href="{{ route('admin.ads.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Ad
                    </a>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="adsTable">
                            <thead>
                                <tr>
                                    <th width="50">Order</th>
                                    <th width="100">Image</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Display On</th>
                                    <th>Date Range</th>
                                    <th>Stats</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                <tr data-ad-id="{{ $ad->id }}">
                                    <td>
                                        <input type="number" class="form-control form-control-sm order-input" 
                                               value="{{ $ad->display_order }}" min="0" style="width: 60px;">
                                    </td>
                                    <td>
                                        <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}" 
                                             class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $ad->title }}</strong>
                                        @if($ad->description)
                                            <br><small class="text-muted">{{ Str::limit($ad->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ad->status_color }}">{{ $ad->status_text }}</span>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input toggle-status" type="checkbox" 
                                                   data-ad-id="{{ $ad->id }}" 
                                                   {{ $ad->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small">Active</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($ad->show_on_owner_dashboard)
                                                <span class="badge bg-info">Owner Dashboard</span>
                                            @endif
                                            @if($ad->show_on_tenant_dashboard)
                                                <span class="badge bg-success">Tenant Dashboard</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Start:</strong> {{ $ad->start_date->format('M d, Y') }}<br>
                                            <strong>End:</strong> {{ $ad->end_date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            <div class="text-success">Clicks: {{ $ad->clicks_count }}</div>
                                            <div class="text-info">Impressions: {{ $ad->impressions_count }}</div>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.ads.show', $ad) }}" 
                                               class="btn btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.ads.edit', $ad) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger delete-ad" 
                                                    data-ad-id="{{ $ad->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-image fa-3x mb-3"></i>
                                            <p>No ads found. Create your first ad to get started!</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($ads->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $ads->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this ad? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle ad status
    $('.toggle-status').on('change', function() {
        const adId = $(this).data('ad-id');
        const isChecked = $(this).is(':checked');
        
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
                    
                    // Reload page to update status badges
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Failed to update ad status');
                // Revert checkbox
                $(this).prop('checked', !isChecked);
            }
        });
    });

    // Update display order
    let orderTimeout;
    $('.order-input').on('input', function() {
        clearTimeout(orderTimeout);
        const $input = $(this);
        const adId = $input.closest('tr').data('ad-id');
        const newOrder = $input.val();
        
        orderTimeout = setTimeout(() => {
            updateAdOrder(adId, newOrder);
        }, 500);
    });

    function updateAdOrder(adId, order) {
        $.ajax({
            url: '/admin/ads/update-order',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                ads: [{
                    id: adId,
                    display_order: order
                }]
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                showAlert('error', 'Failed to update display order');
            }
        });
    }

    // Delete ad
    let adIdToDelete;
    
    $('.delete-ad').on('click', function() {
        adIdToDelete = $(this).data('ad-id');
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDelete').on('click', function() {
        if (adIdToDelete) {
            $.ajax({
                url: `/admin/ads/${adIdToDelete}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    showAlert('success', 'Ad deleted successfully!');
                    setTimeout(() => location.reload(), 1000);
                },
                error: function(xhr) {
                    showAlert('error', 'Failed to delete ad');
                }
            });
        }
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
        
        $('.card-body').prepend(alertHtml);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush
