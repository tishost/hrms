<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Api\CheckoutController;


// Tenant Registration Routes
Route::post('/tenant/request-otp', [TenantRegistrationController::class, 'requestOtp']);
Route::post('/tenant/verify-otp', [TenantRegistrationController::class, 'verifyOtp']);
Route::post('/tenant/register', [TenantRegistrationController::class, 'register']);

// Role-based login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-owner', [AuthController::class, 'registerOwner']);

// Password reset routes
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// OTP Routes
Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('/resend-otp', [OtpController::class, 'resendOtp']);
Route::get('/otp-settings', [OtpController::class, 'getOtpSettings']);

// OTP Settings API Route (Public)
Route::get('/otp-settings', [App\Http\Controllers\Admin\OtpSettingsController::class, 'getSettings']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
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

Route::middleware('auth:sanctum')->group(function () {
    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/{invoiceId}/pay', [InvoiceController::class, 'pay']);
    Route::get('/invoices/{invoiceId}/pdf', [InvoiceController::class, 'generatePdf']);

    // Owner Profile Update
    Route::post('/owner/profile/update', [OwnerController::class, 'updateProfile']);

    // Owner PDF Routes
    Route::get('/owner/invoices/{id}/pdf-file', [OwnerController::class, 'downloadInvoicePDF']);
    Route::get('/owner/profile', [OwnerController::class, 'profile']);

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
        Route::get('/tenant/invoices', [TenantDashboardController::class, 'getInvoices']);
        Route::get('/tenant/dashboard', [TenantDashboardController::class, 'getDashboard']);

        // Tenant Invoice PDF Routes
        Route::get('/tenant/invoices/{id}/pdf', [TenantController::class, 'getInvoicePDF']);
        Route::get('/tenant/invoices/{id}/pdf-file', [TenantController::class, 'downloadInvoicePDF']);

        // Test endpoint for debugging
        Route::get('/tenant/test', [TenantController::class, 'testEndpoint']);
    });

    // Universal User Profile (for user type detection)
    Route::get('/user/profile', [AuthController::class, 'getUserProfile']);
});


