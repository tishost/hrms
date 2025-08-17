<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantDashboardController extends Controller
{
    // Get tenant profile
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasRole('tenant')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $tenant = Tenant::where('id', $user->tenant_id)
                ->with(['property', 'unit'])
                ->first();

            if (!$tenant) {
                return response()->json(['error' => 'Tenant not found'], 404);
            }

            return response()->json([
                'success' => true,
                'tenant' => $tenant,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant profile error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load profile'], 500);
        }
    }

    // Get tenant invoices
    public function getInvoices(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasRole('tenant')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $invoices = Invoice::where('tenant_id', $user->tenant_id)
                ->orderBy('created_at', 'desc')
                ->select(['id', 'invoice_number', 'amount', 'status', 'created_at', 'issue_date', 'due_date'])
                ->get();

            // Log for debugging
            \Log::info('Tenant invoices query', [
                'tenant_id' => $user->tenant_id,
                'user_id' => $user->id,
                'invoices_count' => $invoices->count(),
                'invoices' => $invoices->toArray(),
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method()
            ]);

            // Additional debug for status field
            foreach($invoices as $invoice) {
                \Log::info("Invoice {$invoice->id} status: {$invoice->status}");
            }

            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant invoices error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load invoices'], 500);
        }
    }

    // Get tenant dashboard summary
    public function getDashboard(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasRole('tenant')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $tenant = Tenant::where('id', $user->tenant_id)
                ->with(['property', 'unit'])
                ->first();

            if (!$tenant) {
                return response()->json(['error' => 'Tenant not found'], 404);
            }

            // Get recent invoices
            $recentInvoices = Invoice::where('tenant_id', $user->tenant_id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->select(['id', 'invoice_number', 'amount', 'status', 'created_at', 'issue_date', 'due_date'])
                ->get();

            // Get payment statistics
            $totalInvoices = Invoice::where('tenant_id', $user->tenant_id)->count();
            $paidInvoices = Invoice::where('tenant_id', $user->tenant_id)
                ->where('status', 'paid')
                ->count();
            $pendingInvoices = Invoice::where('tenant_id', $user->tenant_id)
                ->where('status', 'unpaid')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'tenant' => $tenant,
                    'summary' => [
                        'total_invoices' => $totalInvoices,
                        'paid_invoices' => $paidInvoices,
                        'pending_invoices' => $pendingInvoices,
                    ],
                    'recent_invoices' => $recentInvoices,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant dashboard error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load dashboard'], 500);
        }
    }

    // Update tenant personal info
    public function updatePersonalInfo(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->hasRole('tenant')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validator = \Validator::make($request->all(), [
                'first_name' => 'required|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'occupation' => 'nullable|in:service,business,student,other',
                'company_name' => 'nullable|string|max:150',
                'business_name' => 'nullable|string|max:150',
                'university' => 'nullable|string|max:150',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $tenant = Tenant::where('id', $user->tenant_id)->first();
            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Tenant not found'], 404);
            }

            $tenant->first_name = $request->input('first_name');
            $tenant->last_name = $request->input('last_name');
            if ($request->filled('email')) {
                $tenant->email = $request->input('email');
            }
            if ($request->filled('phone')) {
                // tenant table uses 'mobile' for phone
                $tenant->mobile = $request->input('phone');
            }
            if ($request->filled('gender')) {
                $tenant->gender = $request->input('gender');
            }

            // Occupation mapping
            if ($request->filled('occupation')) {
                $tenant->occupation = $request->input('occupation');
            }
            if ($request->filled('company_name')) {
                $tenant->company_name = $request->input('company_name');
            }
            if ($request->filled('business_name')) {
                $tenant->business_name = $request->input('business_name');
            }
            if ($request->filled('university')) {
                $tenant->university = $request->input('university');
            }

            $tenant->save();

            return response()->json([
                'success' => true,
                'message' => 'Personal information updated successfully',
                'tenant' => $tenant,
            ]);
        } catch (\Exception $e) {
            \Log::error('Tenant personal info update error', [
                'user_id' => optional($request->user())->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update personal information',
            ], 500);
        }
    }
}
