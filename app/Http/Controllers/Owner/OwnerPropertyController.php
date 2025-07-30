<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Helpers\CountryHelper;
use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitCharge;
use App\Helpers\SettingHelper;
use App\Services\PackageLimitService;


class OwnerPropertyController extends Controller
{
    public function index()
    {
       $ownerId = auth()->user()->owner->id;
       $properties = Property::where('owner_id', $ownerId)->get();


        return view('owner.property.index', compact('properties'));
    }


    public function create()
    {
        $countries = CountryHelper::countryList();
        $owner = auth()->user()->owner;
        $packageLimitService = new PackageLimitService();

        // Check package limits
        if (!$packageLimitService->canPerformAction($owner, 'properties')) {
            $stats = $packageLimitService->getUsageStats($owner);
            $propertyStats = $stats['properties'] ?? null;

            $message = 'You have reached your property limit. ';
            if ($propertyStats) {
                $message .= "Current: {$propertyStats['current']}, Limit: {$propertyStats['max']}. ";
            }
            $message .= 'Please upgrade your plan to add more properties.';

            return back()->with('error', $message);
        }

        return view('owner.property.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string',
            'total_units' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $owner = auth()->user()->owner;
        $packageLimitService = new PackageLimitService();

        // Check package limits before creating property
        if (!$packageLimitService->canPerformAction($owner, 'properties')) {
            $stats = $packageLimitService->getUsageStats($owner);
            $propertyStats = $stats['properties'] ?? null;

            $message = 'You have reached your property limit. ';
            if ($propertyStats) {
                $message .= "Current: {$propertyStats['current']}, Limit: {$propertyStats['max']}. ";
            }
            $message .= 'Please upgrade your plan to add more properties.';

            return back()->with('error', $message);
        }

                $property = new Property($request->only([
            'name', 'address', 'city', 'state', 'zip_code', 'country',
            'total_units', 'description'
        ]));
        $property->property_type = $request->type ?? 'residential';
        $property->owner_id = $owner->id;
        $property->save();

        // Increment usage after successful creation
        $packageLimitService->incrementUsage($owner, 'properties');

        // ✅ Redirect to property list after save
        return redirect()->route('owner.property.index')
                        ->with('success', 'Property created successfully!');
    }
    public function edit(Property $property)
    {
        $countries = CountryHelper::countryList();
        return view('owner.property.edit', compact('property', 'countries'));
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'type'     => 'nullable|string',
            'address'  => 'nullable|string|max:255',
            'country'  => 'required|string',
        ]);

        // Update building
        $property->update($request->only(['name', 'type', 'address', 'country']));
        $property->save();

        // Update each unit
        foreach ($request->input('units', []) as $unitId => $data) {
            $unit = Unit::find($unitId);
            if ($unit && $unit->property_id == $property->id) {
                $unit->update([
                    'name' => $data['name'],
                    'rent' => $data['rent'],
                ]);

                $unit->charges()->delete();

                foreach ($data['charges'] ?? [] as $charge) {
                    UnitCharge::create([
                        'unit_id' => $unit->id,
                        'label'   => $charge['label'],
                        'amount'  => $charge['amount'],
                    ]);
                }
            }
        }

        return redirect()->route('owner.property.index')
                         ->with('success', 'Building and units updated successfully!');
    }
}



