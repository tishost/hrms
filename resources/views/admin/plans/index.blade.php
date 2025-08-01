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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Plans</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="plansTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Properties</th>
                            <th>Units</th>
                            <th>Tenants</th>
                            <th>SMS</th>
                            <th>SMS Credits</th>
                            <th>Status</th>
                            <th>Popular</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                        <tr>
                            <td>
                                <strong>{{ $plan->name }}</strong>
                                @if($plan->is_popular)
                                    <span class="badge bg-warning ms-2">Popular</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->price == 0)
                                    <span class="text-success">Free</span>
                                @else
                                    ৳{{ number_format($plan->price) }}
                                @endif
                            </td>
                            <td>
                                @if($plan->properties_limit == -1)
                                    <span class="text-info">Unlimited</span>
                                @else
                                    {{ $plan->properties_limit }}
                                @endif
                            </td>
                            <td>
                                @if($plan->units_limit == -1)
                                    <span class="text-info">Unlimited</span>
                                @else
                                    {{ $plan->units_limit }}
                                @endif
                            </td>
                            <td>
                                @if($plan->tenants_limit == -1)
                                    <span class="text-info">Unlimited</span>
                                @else
                                    {{ $plan->tenants_limit }}
                                @endif
                            </td>
                            <td>
                                @if($plan->sms_notification)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->sms_notification && $plan->sms_credit > 0)
                                    <span class="badge bg-info">{{ number_format($plan->sms_credit) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->is_popular)
                                    <span class="badge bg-warning">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.plans.edit', $plan) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.plans.destroy', $plan) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this plan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#plansTable').DataTable({
        "order": [[1, "asc"]],
        "pageLength": 25
    });
});
</script>
@endpush
@endsection
