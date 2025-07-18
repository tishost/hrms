<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;


class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'default_building_limit' => 'required|integer|min:1',
            'default_unit_limit' => 'required|integer|min:1',
        ]);

        foreach ($request->only(['default_building_limit', 'default_unit_limit']) as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}