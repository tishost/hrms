<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use Illuminate\Http\Request;

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
}
