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
                                    <div id="html-content-display" class="email-content-display">
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
