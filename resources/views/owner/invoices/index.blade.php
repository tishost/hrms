@extends('layouts.owner')

@section('title', 'Rent Invoices')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice"></i> Rent Invoices
                    </h3>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Tenant</th>
                                        <th>Property</th>
                                        <th>Unit</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->id }}</td>
                                        <td>{{ $invoice->tenant->full_name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->tenant->property->name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->tenant->unit->unit_number ?? 'N/A' }}</td>
                                        <td>৳{{ number_format($invoice->amount, 2) }}</td>
                                        <td>{{ $invoice->payment_date ? $invoice->payment_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @if($invoice->status === 'paid')
                                                <span class="badge badge-success">Paid</span>
                                            @elseif($invoice->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('owner.invoices.show', $invoice->id) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5>No Rent Invoices Found</h5>
                            <p class="text-muted">No rent invoices have been generated yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


