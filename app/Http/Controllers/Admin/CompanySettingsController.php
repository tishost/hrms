<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.company');
    }

    public function update(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'company_name' => 'required|string|max:255',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'nullable|string|max:50',
                'company_website' => 'nullable|url|max:255',
                'company_address' => 'nullable|string|max:500',
                'company_city' => 'nullable|string|max:100',
                'company_state' => 'nullable|string|max:100',
                'company_country' => 'nullable|string|max:100',
                'company_postal_code' => 'nullable|string|max:20',
                'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_favicon' => 'nullable|image|mimes:ico,png,jpg,gif|max:1024',
                'company_facebook' => 'nullable|url|max:255',
                'company_twitter' => 'nullable|url|max:255',
                'company_linkedin' => 'nullable|url|max:255',
                'company_instagram' => 'nullable|url|max:255',
                'company_registration_number' => 'nullable|string|max:100',
                'company_tax_id' => 'nullable|string|max:100',
                'company_established' => 'nullable|integer|min:1900|max:2030',
                'company_description' => 'nullable|string|max:1000',
                'company_support_email' => 'nullable|email|max:255',
                'company_support_phone' => 'nullable|string|max:50',
                'company_working_hours' => 'nullable|string|max:255',
                'company_timezone' => 'nullable|string|max:50',
            ]);

            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                $logo = $request->file('company_logo');
                $logoPath = $logo->store('company', 'public');
                SystemSetting::setValue('company_logo', $logoPath);
            }

            // Handle favicon upload
            if ($request->hasFile('company_favicon')) {
                $favicon = $request->file('company_favicon');
                $faviconPath = $favicon->store('company', 'public');
                SystemSetting::setValue('company_favicon', $faviconPath);
            }

            // Save all other settings
            $settings = [
                'company_name', 'company_tagline', 'company_email', 'company_phone', 'company_website',
                'company_address', 'company_city', 'company_state', 'company_country', 'company_postal_code',
                'company_facebook', 'company_twitter', 'company_linkedin', 'company_instagram',
                'company_registration_number', 'company_tax_id', 'company_established', 'company_description',
                'company_support_email', 'company_support_phone', 'company_working_hours', 'company_timezone'
            ];

            foreach ($settings as $setting) {
                if ($request->has($setting)) {
                    SystemSetting::setValue($setting, $request->input($setting));
                }
            }

            return redirect()->back()->with('success', 'Company information updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update company information: ' . $e->getMessage());
        }
    }
} 