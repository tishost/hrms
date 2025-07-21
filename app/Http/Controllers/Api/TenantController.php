<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;

class TenantController extends Controller
{
    // List all tenants for the authenticated owner
    public function index(Request $request)
    {
        $ownerId = $request->user()->owner->id;
        $tenants = Tenant::where('owner_id', $ownerId)
            ->with(['unit', 'property'])
            ->get();
        $tenantsTransformed = $tenants->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
                'gender' => $tenant->gender,
                'mobile' => $tenant->mobile,
                'alt_mobile' => $tenant->alt_mobile,
                'email' => $tenant->email,
                'nid_number' => $tenant->nid_number,
                'address' => $tenant->address,
                'country' => $tenant->country,
                'total_family_member' => $tenant->total_family_member,
                'occupation' => $tenant->occupation,
                'company_name' => $tenant->company_name,
                'is_driver' => $tenant->is_driver,
                'driver_name' => $tenant->driver_name,
                'unit_id' => $tenant->unit_id,
                'unit_name' => $tenant->unit ? $tenant->unit->name : null,
                'property_id' => $tenant->building_id,
                'property_name' => $tenant->property ? $tenant->property->name : null,
                'status' => $tenant->status,
                'check_in_date' => $tenant->check_in_date,
                'check_out_date' => $tenant->check_out_date,
            ];
        });
        return response()->json(['tenants' => $tenantsTransformed]);
    }

    // Show a single tenant
    public function show(Request $request, $id)
    {
        $ownerId = $request->user()->owner->id;
        $tenant = Tenant::where('id', $id)
            ->where('owner_id', $ownerId)
            ->with(['unit', 'property'])
            ->firstOrFail();
        return response()->json($tenant);
    }

    // Store a new tenant
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'total_family_member' => 'required|integer|min:1',
            'property_id' => 'required|exists:properties,id',
            'unit_id' => 'required|exists:units,id',
            'advance_amount' => 'required|numeric|min:0',
            'start_month' => 'required|date',
            'frequency' => 'required|string',
        ]);
        $ownerId = $request->user()->owner->id;
        $unit = Unit::where('id', $request->unit_id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->firstOrFail();
        $tenant = new Tenant();
        // Name split
        $fullName = $request->name;
        $nameParts = explode(' ', $fullName, 2);
        $tenant->first_name = $nameParts[0] ?? '';
        $tenant->last_name = $nameParts[1] ?? '';
        $tenant->gender = $request->gender;
        $tenant->mobile = $request->mobile;
        $tenant->alt_mobile = $request->alt_phone;
        $tenant->email = $request->email;
        $tenant->nid_number = $request->nid_number;
        $tenant->address = $request->street_address;
        $tenant->country = $request->country;
        $tenant->total_family_member = $request->total_family_member;
        $tenant->occupation = $request->occupation;
        $tenant->company_name = $request->company_name;
        $tenant->is_driver = $request->is_driver;
        $tenant->driver_name = $request->driver_name;
        $tenant->building_id = $request->property_id;
        $tenant->unit_id = $request->unit_id;
        $tenant->owner_id = $ownerId;
        $tenant->status = 'active';
        $tenant->check_in_date = $request->start_month;
        $tenant->security_deposit = $request->advance_amount;
        $tenant->save();
        return response()->json(['success' => true, 'tenant' => $tenant], 201);
    }
}
