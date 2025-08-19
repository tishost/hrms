<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdsController extends Controller
{
    /**
     * Display a listing of ads
     */
    public function index()
    {
        $ads = Ad::orderBy('display_order', 'asc')
                 ->orderBy('created_at', 'desc')
                 ->paginate(15);

        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new ad
     */
    public function create()
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created ad
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'show_on_owner_dashboard' => 'boolean',
            'show_on_tenant_dashboard' => 'boolean',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Handle image upload
            $imagePath = $this->uploadImage($request->file('image'));

            // Create ad
            $ad = Ad::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'url' => $request->url,
                'is_active' => $request->boolean('is_active', true),
                'show_on_owner_dashboard' => $request->boolean('show_on_owner_dashboard', false),
                'show_on_tenant_dashboard' => $request->boolean('show_on_tenant_dashboard', false),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'display_order' => $request->display_order ?? 0,
            ]);

            return redirect()->route('admin.ads.index')
                           ->with('success', 'Ad created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create ad: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified ad
     */
    public function show(Ad $ad)
    {
        return view('admin.ads.show', compact('ad'));
    }

    /**
     * Show the form for editing the specified ad
     */
    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified ad
     */
    public function update(Request $request, Ad $ad)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'show_on_owner_dashboard' => 'boolean',
            'show_on_tenant_dashboard' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'url' => $request->url,
                'is_active' => $request->boolean('is_active', true),
                'show_on_owner_dashboard' => $request->boolean('show_on_owner_dashboard', false),
                'show_on_tenant_dashboard' => $request->boolean('show_on_tenant_dashboard', false),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'display_order' => $request->display_order ?? 0,
            ];

            // Handle image upload if new image provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($ad->image_path && !str_starts_with($ad->image_path, 'http')) {
                    Storage::disk('public')->delete($ad->image_path);
                }
                
                $data['image_path'] = $this->uploadImage($request->file('image'));
            }

            $ad->update($data);

            return redirect()->route('admin.ads.index')
                           ->with('success', 'Ad updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update ad: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified ad
     */
    public function destroy(Ad $ad)
    {
        try {
            // Delete image file
            if ($ad->image_path && !str_starts_with($ad->image_path, 'http')) {
                Storage::disk('public')->delete($ad->image_path);
            }

            $ad->delete();

            return redirect()->route('admin.ads.index')
                           ->with('success', 'Ad deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ad: ' . $e->getMessage());
        }
    }

    /**
     * Toggle ad status
     */
    public function toggleStatus(Ad $ad)
    {
        try {
            $ad->update(['is_active' => !$ad->is_active]);
            
            $status = $ad->is_active ? 'activated' : 'deactivated';
            return response()->json([
                'success' => true,
                'message' => "Ad {$status} successfully!",
                'is_active' => $ad->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle ad status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update display order
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ads' => 'required|array',
            'ads.*.id' => 'required|exists:ads,id',
            'ads.*.display_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided'
            ], 422);
        }

        try {
            foreach ($request->ads as $adData) {
                Ad::where('id', $adData['id'])
                  ->update(['display_order' => $adData['display_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Display order updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update display order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ads for dashboard (API endpoint)
     */
    public function getDashboardAds(Request $request)
    {
        $type = $request->get('type', 'tenant'); // 'tenant' or 'owner'
        
        $ads = Ad::when($type === 'owner', function($query) {
                return $query->forOwnerDashboard();
            })
            ->when($type === 'tenant', function($query) {
                return $query->forTenantDashboard();
            })
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Increment impression count for each ad
        foreach ($ads as $ad) {
            $ad->incrementImpressions();
        }

        return response()->json([
            'success' => true,
            'ads' => $ads->map(function($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_url,
                    'url' => $ad->url,
                    'display_order' => $ad->display_order,
                ];
            })
        ]);
    }

    /**
     * Record ad click
     */
    public function recordClick(Ad $ad)
    {
        try {
            $ad->incrementClicks();
            
            return response()->json([
                'success' => true,
                'message' => 'Click recorded successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record click: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image and return path
     */
    private function uploadImage($image)
    {
        $filename = 'ads/' . Str::random(40) . '.' . $image->getClientOriginalExtension();
        
        Storage::disk('public')->put($filename, file_get_contents($image));
        
        return $filename;
    }

    /**
     * Get ads statistics
     */
    public function stats()
    {
        $stats = [
            'total_ads' => Ad::count(),
            'active_ads' => Ad::active()->count(),
            'scheduled_ads' => Ad::where('start_date', '>', Carbon::today())->count(),
            'expired_ads' => Ad::where('end_date', '<', Carbon::today())->count(),
            'owner_dashboard_ads' => Ad::where('show_on_owner_dashboard', true)->count(),
            'tenant_dashboard_ads' => Ad::where('show_on_tenant_dashboard', true)->count(),
            'total_clicks' => Ad::sum('clicks_count'),
            'total_impressions' => Ad::sum('impressions_count'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
