<!-- filepath: resources/views/admin/owners/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manage Owners')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Owner List</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.owners.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Owner
            </a>
            <a href="{{ route('admin.subscriptions') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-credit-card fa-sm text-white-50"></i> View Subscriptions
            </a>
        </div>
    </div>



    <!-- Owners Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Owners ({{ $owners->total() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ownersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Country</th>
                            <th>Gender</th>
                            <th>Current Plan</th>
                            <th>Subscription Status</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $owner)
                        <tr>
                            <td>{{ $owner->user->name ?? $owner->name }}</td>
                            <td>{{ $owner->user->email ?? $owner->email }}</td>
                            <td>{{ $owner->phone }}</td>
                            <td>
                                @if($owner->country)
                                    <span class="badge bg-info">{{ $owner->country }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->gender)
                                    <span class="badge bg-secondary">{{ ucfirst($owner->gender) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->subscription && $owner->subscription->plan)
                                    <span class="badge bg-primary">{{ $owner->subscription->plan->name }}</span>
                                    @if($owner->subscription->plan->price > 0)
                                        <small class="text-muted d-block">৳{{ number_format($owner->subscription->plan->price) }}/year</small>
                                    @else
                                        <small class="text-muted d-block">Free</small>
                                    @endif
                                @elseif($owner->subscription)
                                    <span class="badge bg-warning">Plan Not Found</span>
                                @else
                                    <span class="badge bg-secondary">No Plan</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->subscription)
                                    @if($owner->subscription->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($owner->subscription->status === 'pending')
                                        <span class="badge bg-warning">Pending Payment</span>
                                        @php
                                            $pendingInvoice = $owner->subscription->getPendingInvoice();
                                        @endphp
                                        @if($pendingInvoice)
                                            <small class="text-muted d-block">Invoice: {{ $pendingInvoice->invoice_number }}</small>
                                        @endif
                                    @elseif($owner->subscription->status === 'expired')
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif($owner->subscription->status === 'suspended')
                                        <span class="badge bg-warning">Suspended</span>
                                    @elseif($owner->subscription->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($owner->subscription->status ?? 'Unknown') }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No Subscription</span>
                                @endif
                            </td>
                            <td>
                                @if($owner->subscription)
                                    @if($owner->subscription->status === 'active' && $owner->subscription->end_date)
                                        {{ $owner->subscription->end_date->format('M d, Y') }}
                                        <small class="text-muted d-block">
                                            @if($owner->subscription->daysUntilExpiry() !== null)
                                                {{ $owner->subscription->daysUntilExpiry() }} days left
                                            @else
                                                Active
                                            @endif
                                        </small>
                                    @elseif($owner->subscription->status === 'pending')
                                        <span class="text-warning">Payment Required</span>
                                        @php
                                            $pendingInvoice = $owner->subscription->getPendingInvoice();
                                        @endphp
                                        @if($pendingInvoice)
                                            <small class="text-muted d-block">
                                                Due: {{ $pendingInvoice->due_date->format('M d, Y') }}
                                            </small>
                                        @endif
                                    @elseif($owner->subscription->status === 'expired' && $owner->subscription->end_date)
                                        <span class="text-danger">{{ $owner->subscription->end_date->format('M d, Y') }}</span>
                                        <small class="text-muted d-block">Expired</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.owners.show', $owner->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($owner->subscription && $owner->subscription->status === 'pending')
                                        <a href="#" class="btn btn-sm btn-danger" title="View Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-success" title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-sm btn-primary" title="Manage Subscription">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.owners.destroy', $owner->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this owner? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remove Owner">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No owners found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($owners->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $owners->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#ownersTable').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ]]
    });
});
</script>
@endpush
@endsection
