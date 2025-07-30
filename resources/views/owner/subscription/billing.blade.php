@extends('layouts.owner')

@section('title', 'Billing History')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">
        <div class="content-card">
                <div class="card-header">
                    <h4 class="card-title">💳 Billing History</h4>
                </div>

                <div class="card-body">
                    @if($billing->count() > 0)
                                                                                    <div class="table-responsive">
                            <table class="table" style="border-collapse: collapse; width: 100%; border: 1px solid #dee2e6;">
                                            <thead>
                                                <tr style="background-color: #f8f9fa;">
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Invoice #</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Plan</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Amount</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Status</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Due Date</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Paid Date</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Payment Method</th>
                                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: bold;">Actions</th>
                                                </tr>
                                            </thead>
                                <tbody style="border: 1px solid #dee2e6;">
                                    @foreach($billing as $bill)
                                        <tr style="border: 1px solid #dee2e6;">
                                            <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                <strong>{{ $bill->invoice_number }}</strong>
                                            </td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                @if($bill->subscription && $bill->subscription->plan)
                                                    <span class="badge badge-{{ $bill->subscription->plan->name === 'Free' ? 'secondary' : ($bill->subscription->plan->name === 'Lite' ? 'info' : 'primary') }}">
                                                        {{ $bill->subscription->plan->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                <strong>{{ $bill->formatted_amount }}</strong>
                                                @if($bill->transaction_fee > 0)
                                                    <small class="text-muted d-block">Fee: ৳{{ number_format($bill->transaction_fee, 2) }}</small>
                                                @endif
                                            </td>
                                                                                        <td style="background-color: #f8f9fa; padding: 8px; border: 1px solid #dee2e6;">
                                                @php
                                                    $statusText = ($bill->status === 'paid' || $bill->paid_date) ? 'Paid' : 'Pending';
                                                    $statusColor = ($bill->status === 'paid' || $bill->paid_date) ? '#28a745' : '#ffc107';
                                                @endphp
                                                <span style="background-color: {{ $statusColor }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $statusText }}</span>
                                            </td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $bill->due_date->format('M d, Y') }}</td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                @if($bill->paid_date)
                                                    {{ $bill->paid_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                                                                                                                                                                                                            <td style="background-color: #f8f9fa; padding: 8px; border: 1px solid #dee2e6;">
                                                @if($bill->paymentMethod)
                                                    <span style="background-color: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $bill->paymentMethod->name }}</span>
                                                @else
                                                    <span style="color: #6c757d;">-</span>
                                                @endif
                                            </td>
                                            <td style="border: 1px solid #dee2e6; padding: 8px;">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('owner.invoice.view', $bill->id) }}" target="_blank" class="btn btn-sm btn-info" title="View Invoice">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('owner.invoice.download', $bill->id) }}" class="btn btn-sm btn-secondary" title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($bill->isPending())
                                                        <a href="{{ route('owner.subscription.payment') }}" class="btn btn-sm btn-success" title="Pay Now">
                                                            <i class="fas fa-credit-card"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($billing->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $billing->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h4>No Billing Records</h4>
                            <p class="text-muted">You don't have any billing records yet.</p>
                            <a href="{{ route('owner.subscription.plans') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Purchase a Plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
