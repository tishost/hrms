@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> System Settings
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

                    <form action="{{ route('admin.settings.system.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Currency Settings -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-money-bill-wave"></i> Currency Settings
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="system_currency">Currency</label>
                                            <select class="form-control" id="system_currency" name="system_currency">
                                                <option value="BDT" {{ \App\Models\SystemSetting::getValue('system_currency', 'BDT') == 'BDT' ? 'selected' : '' }}>৳ BDT (Bangladeshi Taka)</option>
                                                <option value="USD" {{ \App\Models\SystemSetting::getValue('system_currency', 'BDT') == 'USD' ? 'selected' : '' }}>$ USD (US Dollar)</option>
                                                <option value="EUR" {{ \App\Models\SystemSetting::getValue('system_currency', 'BDT') == 'EUR' ? 'selected' : '' }}>€ EUR (Euro)</option>
                                                <option value="GBP" {{ \App\Models\SystemSetting::getValue('system_currency', 'BDT') == 'GBP' ? 'selected' : '' }}>£ GBP (British Pound)</option>
                                                <option value="INR" {{ \App\Models\SystemSetting::getValue('system_currency', 'BDT') == 'INR' ? 'selected' : '' }}>₹ INR (Indian Rupee)</option>
                                                <option value="PKR" {{ \App\Models\SystemSetting::getValue('system_currency', 'BDT') == 'PKR' ? 'selected' : '' }}>₨ PKR (Pakistani Rupee)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_currency_symbol">Currency Symbol</label>
                                            <input type="text" class="form-control" id="system_currency_symbol" name="system_currency_symbol" 
                                                   value="{{ \App\Models\SystemSetting::getValue('system_currency_symbol', '৳') }}" maxlength="5">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_currency_position">Currency Position</label>
                                            <select class="form-control" id="system_currency_position" name="system_currency_position">
                                                <option value="left" {{ \App\Models\SystemSetting::getValue('system_currency_position', 'left') == 'left' ? 'selected' : '' }}>Left (৳100)</option>
                                                <option value="right" {{ \App\Models\SystemSetting::getValue('system_currency_position', 'left') == 'right' ? 'selected' : '' }}>Right (100৳)</option>
                                                <option value="left_space" {{ \App\Models\SystemSetting::getValue('system_currency_position', 'left') == 'left_space' ? 'selected' : '' }}>Left with Space (৳ 100)</option>
                                                <option value="right_space" {{ \App\Models\SystemSetting::getValue('system_currency_position', 'left') == 'right_space' ? 'selected' : '' }}>Right with Space (100 ৳)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_decimal_places">Decimal Places</label>
                                            <select class="form-control" id="system_decimal_places" name="system_decimal_places">
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_decimal_places', '2') == '0' ? 'selected' : '' }}>0 (৳100)</option>
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_decimal_places', '2') == '1' ? 'selected' : '' }}>1 (৳100.5)</option>
                                                <option value="2" {{ \App\Models\SystemSetting::getValue('system_decimal_places', '2') == '2' ? 'selected' : '' }}>2 (৳100.50)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_thousand_separator">Thousand Separator</label>
                                            <select class="form-control" id="system_thousand_separator" name="system_thousand_separator">
                                                <option value="," {{ \App\Models\SystemSetting::getValue('system_thousand_separator', ',') == ',' ? 'selected' : '' }}>Comma (1,000)</option>
                                                <option value="." {{ \App\Models\SystemSetting::getValue('system_thousand_separator', ',') == '.' ? 'selected' : '' }}>Dot (1.000)</option>
                                                <option value=" " {{ \App\Models\SystemSetting::getValue('system_thousand_separator', ',') == ' ' ? 'selected' : '' }}>Space (1 000)</option>
                                                <option value="" {{ \App\Models\SystemSetting::getValue('system_thousand_separator', ',') == '' ? 'selected' : '' }}>None (1000)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time Settings -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-calendar-alt"></i> Date & Time Settings
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="system_timezone">System Timezone</label>
                                            <select class="form-control" id="system_timezone" name="system_timezone">
                                                <option value="Asia/Dhaka" {{ \App\Models\SystemSetting::getValue('system_timezone', 'Asia/Dhaka') == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (UTC+6)</option>
                                                <option value="UTC" {{ \App\Models\SystemSetting::getValue('system_timezone', 'Asia/Dhaka') == 'UTC' ? 'selected' : '' }}>UTC (UTC+0)</option>
                                                <option value="America/New_York" {{ \App\Models\SystemSetting::getValue('system_timezone', 'Asia/Dhaka') == 'America/New_York' ? 'selected' : '' }}>America/New_York (UTC-5)</option>
                                                <option value="Europe/London" {{ \App\Models\SystemSetting::getValue('system_timezone', 'Asia/Dhaka') == 'Europe/London' ? 'selected' : '' }}>Europe/London (UTC+0)</option>
                                                <option value="Asia/Kolkata" {{ \App\Models\SystemSetting::getValue('system_timezone', 'Asia/Dhaka') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (UTC+5:30)</option>
                                                <option value="Asia/Karachi" {{ \App\Models\SystemSetting::getValue('system_timezone', 'Asia/Dhaka') == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi (UTC+5)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_date_format">Date Format</label>
                                            <select class="form-control" id="system_date_format" name="system_date_format">
                                                <option value="Y-m-d" {{ \App\Models\SystemSetting::getValue('system_date_format', 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-08-03)</option>
                                                <option value="d/m/Y" {{ \App\Models\SystemSetting::getValue('system_date_format', 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (03/08/2024)</option>
                                                <option value="m/d/Y" {{ \App\Models\SystemSetting::getValue('system_date_format', 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (08/03/2024)</option>
                                                <option value="d-m-Y" {{ \App\Models\SystemSetting::getValue('system_date_format', 'Y-m-d') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (03-08-2024)</option>
                                                <option value="m-d-Y" {{ \App\Models\SystemSetting::getValue('system_date_format', 'Y-m-d') == 'm-d-Y' ? 'selected' : '' }}>MM-DD-YYYY (08-03-2024)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_time_format">Time Format</label>
                                            <select class="form-control" id="system_time_format" name="system_time_format">
                                                <option value="H:i" {{ \App\Models\SystemSetting::getValue('system_time_format', 'H:i') == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                                <option value="h:i A" {{ \App\Models\SystemSetting::getValue('system_time_format', 'H:i') == 'h:i A' ? 'selected' : '' }}>12 Hour (2:30 PM)</option>
                                                <option value="h:i a" {{ \App\Models\SystemSetting::getValue('system_time_format', 'H:i') == 'h:i a' ? 'selected' : '' }}>12 Hour (2:30 pm)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_datetime_format">Date & Time Format</label>
                                            <select class="form-control" id="system_datetime_format" name="system_datetime_format">
                                                <option value="Y-m-d H:i" {{ \App\Models\SystemSetting::getValue('system_datetime_format', 'Y-m-d H:i') == 'Y-m-d H:i' ? 'selected' : '' }}>YYYY-MM-DD HH:MM (2024-08-03 14:30)</option>
                                                <option value="d/m/Y H:i" {{ \App\Models\SystemSetting::getValue('system_datetime_format', 'Y-m-d H:i') == 'd/m/Y H:i' ? 'selected' : '' }}>DD/MM/YYYY HH:MM (03/08/2024 14:30)</option>
                                                <option value="m/d/Y h:i A" {{ \App\Models\SystemSetting::getValue('system_datetime_format', 'Y-m-d H:i') == 'm/d/Y h:i A' ? 'selected' : '' }}>MM/DD/YYYY h:mm AM/PM (08/03/2024 2:30 PM)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_week_start">Week Start Day</label>
                                            <select class="form-control" id="system_week_start" name="system_week_start">
                                                <option value="monday" {{ \App\Models\SystemSetting::getValue('system_week_start', 'monday') == 'monday' ? 'selected' : '' }}>Monday</option>
                                                <option value="sunday" {{ \App\Models\SystemSetting::getValue('system_week_start', 'monday') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Configuration -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-server"></i> System Configuration
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="system_language">System Language</label>
                                            <select class="form-control" id="system_language" name="system_language">
                                                <option value="en" {{ \App\Models\SystemSetting::getValue('system_language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                                <option value="bn" {{ \App\Models\SystemSetting::getValue('system_language', 'en') == 'bn' ? 'selected' : '' }}>বাংলা (Bengali)</option>
                                                <option value="ar" {{ \App\Models\SystemSetting::getValue('system_language', 'en') == 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_pagination">Items Per Page</label>
                                            <select class="form-control" id="system_pagination" name="system_pagination">
                                                <option value="10" {{ \App\Models\SystemSetting::getValue('system_pagination', '20') == '10' ? 'selected' : '' }}>10 items</option>
                                                <option value="20" {{ \App\Models\SystemSetting::getValue('system_pagination', '20') == '20' ? 'selected' : '' }}>20 items</option>
                                                <option value="50" {{ \App\Models\SystemSetting::getValue('system_pagination', '20') == '50' ? 'selected' : '' }}>50 items</option>
                                                <option value="100" {{ \App\Models\SystemSetting::getValue('system_pagination', '20') == '100' ? 'selected' : '' }}>100 items</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_maintenance_mode">Maintenance Mode</label>
                                            <select class="form-control" id="system_maintenance_mode" name="system_maintenance_mode">
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_maintenance_mode', '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_maintenance_mode', '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_debug_mode">Debug Mode</label>
                                            <select class="form-control" id="system_debug_mode" name="system_debug_mode">
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_debug_mode', '0') == '0' ? 'selected' : '' }}>Disabled</option>
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_debug_mode', '0') == '1' ? 'selected' : '' }}>Enabled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Settings -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-bell"></i> Notification Settings
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="system_email_notifications">Email Notifications</label>
                                            <select class="form-control" id="system_email_notifications" name="system_email_notifications">
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_email_notifications', '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_email_notifications', '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_sms_notifications">SMS Notifications</label>
                                            <select class="form-control" id="system_sms_notifications" name="system_sms_notifications">
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_sms_notifications', '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_sms_notifications', '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_push_notifications">Push Notifications</label>
                                            <select class="form-control" id="system_push_notifications" name="system_push_notifications">
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_push_notifications', '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_push_notifications', '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="system_notification_sound">Notification Sound</label>
                                            <select class="form-control" id="system_notification_sound" name="system_notification_sound">
                                                <option value="1" {{ \App\Models\SystemSetting::getValue('system_notification_sound', '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ \App\Models\SystemSetting::getValue('system_notification_sound', '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save System Settings
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