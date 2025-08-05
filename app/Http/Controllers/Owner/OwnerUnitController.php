<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitCharge;
use App\Helpers\SettingHelper; // Assuming you have a SettingHelper for settings
use App\Models\TempData;
use App\Services\PackageLimitService;


class OwnerUnitController extends Controller
{
    /**
     * Show all units for the logged-in owner (across all properties)
     */
    public function index(Request $request)
    {
        $ownerId = auth()->user()->owner->id;
        $query = \App\Models\Unit::whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->with(['property', 'charges', 'tenant']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('property', function($propertyQuery) use ($search) {
                      $propertyQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('address', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tenant', function($tenantQuery) use ($search) {
                      $tenantQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'rented') {
                $query->whereHas('tenant');
            } elseif ($request->status === 'free') {
                $query->whereDoesntHave('tenant');
            }
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by rent range
        if ($request->filled('rent_min')) {
            $query->where('rent', '>=', $request->rent_min);
        }
        if ($request->filled('rent_max')) {
            $query->where('rent', '<=', $request->rent_max);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $units = $query->get();

        // Get properties for filter dropdown
        $properties = \App\Models\Property::where('owner_id', $ownerId)
                                        ->orderBy('name')
                                        ->get();

        return view('owner.units.index', compact('units', 'properties'));
    }

    /**
     * Export units to PDF
     */
    public function exportPdf(Request $request)
    {
        $ownerId = auth()->user()->owner->id;
        $query = \App\Models\Unit::whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->with(['property', 'charges', 'tenant']);

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('property', function($propertyQuery) use ($search) {
                      $propertyQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('address', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tenant', function($tenantQuery) use ($search) {
                      $tenantQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'rented') {
                $query->whereHas('tenant');
            } elseif ($request->status === 'free') {
                $query->whereDoesntHave('tenant');
            }
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('rent_min')) {
            $query->where('rent', '>=', $request->rent_min);
        }
        if ($request->filled('rent_max')) {
            $query->where('rent', '<=', $request->rent_max);
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $units = $query->get();

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.units.export-pdf', compact('units'));
        
        return $pdf->download('units_' . date('Y-m-d') . '.pdf');
    }

    // Step 1: Show unit config form
    public function setup(Property $property)
    {
        // Remove old temp data for this user/property
        // TempData::where('user_id', auth()->id())
        //     ->where('key', 'unit_draft')
        //     ->where('related_id', $property->id)
        //     ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
        //     ->delete();
        // Retrieve temp data if exists
        $draft = TempData::where('user_id', auth()->id())
            ->where('key', 'unit_draft')
            ->where('related_id', $property->id)
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->first();
        $generated_units = $draft ? $draft->data : null;

        // Load predefined charges
        $predefined_charges = \App\Models\Charge::all();

        return view('owner.units.setup', compact('property', 'generated_units', 'predefined_charges'));
    }



    public function getFees($unitId)
    {
        $charges = UnitCharge::where('unit_id', $unitId)
            ->pluck('amount', 'label'); // returns [ "Gas Bill" => 100.00, "ET" => 200.00, ... ]
         // Fetch all dynamic charges for the unit


    // Add the unit’s base fare using correct column name ("rent")
        $charges['Base Fare'] = Unit::find($unitId)?->rent ?? 0;


        return response()->json($charges);
    }

    // Step 2: Generate unit drafts based on floor/unit input
    public function generate(Request $request, Property $property)
    {
        \Log::info('Generate called', $request->all());
        \Log::info('User ID: ' . auth()->id());
        $request->validate([
            'total_floors' => 'required|integer|min:1',
            'total_units' => 'required|integer|min:1',
        ]);

        $owner = auth()->user()->owner;
        $packageLimitService = new PackageLimitService();

        // Check package limits for units
        if (!$packageLimitService->canPerformAction($owner, 'units', $request->total_units)) {
            $stats = $packageLimitService->getUsageStats($owner);
            $unitStats = $stats['units'] ?? null;

            $message = 'You have reached your unit limit. ';
            if ($unitStats) {
                $message .= "Current: {$unitStats['current']}, Limit: {$unitStats['max']}. ";
            }
            $message .= 'Please upgrade your plan to add more units.';

            return back()->with('error', $message);
        }
        $property->save();
        $units = [];
        for ($i = 1; $i <= $request->total_units; $i++) {
            $unitName = 'Unit-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $unit = [
                'id' => $i,
                'name' => $unitName,
                'rent' => 0, // Default rent value
            ];
            $units[] = $unit;
        }
        \Log::info('Units array:', $units);
        // Store unit list in temp_data
        $result = TempData::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'key' => 'unit_draft',
                'related_id' => $property->id,
            ],
            [
                'data' => $units,
                'expires_at' => now()->addMinutes(30),
            ]
        );
        \Log::info('TempData save result:', [$result]);
        return redirect()->route('owner.units.setup', $property->id);
    }

    // Step 3: Save rent & utility charges for each unit
    public function saveFees(Request $request, Property $property)
    {
        $owner = auth()->user()->owner;
        $packageLimitService = new PackageLimitService();
        $units = $request->input('units');
        $totalUnits = count($units);

        // Check package limits before creating units
        if (!$packageLimitService->canPerformAction($owner, 'units', $totalUnits)) {
            $stats = $packageLimitService->getUsageStats($owner);
            $unitStats = $stats['units'] ?? null;

            $message = 'You have reached your unit limit. ';
            if ($unitStats) {
                $message .= "Current: {$unitStats['current']}, Limit: {$unitStats['max']}. ";
            }
            $message .= 'Please upgrade your plan to add more units.';

            return back()->with('error', $message);
        }

        foreach ($units as $index => $unitData) {
            $unit = Unit::create([
                'property_id' => $property->id,
                'name' => 'Unit-' . str_pad($index, 2, '0', STR_PAD_LEFT),
                'rent' => $unitData['rent'],
            ]);
            if (isset($unitData['charges'])) {
                foreach ($unitData['charges'] as $charge) {
                    UnitCharge::create([
                        'unit_id' => $unit->id,
                        'label' => $charge['label'],
                        'amount' => $charge['amount'],
                    ]);
                }
            }
        }

        // Increment usage after successful creation
        $packageLimitService->incrementUsage($owner, 'units', $totalUnits);

        // Remove temp data after saving
        TempData::where('user_id', auth()->id())
            ->where('key', 'unit_draft')
            ->where('related_id', $property->id)
            ->delete();
        return redirect()->route('owner.dashboard')->with('success', 'Units and fees saved successfully!');
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit($id)
    {
        $ownerId = auth()->user()->owner->id;
        $unit = \App\Models\Unit::where('id', $id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->with('property')
            ->firstOrFail();
        return view('owner.units.edit', compact('unit'));
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'rent' => 'required|numeric|min:0',
        ]);
        $ownerId = auth()->user()->owner->id;
        $unit = \App\Models\Unit::where('id', $id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->firstOrFail();
        $unit->update([
            'name' => $request->name,
            'rent' => $request->rent,
        ]);
        // Update charges
        $unit->charges()->delete();
        if ($request->charges) {
            foreach ($request->charges as $charge) {
                if (!empty($charge['label']) && isset($charge['amount'])) {
                    $unit->charges()->create([
                        'label' => $charge['label'],
                        'amount' => $charge['amount'],
                    ]);
                }
            }
        }
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit updated successfully.']);
        }
        return redirect()->route('owner.units.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy($id)
    {
        $ownerId = auth()->user()->owner->id;
        $unit = \App\Models\Unit::where('id', $id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->firstOrFail();
        $unit->delete();
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit deleted successfully.']);
        }
        return redirect()->route('owner.units.index')->with('success', 'Unit deleted successfully.');
    }
}
