@extends('layouts.admin')

@section('title', 'Edit Email Template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">
                                <i class="fas fa-edit text-primary"></i> Edit Email Template
                            </h4>
                            <p class="card-subtitle text-muted">Update template: {{ $emailTemplate->name }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.templates.email.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Templates
                            </a>
                            <a href="{{ route('admin.templates.email.show', $emailTemplate) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Template
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <form action="{{ route('admin.templates.email.update', $emailTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Template Information Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-info-circle text-primary"></i> Template Information
                                    <span class="badge badge-primary ml-2">Basic Details</span>
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
                                               id="name" name="name" value="{{ old('name', $emailTemplate->name) }}" 
                                               placeholder="e.g., Welcome Email" required>
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
                                            <option value="system" {{ old('category', $emailTemplate->category) == 'system' ? 'selected' : '' }}>System</option>
                                            <option value="owner" {{ old('category', $emailTemplate->category) == 'owner' ? 'selected' : '' }}>Owner</option>
                                            <option value="tenant" {{ old('category', $emailTemplate->category) == 'tenant' ? 'selected' : '' }}>Tenant</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority" class="form-label">
                                            <i class="fas fa-sort-numeric-up text-muted"></i> Priority
                                        </label>
                                        <select class="form-control @error('priority') is-invalid @enderror" 
                                                id="priority" name="priority">
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ old('priority', $emailTemplate->priority) == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 1 ? '(Highest)' : ($i == 10 ? '(Lowest)' : '') }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tags" class="form-label">
                                            <i class="fas fa-tags text-muted"></i> Tags
                                        </label>
                                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                               id="tags" name="tags" value="{{ old('tags', is_array($emailTemplate->tags) ? implode(', ', $emailTemplate->tags) : $emailTemplate->tags) }}" 
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
                                          placeholder="Brief description of this template">{{ old('description', $emailTemplate->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        <i class="fas fa-toggle-on text-success"></i> <strong>Active Template</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">Only active templates can be used</small>
                            </div>
                        </div>

                        <!-- Trigger Event Selection Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-bolt text-warning"></i> Trigger Event Selection
                                    <span class="badge badge-warning ml-2">Auto-Trigger</span>
                                </h5>
                                <p class="group-description">Configure which event should automatically trigger this email</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="trigger_event" class="form-label">
                                    <i class="fas fa-bolt text-warning"></i> Select Trigger Event
                                </label>
                                <select class="form-control @error('trigger_event') is-invalid @enderror" 
                                        id="trigger_event" name="trigger_event">
                                    <option value="">No Trigger (Manual Only)</option>
                                    @foreach($availableTriggers as $key => $trigger)
                                        <option value="{{ $key }}" {{ old('trigger_event', $emailTemplate->trigger_event) == $key ? 'selected' : '' }}>
                                            {{ $trigger['name'] }} - {{ $trigger['description'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Select which event should automatically trigger this email template. Leave empty for manual sending only.
                                </small>
                                @error('trigger_event')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Available Variables Display -->
                            <div id="available-variables" class="mt-3" style="display: none;">
                                <h6 class="font-weight-bold text-primary">Available Variables for Selected Trigger:</h6>
                                <div id="variables-list" class="row">
                                    <!-- Variables will be populated by JavaScript -->
                                </div>
                                <small class="form-text text-muted">
                                    Use these variables in your template content with double curly braces: {!! '{{variable_name}}' !!}
                                </small>
                            </div>
                        </div>

                        <!-- Email Content Group -->
                        <div class="template-group mb-4">
                            <div class="group-header">
                                <h5 class="group-title">
                                    <i class="fas fa-envelope text-success"></i> Email Content
                                    <span class="badge badge-success ml-2">Message Content</span>
                                </h5>
                                <p class="group-description">Configure the email subject and message content</p>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-heading text-muted"></i> Subject <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                       id="subject" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" 
                                       placeholder="Email Subject" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="content" class="form-label">
                                    <i class="fas fa-align-left text-muted"></i> Content <span class="text-danger">*</span>
                                </label>
                                
                                <!-- Content Type Toggle -->
                                <div class="mb-3">
                                    <div class="btn-group" role="group" aria-label="Content Type">
                                        <input type="radio" class="btn-check" name="content_type" id="content_type_text" value="text" 
                                               {{ !$emailTemplate->isHtml() ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary" for="content_type_text">
                                            <i class="fas fa-align-left"></i> Plain Text
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="content_type" id="content_type_html" value="html"
                                               {{ $emailTemplate->isHtml() ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary" for="content_type_html">
                                            <i class="fas fa-code"></i> HTML
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Plain Text Editor -->
                                <div id="text-editor" class="content-editor" style="{{ $emailTemplate->isHtml() ? 'display: none;' : '' }}">
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="12" 
                                              placeholder="Email Content" {{ !$emailTemplate->isHtml() ? 'required' : '' }}>{{ old('content', $emailTemplate->isHtml() ? '' : $emailTemplate->content) }}</textarea>
                                </div>
                                
                                <!-- HTML Editor -->
                                <div id="html-editor" class="content-editor" style="{{ $emailTemplate->isHtml() ? '' : 'display: none;' }}">
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="html_content" name="html_content" rows="15" 
                                              placeholder="HTML Email Content" {{ $emailTemplate->isHtml() ? 'required' : '' }}>{{ old('html_content', $emailTemplate->isHtml() ? $emailTemplate->content : '') }}</textarea>
                                </div>
                                
                                <small class="form-text text-muted">
                                    <strong>Variables:</strong> {user_name}, {company_name}, {otp}, {verification_url}, {amount}, {invoice_number}
                                    <br>
                                    <strong>HTML Support:</strong> Use HTML tags for rich formatting, images, links, and styling
                                </small>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                                <code class="variable-code">{verification_url}</code>
                                                <span class="variable-desc">Verification link</span>
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
                                        <span class="info-value">{{ $emailTemplate->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="info-label">Last Updated:</label>
                                        <span class="info-value">{{ $emailTemplate->updated_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        <i class="fas fa-save"></i> Update Template
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('admin.templates.email.index') }}" class="btn btn-secondary btn-lg btn-block">
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
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #e74a3b;
}

.content-editor {
    transition: all 0.3s ease;
}

.btn-check:checked + .btn {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#html-editor .form-control {
    font-family: 'Courier New', monospace;
    font-size: 14px;
}

.simple-html-editor {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
}

.editor-toolbar {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 0.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.editor-toolbar .btn {
    border-radius: 0.25rem;
    padding: 0.375rem 0.5rem;
}

.editor-content {
    outline: none;
    line-height: 1.5;
}

.editor-content:focus {
    box-shadow: inset 0 0 0 2px #4e73df;
}

.editor-content p {
    margin-bottom: 0.5rem;
}

.editor-content ul, .editor-content ol {
    margin-left: 1.5rem;
    margin-bottom: 0.5rem;
}

.advanced-html-editor {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
    background: white;
}

.editor-toolbar {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.toolbar-group {
    display: flex;
    gap: 0.25rem;
    align-items: center;
    margin-right: 1rem;
}

.toolbar-group:last-child {
    margin-right: 0;
}

.editor-toolbar .btn {
    border-radius: 0.25rem;
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
}

.editor-content-container {
    position: relative;
}

.editor-content {
    outline: none;
    line-height: 1.6;
    font-size: 14px;
}

.editor-content:focus {
    box-shadow: inset 0 0 0 2px #4e73df;
}

.editor-content p {
    margin-bottom: 0.75rem;
}

.editor-content ul, .editor-content ol {
    margin-left: 1.5rem;
    margin-bottom: 0.75rem;
}

.editor-content h1, .editor-content h2, .editor-content h3, 
.editor-content h4, .editor-content h5, .editor-content h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.editor-content table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 1rem;
}

.editor-content table td, .editor-content table th {
    border: 1px solid #dee2e6;
    padding: 8px;
    text-align: left;
}

.preview-panel {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 1rem;
}

.preview-panel h6 {
    margin-bottom: 0.5rem;
    color: #495057;
    font-weight: 600;
}

.preview-content {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    min-height: 150px;
    max-height: 300px;
    overflow-y: auto;
}

.code-view-textarea {
    font-family: 'Courier New', 'Monaco', 'Menlo', 'Ubuntu Mono', monospace !important;
    font-size: 14px !important;
    line-height: 1.4 !important;
    background: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
    padding: 20px !important;
    resize: vertical !important;
    tab-size: 2 !important;
    white-space: pre !important;
    word-wrap: normal !important;
    overflow-x: auto !important;
}

.code-view-textarea:focus {
    outline: none !important;
    border-color: #4e73df !important;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25) !important;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTypeText = document.getElementById('content_type_text');
    const contentTypeHtml = document.getElementById('content_type_html');
    const textEditor = document.getElementById('text-editor');
    const htmlEditor = document.getElementById('html-editor');
    const contentField = document.getElementById('content');
    const htmlContentField = document.getElementById('html_content');
    
    // Advanced HTML editor with rich features
    function initializeAdvancedHTMLEditor() {
        // Check if editor already exists
        const existingEditor = document.querySelector('.advanced-html-editor');
        if (existingEditor) {
            return; // Don't create duplicate editor
        }
        
        const editorContainer = document.createElement('div');
        editorContainer.className = 'advanced-html-editor';
        editorContainer.innerHTML = `
            <div class="editor-toolbar">
                <div class="toolbar-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')" title="Bold">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')" title="Italic">
                        <i class="fas fa-italic"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')" title="Underline">
                        <i class="fas fa-underline"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('strikeThrough')" title="Strikethrough">
                        <i class="fas fa-strikethrough"></i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Text Color">
                            <i class="fas fa-palette"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="formatText('foreColor', '#000000')">Black</a></li>
                            <li><a class="dropdown-item" href="#" onclick="formatText('foreColor', '#4e73df')">Blue</a></li>
                            <li><a class="dropdown-item" href="#" onclick="formatText('foreColor', '#1cc88a')">Green</a></li>
                            <li><a class="dropdown-item" href="#" onclick="formatText('foreColor', '#e74a3b')">Red</a></li>
                            <li><a class="dropdown-item" href="#" onclick="formatText('foreColor', '#f6c23e')">Yellow</a></li>
                            <li><a class="dropdown-item" href="#" onclick="formatText('foreColor', '#6c757d')">Gray</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="toolbar-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('justifyLeft')" title="Align Left">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('justifyCenter')" title="Align Center">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('justifyRight')" title="Align Right">
                        <i class="fas fa-align-right"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('justifyFull')" title="Justify">
                        <i class="fas fa-align-justify"></i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('insertUnorderedList')" title="Bullet List">
                        <i class="fas fa-list-ul"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('insertOrderedList')" title="Numbered List">
                        <i class="fas fa-list-ol"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('outdent')" title="Decrease Indent">
                        <i class="fas fa-outdent"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('indent')" title="Increase Indent">
                        <i class="fas fa-indent"></i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('createLink')" title="Insert Link">
                        <i class="fas fa-link"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('insertImage')" title="Insert Image">
                        <i class="fas fa-image"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertTable()" title="Insert Table">
                        <i class="fas fa-table"></i>
                    </button>
                </div>
                
                <div class="toolbar-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertTemplateVariable()" title="Insert Template Variable">
                        <i class="fas fa-code"></i> Variables
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('removeFormat')" title="Remove Formatting">
                        <i class="fas fa-remove-format"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleCodeView()" title="Toggle Code View">
                        <i class="fas fa-code"></i> Code View
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="togglePreview()" title="Preview">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                </div>
            </div>
            
            <div class="editor-content-container">
                <div class="editor-content" contenteditable="true" id="html-editor-content" style="min-height: 350px; border: 1px solid #dee2e6; padding: 20px; background: white; font-family: Arial, sans-serif; line-height: 1.6;">
                    ${htmlContentField.value || '<p>Start typing your HTML email content here...</p>'}
                </div>
                <textarea id="code-view-textarea" class="code-view-textarea" style="display: none; min-height: 350px; width: 100%; border: 1px solid #dee2e6; padding: 20px; font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.4; background: #f8f9fa; resize: vertical;">${htmlContentField.value || '<p>Start typing your HTML email content here...</p>'}</textarea>
                <div id="preview-panel" class="preview-panel" style="display: none;">
                    <h6>Preview:</h6>
                    <div id="preview-content" class="preview-content email-html-preview"></div>
                </div>
            </div>
        `;
        
        // Replace the textarea with our editor
        htmlContentField.style.display = 'none';
        htmlContentField.parentNode.insertBefore(editorContainer, htmlContentField);
        
        // Sync content back to textarea
        const editorContent = document.getElementById('html-editor-content');
        const codeViewTextarea = document.getElementById('code-view-textarea');
        
        editorContent.addEventListener('input', function() {
            htmlContentField.value = this.innerHTML;
            codeViewTextarea.value = this.innerHTML;
            updatePreview();
        });
        
        codeViewTextarea.addEventListener('input', function() {
            htmlContentField.value = this.value;
            editorContent.innerHTML = this.value;
            updatePreview();
        });
        
        // Initial sync
        htmlContentField.value = editorContent.innerHTML;
        codeViewTextarea.value = editorContent.innerHTML;
        updatePreview();
    }
    
    // Global functions for formatting
    window.formatText = function(command, value = null) {
        const editor = document.getElementById('html-editor-content');
        if (editor) {
            editor.focus();
            if (command === 'createLink') {
                const url = prompt('Enter URL:');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
            } else if (command === 'insertImage') {
                const url = prompt('Enter Image URL:');
                if (url) {
                    document.execCommand('insertImage', false, url);
                }
            } else if (command === 'foreColor') {
                document.execCommand('foreColor', false, value);
            } else {
                document.execCommand(command, false, null);
            }
            // Sync content
            const htmlContentField = document.getElementById('html_content');
            if (htmlContentField) {
                htmlContentField.value = editor.innerHTML;
                updatePreview();
            }
        }
    };
    
    window.insertTemplateVariable = function() {
        const variables = [
            '{user_name}', '{user_email}', '{user_phone}',
            '{company_name}', '{company_email}', '{company_phone}',
            '{otp}', '{verification_url}', '{amount}', '{invoice_number}'
        ];
        
        const selectedVar = prompt('Select a variable:\n\n' + variables.join('\n'));
        if (selectedVar && variables.includes(selectedVar)) {
            const editor = document.getElementById('html-editor-content');
            if (editor) {
                editor.focus();
                document.execCommand('insertText', false, selectedVar);
                const htmlContentField = document.getElementById('html_content');
                if (htmlContentField) {
                    htmlContentField.value = editor.innerHTML;
                    updatePreview();
                }
            }
        }
    };
    
    window.insertTable = function() {
        const rows = prompt('Number of rows:', '3');
        const cols = prompt('Number of columns:', '3');
        if (rows && cols) {
            const editor = document.getElementById('html-editor-content');
            if (editor) {
                editor.focus();
                let tableHtml = '<table border="1" style="border-collapse: collapse; width: 100%;">';
                for (let i = 0; i < parseInt(rows); i++) {
                    tableHtml += '<tr>';
                    for (let j = 0; j < parseInt(cols); j++) {
                        tableHtml += '<td style="padding: 8px; border: 1px solid #ccc;">Cell ' + (i + 1) + ',' + (j + 1) + '</td>';
                    }
                    tableHtml += '</tr>';
                }
                tableHtml += '</table>';
                document.execCommand('insertHTML', false, tableHtml);
                const htmlContentField = document.getElementById('html_content');
                if (htmlContentField) {
                    htmlContentField.value = editor.innerHTML;
                    updatePreview();
                }
            }
        }
    };
    
    window.toggleCodeView = function() {
        const editorContent = document.getElementById('html-editor-content');
        const codeViewTextarea = document.getElementById('code-view-textarea');
        const codeViewBtn = document.querySelector('button[onclick="toggleCodeView()"]');
        
        if (editorContent.style.display === 'none') {
            // Switch to visual editor
            editorContent.style.display = 'block';
            codeViewTextarea.style.display = 'none';
            codeViewBtn.innerHTML = '<i class="fas fa-code"></i> Code View';
            codeViewBtn.className = 'btn btn-sm btn-outline-warning';
        } else {
            // Switch to code view
            editorContent.style.display = 'none';
            codeViewTextarea.style.display = 'block';
            codeViewBtn.innerHTML = '<i class="fas fa-eye"></i> Visual View';
            codeViewBtn.className = 'btn btn-sm btn-outline-success';
        }
    };
    
    window.togglePreview = function() {
        const previewPanel = document.getElementById('preview-panel');
        if (previewPanel.style.display === 'none') {
            previewPanel.style.display = 'block';
            updatePreview();
        } else {
            previewPanel.style.display = 'none';
        }
    };
    
    function updatePreview() {
        const editor = document.getElementById('html-editor-content');
        const preview = document.getElementById('preview-content');
        if (editor && preview) {
            preview.innerHTML = editor.innerHTML;
        }
    }
    
    // Content type toggle functionality
    contentTypeText.addEventListener('change', function() {
        if (this.checked) {
            textEditor.style.display = 'block';
            htmlEditor.style.display = 'none';
            contentField.required = true;
            htmlContentField.required = false;
            
            // Clean up any existing HTML editor
            const existingEditor = document.querySelector('.advanced-html-editor');
            if (existingEditor) {
                existingEditor.remove();
            }
            htmlContentField.style.display = 'block';
        }
    });
    
    contentTypeHtml.addEventListener('change', function() {
        if (this.checked) {
            textEditor.style.display = 'none';
            htmlEditor.style.display = 'block';
            contentField.required = false;
            htmlContentField.required = true;
            
            // Clean up any existing HTML editor first
            const existingEditor = document.querySelector('.advanced-html-editor');
            if (existingEditor) {
                existingEditor.remove();
            }
            
            // Initialize advanced HTML editor when switching to HTML mode
            setTimeout(() => {
                initializeAdvancedHTMLEditor();
            }, 100);
        }
    });
    
    // Form submission handling
    document.querySelector('form').addEventListener('submit', function(e) {
        const isHtmlMode = contentTypeHtml.checked;
        
        if (isHtmlMode) {
            // If HTML mode is selected, use html_content and disable content field
            contentField.required = false;
            htmlContentField.required = true;
            contentField.disabled = true; // Disable content field completely
            contentField.name = ''; // Remove name attribute so it's not submitted
        } else {
            // If text mode is selected, use content and disable html_content field
            htmlContentField.required = false;
            contentField.required = true;
            htmlContentField.disabled = true; // Disable html_content field completely
            htmlContentField.name = ''; // Remove name attribute so it's not submitted
        }
    });
    
    // Initialize advanced HTML editor if HTML mode is already selected
    if (contentTypeHtml.checked) {
        setTimeout(() => {
            initializeAdvancedHTMLEditor();
        }, 100);
    }

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
});
</script>
@endpush

@push('styles')
<style>
/* HTML Email Preview Styling for Edit View */
.email-html-preview {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    background: #ffffff;
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
}

.email-html-preview h1,
.email-html-preview h2,
.email-html-preview h3,
.email-html-preview h4,
.email-html-preview h5,
.email-html-preview h6 {
    color: #2c3e50;
    margin-top: 0;
    margin-bottom: 16px;
    font-weight: 600;
}

.email-html-preview h1 { font-size: 28px; }
.email-html-preview h2 { font-size: 24px; }
.email-html-preview h3 { font-size: 20px; }
.email-html-preview h4 { font-size: 18px; }
.email-html-preview h5 { font-size: 16px; }
.email-html-preview h6 { font-size: 14px; }

.email-html-preview p {
    margin-bottom: 16px;
    color: #555;
}

.email-html-preview a {
    color: #007bff;
    text-decoration: none;
}

.email-html-preview a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.email-html-preview ul,
.email-html-preview ol {
    margin-bottom: 16px;
    padding-left: 20px;
}

.email-html-preview li {
    margin-bottom: 8px;
    color: #555;
}

.email-html-preview blockquote {
    border-left: 4px solid #007bff;
    margin: 16px 0;
    padding: 12px 16px;
    background: #f8f9fa;
    font-style: italic;
}

.email-html-preview .btn,
.email-html-preview button {
    display: inline-block;
    padding: 12px 24px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.email-html-preview .btn:hover,
.email-html-preview button:hover {
    background: #0056b3;
}

.email-html-preview .btn-secondary {
    background: #6c757d;
}

.email-html-preview .btn-secondary:hover {
    background: #545b62;
}

.email-html-preview .btn-success {
    background: #28a745;
}

.email-html-preview .btn-success:hover {
    background: #1e7e34;
}

.email-html-preview .btn-danger {
    background: #dc3545;
}

.email-html-preview .btn-danger:hover {
    background: #c82333;
}

.email-html-preview .alert {
    padding: 12px 16px;
    margin: 16px 0;
    border-radius: 4px;
    border: 1px solid transparent;
}

.email-html-preview .alert-success {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.email-html-preview .alert-danger {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.email-html-preview .alert-warning {
    background: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}

.email-html-preview .alert-info {
    background: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.email-html-preview .card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin: 16px 0;
    overflow: hidden;
}

.email-html-preview .card-header {
    background: #f8f9fa;
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}

.email-html-preview .card-body {
    padding: 16px;
}

.email-html-preview .card-footer {
    background: #f8f9fa;
    padding: 12px 16px;
    border-top: 1px solid #e9ecef;
    font-size: 14px;
    color: #6c757d;
}

.email-html-preview .text-center { text-align: center; }
.email-html-preview .text-left { text-align: left; }
.email-html-preview .text-right { text-align: right; }

.email-html-preview .text-primary { color: #007bff; }
.email-html-preview .text-success { color: #28a745; }
.email-html-preview .text-danger { color: #dc3545; }
.email-html-preview .text-warning { color: #ffc107; }
.email-html-preview .text-info { color: #17a2b8; }
.email-html-preview .text-muted { color: #6c757d; }

.email-html-preview .bg-primary { background-color: #007bff; color: white; }
.email-html-preview .bg-success { background-color: #28a745; color: white; }
.email-html-preview .bg-danger { background-color: #dc3545; color: white; }
.email-html-preview .bg-warning { background-color: #ffc107; color: #212529; }
.email-html-preview .bg-info { background-color: #17a2b8; color: white; }
.email-html-preview .bg-light { background-color: #f8f9fa; color: #212529; }
.email-html-preview .bg-dark { background-color: #343a40; color: white; }

.email-html-preview .border { border: 1px solid #dee2e6; }
.email-html-preview .border-primary { border-color: #007bff; }
.email-html-preview .border-success { border-color: #28a745; }
.email-html-preview .border-danger { border-color: #dc3545; }
.email-html-preview .border-warning { border-color: #ffc107; }
.email-html-preview .border-info { border-color: #17a2b8; }

.email-html-preview .rounded { border-radius: 4px; }
.email-html-preview .rounded-lg { border-radius: 8px; }
.email-html-preview .rounded-circle { border-radius: 50%; }

.email-html-preview .shadow { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
.email-html-preview .shadow-sm { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
.email-html-preview .shadow-lg { box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175); }

.email-html-preview .mt-1 { margin-top: 0.25rem; }
.email-html-preview .mt-2 { margin-top: 0.5rem; }
.email-html-preview .mt-3 { margin-top: 1rem; }
.email-html-preview .mt-4 { margin-top: 1.5rem; }
.email-html-preview .mt-5 { margin-top: 3rem; }

.email-html-preview .mb-1 { margin-bottom: 0.25rem; }
.email-html-preview .mb-2 { margin-bottom: 0.5rem; }
.email-html-preview .mb-3 { margin-bottom: 1rem; }
.email-html-preview .mb-4 { margin-bottom: 1.5rem; }
.email-html-preview .mb-5 { margin-bottom: 3rem; }

.email-html-preview .p-1 { padding: 0.25rem; }
.email-html-preview .p-2 { padding: 0.5rem; }
.email-html-preview .p-3 { padding: 1rem; }
.email-html-preview .p-4 { padding: 1.5rem; }
.email-html-preview .p-5 { padding: 3rem; }

.email-html-preview .container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 15px;
}

.email-html-preview .row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.email-html-preview .col,
.email-html-preview .col-1,
.email-html-preview .col-2,
.email-html-preview .col-3,
.email-html-preview .col-4,
.email-html-preview .col-5,
.email-html-preview .col-6,
.email-html-preview .col-7,
.email-html-preview .col-8,
.email-html-preview .col-9,
.email-html-preview .col-10,
.email-html-preview .col-11,
.email-html-preview .col-12 {
    padding: 0 15px;
    flex: 1;
}

.email-html-preview .col-1 { flex: 0 0 8.333333%; }
.email-html-preview .col-2 { flex: 0 0 16.666667%; }
.email-html-preview .col-3 { flex: 0 0 25%; }
.email-html-preview .col-4 { flex: 0 0 33.333333%; }
.email-html-preview .col-5 { flex: 0 0 41.666667%; }
.email-html-preview .col-6 { flex: 0 0 50%; }
.email-html-preview .col-7 { flex: 0 0 58.333333%; }
.email-html-preview .col-8 { flex: 0 0 66.666667%; }
.email-html-preview .col-9 { flex: 0 0 75%; }
.email-html-preview .col-10 { flex: 0 0 83.333333%; }
.email-html-preview .col-11 { flex: 0 0 91.666667%; }
.email-html-preview .col-12 { flex: 0 0 100%; }
</style>
@endpush
@endsection