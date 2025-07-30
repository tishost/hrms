@extends('layouts.admin')

@section('title', 'Billing Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Billing Management</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.subscriptions') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-credit-card fa-sm text-white-50"></i> View Subscriptions
            </a>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Report
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($totalRevenue) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Pending Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($pendingAmount) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $billing->where('status', 'pending')->where('due_date', '<', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                This Month Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">৳{{ number_format($monthlyRevenue) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Billing Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="billingTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Owner</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Paid Date</th>
                            <th>Payment Method</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billing as $bill)
                        <tr>
                            <td>
                                <strong>{{ $bill->invoice_number }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <span class="text-white font-weight-bold">{{ substr($bill->owner->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $bill->owner->name }}</div>
                                        <small class="text-muted">{{ $bill->owner->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $planColor = $bill->subscription->plan->name === 'Free' ? '#6c757d' : ($bill->subscription->plan->name === 'Lite' ? '#17a2b8' : '#007bff');
                                @endphp
                                <span style="background-color: {{ $planColor }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $bill->subscription->plan->name }}</span>
                            </td>
                            <td>
                                <strong>{{ $bill->formatted_amount }}</strong>
                            </td>
                            <td>
                                @php
                                    $statusText = $bill->status ?? 'pending';
                                    if ($bill->paid_date && !$bill->status) {
                                        $statusText = 'paid';
                                    }
                                    $statusColor = $statusText === 'paid' ? '#28a745' : ($statusText === 'pending' ? '#ffc107' : '#6c757d');
                                @endphp
                                <span style="background-color: {{ $statusColor }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ ucfirst($statusText) }}</span>
                                @if($bill->isOverdue())
                                    <span style="background-color: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Overdue</span>
                                @endif
                            </td>
                            <td>{{ $bill->due_date->format('M d, Y') }}</td>
                            <td>
                                @if($bill->paid_date)
                                    {{ $bill->paid_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($bill->paymentMethod)
                                    <span style="background-color: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $bill->paymentMethod->name }}</span>
                                    @if($bill->transaction_fee > 0)
                                        <small class="text-muted d-block">Fee: ৳{{ number_format($bill->transaction_fee, 2) }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="#" class="btn btn-sm btn-info" title="View Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($bill->isPending())
                                        <a href="{{ route('admin.payments.form', $bill->id) }}"
                                           class="btn btn-sm btn-success" title="Process Payment">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                        <a href="{{ route('admin.payments.mark-paid', $bill->id) }}"
                                           class="btn btn-sm btn-primary"
                                           onclick="return confirm('Mark this invoice as paid?')"
                                           title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    @endif
                                    <a href="#" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No billing records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($billing->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $billing->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#billingTable').DataTable({
        "pageLength": 25,
        "order": [[ 5, "desc" ]]
    });
});
</script>
@endpush
@endsection
