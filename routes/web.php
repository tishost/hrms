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
    Route::get('units', [OwnerUnitController::class, 'index'])->name('units.index');
    Route::get('units/{unit}/edit', [OwnerUnitController::class, 'edit'])->name('units.edit');
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
    Route::get('/tenants/{tenant}/assign-rent', [TenantRentController::class, 'create'])->name('rents.create');
    Route::post('/tenants/{tenant}/assign-rent', [TenantRentController::class, 'store'])->name('rents.store');
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
    Route::post('owners', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeOwner'])->name('owners.store');
    Route::delete('owners/{id}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'destroyOwner'])->name('owners.destroy');

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
                'roles' => $user->roles->pluck('name'),
                'is_super_admin' => $user->owner->is_super_admin ?? false
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
    Route::put('settings/notifications/email', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'updateEmailSettings'])->name('notifications.email.update');
    Route::put('settings/notifications/sms', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'updateSmsSettings'])->name('notifications.sms.update');
    Route::post('settings/notifications/email/test', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'testEmail'])->name('notifications.email.test');
    Route::post('settings/notifications/sms/test', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'testSms'])->name('notifications.sms.test');
    Route::post('settings/notifications/sms/reset', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'resetSmsCount'])->name('notifications.sms.reset');
    Route::get('settings/notifications/template', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'getTemplate'])->name('notifications.template.get');
    Route::post('settings/notifications/template', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'saveTemplate'])->name('notifications.template.save');
    Route::get('settings/notifications/log', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'viewLog'])->name('notifications.log.view');

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
