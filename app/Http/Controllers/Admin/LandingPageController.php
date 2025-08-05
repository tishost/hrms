<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('admin.settings.landing');
    }

    public function update(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'hero_title' => 'required|string|max:255',
                'hero_subtitle' => 'nullable|string|max:255',
                'hero_description' => 'nullable|string|max:1000',
                'hero_button_text' => 'nullable|string|max:100',
                'hero_button_url' => 'nullable|url|max:255',
                'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'features_title' => 'nullable|string|max:255',
                'features_subtitle' => 'nullable|string|max:500',
                'feature1_title' => 'nullable|string|max:255',
                'feature1_icon' => 'nullable|string|max:100',
                'feature1_description' => 'nullable|string|max:500',
                'feature2_title' => 'nullable|string|max:255',
                'feature2_icon' => 'nullable|string|max:100',
                'feature2_description' => 'nullable|string|max:500',
                'feature3_title' => 'nullable|string|max:255',
                'feature3_icon' => 'nullable|string|max:100',
                'feature3_description' => 'nullable|string|max:500',
                'about_title' => 'nullable|string|max:255',
                'about_subtitle' => 'nullable|string|max:255',
                'about_content' => 'nullable|string|max:2000',
                'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'contact_title' => 'nullable|string|max:255',
                'contact_description' => 'nullable|string|max:1000',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:50',
                'contact_address' => 'nullable|string|max:500',
                'footer_copyright' => 'nullable|string|max:255',
                'footer_description' => 'nullable|string|max:1000',
            ]);

            // Handle image uploads
            if ($request->hasFile('hero_image')) {
                $heroImage = $request->file('hero_image');
                $heroImagePath = $heroImage->store('landing', 'public');
                SystemSetting::setValue('hero_image', $heroImagePath);
            }

            if ($request->hasFile('about_image')) {
                $aboutImage = $request->file('about_image');
                $aboutImagePath = $aboutImage->store('landing', 'public');
                SystemSetting::setValue('about_image', $aboutImagePath);
            }

            // Save all text content
            $textSettings = [
                'hero_title', 'hero_subtitle', 'hero_description', 'hero_button_text', 'hero_button_url',
                'features_title', 'features_subtitle',
                'feature1_title', 'feature1_icon', 'feature1_description',
                'feature2_title', 'feature2_icon', 'feature2_description',
                'feature3_title', 'feature3_icon', 'feature3_description',
                'about_title', 'about_subtitle', 'about_content',
                'contact_title', 'contact_description', 'contact_email', 'contact_phone', 'contact_address',
                'footer_copyright', 'footer_description'
            ];

            foreach ($textSettings as $setting) {
                if ($request->has($setting)) {
                    SystemSetting::setValue($setting, $request->input($setting));
                }
            }

            return redirect()->back()->with('success', 'Landing page settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update landing page settings: ' . $e->getMessage());
        }
    }
} 