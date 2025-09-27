@extends('layouts.admin')

@section('title', 'View Email Template')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Email Template Details</h1>
            <p class="text-muted">View and manage email template details</p>
        </div>
        <div>
            <a href="{{ route('admin.templates.email.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
            <a href="{{ route('admin.templates.email.edit', $emailTemplate) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Template
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Template Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $emailTemplate->name }}</h6>
                    <div>
                        @if($emailTemplate->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Active
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-times"></i> Inactive
                            </span>
                        @endif
                        <span class="badge badge-info">{{ ucfirst($emailTemplate->category) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($emailTemplate->description)
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Description</h6>
                            <p class="text-muted">{{ $emailTemplate->description }}</p>
                        </div>
                    @endif

                    @if($emailTemplate->tags && is_array($emailTemplate->tags) && count($emailTemplate->tags) > 0)
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Tags</h6>
                            @foreach($emailTemplate->tags as $tag)
                                <span class="badge badge-light mr-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Email Content -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary">
                            <i class="fas fa-envelope"></i> Email Content
                        </h6>
                        <div class="border rounded p-3 bg-light">
                            <strong>Subject:</strong> {{ $emailTemplate->subject }}
                            <hr>
                            <strong>Content:</strong>
                            <div class="mt-2 p-3 bg-white border rounded">
                                @if($emailTemplate->isHtml())
                                    <!-- HTML Content Display -->
                                    <div class="mb-3">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary active" id="html-view-btn" onclick="toggleContentView('html')">
                                                <i class="fas fa-eye"></i> HTML View
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="code-view-btn" onclick="toggleContentView('code')">
                                                <i class="fas fa-code"></i> Code View
                                            </button>
                                        </div>
                                    </div>
                                    <div id="html-content-display" class="email-content-display email-html-preview">
                                        {!! $emailTemplate->content !!}
                                    </div>
                                    <div id="code-content-display" class="email-content-display" style="display: none;">
                                        <pre class="bg-light p-3 rounded"><code>{{ $emailTemplate->content }}</code></pre>
                                    </div>
                                @else
                                    <!-- Plain Text Content Display -->
                                    {!! nl2br(e($emailTemplate->content)) !!}
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Template Metadata -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Template Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Template Name:</strong><br>
                        <span class="text-muted">{{ $emailTemplate->name }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Category:</strong><br>
                        <span class="badge badge-info">{{ ucfirst($emailTemplate->category) }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Priority:</strong><br>
                        <span class="text-muted">{{ $emailTemplate->priority }}/10</span>
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($emailTemplate->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>


                    <hr>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <span class="text-muted">{{ $emailTemplate->created_at->format('M d, Y H:i') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        <span class="text-muted">{{ $emailTemplate->updated_at->format('M d, Y H:i') }}</span>
                    </div>

                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.templates.email.edit', $emailTemplate) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Template
                        </a>
                        
                        <form action="{{ route('admin.templates.email.toggle-status', $emailTemplate) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn btn-{{ $emailTemplate->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $emailTemplate->is_active ? 'pause' : 'play' }}"></i>
                                {{ $emailTemplate->is_active ? 'Deactivate' : 'Activate' }} Template
                            </button>
                        </form>

                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#previewModal">
                            <i class="fas fa-eye"></i> Preview Template
                        </button>

                        <form action="{{ route('admin.templates.email.destroy', $emailTemplate) }}" 
                              method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this template?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete Template
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Template Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="border rounded p-3 bg-light">
                    <strong>Subject:</strong> {{ $emailTemplate->subject }}
                    <hr>
                    <div class="p-3 bg-white border rounded">
                        @if($emailTemplate->isHtml())
                            {!! $emailTemplate->content !!}
                        @else
                            {!! nl2br(e($emailTemplate->content)) !!}
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.email-content-display {
    min-height: 200px;
    max-height: 500px;
    overflow-y: auto;
}

.email-content-display pre {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
}

.email-content-display code {
    font-family: 'Courier New', 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
    color: #333;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.email-content-display img {
    max-width: 100%;
    height: auto;
}

.email-content-display table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
}

.email-content-display table td,
.email-content-display table th {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.email-content-display table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

/* HTML Email Preview Styling */
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

@push('scripts')
<script>
function toggleContentView(viewType) {
    const htmlView = document.getElementById('html-content-display');
    const codeView = document.getElementById('code-content-display');
    const htmlBtn = document.getElementById('html-view-btn');
    const codeBtn = document.getElementById('code-view-btn');
    
    if (viewType === 'html') {
        htmlView.style.display = 'block';
        codeView.style.display = 'none';
        htmlBtn.classList.remove('btn-outline-primary');
        htmlBtn.classList.add('btn-primary');
        codeBtn.classList.remove('btn-primary');
        codeBtn.classList.add('btn-outline-secondary');
    } else {
        htmlView.style.display = 'none';
        codeView.style.display = 'block';
        codeBtn.classList.remove('btn-outline-secondary');
        codeBtn.classList.add('btn-primary');
        htmlBtn.classList.remove('btn-primary');
        htmlBtn.classList.add('btn-outline-primary');
    }
}
</script>
@endpush
@endsection
