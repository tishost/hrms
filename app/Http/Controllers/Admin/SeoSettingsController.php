<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoSettingsController extends Controller
{
    public function __construct()
    {
        // Laravel 11 doesn't support middleware() in controllers
        // Middleware is handled in routes
    }

    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            abort(403, 'Access denied. Authentication required.');
        }

        $user = auth()->user();

        // Check if user has super_admin role
        if ($user->hasRole('super_admin')) {
            return;
        }

        // Check if user is super admin through owner relationship
        if ($user->owner && $user->owner->is_super_admin) {
            return;
        }

        abort(403, 'Access denied. Super admin privileges required.');
    }

    public function index()
    {
        $this->checkSuperAdmin();

        try {
            $settings = SystemSetting::pluck('value', 'key');
            
            // Default SEO settings
            $seoSettings = [
                'seo_meta_title' => $settings['seo_meta_title'] ?? 'Bari Manager - Complete Property Management Solution',
                'seo_meta_description' => $settings['seo_meta_description'] ?? 'Manage your properties, tenants, and rentals with our comprehensive property management system. Features include tenant management, rent collection, maintenance tracking, and more.',
                'seo_meta_keywords' => $settings['seo_meta_keywords'] ?? 'property management, tenant management, rent collection, maintenance tracking, real estate software',
                'seo_og_title' => $settings['seo_og_title'] ?? 'Bari Manager - Complete Property Management Solution',
                'seo_og_description' => $settings['seo_og_description'] ?? 'Streamline your property management with our comprehensive solution. Manage tenants, collect rent, track maintenance, and more.',
                'seo_og_image' => $settings['seo_og_image'] ?? '/images/og-image.jpg',
                'seo_twitter_title' => $settings['seo_twitter_title'] ?? 'Bari Manager - Property Management Made Easy',
                'seo_twitter_description' => $settings['seo_twitter_description'] ?? 'Complete property management solution for landlords and property managers.',
                'seo_twitter_image' => $settings['seo_twitter_image'] ?? '/images/twitter-image.jpg',
                'seo_canonical_url' => $settings['seo_canonical_url'] ?? 'https://barimanager.com',
                'seo_google_analytics' => $settings['seo_google_analytics'] ?? '',
                'seo_facebook_pixel' => $settings['seo_facebook_pixel'] ?? '',
                'seo_schema_org' => $settings['seo_schema_org'] ?? '{"@context":"https://schema.org","@type":"SoftwareApplication","name":"Bari Manager","description":"Complete property management solution","applicationCategory":"BusinessApplication","operatingSystem":"Web","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}',
                'seo_breadcrumb_enabled' => $settings['seo_breadcrumb_enabled'] ?? '1',
                'seo_sitemap_enabled' => $settings['seo_sitemap_enabled'] ?? '1',
                'seo_robots_txt' => $settings['seo_robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nSitemap: https://barimanager.com/sitemap.xml",
                'seo_hreflang_enabled' => $settings['seo_hreflang_enabled'] ?? '1',
                'seo_lazy_loading_enabled' => $settings['seo_lazy_loading_enabled'] ?? '1',
                'seo_minify_enabled' => $settings['seo_minify_enabled'] ?? '1',
                'seo_compression_enabled' => $settings['seo_compression_enabled'] ?? '1',
            ];

            return view('admin.settings.seo', compact('seoSettings'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading SEO settings: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'seo_meta_title' => 'required|string|max:60',
            'seo_meta_description' => 'required|string|max:160',
            'seo_meta_keywords' => 'nullable|string|max:255',
            'seo_og_title' => 'required|string|max:60',
            'seo_og_description' => 'required|string|max:160',
            'seo_og_image' => 'nullable|string|max:255',
            'seo_twitter_title' => 'required|string|max:60',
            'seo_twitter_description' => 'required|string|max:160',
            'seo_twitter_image' => 'nullable|string|max:255',
            'seo_canonical_url' => 'required|url',
            'seo_google_analytics' => 'nullable|string',
            'seo_facebook_pixel' => 'nullable|string',
            'seo_schema_org' => 'nullable|string',
            'seo_breadcrumb_enabled' => 'boolean',
            'seo_sitemap_enabled' => 'boolean',
            'seo_robots_txt' => 'nullable|string',
            'seo_hreflang_enabled' => 'boolean',
            'seo_lazy_loading_enabled' => 'boolean',
            'seo_minify_enabled' => 'boolean',
            'seo_compression_enabled' => 'boolean',
        ]);

        try {
            foreach ($request->except(['_token', '_method']) as $key => $value) {
                // Convert boolean values to string
                if (in_array($key, ['seo_breadcrumb_enabled', 'seo_sitemap_enabled', 'seo_hreflang_enabled', 'seo_lazy_loading_enabled', 'seo_minify_enabled', 'seo_compression_enabled'])) {
                    $value = $value ? '1' : '0';
                }
                
                SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            // Clear cache
            Cache::forget('seo_settings');

            return back()->with('success', 'SEO settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating SEO settings: ' . $e->getMessage());
        }
    }

    public function generateSitemap()
    {
        $this->checkSuperAdmin();

        try {
            $sitemap = $this->createSitemap();
            
            // Save sitemap to public directory
            file_put_contents(public_path('sitemap.xml'), $sitemap);
            
            return back()->with('success', 'Sitemap generated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating sitemap: ' . $e->getMessage());
        }
    }

    public function generateRobotsTxt()
    {
        $this->checkSuperAdmin();

        try {
            $robotsTxt = $this->createRobotsTxt();
            
            // Save robots.txt to public directory
            file_put_contents(public_path('robots.txt'), $robotsTxt);
            
            return back()->with('success', 'Robots.txt generated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating robots.txt: ' . $e->getMessage());
        }
    }

    private function createSitemap()
    {
        $settings = SystemSetting::pluck('value', 'key');
        $baseUrl = $settings['seo_canonical_url'] ?? 'https://barimanager.com';
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Landing page
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $baseUrl . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>weekly</changefreq>' . "\n";
        $xml .= '    <priority>1.0</priority>' . "\n";
        $xml .= '  </url>' . "\n";
        
        // Contact page
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $baseUrl . '/contact</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>monthly</changefreq>' . "\n";
        $xml .= '    <priority>0.8</priority>' . "\n";
        $xml .= '  </url>' . "\n";
        
        // Legal pages
        $legalPages = ['terms', 'privacy', 'refund'];
        foreach ($legalPages as $page) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/' . $page . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function createRobotsTxt()
    {
        $settings = SystemSetting::pluck('value', 'key');
        $robotsTxt = $settings['seo_robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nSitemap: https://barimanager.com/sitemap.xml";
        
        return $robotsTxt;
    }

    public function previewSeo(Request $request)
    {
        $this->checkSuperAdmin();

        try {
            $settings = $request->all();
            
            $preview = [
                'meta_title' => $settings['seo_meta_title'] ?? '',
                'meta_description' => $settings['seo_meta_description'] ?? '',
                'og_title' => $settings['seo_og_title'] ?? '',
                'og_description' => $settings['seo_og_description'] ?? '',
                'twitter_title' => $settings['seo_twitter_title'] ?? '',
                'twitter_description' => $settings['seo_twitter_description'] ?? '',
                'canonical_url' => $settings['seo_canonical_url'] ?? '',
            ];
            
            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
} 