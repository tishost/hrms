<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Helpers\CountryHelper;
use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitCharge;
use App\Helpers\SettingHelper; // Assuming you have a SettingHelper for settings


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

        $limit = setting('default_building_limit', 5);
        $current = auth()->user()->owner->properties()->count();

        if ($current >= $limit) {
            return back()->with('error', 'You reached the building creation limit.');
        }

        return view('owner.property.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'country' => 'required|string',
        ]);

        $property = new Property($request->only(['name', 'type', 'address', 'country']));
        $property->owner_id = $ownerId = auth()->user()->owner->id;
        $property->unit_limit = setting('default_unit_limit', 10);;
        $property->save();

        // ✅ Redirect to unit setup after save
        return redirect()->route('owner.units.setup', $property->id);
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
        $property->features = json_encode($request->input('facilities', []));
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



