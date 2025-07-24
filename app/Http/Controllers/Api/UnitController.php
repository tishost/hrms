<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Property;

class UnitController extends Controller
{
    // List all units for the authenticated owner
    public function index(Request $request)
    {
        $ownerId = $request->user()->owner->id;
        $units = Unit::whereHas('property', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
        ->with(['property', 'charges', 'tenant'])
        ->get();
        $unitsTransformed = $units->map(function($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'rent' => $unit->rent,
                'property_id' => $unit->property_id,
                'property_name' => $unit->property ? $unit->property->name : null,
                'charges' => $unit->charges->map(function($charge) {
                    return [
                        'id' => $charge->id,
                        'label' => $charge->label,
                        'amount' => $charge->amount,
                    ];
                })->toArray(),
                'status' => $unit->status,
                'tenant_id' => $unit->tenant_id,
                'tenant_name' => $unit->tenant ? trim(($unit->tenant->first_name ?? '') . ' ' . ($unit->tenant->last_name ?? '')) : null,
                'tenant_mobile' => $unit->tenant ? $unit->tenant->mobile : null,
            ];
        });
        return response()->json(['units' => $unitsTransformed]);
    }

    // Show a single unit
    public function show(Request $request, $id)
    {
        $ownerId = $request->user()->owner->id;
        $unit = Unit::where('id', $id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->with('charges')
            ->firstOrFail();

        $unitData = [
            'id' => $unit->id,
            'name' => $unit->name,
            'rent' => $unit->rent,
            'property_id' => $unit->property_id,
            'property_name' => $unit->property ? $unit->property->name : null,
            'charges' => $unit->charges->map(function($charge) {
                return [
                    'id' => $charge->id,
                    'label' => $charge->label,
                    'amount' => $charge->amount,
                ];
            })->toArray(),
            'status' => $unit->status,
        ];

        return response()->json(['unit' => $unitData]);
    }

    // Store a new unit
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:100',
            'rent' => 'required|numeric|min:0',
            'charges' => 'nullable|array',
        ]);
        $ownerId = $request->user()->owner->id;
        $property = Property::where('id', $request->property_id)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
        $unit = Unit::create([
            'property_id' => $property->id,
            'name' => $request->name,
            'rent' => $request->rent,
        ]);
        if ($request->charges) {
            foreach ($request->charges as $charge) {
                $unit->charges()->create([
                    'label' => $charge['label'],
                    'amount' => $charge['amount'],
                ]);
            }
        }
        return response()->json(['success' => true, 'unit' => $unit], 201);
    }

    // Update a unit
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'rent' => 'required|numeric|min:0',
            'charges' => 'nullable|array',
        ]);
        $ownerId = $request->user()->owner->id;
        $unit = Unit::where('id', $id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->firstOrFail();
        $unit->update([
            'name' => $request->name,
            'rent' => $request->rent,
        ]);
        $unit->charges()->delete();
        if ($request->charges) {
            foreach ($request->charges as $charge) {
                $unit->charges()->create([
                    'label' => $charge['label'],
                    'amount' => $charge['amount'],
                ]);
            }
        }
        return response()->json(['success' => true, 'unit' => $unit]);
    }

    // Delete a unit
    public function destroy(Request $request, $id)
    {
        $ownerId = $request->user()->owner->id;
        $unit = Unit::where('id', $id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->firstOrFail();
        $unit->delete();
        return response()->json(['success' => true]);
    }
}
