@extends('layouts.admin')

@section('title', 'Send Push Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i> Send Push Notifications
                    </h3>
                </div>
                <div class="card-body">
                    <form id="notificationForm" action="{{ route('admin.notifications.send') }}" method="POST">
                        @csrf
                        
                        <!-- Target Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Target Audience</label>
                                <select class="form-select" id="targetType" name="target_type" required>
                                    <option value="">Select Target</option>
                                    <option value="all_users">All Users (Owners + Tenants)</option>
                                    <option value="all_owners">All Owners</option>
                                    <option value="all_tenants">All Tenants</option>
                                    <option value="specific_users">Specific Users</option>
                                    <option value="role_based">Role Based</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="roleSelection" style="display: none;">
                                <label class="form-label">Select Role</label>
                                <select class="form-select" id="roleId" name="role_id">
                                    <option value="">Select Role</option>
                                    @if($roles && $roles->count() > 0)
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No roles available</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Specific Users Selection -->
                        <div class="row mb-4" id="specificUsers" style="display: none;">
                            <div class="col-12">
                                <label class="form-label">Select Specific Users</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Owners</h6>
                                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                            @foreach($owners as $owner)
                                                <div class="form-check">
                                                    <input class="form-check-input owner-checkbox" type="checkbox" 
                                                           value="{{ $owner->id }}" name="user_ids[]" id="owner_{{ $owner->id }}">
                                                    <label class="form-check-label" for="owner_{{ $owner->id }}">
                                                        {{ $owner->name }} ({{ $owner->mobile }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Tenants</h6>
                                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                            @foreach($tenants as $tenant)
                                                <div class="form-check">
                                                    <input class="form-check-input tenant-checkbox" type="checkbox" 
                                                           value="{{ $tenant->id }}" name="user_ids[]" id="tenant_{{ $tenant->id }}">
                                                    <label class="form-check-label" for="tenant_{{ $tenant->id }}">
                                                        {{ $tenant->name }} ({{ $tenant->mobile }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Type -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Notification Type</label>
                                <select class="form-select" name="notification_type" required>
                                    <option value="general">General</option>
                                    <option value="rent_reminder">Rent Reminder</option>
                                    <option value="payment_confirmation">Payment Confirmation</option>
                                    <option value="maintenance_request">Maintenance Request</option>
                                    <option value="subscription_expiry">Subscription Expiry</option>
                                    <option value="announcement">Announcement</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority" required>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Notification Title</label>
                                <input type="text" class="form-control" name="title" 
                                       placeholder="Enter notification title" required maxlength="100">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Notification Message</label>
                                <textarea class="form-control" name="body" rows="4" 
                                          placeholder="Enter notification message" required maxlength="500"></textarea>
                                <small class="text-muted">Maximum 500 characters</small>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Action URL (Optional)</label>
                                <input type="url" class="form-control" name="action_url" 
                                       placeholder="https://example.com/action">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Image URL (Optional)</label>
                                <input type="url" class="form-control" name="image_url" 
                                       placeholder="https://example.com/image.jpg">
                            </div>
                        </div>

                        <!-- Schedule Options -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="scheduleNotification" name="schedule_notification">
                                    <label class="form-check-label" for="scheduleNotification">
                                        Schedule Notification
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6" id="scheduleDateTime" style="display: none;">
                                <label class="form-label">Schedule Date & Time</label>
                                <input type="datetime-local" class="form-control" name="scheduled_at">
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-info" id="previewBtn">
                                    <i class="fas fa-eye"></i> Preview Notification
                                </button>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="sendBtn">
                                    <i class="fas fa-paper-plane"></i> Send Notification
                                </button>
                                <button type="button" class="btn btn-warning" id="testBtn">
                                    <i class="fas fa-vial"></i> Send Test
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Templates -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-template"></i> Quick Templates
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card template-card" data-template="rent_reminder">
                                <div class="card-body">
                                    <h6>Rent Reminder</h6>
                                    <p class="text-muted">Remind tenants about rent payment</p>
                                    <button class="btn btn-sm btn-outline-primary use-template">Use Template</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card template-card" data-template="maintenance">
                                <div class="card-body">
                                    <h6>Maintenance Notice</h6>
                                    <p class="text-muted">Notify about maintenance work</p>
                                    <button class="btn btn-sm btn-outline-primary use-template">Use Template</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card template-card" data-template="announcement">
                                <div class="card-body">
                                    <h6>General Announcement</h6>
                                    <p class="text-muted">Send general announcements</p>
                                    <button class="btn btn-sm btn-outline-primary use-template">Use Template</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="notification-preview">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-3">
                            <i class="fas fa-bell text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 id="previewTitle" class="mb-1"></h6>
                            <p id="previewBody" class="mb-2 text-muted"></p>
                            <small id="previewType" class="text-info"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Target type change handler
    $('#targetType').change(function() {
        const targetType = $(this).val();
        
        // Hide all conditional sections
        $('#roleSelection, #specificUsers').hide();
        
        // Show relevant sections
        if (targetType === 'role_based') {
            // Check if roles are available
            const roleOptions = $('#roleId option').length;
            if (roleOptions > 1) {
                $('#roleSelection').show();
            } else {
                alert('No roles available. Please select a different target type.');
                $(this).val('');
            }
        } else if (targetType === 'specific_users') {
            $('#specificUsers').show();
        }
    });

    // Schedule notification handler
    $('#scheduleNotification').change(function() {
        if ($(this).is(':checked')) {
            $('#scheduleDateTime').show();
        } else {
            $('#scheduleDateTime').hide();
        }
    });

    // Preview button handler
    $('#previewBtn').click(function() {
        const title = $('input[name="title"]').val();
        const body = $('textarea[name="body"]').val();
        const type = $('select[name="notification_type"]').val();
        
        if (!title || !body) {
            alert('Please fill in title and message to preview');
            return;
        }
        
        $('#previewTitle').text(title);
        $('#previewBody').text(body);
        $('#previewType').text('Type: ' + type.charAt(0).toUpperCase() + type.slice(1));
        $('#previewModal').modal('show');
    });

    // Template handlers
    $('.use-template').click(function() {
        const template = $(this).closest('.template-card').data('template');
        applyTemplate(template);
    });

    // Form submission with loading state
    $('#notificationForm').submit(function(e) {
        e.preventDefault();
        
        // Basic form validation
        const title = $('input[name="title"]').val().trim();
        const body = $('textarea[name="body"]').val().trim();
        const targetType = $('#targetType').val();
        
        if (!title) {
            alert('Please enter a notification title');
            $('input[name="title"]').focus();
            return;
        }
        
        if (!body) {
            alert('Please enter a notification message');
            $('textarea[name="body"]').focus();
            return;
        }
        
        if (!targetType) {
            alert('Please select a target audience');
            $('#targetType').focus();
            return;
        }
        
        // Validate specific users selection
        if (targetType === 'specific_users') {
            const selectedUsers = $('input[name="user_ids[]"]:checked').length;
            if (selectedUsers === 0) {
                alert('Please select at least one user');
                return;
            }
        }
        
        // Validate role-based selection
        if (targetType === 'role_based') {
            const roleId = $('#roleId').val();
            if (!roleId) {
                alert('Please select a role');
                $('#roleId').focus();
                return;
            }
        }
        
        const formData = new FormData(this);
        
        // Show loading state
        $('#sendBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        // Send AJAX request
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Notification sent successfully!');
                    resetForm();
                } else {
                    alert('Error: ' + (response.message || 'Failed to send notification'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send notification';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const errorList = [];
                        
                        for (const field in errors) {
                            if (errors[field]) {
                                errorList.push(`${field}: ${errors[field].join(', ')}`);
                            }
                        }
                        
                        if (errorList.length > 0) {
                            errorMessage += '\n\nDetails:\n' + errorList.join('\n');
                        }
                    }
                }
                
                alert('Error: ' + errorMessage);
            },
            complete: function() {
                $('#sendBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Notification');
            }
        });
    });

    // Test button handler
    $('#testBtn').click(function() {
        const title = $('input[name="title"]').val() || 'Test Notification';
        const body = $('textarea[name="body"]').val() || 'This is a test notification from HRMS Admin Panel';
        const type = $('select[name="notification_type"]').val() || 'general';
        
        if (!confirm('Send test notification to all users?')) {
            return;
        }
        
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
        
        // Send test notification
        $.ajax({
            url: '{{ route("admin.notifications.test") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                title: title,
                body: body,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    alert('Test notification sent successfully!');
                } else {
                    alert('Error: ' + (response.message || 'Failed to send test notification'));
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Failed to send test notification';
                alert('Error: ' + error);
            },
            complete: function() {
                $('#testBtn').prop('disabled', false).html('<i class="fas fa-vial"></i> Send Test');
            }
        });
    });
});

function resetForm() {
    $('#notificationForm')[0].reset();
    $('#roleSelection, #specificUsers, #scheduleDateTime').hide();
    $('input[name="user_ids[]"]').prop('checked', false);
}

function applyTemplate(template) {
    const templates = {
        rent_reminder: {
            title: 'Rent Payment Reminder',
            body: 'Dear tenant, this is a friendly reminder that your rent payment is due. Please ensure timely payment to avoid any inconvenience.',
            type: 'rent_reminder',
            priority: 'normal'
        },
        maintenance: {
            title: 'Maintenance Notice',
            body: 'We will be conducting maintenance work in your building. Please be prepared for any temporary inconveniences.',
            type: 'maintenance_request',
            priority: 'normal'
        },
        announcement: {
            title: 'Important Announcement',
            body: 'We have an important announcement for all residents. Please check your app for more details.',
            type: 'announcement',
            priority: 'normal'
        }
    };
    
    const templateData = templates[template];
    if (templateData) {
        $('input[name="title"]').val(templateData.title);
        $('textarea[name="body"]').val(templateData.body);
        $('select[name="notification_type"]').val(templateData.type);
        $('select[name="priority"]').val(templateData.priority);
    }
}
</script>
@endpush

@push('styles')
<style>
.template-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.notification-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #007bff;
}

.form-check {
    margin-bottom: 8px;
}

.form-check-label {
    font-size: 14px;
}
</style>
@endpush
