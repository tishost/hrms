@extends('layouts.owner')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="section-header">
    <h4 class="mb-3">🏢 My Buildings</h4>
        <a href="{{ route('owner.property.create') }}" class="add-btn">+ Add New Building</a>
    </div>

    @if($properties->count())
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Building Name</th>
                <th>Type</th>
                <th>Address</th>
                <th>Country</th>
                <th>Total Units</th>
                <th>Facilities</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($properties as $property)
            <tr>
                <td>{{ $property->name }}</td>
                <td>{{ ucfirst($property->type) }}</td>
                <td>{{ $property->address }}</td>
                <td>{{ $property->country }}</td>
                <td>{{ $property->units->count() }}</td>
                <td>
                    @php
                        $facilities = json_decode($property->features ?? '[]');
                    @endphp
                    @if($facilities)
                        {{ implode(', ', $facilities) }}
                    @else
                        <em>None</em>
                    @endif
                </td>
                <td>
                    <a href="{{ route('owner.units.setup', $property->id) }}" class="action-btn add">Add Units</a>
                    <a href="{{ route('owner.property.edit', $property->id) }}" class="action-btn edit">Edit</a>
                    {{-- Optionally add delete if needed --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="alert alert-info">No buildings found. Click "Add New Building" to get started.</div>
    @endif
</div>
@endsection
