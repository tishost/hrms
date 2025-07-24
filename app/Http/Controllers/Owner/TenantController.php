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

class TenantController extends Controller
{

    public function index(Request $request)
    {
        // Show all tenants, regardless of unit/property
        $tenants = Tenant::with(['unit.property'])->paginate(10);
        return view('owner.tenants.index', compact('tenants'));
    }
    public function create()
    {
        $countries = CountryHelper::countryList();
        // Owner-specific building list (optional filter by auth user)
       $ownerId = auth()->user()->owner->id;
       $buildings = Property::where('owner_id', $ownerId)->get();


        return view('owner.tenants.create', compact('buildings', 'countries'));
    }
    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();
        $ownerId = auth()->user()->owner->id;

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
            'owner_id'            => $ownerId,
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
                ->pluck('name', 'id');

            return response()->json($units);
    }

}
