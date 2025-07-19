@extends('layouts.owner')
@section('title', 'Check-out Records')

@section('content')
<div class="container py-4">
    <div class="page-header">
        <div class="page-title">
            <h1>🚪 Check-out Records</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="border-radius: 10px; box-shadow: 0 2px 12px rgba(67,97,238,0.07);">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Unit</th>
                            <th>Check-out Date</th>
                            <th>Reason</th>
                            <th>Final Settlement</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checkouts as $checkout)
                            <tr>
                                <td>
                                    <strong>{{ $checkout->tenant->first_name }} {{ $checkout->tenant->last_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $checkout->tenant->mobile }}</small>
                                </td>
                                <td>{{ $checkout->unit->name ?? 'N/A' }}</td>
                                <td>{{ $checkout->check_out_date->format('M d, Y') }}</td>
                                <td>{{ $checkout->check_out_reason }}</td>
                                <td>
                                    <strong class="{{ $checkout->final_settlement_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                        ৳{{ number_format($checkout->final_settlement_amount, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="status {{ strtolower($checkout->settlement_status) }}">
                                        {{ ucfirst($checkout->settlement_status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('owner.checkouts.show', $checkout->id) }}"
                                       class="action-btn edit">View</a>
                                    <a href="{{ route('owner.checkouts.invoice', $checkout->id) }}"
                                       class="action-btn add">Invoice</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted">No check-out records found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($checkouts->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $checkouts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}
.status.completed {
    background: #d4ffd4;
    color: #00a000;
}
.status.partial {
    background: #fff3cd;
    color: #856404;
}
.status.pending {
    background: #ffd4d4;
    color: #d40000;
}
</style>
@endsection
