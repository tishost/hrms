@extends('layouts.admin')

@section('title', 'Subscription Plans')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Subscription Plans</h1>
        <a href="{{ route('admin.plans.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Plan
        </a>
    </div>

    <!-- Plans Grid -->
    <div class="row">
        @foreach($plans as $plan)
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $plan->name }}</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{ route('admin.plans.edit', $plan) }}">
                                <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>Edit
                            </a>
                            <a class="dropdown-item" href="#" onclick="togglePlanStatus({{ $plan->id }})">
                                <i class="fas fa-{{ $plan->is_active ? 'pause' : 'play' }} fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" onclick="deletePlan({{ $plan->id }})">
                                <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-primary">{{ $plan->formatted_price }}</h2>
                        <small class="text-muted">per year</small>
                    </div>

                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="border-right">
                                <h5 class="text-primary">{{ $plan->properties_limit_text }}</h5>
                                <small class="text-muted">Properties</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-right">
                                <h5 class="text-success">{{ $plan->units_limit_text }}</h5>
                                <small class="text-muted">Units</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h5 class="text-info">{{ $plan->tenants_limit_text }}</h5>
                            <small class="text-muted">Tenants</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">Features:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-{{ $plan->sms_notification ? 'check text-success' : 'times text-danger' }} mr-2"></i>
                                SMS Notifications
                            </li>
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success mr-2"></i>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <div class="text-center">
                        <span class="badge badge-{{ $plan->is_active ? 'success' : 'secondary' }} mb-2">
                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <div class="small text-muted">
                            {{ $plan->subscriptions_count }} active subscriptions
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Delete Plan Modal -->
<div class="modal fade" id="deletePlanModal" tabindex="-1" role="dialog" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePlanModalLabel">Delete Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this plan? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deletePlanForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this plan?')) {
        document.getElementById('deletePlanForm').action = `/admin/plans/${planId}`;
        document.getElementById('deletePlanForm').submit();
    }
}

function togglePlanStatus(planId) {
    if (confirm('Are you sure you want to change the plan status?')) {
        fetch(`/admin/plans/${planId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection
