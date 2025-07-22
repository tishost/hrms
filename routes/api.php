<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\DashboardController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-owner', [AuthController::class, 'registerOwner']);

// OTP Routes
Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('/resend-otp', [OtpController::class, 'resendOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

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
    Route::get('/tenants', [\App\Http\Controllers\Api\TenantController::class, 'index']);
    Route::post('/tenants', [\App\Http\Controllers\Api\TenantController::class, 'store']);
    Route::get('/tenants/{id}', [\App\Http\Controllers\Api\TenantController::class, 'show']);
    Route::put('/tenants/{id}', [\App\Http\Controllers\Api\TenantController::class, 'update']);
    Route::delete('/tenants/{id}', [\App\Http\Controllers\Api\TenantController::class, 'destroy']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
Route::post('/invoices/{invoiceId}/pay', [InvoiceController::class, 'pay']);

    // Owner Profile Update
    Route::post('/owner/profile/update', [OwnerController::class, 'updateProfile']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-transactions', [DashboardController::class, 'getRecentTransactions']);

    // Charges API routes
    Route::get('/charges', [\App\Http\Controllers\Api\ChargeController::class, 'index']);
    Route::post('/charges', [\App\Http\Controllers\Api\ChargeController::class, 'store']);
    Route::get('/charges/{id}', [\App\Http\Controllers\Api\ChargeController::class, 'show']);
    Route::put('/charges/{id}', [\App\Http\Controllers\Api\ChargeController::class, 'update']);
    Route::delete('/charges/{id}', [\App\Http\Controllers\Api\ChargeController::class, 'destroy']);
});


