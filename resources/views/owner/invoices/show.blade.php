@extends('layouts.owner')
@section('title', 'Invoice Details')
@section('content')
<div class="container py-4">
    <h4>Invoice #{{ $invoice->invoice_number }}</h4>
    <p><strong>Tenant:</strong> {{ $invoice->tenant->first_name ?? '' }} {{ $invoice->tenant->last_name ?? '' }}</p>
    <p><strong>Unit:</strong> {{ $invoice->unit->name ?? '' }}</p>
    <p><strong>Month:</strong> {{ $invoice->rent_month }}</p>
    <p><strong>Amount:</strong> {{ $invoice->amount }}</p>
    <p><strong>Status:</strong> {{ $invoice->status }}</p>
    <p><strong>Issue Date:</strong> {{ $invoice->issue_date }}</p>
    <p><strong>Type:</strong> {{ ucfirst($invoice->type) }}</p>
    <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
</div>
@endsection
