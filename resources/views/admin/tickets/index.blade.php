@extends('layouts.admin')

@section('title', 'Contact Tickets')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Contact Tickets</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Tickets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tickets->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tickets->where('status', 'pending')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                In Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tickets->where('status', 'in_progress')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tickets->where('status', 'resolved')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Contact Tickets</h6>
        </div>
        <div class="card-body">
            @if($tickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="ticketsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $ticket->ticket_number }}</span>
                                    </td>
                                    <td>{{ $ticket->name }}</td>
                                    <td>
                                        <div>
                                            <i class="fas fa-phone me-1"></i>{{ $ticket->mobile }}
                                            @if($ticket->email)
                                                <br><i class="fas fa-envelope me-1"></i>{{ $ticket->email }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $ticket->details }}">
                                            {{ Str::limit($ticket->details, 50) }}
                                        </div>
                                    </td>
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
                                    <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#statusModal{{ $ticket->id }}"
                                                    title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.tickets.destroy', $ticket) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Delete Ticket">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Status Update Modal -->
                                <div class="modal fade" id="statusModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $ticket->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel{{ $ticket->id }}">Update Ticket Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.tickets.update-status', $ticket) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="status{{ $ticket->id }}" class="form-label">Status</label>
                                                        <select class="form-select" id="status{{ $ticket->id }}" name="status" required>
                                                            <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                                        </select>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No tickets found</h5>
                    <p class="text-gray-400">There are no contact tickets to display.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable if needed
        if ($.fn.DataTable) {
            $('#ticketsTable').DataTable({
                "order": [[5, "desc"]], // Sort by created date descending
                "pageLength": 20,
                "language": {
                    "search": "Search tickets:",
                    "lengthMenu": "Show _MENU_ tickets per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ tickets",
                    "infoEmpty": "Showing 0 to 0 of 0 tickets",
                    "infoFiltered": "(filtered from _MAX_ total tickets)"
                }
            });
        }
    });
</script>
@endpush
@endsection 