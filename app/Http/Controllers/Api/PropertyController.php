<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\PackageLimitService;

class PropertyController extends Controller
{
    /**
     * Get all properties for the authenticated owner
     */
    public function index()
    {
        try {
            $owner = Auth::user()->owner;

            if (!$owner) {
                return response()->json([
                    'message' => 'Owner not found'
                ], 404);
            }

            $properties = Property::where('owner_id', $owner->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'properties' => $properties
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch properties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new property
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner->id;
        $request->validate([
            'name' => 'required|string|max:100|unique:properties,name,NULL,id,owner_id,' . $ownerId,
            'property_type' => 'required|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string',
            'total_units' => 'required|integer|min:1',
        ]);
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'property_type' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip_code' => 'required|string|max:20',
                'country' => 'required|string|max:255',
                'total_units' => 'required|integer|min:1',
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Auth::user()->owner;

            if (!$owner) {
                return response()->json([
                    'message' => 'Owner not found'
                ], 404);
            }

            // Enforce subscription limit: properties
            $packageLimitService = new PackageLimitService();
            if (!$packageLimitService->canPerformAction($owner, 'properties')) {
                $stats = $packageLimitService->getUsageStats($owner);
                $propStats = $stats['properties'] ?? null;
                return response()->json([
                    'error' => 'Property limit exceeded. Please upgrade your plan.',
                    'limit_type' => 'properties',
                    'current_usage' => $propStats['current'] ?? null,
                    'max_limit' => $propStats['max'] ?? null,
                    'upgrade_required' => true,
                ], 403);
            }

            $property = Property::create([
                'owner_id' => $owner->id,
                'name' => $request->name,
                'property_type' => $request->property_type,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'total_units' => $request->total_units,
                'description' => $request->description,
                'status' => 'active',
            ]);

            // Increment usage after successful creation
            $packageLimitService->incrementUsage($owner, 'properties');

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully',
                'property' => $property
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific property
     */
    public function show($id)
    {
        $user = auth()->user();
        $ownerId = $user->owner->id;
        $property = \App\Models\Property::where('id', $id)
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        return response()->json([
            'id' => $property->id,
            'name' => $property->name,
            'property_type' => $property->property_type,
            'address' => $property->address,
            'city' => $property->city,
            'state' => $property->state,
            'zip_code' => $property->zip_code,
            'country' => $property->country,
            'total_units' => $property->total_units,
            'description' => $property->description,
            'status' => $property->status,
            // Add more fields as needed
        ]);
    }

    /**
     * Update a property
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $ownerId = $user->owner->id;
        $property = \App\Models\Property::where('id', $id)
            ->where('owner_id', $ownerId)
            ->firstOrFail();
        $request->validate([
            'name' => 'required|string|max:100|unique:properties,name,' . $id . ',id,owner_id,' . $ownerId,
            'property_type' => 'required|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'required|string',
            'total_units' => 'required|integer|min:1',
        ]);
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'property_type' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip_code' => 'required|string|max:20',
                'country' => 'required|string|max:255',
                'total_units' => 'required|integer|min:1',
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Auth::user()->owner;

            if (!$owner) {
                return response()->json([
                    'message' => 'Owner not found'
                ], 404);
            }

            $property = Property::where('id', $id)
                ->where('owner_id', $owner->id)
                ->first();

            if (!$property) {
                return response()->json([
                    'message' => 'Property not found'
                ], 404);
            }

            $property->update([
                'name' => $request->name,
                'property_type' => $request->property_type,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'total_units' => $request->total_units,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'property' => $property
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a property
     */
    public function destroy($id)
    {
        try {
            $owner = Auth::user()->owner;

            if (!$owner) {
                return response()->json([
                    'message' => 'Owner not found'
                ], 404);
            }

            $property = Property::where('id', $id)
                ->where('owner_id', $owner->id)
                ->first();

            if (!$property) {
                return response()->json([
                    'message' => 'Property not found'
                ], 404);
            }

            // Check if property has any units or tenants
            if ($property->units()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete property with existing units'
                ], 400);
            }

            $property->delete();

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete property',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get property statistics
     */
    public function stats()
    {
        try {
            $owner = Auth::user()->owner;

            if (!$owner) {
                return response()->json([
                    'message' => 'Owner not found'
                ], 404);
            }

            $totalProperties = Property::where('owner_id', $owner->id)->count();
            $activeProperties = Property::where('owner_id', $owner->id)
                ->where('status', 'active')
                ->count();
            $totalUnits = Property::where('owner_id', $owner->id)
                ->sum('total_units');

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_properties' => $totalProperties,
                    'active_properties' => $activeProperties,
                    'total_units' => $totalUnits,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch property statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
