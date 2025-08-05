@extends('layouts.owner')
@section('title', 'Edit Tenant')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold">✏️ Edit Tenant</h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('owner.tenants.update', $tenant->id) }}">
        @csrf
        @method('PUT')
        
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $tenant->first_name ?? '') }}" required>
                        @error('first_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $tenant->last_name ?? '') }}" required>
                        @error('last_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-control" required>
                            <option value="male" {{ old('gender', strtolower($tenant->gender ?? '')) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', strtolower($tenant->gender ?? '')) == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile *</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $tenant->mobile ?? '') }}" required>
                        @error('mobile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alternative Mobile</label>
                        <input type="text" name="alt_mobile" class="form-control" value="{{ old('alt_mobile', $tenant->alt_mobile ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->getRawOriginal('email') ?? '') }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NID Number</label>
                        <input type="text" name="nid_number" class="form-control" value="{{ old('nid_number', $tenant->nid_number ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ old('status', $tenant->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $tenant->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt"></i> Address & Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Address *</label>
                        <textarea name="address" class="form-control" rows="3" required>{{ old('address', $tenant->address ?? '') }}</textarea>
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country *</label>
                        <select name="country" class="form-control" required>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('country', $tenant->country ?? '') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Total Family Members *</label>
                        <input type="number" name="total_family_member" class="form-control" value="{{ old('total_family_member', $tenant->total_family_member ?? 1) }}" min="1" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-briefcase"></i> Professional Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Occupation *</label>
                        <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $tenant->occupation ?? '') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $tenant->company_name ?? '') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Has Driver</label>
                        <div class="form-check">
                            <input type="checkbox" name="is_driver" class="form-check-input" value="1" {{ old('is_driver', $tenant->is_driver == 1) ? 'checked' : '' }}>
                            <label class="form-check-label">Yes, has driver</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name', $tenant->driver_name ?? '') }}">
                    </div>
                </div>
            </div>
        </div>



        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sticky-note"></i> Remarks
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="4">{{ old('remarks', $tenant->remarks ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-save"></i> Update Tenant
            </button>
            <a href="{{ route('owner.tenants.show', $tenant->id) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection 