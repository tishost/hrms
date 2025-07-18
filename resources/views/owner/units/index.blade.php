@extends('layouts.owner')

@section('title', 'All Units')

@section('head')
<style>
@media (max-width: 768px) {
    .table thead {
        display: none;
    }
    .table, .table tbody, .table tr, .table td {
        display: block;
        width: 100%;
    }
    .table tr {
        margin-bottom: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    .table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    .table td::before {
        content: attr(data-label);
        position: absolute;
        left: 16px;
        top: 10px;
        font-weight: 600;
        color: #3f37c9;
        text-align: left;
    }
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h4 class="mb-0">🏬 All Units</h4>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Property Name</th>
                <th>Unit Name</th>
                <th>Total Rent (৳)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $unit)
            <tr>
                <td data-label="Property Name">{{ $unit->property->name ?? '-' }}</td>
                <td data-label="Unit Name">{{ $unit->name }}</td>
                <td data-label="Total Rent (৳)">{{ $unit->rent + ($unit->charges ? $unit->charges->sum('amount') : 0) }}</td>
                <td data-label="Status">
                    @if($unit->status === 'rent' && $unit->tenant)
                        Rented |
                        <a href="{{ route('owner.tenants.show', $unit->tenant->id) }}">
                            {{ $unit->tenant->first_name }} {{ $unit->tenant->last_name }}
                        </a>
                    @else
                        Free
                    @endif
                </td>
                <td data-label="Actions">
                    <a href="{{ route('owner.units.edit', $unit->id) }}" class="action-btn edit">Edit</a>
                    <form action="{{ route('owner.units.destroy', $unit->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this unit?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No units found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
