@extends('layouts.admin')

@section('title', 'Ticket Details - ' . $ticket->ticket_number)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ticket Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tickets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Tickets
            </a>
            <button type="button" 
                    class="btn btn-sm btn-warning shadow-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#statusModal">
                <i class="fas fa-edit fa-sm text-white-50"></i> Update Status
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Ticket Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ticket Information</h6>
                    <span class="badge bg-primary fs-6">{{ $ticket->ticket_number }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Contact Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 120px;">Name:</td>
                                    <td>{{ $ticket->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Mobile:</td>
                                    <td>
                                        <i class="fas fa-phone me-2"></i>
                                        <a href="tel:{{ $ticket->mobile }}" class="text-decoration-none">
                                            {{ $ticket->mobile }}
                                        </a>
                                    </td>
                                </tr>
                                @if($ticket->email)
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>
                                        <i class="fas fa-envelope me-2"></i>
                                        <a href="mailto:{{ $ticket->email }}" class="text-decoration-none">
                                            {{ $ticket->email }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @switch($ticket->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-info">In Progress</span>
                                                @break
                                            @case('resolved')
                                                <span class="badge bg-success">Resolved</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary">Closed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $ticket->created_at->format('F d, Y \a\t g:i A') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Updated:</td>
                                    <td>
                                        <i class="fas fa-clock me-2"></i>
                                        {{ $ticket->updated_at->format('F d, Y \a\t g:i A') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                @if($ticket->mobile)
                                <a href="tel:{{ $ticket->mobile }}" class="btn btn-outline-primary">
                                    <i class="fas fa-phone me-2"></i>Call Customer
                                </a>
                                @endif
                                @if($ticket->email)
                                <a href="mailto:{{ $ticket->email }}?subject=Re: Ticket {{ $ticket->ticket_number }}" class="btn btn-outline-info">
                                    <i class="fas fa-envelope me-2"></i>Send Email
                                </a>
                                @endif
                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#statusModal">
                                    <i class="fas fa-edit me-2"></i>Update Status
                                </button>
                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this ticket?')">
                                        <i class="fas fa-trash me-2"></i>Delete Ticket
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Message Details</h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 rounded">
                        <h6 class="text-muted mb-3">Customer Message:</h6>
                        <div class="border-start border-primary ps-3">
                            {!! nl2br(e($ticket->details)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status History & Notes -->
        <div class="col-lg-4">
            <!-- Status History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Ticket Created</h6>
                                <p class="timeline-text text-muted">{{ $ticket->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        @if($ticket->updated_at != $ticket->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Last Updated</h6>
                                <p class="timeline-text text-muted">{{ $ticket->updated_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Notes</h6>
                </div>
                <div class="card-body">
                                         <form action="{{ route('admin.tickets.add-note', $ticket) }}" method="POST" class="csrf-form">
                         @csrf
                         <div class="mb-3">
                            <label for="admin_note" class="form-label">Add Note</label>
                            <textarea class="form-control" id="admin_note" name="admin_note" rows="3" placeholder="Add a note about this ticket..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Note
                        </button>
                    </form>
                    
                    @if($ticket->admin_notes)
                    <hr>
                    <div class="mt-3">
                        <h6 class="text-muted">Previous Notes:</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($ticket->admin_notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Ticket Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                         <form action="{{ route('admin.tickets.update-status', $ticket) }}" method="POST" class="csrf-form">
                 @csrf
                 @method('PATCH')
                 <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_note" class="form-label">Status Note (Optional)</label>
                        <textarea class="form-control" id="status_note" name="status_note" rows="3" placeholder="Add a note about the status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    font-size: 0.875rem;
    margin-bottom: 0;
}
</style>
@endsection 