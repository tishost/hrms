@extends('layouts.admin')

@section('title', 'Edit SMS Template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">
                                <i class="fas fa-edit text-success"></i> Edit SMS Template
                            </h4>
                            <p class="card-subtitle text-muted">Update template: {{ $smsTemplate->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Templates
                            </a>
                            <a href="{{ route('admin.templates.sms.show', $smsTemplate) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Template
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('admin.templates.sms.update', $smsTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Template Information Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-info-circle text-success"></i> Template Information
                                    <span class="badge badge-success ml-2">Basic Details</span>
                                </h5>
                                <p class="group-description">Configure the basic template information and settings</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-tag text-muted"></i> Template Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $smsTemplate->name) }}" 
                                               placeholder="e.g., Welcome SMS" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category" class="form-label">
                                            <i class="fas fa-layer-group text-muted"></i> Category <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control @error('category') is-invalid @enderror" 
                                                id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="system" {{ old('category', $smsTemplate->category) == 'system' ? 'selected' : '' }}>System</option>
                                            <option value="owner" {{ old('category', $smsTemplate->category) == 'owner' ? 'selected' : '' }}>Owner</option>
                                            <option value="tenant" {{ old('category', $smsTemplate->category) == 'tenant' ? 'selected' : '' }}>Tenant</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="priority" class="form-label">
                                            <i class="fas fa-sort-numeric-up text-muted"></i> Priority
                                        </label>
                                        <select class="form-control @error('priority') is-invalid @enderror" 
                                                id="priority" name="priority">
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('priority', $smsTemplate->priority) == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 1 ? '(Highest)' : ($i == 10 ? '(Lowest)' : '') }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="character_limit" class="form-label">
                                            <i class="fas fa-text-width text-muted"></i> Character Limit
                                        </label>
                                        <input type="number" class="form-control @error('character_limit') is-invalid @enderror" 
                                               id="character_limit" name="character_limit" value="{{ old('character_limit', $smsTemplate->character_limit) }}" 
                                               min="1" max="1000" placeholder="160">
                                        @error('character_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tags" class="form-label">
                                            <i class="fas fa-tags text-muted"></i> Tags
                                        </label>
                                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                               id="tags" name="tags" value="{{ old('tags', is_array($smsTemplate->tags) ? implode(', ', $smsTemplate->tags) : $smsTemplate->tags) }}" 
                                               placeholder="welcome, user, registration (comma separated)">
                                        <small class="form-text text-muted">Separate tags with commas</small>
                                        @error('tags')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left text-muted"></i> Description
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="2" 
                                          placeholder="Brief description of this template">{{ old('description', $smsTemplate->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', $smsTemplate->is_active) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">
                                                <i class="fas fa-toggle-on text-success"></i> <strong>Active Template</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Only active templates can be used</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="unicode_support" name="unicode_support" value="1" 
                                                   {{ old('unicode_support', $smsTemplate->unicode_support) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="unicode_support">
                                                <i class="fas fa-language text-info"></i> <strong>Unicode Support</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Enable for Bengali/Unicode text</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trigger Event Selection -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-bolt text-warning"></i> Trigger Event Selection
                                    <span class="badge badge-warning ml-2">Auto-Trigger</span>
                                </h5>
                                <p class="group-description">Configure when this SMS template should be automatically sent</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="trigger_event" class="form-label">
                                            <i class="fas fa-bolt text-warning"></i> Select Trigger Event
                                        </label>
                                        <select class="form-control @error('trigger_event') is-invalid @enderror" 
                                                id="trigger_event" name="trigger_event">
                                            <option value="">No Trigger (Manual Only)</option>
                                            @foreach($availableTriggers as $key => $trigger)
                                                <option value="{{ $key }}" {{ old('trigger_event', $smsTemplate->trigger_event) == $key ? 'selected' : '' }}>
                                                    {{ $trigger['name'] }} - {{ $trigger['description'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Select which event should automatically trigger this SMS template. Leave empty for manual sending only.
                                        </small>
                                        @error('trigger_event')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Available Variables Display -->
                                    <div id="available-variables" class="mt-3" style="display: none;">
                                        <h6 class="font-weight-bold text-primary">
                                            <i class="fas fa-code"></i> Available Variables for Selected Trigger:
                                        </h6>
                                        <div id="variables-list" class="row">
                                            <!-- Variables will be populated by JavaScript -->
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Use these variables in your SMS content with double curly braces: {!! '{{variable_name}}' !!}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SMS Content Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-sms text-success"></i> SMS Content
                                    <span class="badge badge-success ml-2">Message Content</span>
                                </h5>
                                <p class="group-description">Configure the SMS message content (keep it concise)</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="content" class="form-label">
                                    <i class="fas fa-align-left text-muted"></i> Content <span class="text-danger">*</span>
                                    <span class="character-count text-muted ml-2">(<span id="char-count">0</span>/<span id="char-limit">{{ $smsTemplate->character_limit }}</span> characters)</span>
                                </label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="6" 
                                          placeholder="SMS Content" required maxlength="1000">{{ old('content', $smsTemplate->content) }}</textarea>
                                <small class="form-text text-muted">
                                    Use variables like {user_name}, {company_name}, {otp} etc. Keep messages short and clear.
                                </small>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Character count indicator -->
                            <div class="character-indicator">
                                <div class="progress" style="height: 8px;">
                                    <div id="char-progress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted">
                                    <span id="char-status">Good length</span>
                                </small>
                            </div>
                        </div>

                        <!-- Available Variables Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-code text-info"></i> Available Variables
                                    <span class="badge badge-info ml-2">Dynamic Content</span>
                                </h5>
                                <p class="group-description">Use these variables in your template content for dynamic data</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="variable-category">
                                        <h6 class="variable-category-title">
                                            <i class="fas fa-user text-primary"></i> User Variables
                                        </h6>
                                        <div class="variable-list">
                                            <div class="variable-item">
                                                <code class="variable-code">{user_name}</code>
                                                <span class="variable-desc">User's full name</span>
                                            </div>
                                            <div class="variable-item">
                                                <code class="variable-code">{user_email}</code>
                                                <span class="variable-desc">User's email address</span>
                                            </div>
                                            <div class="variable-item">
                                                <code class="variable-code">{user_phone}</code>
                                                <span class="variable-desc">User's phone number</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="variable-category">
                                        <h6 class="variable-category-title">
                                            <i class="fas fa-building text-success"></i> Company Variables
                                        </h6>
                                        <div class="variable-list">
                                            <div class="variable-item">
                                                <code class="variable-code">{company_name}</code>
                                                <span class="variable-desc">Company name</span>
                                            </div>
                                            <div class="variable-item">
                                                <code class="variable-code">{company_email}</code>
                                                <span class="variable-desc">Company email</span>
                                            </div>
                                            <div class="variable-item">
                                                <code class="variable-code">{company_phone}</code>
                                                <span class="variable-desc">Company phone</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="variable-category">
                                        <h6 class="variable-category-title">
                                            <i class="fas fa-cog text-warning"></i> System Variables
                                        </h6>
                                        <div class="variable-list">
                                            <div class="variable-item">
                                                <code class="variable-code">{otp}</code>
                                                <span class="variable-desc">One-time password</span>
                                            </div>
                                            <div class="variable-item">
                                                <code class="variable-code">{amount}</code>
                                                <span class="variable-desc">Payment amount</span>
                                            </div>
                                            <div class="variable-item">
                                                <code class="variable-code">{invoice_number}</code>
                                                <span class="variable-desc">Invoice number</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Template Information Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-info-circle text-secondary"></i> Template Information
                                    <span class="badge badge-secondary ml-2">Metadata</span>
                                </h5>
                                <p class="group-description">Template creation and modification details</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="info-label">Created:</label>
                                        <span class="info-value">{{ $smsTemplate->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="info-label">Last Updated:</label>
                                        <span class="info-value">{{ $smsTemplate->updated_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success btn-lg btn-block">
                                        <i class="fas fa-save"></i> Update SMS Template
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-secondary btn-lg btn-block">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.template-group {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    background: #fff;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
}

.group-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fc;
}

.group-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.group-title i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

.group-description {
    color: #858796;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.form-label {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-label i {
    margin-right: 0.5rem;
    font-size: 0.9rem;
}

.variable-category {
    background: #f8f9fc;
    border-radius: 0.35rem;
    padding: 1rem;
    height: 100%;
}

.variable-category-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
}

.variable-category-title i {
    margin-right: 0.5rem;
    font-size: 1rem;
}

.variable-list {
    space-y: 0.5rem;
}

.variable-item {
    display: flex;
    flex-direction: column;
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background: #fff;
    border-radius: 0.25rem;
    border: 1px solid #e3e6f0;
}

.variable-code {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: inline-block;
}

.variable-desc {
    color: #6c757d;
    font-size: 0.8rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    padding: 0.75rem;
    background: #f8f9fc;
    border-radius: 0.35rem;
    border: 1px solid #e3e6f0;
}

.info-label {
    color: #6c757d;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.info-value {
    color: #5a5c69;
    font-size: 0.9rem;
}

.action-buttons {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid #f8f9fc;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
}

.btn-block {
    width: 100%;
}

.badge {
    font-size: 0.7rem;
    font-weight: 500;
}

.custom-control-label {
    color: #5a5c69;
    font-weight: 500;
}

.custom-control-label i {
    margin-right: 0.5rem;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #e74a3b;
}

.character-count {
    font-size: 0.8rem;
}

.character-indicator {
    margin-top: 0.5rem;
}

.progress-bar {
    transition: width 0.3s ease;
}

.progress-bar.bg-success {
    background-color: #28a745 !important;
}

.progress-bar.bg-warning {
    background-color: #ffc107 !important;
}

.progress-bar.bg-danger {
    background-color: #dc3545 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('char-count');
    const charLimit = document.getElementById('char-limit');
    const charProgress = document.getElementById('char-progress');
    const charStatus = document.getElementById('char-status');
    const characterLimitInput = document.getElementById('character_limit');
    
    function updateCharacterCount() {
        const currentLength = contentTextarea.value.length;
        const limit = parseInt(charLimit.textContent);
        const percentage = (currentLength / limit) * 100;
        
        charCount.textContent = currentLength;
        charProgress.style.width = percentage + '%';
        
        // Update progress bar color and status
        if (percentage < 80) {
            charProgress.className = 'progress-bar bg-success';
            charStatus.textContent = 'Good length';
            charStatus.className = 'text-success';
        } else if (percentage < 95) {
            charProgress.className = 'progress-bar bg-warning';
            charStatus.textContent = 'Getting long';
            charStatus.className = 'text-warning';
        } else {
            charProgress.className = 'progress-bar bg-danger';
            charStatus.textContent = 'Too long';
            charStatus.className = 'text-danger';
        }
    }
    
    // Update character count on input
    contentTextarea.addEventListener('input', updateCharacterCount);
    
    // Update character limit when changed
    characterLimitInput.addEventListener('input', function() {
        charLimit.textContent = this.value;
        updateCharacterCount();
    });
    
    // Initial count
    updateCharacterCount();
});

// Trigger Event Selection JavaScript
const triggerEventSelect = document.getElementById('trigger_event');
const availableVariablesDiv = document.getElementById('available-variables');
const variablesListDiv = document.getElementById('variables-list');

// Available triggers data from PHP
const availableTriggers = @json($availableTriggers);

triggerEventSelect.addEventListener('change', function() {
    const selectedTrigger = this.value;
    
    if (selectedTrigger && availableTriggers[selectedTrigger]) {
        const trigger = availableTriggers[selectedTrigger];
        const variables = trigger.variables || [];
        
        // Show variables section
        availableVariablesDiv.style.display = 'block';
        
        // Clear existing variables
        variablesListDiv.innerHTML = '';
        
        // Add variables
        variables.forEach(variable => {
            const variableCol = document.createElement('div');
            variableCol.className = 'col-md-4 mb-2';
            variableCol.innerHTML = `
                <div class="card border-primary">
                    <div class="card-body p-2">
                        <code class="text-primary">{{` + variable + `}}</code>
                        <small class="d-block text-muted">` + variable.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + `</small>
                    </div>
                </div>
            `;
            variablesListDiv.appendChild(variableCol);
        });
    } else {
        // Hide variables section
        availableVariablesDiv.style.display = 'none';
    }
});

// Initialize on page load if there's a selected trigger
if (triggerEventSelect.value) {
    triggerEventSelect.dispatchEvent(new Event('change'));
}
</script>
@endsection

