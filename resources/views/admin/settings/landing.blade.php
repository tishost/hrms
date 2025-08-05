@extends('layouts.admin')

@section('title', 'Landing Page Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe"></i> Landing Page Management
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.landing.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Hero Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-star"></i> Hero Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="hero_title">Hero Title</label>
                                                    <input type="text" class="form-control" id="hero_title" name="hero_title" 
                                                           value="{{ \App\Models\SystemSetting::getValue('hero_title', 'Welcome to HRMS') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="hero_subtitle">Hero Subtitle</label>
                                                    <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" 
                                                           value="{{ \App\Models\SystemSetting::getValue('hero_subtitle', 'Property Management System') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="hero_description">Hero Description</label>
                                            <textarea class="form-control" id="hero_description" name="hero_description" rows="3">{{ \App\Models\SystemSetting::getValue('hero_description', 'Manage your properties, tenants, and financial transactions efficiently with our comprehensive property management system.') }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="hero_button_text">Primary Button Text</label>
                                                    <input type="text" class="form-control" id="hero_button_text" name="hero_button_text" 
                                                           value="{{ \App\Models\SystemSetting::getValue('hero_button_text', 'Get Started') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="hero_button_url">Primary Button URL</label>
                                                    <input type="url" class="form-control" id="hero_button_url" name="hero_button_url" 
                                                           value="{{ \App\Models\SystemSetting::getValue('hero_button_url', '/register') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="hero_image">Hero Background Image</label>
                                            <input type="file" class="form-control" id="hero_image" name="hero_image" accept="image/*">
                                            <small class="form-text text-muted">Recommended size: 1920x1080px, Max size: 2MB</small>
                                            @if(\App\Models\SystemSetting::getValue('hero_image'))
                                                <div class="mt-2">
                                                    <label>Current Image:</label>
                                                    <img src="{{ asset('storage/' . \App\Models\SystemSetting::getValue('hero_image')) }}" 
                                                         alt="Hero Background" class="img-fluid" style="max-height: 100px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-list"></i> Features Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="features_title">Features Section Title</label>
                                            <input type="text" class="form-control" id="features_title" name="features_title" 
                                                   value="{{ \App\Models\SystemSetting::getValue('features_title', 'Why Choose HRMS?') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="features_subtitle">Features Section Subtitle</label>
                                            <input type="text" class="form-control" id="features_subtitle" name="features_subtitle" 
                                                   value="{{ \App\Models\SystemSetting::getValue('features_subtitle', 'Discover the features that make HRMS the perfect solution for property management') }}">
                                        </div>
                                        
                                        <!-- Feature 1 -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="feature1_title">Feature 1 Title</label>
                                                    <input type="text" class="form-control" id="feature1_title" name="feature1_title" 
                                                           value="{{ \App\Models\SystemSetting::getValue('feature1_title', 'Property Management') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="feature1_icon">Feature 1 Icon (FontAwesome)</label>
                                                    <input type="text" class="form-control" id="feature1_icon" name="feature1_icon" 
                                                           value="{{ \App\Models\SystemSetting::getValue('feature1_icon', 'fas fa-building') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature1_description">Feature 1 Description</label>
                                                    <textarea class="form-control" id="feature1_description" name="feature1_description" rows="2">{{ \App\Models\SystemSetting::getValue('feature1_description', 'Efficiently manage multiple properties with detailed tracking and reporting.') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Feature 2 -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="feature2_title">Feature 2 Title</label>
                                                    <input type="text" class="form-control" id="feature2_title" name="feature2_title" 
                                                           value="{{ \App\Models\SystemSetting::getValue('feature2_title', 'Tenant Management') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="feature2_icon">Feature 2 Icon (FontAwesome)</label>
                                                    <input type="text" class="form-control" id="feature2_icon" name="feature2_icon" 
                                                           value="{{ \App\Models\SystemSetting::getValue('feature2_icon', 'fas fa-users') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature2_description">Feature 2 Description</label>
                                                    <textarea class="form-control" id="feature2_description" name="feature2_description" rows="2">{{ \App\Models\SystemSetting::getValue('feature2_description', 'Manage tenant information, rent collection, and communication efficiently.') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Feature 3 -->
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="feature3_title">Feature 3 Title</label>
                                                    <input type="text" class="form-control" id="feature3_title" name="feature3_title" 
                                                           value="{{ \App\Models\SystemSetting::getValue('feature3_title', 'Financial Tracking') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="feature3_icon">Feature 3 Icon (FontAwesome)</label>
                                                    <input type="text" class="form-control" id="feature3_icon" name="feature3_icon" 
                                                           value="{{ \App\Models\SystemSetting::getValue('feature3_icon', 'fas fa-chart-line') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="feature3_description">Feature 3 Description</label>
                                                    <textarea class="form-control" id="feature3_description" name="feature3_description" rows="2">{{ \App\Models\SystemSetting::getValue('feature3_description', 'Track income, expenses, and generate detailed financial reports.') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- About Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-info-circle"></i> About Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="about_title">About Section Title</label>
                                                    <input type="text" class="form-control" id="about_title" name="about_title" 
                                                           value="{{ \App\Models\SystemSetting::getValue('about_title', 'About HRMS') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="about_subtitle">About Section Subtitle</label>
                                                    <input type="text" class="form-control" id="about_subtitle" name="about_subtitle" 
                                                           value="{{ \App\Models\SystemSetting::getValue('about_subtitle', 'Your Trusted Property Management Partner') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="about_content">About Content</label>
                                            <textarea class="form-control" id="about_content" name="about_content" rows="6">{{ \App\Models\SystemSetting::getValue('about_content', 'HRMS is a comprehensive property management system designed to help property owners and managers efficiently manage their properties, tenants, and financial transactions. Our platform provides powerful tools for tracking rent payments, managing maintenance requests, and generating detailed reports.') }}</textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="about_image">About Section Image</label>
                                            <input type="file" class="form-control" id="about_image" name="about_image" accept="image/*">
                                            <small class="form-text text-muted">Recommended size: 600x400px, Max size: 2MB</small>
                                            @if(\App\Models\SystemSetting::getValue('about_image'))
                                                <div class="mt-2">
                                                    <label>Current Image:</label>
                                                    <img src="{{ asset('storage/' . \App\Models\SystemSetting::getValue('about_image')) }}" 
                                                         alt="About Section" class="img-fluid" style="max-height: 100px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-phone"></i> Contact Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="contact_title">Contact Section Title</label>
                                            <input type="text" class="form-control" id="contact_title" name="contact_title" 
                                                   value="{{ \App\Models\SystemSetting::getValue('contact_title', 'Contact Us') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="contact_description">Contact Description</label>
                                            <textarea class="form-control" id="contact_description" name="contact_description" rows="3">{{ \App\Models\SystemSetting::getValue('contact_description', 'Get in touch with us for any questions or support. We are here to help you with your property management needs.') }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="contact_email">Contact Email</label>
                                                    <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                                           value="{{ \App\Models\SystemSetting::getValue('contact_email', 'info@hrms.com') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="contact_phone">Contact Phone</label>
                                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                                           value="{{ \App\Models\SystemSetting::getValue('contact_phone', '+880 1234-567890') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="contact_address">Contact Address</label>
                                            <textarea class="form-control" id="contact_address" name="contact_address" rows="2">{{ \App\Models\SystemSetting::getValue('contact_address', 'Dhaka, Bangladesh') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-copyright"></i> Footer Section
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="footer_copyright">Copyright Text</label>
                                            <input type="text" class="form-control" id="footer_copyright" name="footer_copyright" 
                                                   value="{{ \App\Models\SystemSetting::getValue('footer_copyright', '© 2024 HRMS. All rights reserved.') }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="footer_description">Footer Description</label>
                                            <textarea class="form-control" id="footer_description" name="footer_description" rows="3">{{ \App\Models\SystemSetting::getValue('footer_description', 'HRMS is your trusted partner for comprehensive property management solutions.') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Landing Page Settings
                                </button>
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Settings
                                </a>
                                <a href="{{ route('home') }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Preview Landing Page
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