@extends('layouts.admin')

@section('title', 'View SMS Template')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">SMS Template Details</h1>
            <p class="text-muted">View and manage SMS template details</p>
        </div>
        <div>
            <a href="{{ route('admin.templates.sms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
            <a href="{{ route('admin.templates.sms.edit', $smsTemplate) }}" class="btn btn-success">
                <i class="fas fa-edit"></i> Edit Template
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Template Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">{{ $smsTemplate->name }}</h6>
                    <div>
                        @if($smsTemplate->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Active
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-times"></i> Inactive
                            </span>
                        @endif
                        <span class="badge badge-info">{{ ucfirst($smsTemplate->category) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($smsTemplate->description)
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Description</h6>
                            <p class="text-muted">{{ $smsTemplate->description }}</p>
                        </div>
                    @endif

                    @if($smsTemplate->tags && is_array($smsTemplate->tags) && count($smsTemplate->tags) > 0)
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Tags</h6>
                            @foreach($smsTemplate->tags as $tag)
                                <span class="badge badge-light mr-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- SMS Content -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-success">
                            <i class="fas fa-sms"></i> SMS Content
                        </h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Content:</strong>
                                <span class="badge badge-info">
                                    {{ strlen($smsTemplate->content) }}/{{ $smsTemplate->character_limit }} characters
                                </span>
                            </div>
                            <div class="p-3 bg-white border rounded">
                                {!! nl2br(e($smsTemplate->content)) !!}
                            </div>
                            
                            @if($smsTemplate->unicode_support)
                                <div class="mt-2">
                                    <span class="badge badge-warning">
                                        <i class="fas fa-language"></i> Unicode Support Enabled
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Template Metadata -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Template Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Template Name:</strong><br>
                        <span class="text-muted">{{ $smsTemplate->name }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Category:</strong><br>
                        <span class="badge badge-info">{{ ucfirst($smsTemplate->category) }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Priority:</strong><br>
                        <span class="text-muted">{{ $smsTemplate->priority }}/10</span>
                    </div>

                    <div class="mb-3">
                        <strong>Character Limit:</strong><br>
                        <span class="text-muted">{{ $smsTemplate->character_limit }} characters</span>
                    </div>

                    <div class="mb-3">
                        <strong>Unicode Support:</strong><br>
                        @if($smsTemplate->unicode_support)
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-secondary">Disabled</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($smsTemplate->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <span class="text-muted">{{ $smsTemplate->created_at->format('M d, Y H:i') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        <span class="text-muted">{{ $smsTemplate->updated_at->format('M d, Y H:i') }}</span>
                    </div>

                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.templates.sms.edit', $smsTemplate) }}" 
                           class="btn btn-success">
                            <i class="fas fa-edit"></i> Edit Template
                        </a>
                        
                        <form action="{{ route('admin.templates.sms.toggle-status', $smsTemplate) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn btn-{{ $smsTemplate->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $smsTemplate->is_active ? 'pause' : 'play' }}"></i>
                                {{ $smsTemplate->is_active ? 'Deactivate' : 'Activate' }} Template
                            </button>
                        </form>

                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#previewModal">
                            <i class="fas fa-eye"></i> Preview Template
                        </button>

                        <form action="{{ route('admin.templates.sms.destroy', $smsTemplate) }}" 
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
                <h5 class="modal-title" id="previewModalLabel">SMS Template Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>SMS Content:</strong>
                        <span class="badge badge-info">
                            {{ strlen($smsTemplate->content) }}/{{ $smsTemplate->character_limit }} characters
                        </span>
                    </div>
                    <div class="p-3 bg-white border rounded">
                        {!! nl2br(e($smsTemplate->content)) !!}
                    </div>
                    
                    @if($smsTemplate->unicode_support)
                        <div class="mt-2">
                            <span class="badge badge-warning">
                                <i class="fas fa-language"></i> Unicode Support Enabled
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection



