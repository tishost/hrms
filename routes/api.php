<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\OwnerController;



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

    // Property Routes
    Route::get('/properties', [PropertyController::class, 'index']);
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::get('/properties/{id}', [PropertyController::class, 'show']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    Route::get('/properties/stats', [PropertyController::class, 'stats']);

    // Owner Profile Update
    Route::post('/owner/profile/update', [OwnerController::class, 'updateProfile']);

    // Tenant API routes
    Route::get('/tenants', [\App\Http\Controllers\Api\TenantController::class, 'index']);
    Route::get('/tenants/{id}', [\App\Http\Controllers\Api\TenantController::class, 'show']);
    Route::post('/tenants', [\App\Http\Controllers\Api\TenantController::class, 'store']);

    // Unit API routes
    Route::get('/units', [\App\Http\Controllers\Api\UnitController::class, 'index']);
    Route::get('/units/{id}', [\App\Http\Controllers\Api\UnitController::class, 'show']);
    Route::post('/units', [\App\Http\Controllers\Api\UnitController::class, 'store']);
    Route::put('/units/{id}', [\App\Http\Controllers\Api\UnitController::class, 'update']);
    Route::delete('/units/{id}', [\App\Http\Controllers\Api\UnitController::class, 'destroy']);

    // Charges API routes
    Route::get('/charges', [\App\Http\Controllers\Api\ChargeController::class, 'index']);
});


