@extends('layouts.admin')

@section('title', 'Company Information Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Company Information Settings
                    </h3>
                </div>
                <div class="card-body">
                   

                    <form action="{{ route('admin.settings.company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Company Basic Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-info-circle"></i> Basic Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="company_name">Company Name *</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_name', 'HRMS') }}" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_tagline">Company Tagline</label>
                                            <input type="text" class="form-control" id="company_tagline" name="company_tagline" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_tagline', 'Property Management System') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_email">Company Email *</label>
                                            <input type="email" class="form-control" id="company_email" name="company_email" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_email', 'info@hrms.com') }}" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_phone">Company Phone</label>
                                            <input type="text" class="form-control" id="company_phone" name="company_phone" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_phone', '+880 1234-567890') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_website">Company Website</label>
                                            <input type="url" class="form-control" id="company_website" name="company_website" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_website', 'https://hrms.com') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Address -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-map-marker-alt"></i> Address Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="company_address">Company Address</label>
                                            <textarea class="form-control" id="company_address" name="company_address" rows="3">{{ \App\Models\SystemSetting::getValue('company_address', 'Dhaka, Bangladesh') }}</textarea>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_city">City</label>
                                            <input type="text" class="form-control" id="company_city" name="company_city" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_city', 'Dhaka') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_state">State/Province</label>
                                            <input type="text" class="form-control" id="company_state" name="company_state" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_state', 'Dhaka') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_country">Country</label>
                                            <input type="text" class="form-control" id="company_country" name="company_country" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_country', 'Bangladesh') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_postal_code">Postal Code</label>
                                            <input type="text" class="form-control" id="company_postal_code" name="company_postal_code" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_postal_code', '1000') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Logo & Favicon -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-image"></i> Company Logo
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="company_logo">Upload Logo</label>
                                            <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                                            <small class="form-text text-muted">Recommended size: 200x80px, Max size: 2MB</small>
                                        </div>

                                        @if(\App\Models\SystemSetting::getValue('company_logo'))
                                            <div class="mb-3">
                                                <label>Current Logo:</label>
                                                <div>
                                                    <img src="{{ asset('storage/' . \App\Models\SystemSetting::getValue('company_logo')) }}" 
                                                         alt="Company Logo" class="img-fluid" style="max-height: 80px;">
                                                </div>
                                            </div>
                                        @endif
                                        <dev>
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-star"></i> Website Favicon
                                        </h5>
                                        </dev>
                                        <div class="form-group mb-3">
                                            <label for="company_favicon">Upload Favicon</label>
                                            <input type="file" class="form-control" id="company_favicon" name="company_favicon" accept="image/*">
                                            <small class="form-text text-muted">Recommended size: 32x32px or 16x16px, Max size: 1MB. Supported formats: ICO, PNG, GIF</small>
                                        </div>

                                        @if(\App\Models\SystemSetting::getValue('company_favicon'))
                                            <div class="mb-3">
                                                <label>Current Favicon:</label>
                                                <div>
                                                    <img src="{{ asset('storage/' . \App\Models\SystemSetting::getValue('company_favicon')) }}" 
                                                         alt="Website Favicon" class="img-fluid" style="max-width: 32px; max-height: 32px;">
                                                    <small class="form-text text-muted d-block mt-1">This favicon will appear in browser tabs and bookmarks</small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Favicon Tips:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Use square images (32x32px recommended)</li>
                                                <li>Simple designs work best at small sizes</li>
                                                <li>ICO format is most compatible</li>
                                                <li>Will appear in browser tabs and bookmarks</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        
                    

                            <!-- Social Media Links -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-share-alt"></i> Social Media Links
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="company_facebook">Facebook</label>
                                            <input type="url" class="form-control" id="company_facebook" name="company_facebook" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_facebook', '') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_twitter">Twitter</label>
                                            <input type="url" class="form-control" id="company_twitter" name="company_twitter" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_twitter', '') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_linkedin">LinkedIn</label>
                                            <input type="url" class="form-control" id="company_linkedin" name="company_linkedin" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_linkedin', '') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_instagram">Instagram</label>
                                            <input type="url" class="form-control" id="company_instagram" name="company_instagram" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_instagram', '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-briefcase"></i> Business Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="company_registration_number">Registration Number</label>
                                            <input type="text" class="form-control" id="company_registration_number" name="company_registration_number" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_registration_number', '') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_tax_id">Tax ID/VAT Number</label>
                                            <input type="text" class="form-control" id="company_tax_id" name="company_tax_id" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_tax_id', '') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_established">Established Year</label>
                                            <input type="number" class="form-control" id="company_established" name="company_established" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_established', '2024') }}" min="1900" max="2030">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_description">Company Description</label>
                                            <textarea class="form-control" id="company_description" name="company_description" rows="4">{{ \App\Models\SystemSetting::getValue('company_description', 'HRMS is a comprehensive property management system designed to help property owners and managers efficiently manage their properties, tenants, and financial transactions.') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-phone"></i> Contact Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="company_support_email">Support Email</label>
                                            <input type="email" class="form-control" id="company_support_email" name="company_support_email" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_support_email', 'support@hrms.com') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_support_phone">Support Phone</label>
                                            <input type="text" class="form-control" id="company_support_phone" name="company_support_phone" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_support_phone', '+880 1234-567890') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_working_hours">Working Hours</label>
                                            <input type="text" class="form-control" id="company_working_hours" name="company_working_hours" 
                                                   value="{{ \App\Models\SystemSetting::getValue('company_working_hours', 'Monday - Friday: 9:00 AM - 6:00 PM') }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="company_timezone">Company Timezone</label>
                                            <select class="form-control" id="company_timezone" name="company_timezone">
                                                <option value="Asia/Dhaka" {{ \App\Models\SystemSetting::getValue('company_timezone', 'Asia/Dhaka') == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (UTC+6)</option>
                                                <option value="UTC" {{ \App\Models\SystemSetting::getValue('company_timezone', 'Asia/Dhaka') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="America/New_York" {{ \App\Models\SystemSetting::getValue('company_timezone', 'Asia/Dhaka') == 'America/New_York' ? 'selected' : '' }}>America/New_York (UTC-5)</option>
                                                <option value="Europe/London" {{ \App\Models\SystemSetting::getValue('company_timezone', 'Asia/Dhaka') == 'Europe/London' ? 'selected' : '' }}>Europe/London (UTC+0)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Company Information
                                </button>
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Settings
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 