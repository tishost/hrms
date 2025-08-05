<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTenantRequest;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use App\Helpers\CountryHelper;
use App\Models\Owner;
use App\Services\PackageLimitService;
use Illuminate\Support\Facades\Auth;
use App\Models\OwnerSetting;

class TenantController extends Controller
{

    public function index(Request $request)
    {
        $ownerId = auth()->user()->owner->id;
        
        // Build query
        $query = Tenant::with(['unit.property'])
            ->whereHas('unit.property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nid_number', 'like', "%{$search}%")
                  ->orWhere('occupation', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('unit.property', function($propertyQuery) use ($search) {
                      $propertyQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('address', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        $statusFilter = $request->get('status', 'active');
        if ($statusFilter === 'active') {
            $query->where('status', 'active');
        } elseif ($statusFilter === 'inactive') {
            $query->where('status', 'inactive');
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by occupation
        if ($request->filled('occupation')) {
            $query->where('occupation', 'like', "%{$request->occupation}%");
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->whereHas('unit', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        // Filter by check-in date range
        if ($request->filled('check_in_from')) {
            $query->where('check_in_date', '>=', $request->check_in_from);
        }
        if ($request->filled('check_in_to')) {
            $query->where('check_in_date', '<=', $request->check_in_to);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tenants = $query->get();

        // Get properties for filter dropdown
        $properties = Property::where('owner_id', $ownerId)
                            ->orderBy('name')
                            ->get();

        return view('owner.tenants.index', compact('tenants', 'statusFilter', 'properties'));
    }

    /**
     * Export tenants to PDF
     */
    public function exportPdf(Request $request)
    {
        $ownerId = auth()->user()->owner->id;
        
        $query = Tenant::with(['unit.property'])
            ->whereHas('unit.property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nid_number', 'like', "%{$search}%")
                  ->orWhere('occupation', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('unit.property', function($propertyQuery) use ($search) {
                      $propertyQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('address', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'active');
            } elseif ($request->status === 'inactive') {
                $query->where('status', 'inactive');
            }
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('occupation')) {
            $query->where('occupation', 'like', "%{$request->occupation}%");
        }

        if ($request->filled('property_id')) {
            $query->whereHas('unit', function($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }

        if ($request->filled('check_in_from')) {
            $query->where('check_in_date', '>=', $request->check_in_from);
        }
        if ($request->filled('check_in_to')) {
            $query->where('check_in_date', '<=', $request->check_in_to);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tenants = $query->get();

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.tenants.export-pdf', compact('tenants'));
        
        return $pdf->download('tenants_' . date('Y-m-d') . '.pdf');
    }
    public function create()
    {
        $countries = CountryHelper::countryList();
        $owner = auth()->user()->owner;
        $packageLimitService = new PackageLimitService();

        // Check package limits
        if (!$packageLimitService->canPerformAction($owner, 'tenants')) {
            $stats = $packageLimitService->getUsageStats($owner);
            $tenantStats = $stats['tenants'] ?? null;

            $message = 'You have reached your tenant limit. ';
            if ($tenantStats) {
                $message .= "Current: {$tenantStats['current']}, Limit: {$tenantStats['max']}. ";
            }
            $message .= 'Please upgrade your plan to add more tenants.';

            return back()->with('error', $message);
        }

        // Owner-specific building list (optional filter by auth user)
        $buildings = Property::where('owner_id', $owner->id)->get();

        return view('owner.tenants.create', compact('buildings', 'countries'));
    }
    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:tenants,email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'permanent_address' => 'required|string',
            'present_address' => 'required|string',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric',
            'total_family_member' => 'nullable|integer|min:1',
            'is_driver' => 'boolean',
            'driver_license' => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:50',
            'unit_id' => 'required|exists:units,id',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'lease_start_date' => 'required|date',
            'lease_end_date' => 'required|date|after:lease_start_date',
            'rent_due_date' => 'required|integer|min:1|max:31',
            'payment_method' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            $owner = Auth::user()->owner;
            
            $tenant = Tenant::create([
                'owner_id' => $owner->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'national_id' => $request->national_id,
                'passport_number' => $request->passport_number,
                'emergency_contact' => $request->emergency_contact,
                'emergency_contact_name' => $request->emergency_contact_name,
                'permanent_address' => $request->permanent_address,
                'present_address' => $request->present_address,
                'occupation' => $request->occupation,
                'employer' => $request->employer,
                'monthly_income' => $request->monthly_income,
                'total_family_member' => $request->total_family_member,
                'is_driver' => $request->has('is_driver'),
                'driver_license' => $request->driver_license,
                'vehicle_number' => $request->vehicle_number,
                'unit_id' => $request->unit_id,
                'rent_amount' => $request->rent_amount,
                'security_deposit' => $request->security_deposit,
                'lease_start_date' => $request->lease_start_date,
                'lease_end_date' => $request->lease_end_date,
                'rent_due_date' => $request->rent_due_date,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'notes' => $request->notes,
            ]);

            // Send welcome notification with language preference
            if (OwnerSetting::isNotificationEnabled($owner->id, 'new_tenant')) {
                $unit = $tenant->unit;
                $property = $unit->property;
                
                $variables = [
                    'tenant_name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'unit_name' => $unit->name,
                    'property_name' => $property->name,
                    'owner_phone' => $owner->phone,
                    'owner_email' => $owner->email,
                ];

                // Send SMS with language preference
                if ($tenant->phone) {
                    \App\Helpers\NotificationHelper::sendNotificationWithLanguage(
                        $owner->id,
                        'sms',
                        $tenant->phone,
                        'tenant_welcome_sms_template',
                        $variables
                    );
                }

                // Send email with language preference
                if ($tenant->email) {
                    \App\Helpers\NotificationHelper::sendNotificationWithLanguage(
                        $owner->id,
                        'email',
                        $tenant->email,
                        'tenant_welcome_email_template',
                        $variables
                    );
                }
            }

            return redirect()->route('owner.tenants.index')
                ->with('success', 'Tenant added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error adding tenant: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Tenant $tenant)
    {
        // Get financial data
        $rentPayments = \App\Models\RentPayment::where('tenant_id', $tenant->id)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $invoices = \App\Models\Invoice::where('tenant_id', $tenant->id)
            ->orderBy('issue_date', 'desc')
            ->get();
        
        // Calculate financial summary
        $totalPaid = $rentPayments->where('status', 'paid')->sum('amount');
        $totalDue = $invoices->where('status', 'unpaid')->sum('amount');
        $totalInvoiced = $invoices->sum('amount');
        $advanceBalance = $totalPaid - $totalInvoiced;
        
        // Get next due date (earliest unpaid invoice due date)
        $nextDueDate = $invoices->where('status', 'unpaid')
            ->where('due_date', '>=', now())
            ->sortBy('due_date')
            ->first();
        
        // Get recent payments (last 5)
        $recentPayments = $rentPayments->take(5);
        
        // Get recent invoices (last 5)
        $recentInvoices = $invoices->take(5);
        
        return view('owner.tenants.show', compact(
            'tenant', 
            'rentPayments', 
            'invoices', 
            'totalPaid', 
            'totalDue', 
            'totalInvoiced', 
            'advanceBalance', 
            'nextDueDate',
            'recentPayments',
            'recentInvoices'
        ));
    }

    public function edit(Tenant $tenant)
    {
        // Check if tenant belongs to the current owner
        $ownerId = auth()->user()->owner->id;
        if ($tenant->owner_id !== $ownerId) {
            return redirect()->route('owner.tenants.index')->with('error', 'Unauthorized access.');
        }

        $countries = CountryHelper::countryList();

        return view('owner.tenants.edit', compact('tenant', 'countries'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        // Check if tenant belongs to the current owner
        $ownerId = auth()->user()->owner->id;
        if ($tenant->owner_id !== $ownerId) {
            return redirect()->route('owner.tenants.index')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'mobile' => 'required|string|max:20',
            'alt_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nid_number' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'country' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'total_family_member' => 'required|integer|min:1',
            'is_driver' => 'boolean',
            'driver_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ]);

        $tenant->update($request->all());

        return redirect()->route('owner.tenants.show', $tenant->id)
            ->with('success', 'Tenant updated successfully!');
    }

    public function getUnitsByBuilding($buildingId)
    {
            $ownerId = auth()->user()->owner->id;
            $building = Property::where('id', $buildingId)
                ->where('owner_id', $ownerId)
                ->firstOrFail();

            $units = Unit::where('property_id', $building->id)
                ->whereDoesntHave('tenant')
                ->get(['id', 'name', 'rent']);

            $unitData = [];
            foreach ($units as $unit) {
                $unitData[$unit->id] = $unit->name . ' (Rent: ৳' . number_format($unit->rent) . ')';
            }

            return response()->json($unitData);
    }

}
