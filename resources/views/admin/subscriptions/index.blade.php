@extends('layouts.admin')

@section('title', 'Manage Subscriptions')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Subscriptions</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.plans.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-cube fa-sm text-white-50"></i> Manage Plans
            </a>
                            <a href="{{ route('admin.billing.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-file-invoice-dollar fa-sm text-white-50"></i> View Billing
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
                                Active Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Expired Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('status', 'active')->where('end_date', '<', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($subscriptions->sum('plan.price')) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Expiring Soon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->where('status', 'active')->where('end_date', '>', now())->where('end_date', '<', now()->addDays(30))->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Subscriptions</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="subscriptionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Owner</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days Left</th>
                            <th>Auto Renew</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <span class="text-white font-weight-bold">{{ substr($subscription->owner->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $subscription->owner->user->name }}</div>
                                        <small class="text-muted">{{ $subscription->owner->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $subscription->plan->name === 'Free' ? 'secondary' : ($subscription->plan->name === 'Lite' ? 'info' : 'primary') }}">
                                    {{ $subscription->plan->name }}
                                </span>
                                <div class="small text-muted">৳{{ number_format($subscription->plan->price) }}/year</div>
                            </td>
                            <td>
                                @if($subscription->isActive())
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscription->isExpired())
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($subscription->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                            <td>{{ $subscription->end_date->format('M d, Y') }}</td>
                            <td>
                                @if($subscription->isActive())
                                    @if($subscription->daysUntilExpiry() <= 30)
                                        <span class="text-warning font-weight-bold">{{ $subscription->daysUntilExpiry() }} days</span>
                                    @else
                                        <span class="text-success">{{ $subscription->daysUntilExpiry() }} days</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($subscription->auto_renew)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="#" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-warning" title="Edit Subscription">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-success" title="Renew Subscription">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No subscriptions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($subscriptions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $subscriptions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#subscriptionsTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "asc" ]]
    });
});
</script>
@endpush
@endsection
