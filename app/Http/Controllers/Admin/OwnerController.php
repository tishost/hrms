<?php

namespace App\Http\Controllers\Admin;
use App\Models\Owner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CountryHelper;
use App\Models\Role; // Correct
class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::with('user')->get();
        $countries = CountryHelper::countryList();
        return view('admin.owners.index', compact('owners', 'countries'));
    }
    public function create()
    {
        $countries = CountryHelper::countryList();
        return view('admin.owners.create', compact('countries'));
    }

    public function edit($id)
    {
        $countries = CountryHelper::countryList();
        $owner = Owner::findOrFail($id);

        return response()->json([
            'owner' => $owner,
            'countries' => $countries,
        ]);

    }
    public function update(Request $request, Owner $owner)
    {
        $owner->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            // add other fields as needed
        ]);

        return response()->json(['success' => true]);
    }
//
}
