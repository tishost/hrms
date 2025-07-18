<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Str;
use App\Helpers\CountryHelper;
use App\Models\User; // if needed
use Spatie\Permission\Models\Role;

class OwnerRegisterController extends Controller
{
    public function showForm()
    {
        $countries = CountryHelper::countryList();
        return view('auth.owner-register',compact('countries'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                'unique:users,email',
                'unique:owners,email', // <-- Owner table-এও unique validation
            ],
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'country'  => 'nullable|string|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->assignRole('owner');

        $owner = Owner::create([
            'user_id'  => $user->id,
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'country'  => $request->country,
           
        ]);

       

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }
}