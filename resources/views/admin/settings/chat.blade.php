@extends('layouts.admin')

@section('title', 'Chat Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-comments me-2"></i>
                        Live Chat Settings
                    </h4>
                    <p class="text-muted mb-0">Configure your live chat widget settings and functionality</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.chat.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>
                                    Basic Settings
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_enabled" class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Enable Live Chat
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chat_enabled" name="chat_enabled" value="1" 
                                               {{ $chatSettings['chat_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="chat_enabled">
                                            Show live chat widget on website
                                        </label>
                                    </div>
                                    <small class="text-muted">Turn this off to completely disable the chat widget</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_bot_name" class="form-label">
                                        <i class="fas fa-robot me-1"></i>
                                        Bot Name
                                    </label>
                                    <input type="text" class="form-control" id="chat_bot_name" name="chat_bot_name" 
                                           value="{{ $chatSettings['chat_bot_name'] }}" placeholder="Bari Manager Support">
                                    <small class="text-muted">Name displayed for the chatbot</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="chat_welcome_message" class="form-label">
                                        <i class="fas fa-comment me-1"></i>
                                        Welcome Message
                                    </label>
                                    <textarea class="form-control" id="chat_welcome_message" name="chat_welcome_message" rows="3" 
                                              placeholder="Hello! 👋 Welcome to Bari Manager. How can I help you today?">{{ $chatSettings['chat_welcome_message'] }}</textarea>
                                    <small class="text-muted">Initial message shown when chat opens</small>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Features -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-magic me-2"></i>
                                    Advanced Features
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_auto_response_enabled" class="form-label">
                                        <i class="fas fa-brain me-1"></i>
                                        Auto Response
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chat_auto_response_enabled" name="chat_auto_response_enabled" value="1" 
                                               {{ $chatSettings['chat_auto_response_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="chat_auto_response_enabled">
                                            Enable AI chatbot responses
                                        </label>
                                    </div>
                                    <small class="text-muted">Allow chatbot to respond automatically</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_agent_transfer_enabled" class="form-label">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Agent Transfer
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chat_agent_transfer_enabled" name="chat_agent_transfer_enabled" value="1" 
                                               {{ $chatSettings['chat_agent_transfer_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="chat_agent_transfer_enabled">
                                            Allow transfer to human agents
                                        </label>
                                    </div>
                                    <small class="text-muted">Enable human agent support</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_notification_sound" class="form-label">
                                        <i class="fas fa-volume-up me-1"></i>
                                        Notification Sound
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chat_notification_sound" name="chat_notification_sound" value="1" 
                                               {{ $chatSettings['chat_notification_sound'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="chat_notification_sound">
                                            Play sound for new messages
                                        </label>
                                    </div>
                                    <small class="text-muted">Audio notifications for chat messages</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_typing_indicator" class="form-label">
                                        <i class="fas fa-keyboard me-1"></i>
                                        Typing Indicator
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chat_typing_indicator" name="chat_typing_indicator" value="1" 
                                               {{ $chatSettings['chat_typing_indicator'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="chat_typing_indicator">
                                            Show typing indicator
                                        </label>
                                    </div>
                                    <small class="text-muted">Display "typing..." indicator</small>
                                </div>
                            </div>
                        </div>

                        <!-- Appearance Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-palette me-2"></i>
                                    Appearance Settings
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_position" class="form-label">
                                        <i class="fas fa-arrows-alt me-1"></i>
                                        Chat Position
                                    </label>
                                    <select class="form-select" id="chat_position" name="chat_position">
                                        <option value="bottom-right" {{ $chatSettings['chat_position'] == 'bottom-right' ? 'selected' : '' }}>Bottom Right</option>
                                        <option value="bottom-left" {{ $chatSettings['chat_position'] == 'bottom-left' ? 'selected' : '' }}>Bottom Left</option>
                                        <option value="top-right" {{ $chatSettings['chat_position'] == 'top-right' ? 'selected' : '' }}>Top Right</option>
                                        <option value="top-left" {{ $chatSettings['chat_position'] == 'top-left' ? 'selected' : '' }}>Top Left</option>
                                    </select>
                                    <small class="text-muted">Position of chat widget on website</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_theme_color" class="form-label">
                                        <i class="fas fa-paint-brush me-1"></i>
                                        Theme Color
                                    </label>
                                    <select class="form-select" id="chat_theme_color" name="chat_theme_color">
                                        <option value="purple" {{ $chatSettings['chat_theme_color'] == 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="blue" {{ $chatSettings['chat_theme_color'] == 'blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="green" {{ $chatSettings['chat_theme_color'] == 'green' ? 'selected' : '' }}>Green</option>
                                        <option value="red" {{ $chatSettings['chat_theme_color'] == 'red' ? 'selected' : '' }}>Red</option>
                                        <option value="orange" {{ $chatSettings['chat_theme_color'] == 'orange' ? 'selected' : '' }}>Orange</option>
                                        <option value="pink" {{ $chatSettings['chat_theme_color'] == 'pink' ? 'selected' : '' }}>Pink</option>
                                    </select>
                                    <small class="text-muted">Primary color theme for chat widget</small>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours & Offline -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-clock me-2"></i>
                                    Working Hours & Offline Settings
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_working_hours" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>
                                        Working Hours
                                    </label>
                                    <input type="text" class="form-control" id="chat_working_hours" name="chat_working_hours" 
                                           value="{{ $chatSettings['chat_working_hours'] }}" placeholder="9 AM - 6 PM (GMT+6)">
                                    <small class="text-muted">Display working hours to visitors</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="chat_offline_message" class="form-label">
                                        <i class="fas fa-moon me-1"></i>
                                        Offline Message
                                    </label>
                                    <textarea class="form-control" id="chat_offline_message" name="chat_offline_message" rows="2" 
                                              placeholder="We are currently offline. Please leave a message and we'll get back to you soon.">{{ $chatSettings['chat_offline_message'] }}</textarea>
                                    <small class="text-muted">Message shown when chat is offline</small>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-file-upload me-2"></i>
                                    File Upload Settings
                                </h5>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="chat_file_upload_enabled" class="form-label">
                                        <i class="fas fa-upload me-1"></i>
                                        Enable File Upload
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chat_file_upload_enabled" name="chat_file_upload_enabled" value="1" 
                                               {{ $chatSettings['chat_file_upload_enabled'] == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="chat_file_upload_enabled">
                                            Allow file uploads in chat
                                        </label>
                                    </div>
                                    <small class="text-muted">Enable file sharing in chat</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="chat_max_file_size" class="form-label">
                                        <i class="fas fa-weight-hanging me-1"></i>
                                        Max File Size (MB)
                                    </label>
                                    <input type="number" class="form-control" id="chat_max_file_size" name="chat_max_file_size" 
                                           value="{{ $chatSettings['chat_max_file_size'] }}" min="1" max="50">
                                    <small class="text-muted">Maximum file size allowed</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="chat_allowed_file_types" class="form-label">
                                        <i class="fas fa-file me-1"></i>
                                        Allowed File Types
                                    </label>
                                    <input type="text" class="form-control" id="chat_allowed_file_types" name="chat_allowed_file_types" 
                                           value="{{ $chatSettings['chat_allowed_file_types'] }}" placeholder="jpg,jpeg,png,pdf,doc,docx">
                                    <small class="text-muted">Comma-separated file extensions</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-info" id="test-chat-btn">
                                        <i class="fas fa-vial me-2"></i>
                                        Test Chat System
                                    </button>
                                    
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test chat system
    document.getElementById('test-chat-btn').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
        
        fetch('{{ route("admin.settings.chat.test") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Chat system is working properly!', 'success');
                } else {
                    showAlert('Chat system test failed: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Error testing chat system: ' + error.message, 'error');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-vial me-2"></i>Test Chat System';
            });
    });
    
    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength') || 500;
        const counter = document.createElement('small');
        counter.className = 'text-muted float-end';
        counter.textContent = `${textarea.value.length}/${maxLength}`;
        textarea.parentNode.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            counter.textContent = `${this.value.length}/${maxLength}`;
            if (this.value.length > maxLength) {
                counter.className = 'text-danger float-end';
            } else {
                counter.className = 'text-muted float-end';
            }
        });
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush 