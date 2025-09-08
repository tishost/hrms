<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\TenantRegistrationController;
use App\Http\Controllers\Api\TenantDashboardController;
use App\Http\Controllers\Api\SystemController;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\SubscriptionController as ApiSubscriptionController;
use App\Http\Controllers\Api\NotificationController;


// Tenant Registration Routes
Route::post('/tenant/register', [TenantRegistrationController::class, 'register']);

// Role-based login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-owner', [AuthController::class, 'registerOwner']);

// Smart registration routes
Route::post('/check-mobile-role', [AuthController::class, 'checkMobileRole']);
Route::post('/check-google-role', [AuthController::class, 'checkGoogleRole']);
Route::post('/check-tenant-password-status', [AuthController::class, 'checkTenantPasswordStatus']);
// Public geo endpoints
Route::get('/districts', function () {
    return response()->json(
        District::orderBy('name')->select(['id','name'])->get()
    );
});
Route::get('/districts/{id}/upazilas', function ($id) {
    $rows = DB::table('upazilas')
        ->where('district_id', $id)
        ->orderBy('name')
        ->select(['id','name'])
        ->get();
    return response()->json($rows);
});

// Password reset routes
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-password-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// OTP Routes
Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('/resend-otp', [OtpController::class, 'resendOtp']);
Route::get('/otp-settings', [OtpController::class, 'getOtpSettings']);

// Public system status/maintenance endpoint
Route::get('/system/status', [SystemController::class, 'status']);
Route::get('/system-settings', [SystemController::class, 'getSettings']);

// Analytics routes for mobile app
Route::post('/admin/analytics/receive-data', [App\Http\Controllers\Admin\AnalyticsController::class, 'receiveDeviceAnalytics']);
Route::get('/admin/analytics/summary', [App\Http\Controllers\Admin\AnalyticsController::class, 'getAnalyticsSummary']);
Route::post('/admin/analytics/device-stats', [App\Http\Controllers\Admin\AnalyticsController::class, 'getRealTimeDeviceStats']);
Route::post('/admin/analytics/device-trends', [App\Http\Controllers\Admin\AnalyticsController::class, 'getDeviceInstallationTrends']);

// Public subscription plans
Route::get('/subscription/plans', [ApiSubscriptionController::class, 'plans']);

// Public ads endpoints
Route::get('/ads/dashboard', [App\Http\Controllers\Api\AdsController::class, 'getDashboardAds']);
Route::post('/ads/{ad}/click', [App\Http\Controllers\Api\AdsController::class, 'recordClick']);
Route::get('/ads/stats', [App\Http\Controllers\Api\AdsController::class, 'getStats']);
Route::get('/ads/location', [App\Http\Controllers\Api\AdsController::class, 'getAdsByLocation']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/kill-session', [AuthController::class, 'killSession']);
    Route::get('/user', [AuthController::class, 'user']);

    // Admin Test Routes (No CSRF protection)
    Route::post('/admin/test-email', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'testEmail']);
    Route::post('/admin/test-csrf', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'testCsrf']);
    Route::post('/admin/test-validation', [App\Http\Controllers\Admin\NotificationSettingsController::class, 'testEmailValidation']);

    // Properties
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::post('/properties/{id}/archive', [PropertyController::class, 'archive']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    Route::get('/properties/stats', [PropertyController::class, 'stats']);

    // Units
    Route::get('/units', [\App\Http\Controllers\Api\UnitController::class, 'index']);
    Route::post('/units', [\App\Http\Controllers\Api\UnitController::class, 'store']);
    Route::get('/units/{id}', [\App\Http\Controllers\Api\UnitController::class, 'show']);
    Route::put('/units/{id}', [\App\Http\Controllers\Api\UnitController::class, 'update']);
    Route::delete('/units/{id}', [\App\Http\Controllers\Api\UnitController::class, 'destroy']);

    // Tenants
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/dashboard', [TenantController::class, 'dashboard']);
    Route::get('/tenants/{id}', [TenantController::class, 'show']);
    Route::get('/tenants/{id}/outstanding', [TenantController::class, 'getOutstandingAmount']);
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::put('/tenants/{id}', [TenantController::class, 'update']);
    Route::delete('/tenants/{id}', [TenantController::class, 'destroy']);

    // Checkouts
    Route::get('/checkouts', [CheckoutController::class, 'index']);
    Route::post('/checkouts', [CheckoutController::class, 'store']);
    Route::get('/checkouts/{id}', [CheckoutController::class, 'show']);
});

// Media proxy for public assets (profiles/...)
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*');

// File upload routes
Route::post('/common/upload', [App\Http\Controllers\Api\UploadController::class, 'store']);
Route::post('/common/delete-profile-pic', [App\Http\Controllers\Api\UploadController::class, 'deleteOldProfilePic']);
Route::post('/common/delete-nid-image', [App\Http\Controllers\Api\UploadController::class, 'deleteOldNidImage']);

Route::middleware('auth:sanctum')->group(function () {
    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    // Owner-specific alias for invoices (explicit endpoint for mobile app)
    Route::get('/owner/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/{invoiceId}/pay', [InvoiceController::class, 'pay']);
    Route::get('/invoices/{invoiceId}/pdf', [InvoiceController::class, 'generatePdf']);

    // Owner Profile Update
    Route::post('/owner/profile/update', [OwnerController::class, 'updateProfile']);

    // Owner PDF Routes
    Route::get('/owner/invoices/{id}/pdf-file', [OwnerController::class, 'downloadInvoicePDF']);
    Route::get('/owner/profile', [OwnerController::class, 'profile']);
    
    // Owner Subscription
    Route::get('/owner/subscription', [OwnerController::class, 'getSubscription']);
    Route::post('/subscription/purchase', [ApiSubscriptionController::class, 'purchase']);
    Route::get('/subscription/payment-methods', [ApiSubscriptionController::class, 'paymentMethods']);
    Route::get('/subscription/invoices', [ApiSubscriptionController::class, 'invoices']);
    Route::post('/subscription/checkout', [ApiSubscriptionController::class, 'checkout']);
    
    // Subscription Upgrade Routes
    Route::post('/subscription/upgrade', [ApiSubscriptionController::class, 'upgradePlan']);
    Route::post('/subscription/upgrade/complete/{invoiceId}', [ApiSubscriptionController::class, 'completeUpgrade']);
    Route::post('/subscription/upgrade/cancel/{upgradeRequestId}', [ApiSubscriptionController::class, 'cancelUpgrade']);
    Route::get('/subscription/upgrade/status', [ApiSubscriptionController::class, 'getUpgradeStatus']);

    // File Upload (common) - defined publicly above; avoid duplicate route here

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-transactions', [DashboardController::class, 'getRecentTransactions']);

    // Reports API routes
    Route::get('/reports/types', [ReportController::class, 'getReportTypes']);
    Route::post('/reports/financial', [ReportController::class, 'financialReport']);
    Route::get('/reports/occupancy', [ReportController::class, 'occupancyReport']);
    Route::get('/reports/tenant', [ReportController::class, 'tenantReport']);
    Route::post('/reports/transaction', [ReportController::class, 'transactionReport']);

    // Charges API routes
    Route::get('/charges', [\App\Http\Controllers\Api\ChargeController::class, 'index']);
    Route::post('/charges', [\App\Http\Controllers\Api\ChargeController::class, 'store']);
    Route::get('/charges/{id}', [\App\Http\Controllers\Api\ChargeController::class, 'show']);
    Route::put('/charges/{id}', [\App\Http\Controllers\Api\ChargeController::class, 'update']);
    Route::delete('/charges/{id}', [\App\Http\Controllers\Api\ChargeController::class, 'destroy']);

    // Tenant Dashboard Routes (Protected)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/tenant/profile', [TenantDashboardController::class, 'getProfile']);
        Route::put('/tenant/profile/update-personal-info', [TenantDashboardController::class, 'updatePersonalInfo']);
        Route::put('/tenant/profile/update-address', [TenantDashboardController::class, 'updateAddress']);
        Route::get('/tenant/invoices', [TenantDashboardController::class, 'getInvoices']);
Route::get('/tenant/dashboard', [TenantDashboardController::class, 'getDashboard']);
Route::get('/tenant/rent-agreement', [TenantController::class, 'getRentAgreement']);

        // Tenant Invoice PDF Routes
        Route::get('/tenant/invoices/{id}/pdf', [TenantController::class, 'getInvoicePDF']);
        Route::get('/tenant/invoices/{id}/pdf-file', [TenantController::class, 'downloadInvoicePDF']);

        // Test endpoint for debugging
        Route::get('/tenant/test', [TenantController::class, 'testEndpoint']);
    });

    // Universal User Profile (for user type detection)
    Route::get('/user/profile', [AuthController::class, 'getUserProfile']);

    // Notification Routes
    Route::prefix('notifications')->group(function () {
        // Mobile app: notification history and read-state
        Route::get('/history', [NotificationController::class, 'getNotificationHistory']);
        Route::post('/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/delete', [NotificationController::class, 'deleteNotification']);
        Route::get('/stats', [NotificationController::class, 'getNotificationStats']);

        // Mobile app: FCM Token management
        Route::post('/fcm-token', [NotificationController::class, 'updateFCMToken']);
        Route::get('/fcm-token', [NotificationController::class, 'getFCMToken']);
    });
});


