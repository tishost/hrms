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

class TenantController extends Controller
{

    public function index(Request $request)
    {
        // Get filter parameter
        $statusFilter = $request->get('status', 'active');

        // Build query based on filter
        $query = Tenant::with(['unit.property']);

        if ($statusFilter === 'active') {
            $query->where('status', 'active');
        } elseif ($statusFilter === 'inactive') {
            $query->where('status', 'inactive');
        }
        // If 'all' is selected, no where clause is added

        $tenants = $query->paginate(10);
        return view('owner.tenants.index', compact('tenants', 'statusFilter'));
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
    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();
        $owner = auth()->user()->owner;
        $packageLimitService = new PackageLimitService();

        // Check package limits before creating tenant
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

        $tenant = Tenant::create([
            'first_name'          => $validated['first_name'],
            'last_name'           => $validated['last_name'],
            'gender'              => $validated['gender'],
            'mobile'              => $validated['mobile'],
            'alt_mobile'          => $validated['alt_mobile'],
            'email'               => $validated['email'],
            'nid_number'          => $validated['nid_number'],
            'address'             => $validated['address'],
            'country'             => $validated['country'],
            'occupation'          => $validated['occupation'],
            'company_name'        => $validated['company_name'] ?? null,
            'total_family_member' => $validated['total_family_member'],
            'is_driver'           => $validated['is_driver'],
            'driver_name'         => $validated['driver_name'] ?? null,
            'building_id'         => $validated['building_id'],
            'unit_id'             => $validated['unit_id'],
            'check_in_date'       => $validated['check_in_date'],
            'security_deposit'    => $validated['security_deposit'],
            'remarks'             => $validated['remarks'] ?? null,
            'owner_id'            => $owner->id,
            'status'              => 'active',
        ]);

        // Update unit status to rented
        if (isset($validated['unit_id'])) {
            $unit = Unit::find($validated['unit_id']);
            if ($unit) {
                $unit->update([
                    'tenant_id' => $tenant->id,
                    'status' => 'rented'
                ]);
            }
        }

        // Increment usage after successful creation
        $packageLimitService->incrementUsage($owner, 'tenants');

        return redirect()->route('owner.rents.create', $tenant->id)
            ->with('success', 'Tenant added successfully! Now assign rent.');
    }

    public function show(Tenant $tenant)
    {
        // Optionally, you can add owner check here
        return view('owner.tenants.show', compact('tenant'));
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
