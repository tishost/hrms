<?php
use App\Models\Role; // Correct
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\OwnerRegisterController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerPropertyController;
use App\Http\Controllers\Owner\OwnerUnitController;
use App\Http\Controllers\Owner\TenantController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Owner\TenantRentController;
use App\Http\Controllers\Owner\RentPaymentController;
use App\Http\Controllers\Owner\SettingController;

use App\Http\Controllers\Owner\CheckoutController;
use Illuminate\Http\Request;
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('home');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Language switching route
Route::get('/language/switch', function (Request $request) {
    $lang = $request->get('lang');
    if (in_array($lang, ['en', 'bn'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return redirect()->back();
})->name('language.switch');

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

// Test CSRF route
Route::post('/test-csrf', function () {
    return response()->json(['success' => true, 'message' => 'CSRF working']);
})->name('test.csrf');

// Test template save route outside admin middleware
Route::post('/test-template-save', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true, 
        'message' => 'Template save test working',
        'data' => $request->all()
    ]);
})->name('test.template.save');

// Test template save route with same controller but outside admin middleware
Route::post('/test-template-save-admin', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'saveTemplate'])->name('test.template.save.admin');

// Simple CSRF test route
Route::post('/test-csrf-simple', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'CSRF test successful',
        'csrf_token' => $request->header('X-CSRF-TOKEN'),
        'form_token' => $request->input('_token'),
        'session_id' => session()->getId(),
        'session_started' => session()->isStarted()
    ]);
})->name('test.csrf.simple');

// CSRF token refresh route
Route::get('/refresh-csrf', function() {
    return response()->json(['token' => csrf_token()]);
})->name('refresh.csrf');

// CSRF token test route
Route::post('/test-csrf-token', function (\Illuminate\Http\Request $request) {
    $csrfToken = $request->header('X-CSRF-TOKEN');
    $sessionToken = session()->token();
    
    return response()->json([
        'success' => $csrfToken === $sessionToken,
        'csrf_token' => $csrfToken ? substr($csrfToken, 0, 20) . '...' : 'null',
        'session_token' => $sessionToken ? substr($sessionToken, 0, 20) . '...' : 'null',
        'tokens_match' => $csrfToken === $sessionToken,
        'session_id' => session()->getId(),
        'user_authenticated' => auth()->check()
    ]);
})->name('test.csrf.token');

// Template save route (working version)
// Test route for owner creation
Route::get('/test-owner-creation', function (\Illuminate\Http\Request $request) {
    try {
        $user = \App\Models\User::create([
            'name' => 'Test User ' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        
        $owner = \App\Models\Owner::create([
            'user_id' => $user->id,
            'name' => 'Test Owner ' . time(),
            'email' => 'test' . time() . '@example.com',
            'phone' => '+8801718262530',
            'address' => 'Test Address',
            'country' => 'Bangladesh',
            'gender' => 'male',
            'status' => 'active',
            'is_super_admin' => false,
        ]);
        
        $user->update([
            'owner_id' => $owner->id,
            'phone' => $owner->phone
        ]);
        
        $freshUser = \App\Models\User::find($user->id);
        $freshOwner = \App\Models\Owner::find($owner->id);
        
        // Clean up
        $owner->delete();
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Test completed successfully',
            'user_phone' => $freshUser->phone,
            'owner_phone' => $freshOwner->phone,
            'user_id' => $user->id,
            'owner_id' => $owner->id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Test failed: ' . $e->getMessage()
        ]);
    }
})->name('test.owner.creation');

Route::get('/test-save-template', function (\Illuminate\Http\Request $request) {
    $action = $request->get('action');
    $templateName = $request->get('template_name');
    
    // Handle GET action (load template)
    if ($action === 'get' && $templateName) {
        try {
            // Try different possible key formats
            $template = \App\Models\SystemSetting::where('key', 'template_' . $templateName)
                ->orWhere('key', $templateName)
                ->orWhere('key', $templateName . '_template')
                ->first();
            
            if ($template) {
                $templateData = json_decode($template->value, true);
                return response()->json([
                    'success' => true,
                    'template' => $templateData
                ]);
            } else {
                // Debug: Log what we're looking for
                \Log::info('Template not found for: ' . $templateName);
                \Log::info('Available templates: ' . \App\Models\SystemSetting::where('key', 'like', '%template%')->orWhere('key', 'like', '%_email')->orWhere('key', 'like', '%_sms')->pluck('key')->toJson());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading template: ' . $e->getMessage()
            ]);
        }
    }
    
    // Handle save action
    $content = $request->get('content');
    $subject = $request->get('subject');
    
    if ($templateName && $content) {
        try {
            $templateData = [];
            
            // Check if it's an SMS template (contains '_sms' in name)
            if (str_contains($templateName, '_sms')) {
                $templateData = ['content' => $content];
            } else {
                // Email template
                $templateData = [
                    'subject' => $subject ?? 'HRMS Notification',
                    'content' => $content
                ];
            }
            
            $result = \App\Models\SystemSetting::setValue('template_' . $templateName, json_encode($templateData));
            
            return response()->json([
                'success' => true, 
                'message' => 'Template saved successfully!',
                'id' => $result->id
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save template: ' . $e->getMessage()
            ]);
        }
    }
    
    return response()->json([
        'success' => false, 
        'message' => 'Missing template parameters'
    ]);
})->name('test.save.template');

// Debug route for testing features_css
Route::get('/debug-plans', function () {
    $plans = \App\Models\SubscriptionPlan::all(['name', 'features', 'features_css']);
    return response()->json($plans);
});

// Contact routes
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact/submit', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// Legal pages
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/refund', function () {
    return view('refund');
})->name('refund');


Route::get('/register/owner', [OwnerRegisterController::class, 'showForm'])->name('owner.register.form');
Route::post('/register/owner', [OwnerRegisterController::class, 'register'])->name('owner.register');

// Register with plan selection
Route::get('/register/owner/plan/{plan}', function ($plan) {
    session(['selected_plan' => $plan]);
    return redirect()->route('owner.register.form');
})->name('owner.register.with.plan');

// Direct subscription after registration
Route::get('/register/owner/plan/{plan}/subscribe', function ($plan) {
    session(['selected_plan' => $plan, 'direct_subscribe' => true]);
    return redirect()->route('owner.register.form');
})->name('owner.register.with.plan.subscribe');



//Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Owner routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/property/create', [OwnerPropertyController::class, 'create'])->name('property.create');
    Route::get('/property/index', [OwnerPropertyController::class, 'index'])->name('property.index');
    Route::get('/properties/{property}/edit', [OwnerPropertyController::class, 'edit'])->name('property.edit');
    Route::post('/properties/{property}', [OwnerPropertyController::class, 'update'])->name('property.update');
    Route::post('/properties', [OwnerPropertyController::class, 'store'])
        ->name('property.store')
        ->middleware('check.limits:properties');
    Route::get('/property/export/pdf', [OwnerPropertyController::class, 'exportPdf'])->name('property.export.pdf');
    Route::get('units', [OwnerUnitController::class, 'index'])->name('units.index');
    Route::get('/units/export/pdf', [OwnerUnitController::class, 'exportPdf'])->name('units.export.pdf');
    Route::get('units/{unit}/edit', [OwnerUnitController::class, 'edit'])->name('units.edit');
    Route::get('/tenants/export/pdf', [TenantController::class, 'exportPdf'])->name('tenants.export.pdf');
    Route::delete('units/{unit}', [OwnerUnitController::class, 'destroy'])->name('units.destroy');
    Route::put('units/{unit}', [OwnerUnitController::class, 'update'])->name('units.update');
    Route::get('units/setup/{property}', [OwnerUnitController::class, 'setup'])->name('units.setup');
    Route::post('units/generate/{property}', [OwnerUnitController::class, 'generate'])->name('units.generate');
    Route::post('units/saveFees/{property}', [OwnerUnitController::class, 'saveFees'])->name('units.saveFees');
    Route::get('/units-by-building/{id}', [TenantController::class, 'getUnitsByBuilding'])->name('units.byBuilding');
    Route::post('/tenants/store', [TenantController::class, 'store'])
        ->name('tenants.store')
        ->middleware('check.limits:tenants');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::get('/tenants/{tenant}/assign-rent', [TenantRentController::class, 'create'])->name('rents.create');
    Route::post('/tenants/{tenant}/assign-rent', [TenantRentController::class, 'store'])->name('rents.store');
    
    // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-sms', [SettingController::class, 'testSms'])->name('settings.test-sms');
    Route::post('/settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');
    Route::post('/settings/reset-templates', [SettingController::class, 'resetTemplates'])->name('settings.reset-templates');
    Route::get('/units-by-building/{id}', [TenantController::class, 'getUnitsByBuilding'])->name('units.byBuilding');
    Route::get('/unit-fees/{unit}', [OwnerUnitController::class, 'getFees'])->name('units.fees');
    Route::get('rent-payments/create',[RentPaymentController::class, 'create'])->name('rent_payments.create');
    Route::post('rent-payments', [RentPaymentController::class, 'store'])->name('rent_payments.store');
    Route::get('rent-payments/fees-dues', [RentPaymentController::class, 'getFeesAndDues'])->name('rent_payments.fees_dues');

    // Invoice routes
    Route::get('invoices', [App\Http\Controllers\Owner\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [App\Http\Controllers\Owner\InvoiceController::class, 'show'])->name('invoices.show');

    // Checkout routes
    Route::get('checkouts', [CheckoutController::class, 'index'])->name('checkouts.index');
    Route::get('tenants/{tenant}/checkout', [CheckoutController::class, 'showCheckoutForm'])->name('checkouts.create');
    Route::post('tenants/{tenant}/checkout', [CheckoutController::class, 'processCheckout'])->name('checkouts.process');
    Route::get('checkouts/{checkout}', [CheckoutController::class, 'show'])->name('checkouts.show');
    Route::get('checkouts/{checkout}/invoice', [CheckoutController::class, 'generateInvoice'])->name('checkouts.invoice');

               // Subscription routes
    Route::get('subscription/current', [App\Http\Controllers\Owner\SubscriptionController::class, 'currentPlan'])->name('subscription.current');
    Route::get('subscription/plans', [App\Http\Controllers\Owner\SubscriptionController::class, 'availablePlans'])->name('subscription.plans');
    Route::post('subscription/purchase', [App\Http\Controllers\Owner\SubscriptionController::class, 'purchasePlan'])->name('subscription.purchase');
    Route::post('subscription/upgrade', [App\Http\Controllers\Owner\SubscriptionController::class, 'upgradePlan'])->name('subscription.upgrade');
    Route::post('subscription/upgrade/complete/{invoiceId}', [App\Http\Controllers\Owner\SubscriptionController::class, 'completeUpgrade'])->name('subscription.upgrade.complete');
    Route::post('subscription/upgrade/cancel/{upgradeRequestId}', [App\Http\Controllers\Owner\SubscriptionController::class, 'cancelUpgrade'])->name('subscription.upgrade.cancel');
    Route::get('subscription/upgrade/status', [App\Http\Controllers\Owner\SubscriptionController::class, 'getUpgradeStatus'])->name('subscription.upgrade.status');
    Route::get('subscription/billing', [App\Http\Controllers\Owner\SubscriptionController::class, 'billingHistory'])->name('subscription.billing');
    Route::get('subscription/payment', [App\Http\Controllers\Owner\SubscriptionController::class, 'paymentMethods'])->name('subscription.payment');
    Route::match(['GET', 'POST'], 'subscription/initiate-gateway', [App\Http\Controllers\Owner\SubscriptionController::class, 'initiatePaymentGateway'])->name('subscription.initiate-gateway');
    Route::get('subscription/payment/gateway', [App\Http\Controllers\Owner\SubscriptionController::class, 'paymentGateway'])->name('subscription.payment.gateway');
    Route::get('subscription/payment/success', [App\Http\Controllers\Owner\SubscriptionController::class, 'paymentSuccess'])->name('subscription.payment.success');
    Route::get('subscription/payment/cancel', [App\Http\Controllers\Owner\SubscriptionController::class, 'paymentCancel'])->name('subscription.payment.cancel');
    Route::get('subscription/payment/fail', [App\Http\Controllers\Owner\SubscriptionController::class, 'paymentFail'])->name('subscription.payment.fail');

    // Invoice routes
    Route::get('invoice/{billingId}/view', [App\Http\Controllers\Owner\InvoiceController::class, 'viewInvoice'])->name('invoice.view');
    Route::get('invoice/{billingId}/download', [App\Http\Controllers\Owner\InvoiceController::class, 'downloadInvoice'])->name('invoice.download');

    // SMS Credit routes
    Route::get('sms/credits', [App\Http\Controllers\Owner\SmsCreditController::class, 'index'])->name('sms.credits');
    Route::post('sms/add-credits', [App\Http\Controllers\Owner\SmsCreditController::class, 'addCredits'])->name('sms.add-credits');
    Route::get('sms/stats', [App\Http\Controllers\Owner\SmsCreditController::class, 'getStats'])->name('sms.stats');

    // Owner Backup Management
    Route::get('backups', [App\Http\Controllers\Owner\BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [App\Http\Controllers\Owner\BackupController::class, 'store'])->name('backups.store');
    Route::get('backups/{backup}', [App\Http\Controllers\Owner\BackupController::class, 'show'])->name('backups.show');
    Route::get('backups/{backup}/download', [App\Http\Controllers\Owner\BackupController::class, 'download'])->name('backups.download');
    Route::post('backups/{backup}/restore', [App\Http\Controllers\Owner\BackupController::class, 'restore'])->name('backups.restore');
    Route::get('backups/stats', [App\Http\Controllers\Owner\BackupController::class, 'getStats'])->name('backups.stats');
    Route::get('backups/{backup}/details', [App\Http\Controllers\Owner\BackupController::class, 'getDetails'])->name('backups.details');

    // Owner Backup Settings Routes
    Route::get('settings/backup', [App\Http\Controllers\Owner\BackupSettingsController::class, 'index'])->name('settings.backup');
    Route::put('settings/backup', [App\Http\Controllers\Owner\BackupSettingsController::class, 'update'])->name('settings.backup.update');
    Route::post('settings/backup/test', [App\Http\Controllers\Owner\BackupSettingsController::class, 'testBackup'])->name('settings.backup.test');
    Route::get('settings/backup/stats', [App\Http\Controllers\Owner\BackupSettingsController::class, 'getStats'])->name('settings.backup.stats');

});


// API route for owner profile update (for Flutter)
Route::post('/owner/profile/update', [App\Http\Controllers\Admin\OwnerController::class, 'update']);


// Admin Login Routes (Public)
Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Admin Routes
Route::middleware(['auth', 'super.admin', 'refresh.session'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // Subscription Plans
    Route::resource('plans', App\Http\Controllers\Admin\SubscriptionPlanController::class);
    Route::post('plans/{plan}/toggle-status', [App\Http\Controllers\Admin\SubscriptionPlanController::class, 'toggleStatus'])->name('plans.toggle-status');

    // Subscriptions
    Route::get('subscriptions', [App\Http\Controllers\Admin\AdminDashboardController::class, 'subscriptions'])->name('subscriptions');

    // Billing
    Route::get('billing', [App\Http\Controllers\Admin\AdminDashboardController::class, 'billing'])->name('billing.index');

    // Owners
    Route::get('owners', [App\Http\Controllers\Admin\AdminDashboardController::class, 'owners'])->name('owners.index');
    Route::get('owners/create', [App\Http\Controllers\Admin\AdminDashboardController::class, 'createOwner'])->name('owners.create');
    Route::get('owners/{id}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'showOwner'])->name('owners.show');
    Route::post('owners/{id}/test-notification', [App\Http\Controllers\Admin\AdminDashboardController::class, 'testNotification'])->name('owners.test-notification');
    Route::post('owners/{id}/test-email', [App\Http\Controllers\Admin\AdminDashboardController::class, 'testEmail'])->name('owners.test-email');
    Route::post('owners/{id}/test-sms', [App\Http\Controllers\Admin\AdminDashboardController::class, 'testSms'])->name('owners.test-sms');
    Route::post('owners/{id}/resend-notification/{log_id}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'resendNotification'])->name('owners.resend-notification');
    Route::post('owners/{id}/kill-session', [App\Http\Controllers\Admin\AdminDashboardController::class, 'killUserSession'])->name('owners.kill-session');
    Route::post('owners', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeOwner'])->name('owners.store');
    Route::delete('owners/{id}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'destroyOwner'])->name('owners.destroy');

    // Tenants
    Route::get('tenants', [App\Http\Controllers\Admin\TenantController::class, 'index'])->name('tenants.index');
    Route::get('tenants/{id}', [App\Http\Controllers\Admin\TenantController::class, 'show'])->name('tenants.show');
    Route::get('tenants/export', [App\Http\Controllers\Admin\TenantController::class, 'export'])->name('tenants.export');

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('settings/payment-gateway', [App\Http\Controllers\Admin\AdminSettingController::class, 'paymentGateway'])->name('settings.payment-gateway');
    Route::post('settings/payment-gateway', [App\Http\Controllers\Admin\AdminSettingController::class, 'updatePaymentGateway'])->name('settings.payment-gateway.update');
    Route::post('settings/bkash', [App\Http\Controllers\Admin\AdminSettingController::class, 'updateBkashSettings'])->name('settings.bkash.update');
    Route::post('settings/bkash/test', [App\Http\Controllers\Admin\AdminSettingController::class, 'testBkashConnection'])->name('settings.bkash.test');
    Route::post('settings/bkash/test-payment', [App\Http\Controllers\Admin\AdminSettingController::class, 'testBkashPaymentCreation'])->name('settings.bkash.test-payment');
    Route::get('settings/bkash/status', [App\Http\Controllers\Admin\AdminSettingController::class, 'getBkashConfigurationStatus'])->name('settings.bkash.status');

    // Test route for debugging
    Route::get('test-super-admin', function() {
        $user = auth()->user();
        if ($user) {
            return response()->json([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'has_super_admin_role' => $user->hasRole('super_admin'),
                'has_owner_role' => $user->hasRole('owner'),
                'roles' => $user->roles->pluck('name'),
                'is_super_admin' => $user->owner->is_super_admin ?? false,
                'current_route' => request()->route()->getName(),
                'should_see_admin' => $user->hasRole('super_admin') || ($user->owner && $user->owner->is_super_admin)
            ]);
        }
        return response()->json(['error' => 'Not authenticated']);
    })->name('test.super.admin');

    // Test admin settings route
    Route::get('test-admin-settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('test.admin.settings');
    Route::get('debug-admin', [App\Http\Controllers\Admin\AdminSettingController::class, 'debug'])->name('debug.admin');

    // OTP Settings
    Route::get('otp-settings', [App\Http\Controllers\Admin\OtpSettingsController::class, 'index'])->name('otp-settings.index');
    Route::put('otp-settings', [App\Http\Controllers\Admin\OtpSettingsController::class, 'update'])->name('otp-settings.update');
    Route::post('otp-settings/toggle', [App\Http\Controllers\Admin\OtpSettingsController::class, 'toggle'])->name('otp-settings.toggle');

    // Notification Settings
    Route::get('settings/notifications', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'index'])->name('settings.notifications');
    Route::get('settings/notifications/template', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'getTemplate'])->name('notifications.template.get');
    Route::match(['GET', 'POST'], 'settings/notifications/template', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'saveTemplate'])->name('notifications.template.save');
    Route::post('settings/notifications/template/save', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'saveTemplates'])->name('settings.notifications.template.save');
    Route::get('settings/notifications/log', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'viewLog'])->name('notifications.log.view');
    Route::get('settings/notifications/log/details', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'getLogDetails'])->name('notifications.log.details');
    
    // Notification Logs
    Route::get('settings/notification-logs', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'notificationLogs'])->name('settings.notification-logs');


    // SMS Group Settings Route
    Route::put('settings/notifications/sms-groups', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'updateSmsGroupSettings'])->name('notifications.sms-groups.update');

    // Language Settings Route
    Route::put('settings/notifications/language', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'updateLanguageSettings'])->name('settings.notifications.language.update');

    // Company Information Settings
    Route::get('settings/company', [App\Http\Controllers\Admin\CompanySettingsController::class, 'index'])->name('settings.company');
    Route::post('settings/company/update', [App\Http\Controllers\Admin\CompanySettingsController::class, 'update'])->name('settings.company.update');

    // System Settings
    Route::get('settings/system', [App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('settings.system');
    Route::post('settings/system/update', [App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('settings.system.update');

    // OTP Security Routes
    Route::get('security/otp', [App\Http\Controllers\Admin\OtpSecurityController::class, 'index'])->name('security.otp');
    Route::get('security/otp/logs', [App\Http\Controllers\Admin\OtpSecurityController::class, 'logs'])->name('security.otp.logs');
    Route::post('security/otp/unblock-ip', [App\Http\Controllers\Admin\OtpSecurityController::class, 'unblockIp'])->name('security.otp.unblock-ip');
    Route::post('security/otp/unblock-phone', [App\Http\Controllers\Admin\OtpSecurityController::class, 'unblockPhone'])->name('security.otp.unblock-phone');
    Route::post('security/otp/block-ip', [App\Http\Controllers\Admin\OtpSecurityController::class, 'blockIp'])->name('security.otp.block-ip');
    Route::post('security/otp/block-phone', [App\Http\Controllers\Admin\OtpSecurityController::class, 'blockPhone'])->name('security.otp.block-phone');
    Route::post('security/otp/reset-phone', [App\Http\Controllers\Admin\OtpSecurityController::class, 'resetPhoneLimit'])->name('security.otp.reset-phone');

    // Ads Management
    Route::resource('ads', App\Http\Controllers\Admin\AdsController::class);
    Route::post('ads/{ad}/toggle-status', [App\Http\Controllers\Admin\AdsController::class, 'toggleStatus'])->name('ads.toggle-status');
    Route::post('ads/update-order', [App\Http\Controllers\Admin\AdsController::class, 'updateOrder'])->name('ads.update-order');
    Route::get('ads/stats', [App\Http\Controllers\Admin\AdsController::class, 'stats'])->name('ads.stats');
    Route::get('security/otp/statistics', [App\Http\Controllers\Admin\OtpSecurityController::class, 'getStatistics'])->name('security.otp.statistics');
    Route::get('security/otp/export', [App\Http\Controllers\Admin\OtpSecurityController::class, 'exportLogs'])->name('security.otp.export');

        // Landing Page Management
        Route::get('settings/landing', [App\Http\Controllers\Admin\LandingPageController::class, 'index'])->name('settings.landing');
        Route::post('settings/landing/update', [App\Http\Controllers\Admin\LandingPageController::class, 'update'])->name('settings.landing.update');

        // Financial Reports
        Route::get('reports/financial', [App\Http\Controllers\Admin\FinancialReportController::class, 'index'])->name('reports.financial');
        Route::post('reports/financial/export-pdf', [App\Http\Controllers\Admin\FinancialReportController::class, 'exportPdf'])->name('reports.financial.export-pdf');
        Route::post('reports/financial/export-excel', [App\Http\Controllers\Admin\FinancialReportController::class, 'exportExcel'])->name('reports.financial.export-excel');

        // Analytics
        Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
        Route::post('analytics/real-time', [App\Http\Controllers\Admin\AnalyticsController::class, 'getRealTimeAnalytics'])->name('analytics.real-time');
        Route::post('analytics/custom', [App\Http\Controllers\Admin\AnalyticsController::class, 'getCustomAnalytics'])->name('analytics.custom');

        // Login Logs
        Route::get('login-logs', [App\Http\Controllers\Admin\LoginLogController::class, 'index'])->name('login-logs.index');
        Route::get('login-logs/active-sessions', [App\Http\Controllers\Admin\LoginLogController::class, 'activeSessions'])->name('login-logs.active-sessions');
        Route::get('login-logs/user/{user}/history', [App\Http\Controllers\Admin\LoginLogController::class, 'userHistory'])->name('login-logs.user-history');
        Route::post('login-logs/block-ip', [App\Http\Controllers\Admin\LoginLogController::class, 'blockIp'])->name('login-logs.block-ip');
        Route::post('login-logs/unblock-ip', [App\Http\Controllers\Admin\LoginLogController::class, 'unblockIp'])->name('login-logs.unblock-ip');
        Route::get('login-logs/export', [App\Http\Controllers\Admin\LoginLogController::class, 'export'])->name('login-logs.export');
        Route::get('login-logs/real-time-stats', [App\Http\Controllers\Admin\LoginLogController::class, 'getRealTimeStats'])->name('login-logs.real-time-stats');
        Route::get('login-logs/{loginLog}', [App\Http\Controllers\Admin\LoginLogController::class, 'show'])->name('login-logs.show');

        // Backup Management
        Route::get('backups', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [App\Http\Controllers\Admin\BackupController::class, 'store'])->name('backups.store');
        Route::get('backups/{backup}', [App\Http\Controllers\Admin\BackupController::class, 'show'])->name('backups.show');
        Route::get('backups/{backup}/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
        Route::post('backups/{backup}/restore', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('backups/{backup}', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('backups/clean-old', [App\Http\Controllers\Admin\BackupController::class, 'cleanOld'])->name('backups.clean-old');
        Route::get('backups/stats', [App\Http\Controllers\Admin\BackupController::class, 'getStats'])->name('backups.stats');
                Route::get('backups/{backup}/details', [App\Http\Controllers\Admin\BackupController::class, 'getDetails'])->name('backups.details');

        // Backup Settings Routes
        Route::get('settings/backup', [App\Http\Controllers\Admin\BackupSettingsController::class, 'index'])->name('settings.backup');
        Route::put('settings/backup', [App\Http\Controllers\Admin\BackupSettingsController::class, 'update'])->name('settings.backup.update');
        Route::post('settings/backup/test', [App\Http\Controllers\Admin\BackupSettingsController::class, 'testBackup'])->name('settings.backup.test');
        Route::post('settings/backup/clean', [App\Http\Controllers\Admin\BackupSettingsController::class, 'cleanOldBackups'])->name('settings.backup.clean');
        Route::get('settings/backup/stats', [App\Http\Controllers\Admin\BackupSettingsController::class, 'getStats'])->name('settings.backup.stats');
        Route::post('settings/backup/schedule', [App\Http\Controllers\Admin\BackupSettingsController::class, 'scheduleBackup'])->name('settings.backup.schedule');

        // Email Configuration Routes
        Route::get('settings/email-configuration', [App\Http\Controllers\Admin\EmailConfigurationController::class, 'index'])->name('settings.email-configuration');
        Route::put('settings/email-configuration', [App\Http\Controllers\Admin\EmailConfigurationController::class, 'updateEmailSettings'])->name('settings.email-configuration.update');
        Route::post('settings/email-configuration/test', [App\Http\Controllers\Admin\EmailConfigurationController::class, 'testEmail'])->name('settings.email-configuration.test');
        Route::get('settings/email-configuration/debug', [App\Http\Controllers\Admin\EmailConfigurationController::class, 'debugEmailSettings'])->name('settings.email-configuration.debug');

    // Tickets Management
    Route::get('tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::patch('tickets/{ticket}/status', [App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('tickets/{ticket}/note', [App\Http\Controllers\Admin\TicketController::class, 'addNote'])->name('tickets.add-note');
    Route::delete('tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'destroy'])->name('tickets.destroy');

    // Payment routes
    Route::get('payments/{invoiceId}', [App\Http\Controllers\Admin\PaymentController::class, 'showPaymentForm'])->name('payments.form');
    Route::post('payments/{invoiceId}', [App\Http\Controllers\Admin\PaymentController::class, 'processPayment'])->name('payments.process');
    Route::post('payments/{invoiceId}/mark-paid', [App\Http\Controllers\Admin\PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');

    // SEO Settings
    Route::get('settings/seo', [App\Http\Controllers\Admin\SeoSettingsController::class, 'index'])->name('settings.seo');
    Route::put('settings/seo', [App\Http\Controllers\Admin\SeoSettingsController::class, 'update'])->name('settings.seo.update');
    Route::post('settings/seo/sitemap', [App\Http\Controllers\Admin\SeoSettingsController::class, 'generateSitemap'])->name('settings.seo.sitemap');
    Route::post('settings/seo/robots', [App\Http\Controllers\Admin\SeoSettingsController::class, 'generateRobotsTxt'])->name('settings.seo.robots');
    Route::post('settings/seo/preview', [App\Http\Controllers\Admin\SeoSettingsController::class, 'previewSeo'])->name('settings.seo.preview');
    
    // Chat Settings Routes
    Route::get('settings/chat', [App\Http\Controllers\Admin\ChatSettingsController::class, 'index'])->name('settings.chat');
    Route::put('settings/chat', [App\Http\Controllers\Admin\ChatSettingsController::class, 'update'])->name('settings.chat.update');
    Route::get('settings/chat/test', [App\Http\Controllers\Admin\ChatSettingsController::class, 'testChat'])->name('settings.chat.test');
    Route::get('settings/chat/settings', [App\Http\Controllers\Admin\ChatSettingsController::class, 'getChatSettings'])->name('settings.chat.settings');
    
    // SMS Settings Routes
    Route::get('settings/sms', [App\Http\Controllers\Admin\SmsSettingsController::class, 'index'])->name('settings.sms');
    Route::put('settings/sms', [App\Http\Controllers\Admin\SmsSettingsController::class, 'update'])->name('settings.sms.update');
    Route::post('settings/sms/test', [App\Http\Controllers\Admin\SmsSettingsController::class, 'testSms'])->name('settings.sms.test');
    
    // Charges Setup Routes
    Route::resource('charges', App\Http\Controllers\Admin\ChargeController::class);
    Route::post('charges/{charge}/toggle-status', [App\Http\Controllers\Admin\ChargeController::class, 'toggleStatus'])->name('charges.toggle-status');
    Route::get('charges-api', [App\Http\Controllers\Admin\ChargeController::class, 'getCharges'])->name('charges.api');
    Route::get('settings/sms/test-connection', [App\Http\Controllers\Admin\SmsSettingsController::class, 'testConnection'])->name('settings.sms.test-connection');
    Route::get('settings/sms/settings', [App\Http\Controllers\Admin\SmsSettingsController::class, 'getSmsSettings'])->name('settings.sms.settings');
    Route::post('settings/sms/bulk', [App\Http\Controllers\Admin\SmsSettingsController::class, 'sendBulkSms'])->name('settings.sms.bulk');
    Route::get('settings/sms/balance', [App\Http\Controllers\Admin\SmsSettingsController::class, 'checkBalance'])->name('settings.sms.balance');

    // SMS Credit Management Routes
    Route::get('sms/credits', [App\Http\Controllers\Admin\SmsCreditController::class, 'index'])->name('sms.credits.index');
    Route::get('sms/credits/{owner}', [App\Http\Controllers\Admin\SmsCreditController::class, 'show'])->name('sms.credits.show');
    Route::post('sms/credits/{owner}/add', [App\Http\Controllers\Admin\SmsCreditController::class, 'addCredits'])->name('sms.credits.add');
    Route::post('sms/credits/{owner}/test', [App\Http\Controllers\Admin\SmsCreditController::class, 'sendTestSms'])->name('sms.credits.test');
    Route::get('sms/credits/{owner}/stats', [App\Http\Controllers\Admin\SmsCreditController::class, 'getStats'])->name('sms.credits.stats');
    Route::post('sms/smart-send', [App\Http\Controllers\Admin\SmsCreditController::class, 'sendSmartSms'])->name('sms.smart-send');

    // Notification Settings Routes
    Route::get('settings/notifications', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'index'])->name('settings.notifications');
    Route::get('settings/email-templates', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'emailTemplates'])->name('settings.email.templates');
    Route::get('settings/sms-templates', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'smsTemplates'])->name('settings.sms.templates');
});

// API OTP Settings Route (Public)
Route::get('/api/otp-settings', [App\Http\Controllers\Admin\OtpSettingsController::class, 'getSettings']);

// SEO Routes (Public)
Route::get('/sitemap.xml', function() {
    $sitemap = \App\Services\SeoService::generateSitemap();
    return response($sitemap, 200, ['Content-Type' => 'application/xml']);
});

Route::get('/robots.txt', function() {
    $robots = \App\Services\SeoService::generateRobotsTxt();
    return response($robots, 200, ['Content-Type' => 'text/plain']);
});

// Chat Routes
Route::post('/chat/message', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
Route::get('/chat/analytics', [App\Http\Controllers\ChatController::class, 'analytics'])->name('chat.analytics');
Route::get('/chat/session/{sessionId}', [App\Http\Controllers\ChatController::class, 'session'])->name('chat.session');
Route::post('/chat/request-agent', [App\Http\Controllers\ChatController::class, 'requestAgent'])->name('chat.request-agent');
Route::get('/chat/agent-availability', [App\Http\Controllers\ChatController::class, 'checkAgentAvailability'])->name('chat.agent-availability');

// Agent Routes
Route::middleware(['auth', 'role:admin|super_admin|agent'])->prefix('admin/chat')->name('admin.chat.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\ChatAgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/session/{sessionId}', [App\Http\Controllers\Admin\ChatAgentController::class, 'getSession'])->name('session');
    Route::post('/send-message', [App\Http\Controllers\Admin\ChatAgentController::class, 'sendMessage'])->name('send-message');
    Route::post('/take-session', [App\Http\Controllers\Admin\ChatAgentController::class, 'takeSession'])->name('take-session');
    Route::post('/resolve-session', [App\Http\Controllers\Admin\ChatAgentController::class, 'resolveSession'])->name('resolve-session');
    Route::get('/waiting-sessions', [App\Http\Controllers\Admin\ChatAgentController::class, 'getWaitingSessions'])->name('waiting-sessions');
    Route::get('/my-sessions', [App\Http\Controllers\Admin\ChatAgentController::class, 'getMySessions'])->name('my-sessions');
    Route::get('/analytics', [App\Http\Controllers\Admin\ChatAgentController::class, 'getAnalytics'])->name('analytics');
});

require __DIR__.'/auth.php';
