<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;

class OwnerController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $owner = Owner::where('user_id', $user->id)->first();
        if (!$owner) {
            return response()->json(['success' => false, 'message' => 'Owner not found'], 404);
        }

        $owner->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'gender' => $request->gender,
        ]);

        // User টেবিলেও আপডেট করুন
        $user->name = $request->name ?? $user->name;
        $user->phone = $request->phone ?? $user->phone;
        $user->email = $request->email ?? $user->email;
        if ($request->filled('password')) {
            $user->password = \Hash::make($request->password);
        }
        $user->save();

        return response()->json(['success' => true, 'owner' => $owner]);
    }
}
