<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\Owner;
use App\Models\PaymentMethod;
use App\Services\BkashTokenizedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingController extends Controller
{
    public function __construct()
    {
        // Laravel 11 doesn't support middleware() in controllers
        // Middleware is handled in routes
    }

    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            abort(403, 'Access denied. Authentication required.');
        }

        $user = auth()->user();

        // Check if user has super_admin role
        if ($user->hasRole('super_admin')) {
            return;
        }

        // Check if user is super admin through owner relationship
        if ($user->owner && $user->owner->is_super_admin) {
            return;
        }

        abort(403, 'Access denied. Super admin privileges required.');
    }

    public function debug()
    {
        try {
            $user = auth()->user();
            return response()->json([
                'success' => true,
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? null,
                'has_super_admin_role' => $user ? $user->hasRole('super_admin') : false,
                'owner_is_super_admin' => $user && $user->owner ? $user->owner->is_super_admin : false,
                'payment_methods_count' => PaymentMethod::count(),
                'system_settings_count' => SystemSetting::count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();

        try {
            $settings = SystemSetting::pluck('value', 'key');
            $paymentMethods = PaymentMethod::all();
            return view('admin.settings.index', compact('settings', 'paymentMethods'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading settings: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'default_building_limit' => 'required|integer|min:1',
        ]);

        foreach ($request->only(['default_building_limit']) as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    public function paymentGateway()
    {
        $this->checkSuperAdmin();
        $paymentMethods = PaymentMethod::all();
        return view('admin.settings.payment_gateway', compact('paymentMethods'));
    }

    public function updatePaymentGateway(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'payment_methods' => 'required|array',
            'payment_methods.*.id' => 'required|exists:payment_methods,id',
            'payment_methods.*.is_active' => 'boolean',
            'payment_methods.*.transaction_fee' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->payment_methods as $method) {
            $paymentMethod = PaymentMethod::find($method['id']);
            if ($paymentMethod) {
                $paymentMethod->update([
                    'is_active' => isset($method['is_active']),
                    'transaction_fee' => $method['transaction_fee'],
                    'settings' => array_merge($paymentMethod->settings ?? [], [
                        'merchant_id' => $method['merchant_id'] ?? null,
                        'api_key' => $method['api_key'] ?? null,
                        'api_secret' => $method['api_secret'] ?? null,
                        'gateway_url' => $method['gateway_url'] ?? null,
                        'is_sandbox' => isset($method['is_sandbox']),
                    ])
                ]);
            }
        }

        return back()->with('success', 'Payment gateway settings updated successfully!');
    }

        public function updateBkashSettings(Request $request)
    {
        try {
            // Check if session is valid
            if (!auth()->check()) {
                return redirect()->route('admin.login')->with('error', 'Session expired. Please login again.');
            }

            // Refresh session to prevent timeout
            session()->regenerate();

            $this->checkSuperAdmin();

            // Log the request for debugging
            \Log::info('bKash settings update request', [
                'has_csrf_token' => $request->hasHeader('X-CSRF-TOKEN'),
                'session_id' => $request->session()->getId(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            $request->validate([
                'bkash_merchant_id' => 'required|string|min:5',
                'bkash_merchant_password' => 'required|string|min:5',
                'bkash_api_key' => 'required|string|min:10',
                'bkash_api_secret' => 'required|string|min:10',
                'bkash_gateway_url' => 'required|url',
                'bkash_display_name' => 'required|string|min:2',
                'bkash_sandbox' => 'boolean',
                'bkash_transaction_fee' => 'required|numeric|min:0|max:100',
            ]);

            // Additional validation using helper
            $validation = \App\Helpers\BkashHelper::validateCredentials(
                $request->bkash_merchant_id,
                $request->bkash_merchant_password,
                $request->bkash_api_key,
                $request->bkash_api_secret
            );

            if (!$validation['valid']) {
                return back()->withErrors($validation['errors'])->withInput();
            }

            $bkash = PaymentMethod::where('code', 'bkash')->first();
            if ($bkash) {
                $bkash->update([
                    'name' => $request->bkash_display_name,
                    'is_active' => $request->bkash_is_active ?? false,
                    'transaction_fee' => $request->bkash_transaction_fee,
                    'settings' => [
                        'merchant_id' => $request->bkash_merchant_id,
                        'merchant_password' => $request->bkash_merchant_password,
                        'api_key' => $request->bkash_api_key,
                        'api_secret' => $request->bkash_api_secret,
                        'gateway_url' => $request->bkash_gateway_url,
                        'sandbox_mode' => $request->has('bkash_sandbox'),
                    ]
                ]);
            }

            return back()->with('success', 'bKash settings updated successfully!');

        } catch (\Exception $e) {
            \Log::error('bKash settings update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

        public function testBkashConnection()
    {
        $this->checkSuperAdmin();
        try {
            $bkashService = new BkashTokenizedService();
            $result = $bkashService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'bKash TokenizedCheckout connection successful!',
                    'details' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'bKash TokenizedCheckout connection failed: ' . $result['message'],
                    'details' => $result
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'TokenizedCheckout connection test failed: ' . $e->getMessage()
            ]);
        }
    }

        public function getBkashConfigurationStatus()
    {
        $this->checkSuperAdmin();
        try {
            $bkashService = new BkashTokenizedService();
            $status = $bkashService->getConfigurationStatus();

            return response()->json([
                'success' => true,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get TokenizedCheckout configuration status: ' . $e->getMessage()
            ]);
        }
    }

    public function testBkashPaymentCreation()
    {
        $this->checkSuperAdmin();
        try {
            $bkashService = new BkashTokenizedService();

            // Test payment creation with minimal amount
            $testAmount = 10.00;
            $testInvoiceId = 'TEST_INV_' . time();
            $testPaymentId = 'TEST_PAY_' . time();
            $testDescription = 'Test Payment for HRMS';

            $result = $bkashService->createTokenizedCheckout($testAmount, $testInvoiceId, $testPaymentId, $testDescription);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test payment creation successful!',
                    'paymentID' => $result['paymentID'],
                    'bkashURL' => $result['bkashURL'],
                    'demo_mode' => $result['demo_mode'] ?? false
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Test payment creation failed: ' . $result['error'],
                    'suggestion' => $result['suggestion'] ?? '',
                    'details' => $result['details'] ?? []
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test payment creation error: ' . $e->getMessage()
            ]);
        }
    }
}
