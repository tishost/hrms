<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

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

            // Resolve tenant id robustly
            $tenantId = $user->tenant_id;
            if (empty($tenantId)) {
                $tenantId = Tenant::where('user_id', $user->id)->value('id');
            }

            if (empty($tenantId)) {
                return response()->json([
                    'success' => true,
                    'invoices' => [],
                    'message' => 'No tenant linked to this user'
                ]);
            }

            $invoices = Invoice::where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->select(['id', 'invoice_number', 'amount', 'status', 'created_at', 'issue_date', 'due_date'])
                ->get();

            // Log for debugging
            \Log::info('Tenant invoices query', [
                'tenant_id' => $tenantId,
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
                'college_university' => 'nullable|string|max:150',
                'profile_pic' => 'nullable|string|max:255',
                // Family info
                'total_family_member' => 'nullable|integer|min:0',
                'family_types' => 'nullable|string|max:255',
                'child_qty' => 'nullable|integer|min:0',
                'spouse_name' => 'nullable|string|max:150',
                'father_name' => 'nullable|string|max:150',
                'mother_name' => 'nullable|string|max:150',
                'sister_name' => 'nullable|string|max:150',
                'brother_name' => 'nullable|string|max:150',
            ]);

            // Conditional required fields based on occupation
            $validator->sometimes('company_name', 'required|string|max:150', function ($input) {
                return isset($input->occupation) && strtolower($input->occupation) === 'service';
            });
            $validator->sometimes('business_name', 'required|string|max:150', function ($input) {
                return isset($input->occupation) && strtolower($input->occupation) === 'business';
            });
            $validator->sometimes('university', 'required_without:college_university|string|max:150', function ($input) {
                return isset($input->occupation) && strtolower($input->occupation) === 'student';
            });
            $validator->sometimes('college_university', 'required_without:university|string|max:150', function ($input) {
                return isset($input->occupation) && strtolower($input->occupation) === 'student';
            });

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
                // Keep user email in sync with tenant email
                $user->email = $request->input('email');
                $user->save();
            }
            if ($request->filled('phone')) {
                // tenant table uses 'mobile' for phone
                $tenant->mobile = $request->input('phone');
            }
            if ($request->filled('gender')) {
                $tenant->gender = $request->input('gender');
            }
            if ($request->filled('profile_pic') && Schema::hasColumn('tenants', 'profile_pic')) {
                $tenant->profile_pic = $request->input('profile_pic');
            }

            // Occupation mapping
            if ($request->filled('occupation')) {
                $tenant->occupation = strtolower($request->input('occupation'));
            }
            // Assign only if columns exist to avoid SQL errors
            if ($request->filled('company_name') && Schema::hasColumn('tenants', 'company_name')) {
                $tenant->company_name = $request->input('company_name');
            }
            if ($request->filled('business_name') && Schema::hasColumn('tenants', 'business_name')) {
                $tenant->business_name = $request->input('business_name');
            }
            // Map student institution field to correct DB column
            $institutionName = $request->input('college_university', $request->input('university'));
            if (!empty($institutionName)) {
                if (Schema::hasColumn('tenants', 'college_university')) {
                    $tenant->college_university = $institutionName;
                } elseif (Schema::hasColumn('tenants', 'university')) {
                    $tenant->university = $institutionName;
                }
            }

            // Family information mapping (only if columns exist)
            if ($request->filled('total_family_member') && Schema::hasColumn('tenants', 'total_family_member')) {
                $tenant->total_family_member = (int) $request->input('total_family_member');
            }
            if ($request->filled('family_types') && Schema::hasColumn('tenants', 'family_types')) {
                $tenant->family_types = $request->input('family_types');
            }
            if ($request->filled('child_qty')) {
                if (Schema::hasColumn('tenants', 'child_qty')) {
                    $tenant->child_qty = (int) $request->input('child_qty');
                } elseif (Schema::hasColumn('tenants', 'children')) {
                    $tenant->children = (int) $request->input('child_qty');
                } elseif (Schema::hasColumn('tenants', 'num_children')) {
                    $tenant->num_children = (int) $request->input('child_qty');
                }
            }
            if ($request->filled('spouse_name') && Schema::hasColumn('tenants', 'spouse_name')) {
                $tenant->spouse_name = $request->input('spouse_name');
            }
            if ($request->filled('father_name')) {
                if (Schema::hasColumn('tenants', 'father_name')) {
                    $tenant->father_name = $request->input('father_name');
                } elseif (Schema::hasColumn('tenants', 'fathers_name')) {
                    $tenant->fathers_name = $request->input('father_name');
                } elseif (Schema::hasColumn('tenants', 'guardian_name')) {
                    $tenant->guardian_name = $request->input('father_name');
                }
            }
            if ($request->filled('mother_name')) {
                if (Schema::hasColumn('tenants', 'mother_name')) {
                    $tenant->mother_name = $request->input('mother_name');
                } elseif (Schema::hasColumn('tenants', 'mothers_name')) {
                    $tenant->mothers_name = $request->input('mother_name');
                }
            }
            if ($request->filled('sister_name') && Schema::hasColumn('tenants', 'sister_name')) {
                $tenant->sister_name = $request->input('sister_name');
            }
            if ($request->filled('brother_name') && Schema::hasColumn('tenants', 'brother_name')) {
                $tenant->brother_name = $request->input('brother_name');
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

    // Update address
    public function updateAddress(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->hasRole('tenant')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validator = \Validator::make($request->all(), [
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:191', // upazila (frontend sends as city)
                'district' => 'nullable|string|max:191',
                'zip' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tenant = Tenant::where('id', $user->tenant_id)->first();
            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Tenant not found'], 404);
            }

            if ($request->filled('address')) {
                $tenant->address = $request->input('address');
            }
            // Map city -> upazila
            if ($request->filled('city')) {
                if (Schema::hasColumn('tenants', 'upazila')) {
                    $tenant->upazila = $request->input('city');
                } else {
                    $tenant->city = $request->input('city');
                }
            }
            if ($request->filled('district')) {
                if (Schema::hasColumn('tenants', 'district')) {
                    $tenant->district = $request->input('district');
                } else {
                    $tenant->state = $request->input('district');
                }
            }
            if ($request->filled('zip')) {
                $tenant->zip = $request->input('zip');
            }
            if ($request->filled('country')) {
                if (Schema::hasColumn('tenants', 'country')) {
                    $tenant->country = $request->input('country');
                }
            }

            $tenant->save();

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'tenant' => $tenant,
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant address update error', [
                'user_id' => optional($request->user())->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address',
            ], 500);
        }
    }
}
