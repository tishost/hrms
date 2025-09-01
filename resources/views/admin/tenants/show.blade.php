<!-- filepath: resources/views/admin/tenants/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Tenant Details')

@section('content')
<style>
.tenant-details-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.tenant-details-section .card-header {
    background: rgba(255,255,255,0.15);
    border-bottom: 2px solid rgba(255,255,255,0.2);
    padding: 20px;
}

.tenant-details-section .card-header h6 {
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tenant-details-section .card-body {
    background: rgba(255,255,255,0.95);
    padding: 30px;
}

.info-row {
    display: flex;
    margin-bottom: 15px;
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 15px;
}

.info-label {
    font-weight: 600;
    color: #495057;
    min-width: 150px;
    flex-shrink: 0;
}

.info-value {
    color: #212529;
    flex: 1;
}

.badge-modern {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge-modern.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white;
}

.badge-modern.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    color: white;
}

.badge-modern.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    color: white;
}

.badge-modern.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
    color: white;
}

.badge-modern.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    color: white;
}

.section-title {
    color: #667eea;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #667eea;
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

@media (max-width: 768px) {
    .info-row {
        flex-direction: column;
    }
    
    .info-label {
        min-width: auto;
        margin-bottom: 5px;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tenant Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tenants.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Tenant Details -->
    <div class="card shadow mb-4 tenant-details-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-user"></i> Tenant Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Profile Picture -->
                <div class="col-md-3 text-center mb-4">
                    @if($tenant->profile_pic)
                        <img src="{{ asset('storage/' . $tenant->profile_pic) }}" alt="Profile Picture" class="profile-pic">
                    @else
                        <div class="profile-pic d-flex align-items-center justify-content-center bg-secondary text-white">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Basic Information -->
                <div class="col-md-9">
                    <h4 class="section-title">Basic Information</h4>
                    
                    <div class="info-row">
                        <div class="info-label">Full Name:</div>
                        <div class="info-value">
                            <strong>{{ $tenant->full_name }}</strong>
                            @if($tenant->gender)
                                <span class="badge badge-modern bg-info ml-2">{{ ucfirst($tenant->gender) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Mobile Number:</div>
                        <div class="info-value">
                            {{ $tenant->mobile }}
                            @if($tenant->phone_verified)
                                <span class="badge badge-modern bg-success ml-2">Verified</span>
                            @else
                                <span class="badge badge-modern bg-warning ml-2">Not Verified</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($tenant->alt_mobile)
                    <div class="info-row">
                        <div class="info-label">Alternative Mobile:</div>
                        <div class="info-value">{{ $tenant->alt_mobile }}</div>
                    </div>
                    @endif
                    
                    @if($tenant->email)
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $tenant->email }}</div>
                    </div>
                    @endif
                    
                    @if($tenant->nid_number)
                    <div class="info-row">
                        <div class="info-label">NID Number:</div>
                        <div class="info-value">{{ $tenant->nid_number }}</div>
                    </div>
                    @endif
                    
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            @if($tenant->status === 'active')
                                <span class="badge badge-modern bg-success">Active</span>
                            @elseif($tenant->status === 'inactive')
                                <span class="badge badge-modern bg-warning">Inactive</span>
                            @elseif($tenant->status === 'checkout')
                                <span class="badge badge-modern bg-danger">Checkout</span>
                            @else
                                <span class="badge badge-modern bg-secondary">{{ ucfirst($tenant->status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address & Location Information -->
    <div class="card shadow mb-4 tenant-details-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-map-marker-alt"></i> Address & Location
            </h6>
        </div>
        <div class="card-body">
            <h4 class="section-title">Address Information</h4>
            
            @if($tenant->address)
            <div class="info-row">
                <div class="info-label">Address:</div>
                <div class="info-value">{{ $tenant->address }}</div>
            </div>
            @endif
            
            @if($tenant->district)
            <div class="info-row">
                <div class="info-label">District:</div>
                <div class="info-value">{{ $tenant->district }}</div>
            </div>
            @endif
            
            @if($tenant->upazila)
            <div class="info-row">
                <div class="info-label">Upazila:</div>
                <div class="info-value">{{ $tenant->upazila }}</div>
            </div>
            @endif
            
            @if($tenant->zip)
            <div class="info-row">
                <div class="info-label">ZIP Code:</div>
                <div class="info-value">{{ $tenant->zip }}</div>
            </div>
            @endif
            
            @if($tenant->country)
            <div class="info-row">
                <div class="info-label">Country:</div>
                <div class="info-value">{{ $tenant->country }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Property & Unit Information -->
    <div class="card shadow mb-4 tenant-details-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-home"></i> Property & Unit Information
            </h6>
        </div>
        <div class="card-body">
            <h4 class="section-title">Property Details</h4>
            
            @if($tenant->owner)
            <div class="info-row">
                <div class="info-label">Owner:</div>
                <div class="info-value">
                    <span class="badge badge-modern bg-info">{{ $tenant->owner->name }}</span>
                    @if($tenant->owner->phone)
                        <small class="text-muted d-block mt-1">{{ $tenant->owner->phone }}</small>
                    @endif
                </div>
            </div>
            @endif
            
            @if($tenant->property)
            <div class="info-row">
                <div class="info-label">Property:</div>
                <div class="info-value">{{ $tenant->property->name ?? 'N/A' }}</div>
            </div>
            @endif
            
            @if($tenant->unit)
            <div class="info-row">
                <div class="info-label">Unit:</div>
                <div class="info-value">{{ $tenant->unit->name ?? 'N/A' }}</div>
            </div>
            @endif
            
            @if($tenant->check_in_date)
            <div class="info-row">
                <div class="info-label">Check In Date:</div>
                <div class="info-value">{{ $tenant->check_in_date->format('F d, Y') }}</div>
            </div>
            @endif
            
            @if($tenant->check_out_date)
            <div class="info-row">
                <div class="info-label">Check Out Date:</div>
                <div class="info-value">{{ $tenant->check_out_date->format('F d, Y') }}</div>
            </div>
            @endif
            
            @if($tenant->security_deposit)
            <div class="info-row">
                <div class="info-label">Security Deposit:</div>
                <div class="info-value">৳{{ number_format($tenant->security_deposit) }}</div>
            </div>
            @endif
            
            @if($tenant->cleaning_charges)
            <div class="info-row">
                <div class="info-label">Cleaning Charges:</div>
                <div class="info-value">৳{{ number_format($tenant->cleaning_charges) }}</div>
            </div>
            @endif
            
            @if($tenant->other_charges)
            <div class="info-row">
                <div class="info-label">Other Charges:</div>
                <div class="info-value">৳{{ number_format($tenant->other_charges) }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Family & Personal Information -->
    <div class="card shadow mb-4 tenant-details-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users"></i> Family & Personal Information
            </h6>
        </div>
        <div class="card-body">
            <h4 class="section-title">Family Details</h4>
            
            @if($tenant->total_family_member)
            <div class="info-row">
                <div class="info-label">Total Family Members:</div>
                <div class="info-value">{{ $tenant->total_family_member }}</div>
            </div>
            @endif
            
            @if($tenant->child_qty)
            <div class="info-row">
                <div class="info-label">Number of Children:</div>
                <div class="info-value">{{ $tenant->child_qty }}</div>
            </div>
            @endif
            
            @if($tenant->spouse_name)
            <div class="info-row">
                <div class="info-label">Spouse Name:</div>
                <div class="info-value">{{ $tenant->spouse_name }}</div>
            </div>
            @endif
            
            @if($tenant->father_name)
            <div class="info-row">
                <div class="info-label">Father's Name:</div>
                <div class="info-value">{{ $tenant->father_name }}</div>
            </div>
            @endif
            
            @if($tenant->mother_name)
            <div class="info-row">
                <div class="info-label">Mother's Name:</div>
                <div class="info-value">{{ $tenant->mother_name }}</div>
            </div>
            @endif
            
            @if($tenant->occupation)
            <div class="info-row">
                <div class="info-label">Occupation:</div>
                <div class="info-value">{{ $tenant->occupation }}</div>
            </div>
            @endif
            
            @if($tenant->company_name)
            <div class="info-row">
                <div class="info-label">Company Name:</div>
                <div class="info-value">{{ $tenant->company_name }}</div>
            </div>
            @endif
            
            @if($tenant->business_name)
            <div class="info-row">
                <div class="info-label">Business Name:</div>
                <div class="info-value">{{ $tenant->business_name }}</div>
            </div>
            @endif
            
            @if($tenant->college_university)
            <div class="info-row">
                <div class="info-label">College/University:</div>
                <div class="info-value">{{ $tenant->college_university }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Additional Information -->
    @if($tenant->remarks || $tenant->check_out_reason || $tenant->handover_condition)
    <div class="card shadow mb-4 tenant-details-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-info-circle"></i> Additional Information
            </h6>
        </div>
        <div class="card-body">
            <h4 class="section-title">Additional Details</h4>
            
            @if($tenant->remarks)
            <div class="info-row">
                <div class="info-label">Remarks:</div>
                <div class="info-value">{{ $tenant->remarks }}</div>
            </div>
            @endif
            
            @if($tenant->check_out_reason)
            <div class="info-row">
                <div class="info-label">Check Out Reason:</div>
                <div class="info-value">{{ $tenant->check_out_reason }}</div>
            </div>
            @endif
            
            @if($tenant->handover_condition)
            <div class="info-row">
                <div class="info-label">Handover Condition:</div>
                <div class="info-value">{{ $tenant->handover_condition }}</div>
            </div>
            @endif
            
            @if($tenant->handover_date)
            <div class="info-row">
                <div class="info-label">Handover Date:</div>
                <div class="info-value">{{ $tenant->handover_date->format('F d, Y') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
