<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;

class OwnerController extends Controller
{
    public function updateProfile(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:10|max:15',
            'address' => 'required|string|min:5|max:500',
            'country' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female,Other',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found'
                ], 404);
            }

            // Update Owner table
            $owner->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'gender' => $request->gender,
            ]);

            // Update User table
            $user->name = $request->name;
            $user->phone = $request->phone;

            if ($request->filled('password')) {
                $user->password = \Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'owner' => $owner->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed. Please try again.'
            ], 500);
        }
    }
}
