<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    /**
     * Get properties for the authenticated owner
     */
    public function getProperties()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $properties = Property::where('owner_id', $user->owner_id)
                ->select('id', 'name', 'address')
                ->get();

            return response()->json([
                'success' => true,
                'properties' => $properties
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching properties: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch properties'
            ], 500);
        }
    }

    /**
     * Get units for a specific property
     */
    public function getUnitsByProperty($propertyId)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            // Verify property belongs to the owner
            $property = Property::where('id', $propertyId)
                ->where('owner_id', $user->owner_id)
                ->first();

            if (!$property) {
                return response()->json([
                    'error' => 'Property not found'
                ], 404);
            }

            // Get available units (not assigned to any tenant)
            $units = Unit::where('property_id', $propertyId)
                ->whereDoesntHave('tenant')
                ->select('id', 'name', 'floor', 'rent_amount')
                ->get();

            return response()->json([
                'success' => true,
                'units' => $units,
                'property' => [
                    'id' => $property->id,
                    'name' => $property->name,
                    'address' => $property->address
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching units: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch units'
            ], 500);
        }
    }

    /**
     * Get all properties with their units for tenant entry form
     */
    public function getPropertiesWithUnits()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $properties = Property::where('owner_id', $user->owner_id)
                ->with(['units' => function($query) {
                    $query->whereDoesntHave('tenant')
                        ->select('id', 'property_id', 'name', 'floor', 'rent_amount');
                }])
                ->select('id', 'name', 'address')
                ->get();

            return response()->json([
                'success' => true,
                'properties' => $properties
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching properties with units: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch properties with units'
            ], 500);
        }
    }

    /**
     * Get tenant dashboard data
     */
    public function getDashboard()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->tenant_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $tenant = Tenant::with(['unit.property', 'rents'])
                ->find($user->tenant_id);

            if (!$tenant) {
                return response()->json([
                    'error' => 'Tenant not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'tenant' => $tenant
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching tenant dashboard: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch dashboard data'
            ], 500);
        }
    }

    /**
     * Test endpoint for debugging
     */
    public function testEndpoint()
    {
        return response()->json([
            'success' => true,
            'message' => 'Tenant API is working',
            'user' => Auth::user()
        ]);
    }
}
