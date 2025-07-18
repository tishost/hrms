@extends('layouts.admin')
@section('content')

<div class="container">
    <h4 class="mb-3 bg-dark text-white p-2 rounded">⚙️ Platform Settings</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>🏢 Default Building Limit (per Owner)</label>
            <input type="number" name="default_building_limit" value="{{ $settings['default_building_limit'] ?? 5 }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>🧱 Default Unit Limit (per Building)</label>
            <input type="number" name="default_unit_limit" value="{{ $settings['default_unit_limit'] ?? 10 }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">💾 Save Settings</button>
    </form>
</div>

@endsection