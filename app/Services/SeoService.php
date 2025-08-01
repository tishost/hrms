<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    public static function getSeoSettings()
    {
        return Cache::remember('seo_settings', 3600, function () {
            return SystemSetting::pluck('value', 'key');
        });
    }

    public static function getMetaTags($page = 'landing')
    {
        $settings = self::getSeoSettings();
        
        $metaTags = [
            'title' => $settings['seo_meta_title'] ?? 'Bari Manager - Complete Property Management Solution',
            'description' => $settings['seo_meta_description'] ?? 'Manage your properties, tenants, and rentals with our comprehensive property management system.',
            'keywords' => $settings['seo_meta_keywords'] ?? 'property management, tenant management, rent collection, maintenance tracking, real estate software',
            'canonical' => $settings['seo_canonical_url'] ?? 'https://barimanager.com',
            'og_title' => $settings['seo_og_title'] ?? 'Bari Manager - Complete Property Management Solution',
            'og_description' => $settings['seo_og_description'] ?? 'Streamline your property management with our comprehensive solution.',
            'og_image' => $settings['seo_og_image'] ?? '/images/og-image.jpg',
            'twitter_title' => $settings['seo_twitter_title'] ?? 'Bari Manager - Property Management Made Easy',
            'twitter_description' => $settings['seo_twitter_description'] ?? 'Complete property management solution for landlords and property managers.',
            'twitter_image' => $settings['seo_twitter_image'] ?? '/images/twitter-image.jpg',
            'google_analytics' => $settings['seo_google_analytics'] ?? '',
            'facebook_pixel' => $settings['seo_facebook_pixel'] ?? '',
            'schema_org' => $settings['seo_schema_org'] ?? '',
        ];

        return $metaTags;
    }

    public static function renderMetaTags($page = 'landing')
    {
        $metaTags = self::getMetaTags($page);
        
        $html = '';
        
        // Basic meta tags
        $html .= '<meta name="title" content="' . htmlspecialchars($metaTags['title']) . '">' . "\n";
        $html .= '<meta name="description" content="' . htmlspecialchars($metaTags['description']) . '">' . "\n";
        $html .= '<meta name="keywords" content="' . htmlspecialchars($metaTags['keywords']) . '">' . "\n";
        $html .= '<link rel="canonical" href="' . htmlspecialchars($metaTags['canonical']) . '">' . "\n";
        
        // Open Graph tags
        $html .= '<meta property="og:title" content="' . htmlspecialchars($metaTags['og_title']) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . htmlspecialchars($metaTags['og_description']) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . htmlspecialchars($metaTags['og_image']) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . htmlspecialchars($metaTags['canonical']) . '">' . "\n";
        $html .= '<meta property="og:type" content="website">' . "\n";
        $html .= '<meta property="og:site_name" content="Bari Manager">' . "\n";
        
        // Twitter Card tags
        $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($metaTags['twitter_title']) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars($metaTags['twitter_description']) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . htmlspecialchars($metaTags['twitter_image']) . '">' . "\n";
        
        // Hreflang tags (if enabled)
        $settings = self::getSeoSettings();
        if (($settings['seo_hreflang_enabled'] ?? '1') == '1') {
            $html .= '<link rel="alternate" hreflang="en" href="' . htmlspecialchars($metaTags['canonical']) . '">' . "\n";
            $html .= '<link rel="alternate" hreflang="bn" href="' . htmlspecialchars($metaTags['canonical']) . '?lang=bn">' . "\n";
            $html .= '<link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($metaTags['canonical']) . '">' . "\n";
        }
        
        // Schema.org structured data
        if (!empty($metaTags['schema_org'])) {
            $html .= '<script type="application/ld+json">' . $metaTags['schema_org'] . '</script>' . "\n";
        }
        
        // Google Analytics
        if (!empty($metaTags['google_analytics'])) {
            $html .= '<!-- Google Analytics -->' . "\n";
            $html .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . htmlspecialchars($metaTags['google_analytics']) . '"></script>' . "\n";
            $html .= '<script>' . "\n";
            $html .= '  window.dataLayer = window.dataLayer || [];' . "\n";
            $html .= '  function gtag(){dataLayer.push(arguments);}' . "\n";
            $html .= '  gtag("js", new Date());' . "\n";
            $html .= '  gtag("config", "' . htmlspecialchars($metaTags['google_analytics']) . '");' . "\n";
            $html .= '</script>' . "\n";
        }
        
        // Facebook Pixel
        if (!empty($metaTags['facebook_pixel'])) {
            $html .= '<!-- Facebook Pixel -->' . "\n";
            $html .= '<script>' . "\n";
            $html .= '  !function(f,b,e,v,n,t,s)' . "\n";
            $html .= '  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?' . "\n";
            $html .= '  n.callMethod.apply(n,arguments):n.queue.push(arguments)};' . "\n";
            $html .= '  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";' . "\n";
            $html .= '  n.queue=[];t=b.createElement(e);t.async=!0;' . "\n";
            $html .= '  t.src=v;s=b.getElementsByTagName(e)[0];' . "\n";
            $html .= '  s.parentNode.insertBefore(t,s)}(window, document,"script",' . "\n";
            $html .= '  "https://connect.facebook.net/en_US/fbevents.js");' . "\n";
            $html .= '  fbq("init", "' . htmlspecialchars($metaTags['facebook_pixel']) . '");' . "\n";
            $html .= '  fbq("track", "PageView");' . "\n";
            $html .= '</script>' . "\n";
            $html .= '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . htmlspecialchars($metaTags['facebook_pixel']) . '&ev=PageView&noscript=1"/></noscript>' . "\n";
        }
        
        return $html;
    }

    public static function renderBreadcrumbs($breadcrumbs = [])
    {
        $settings = self::getSeoSettings();
        
        if (($settings['seo_breadcrumb_enabled'] ?? '1') != '1') {
            return '';
        }
        
        if (empty($breadcrumbs)) {
            $breadcrumbs = [
                ['url' => '/', 'title' => 'Home'],
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $schema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['title'],
                'item' => url($breadcrumb['url'])
            ];
        }
        
        return '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }

    public static function optimizeImages($html)
    {
        $settings = self::getSeoSettings();
        
        if (($settings['seo_lazy_loading_enabled'] ?? '1') == '1') {
            // Add loading="lazy" to images
            $html = preg_replace('/<img([^>]*)>/i', '<img$1 loading="lazy">', $html);
        }
        
        return $html;
    }

    public static function minifyHtml($html)
    {
        $settings = self::getSeoSettings();
        
        if (($settings['seo_minify_enabled'] ?? '1') == '1') {
            // Remove comments
            $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
            
            // Remove extra whitespace
            $html = preg_replace('/\s+/', ' ', $html);
            $html = preg_replace('/>\s+</', '><', $html);
            
            // Remove whitespace around tags
            $html = trim($html);
        }
        
        return $html;
    }

    public static function generateSitemap()
    {
        $settings = self::getSeoSettings();
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

    public static function generateRobotsTxt()
    {
        $settings = self::getSeoSettings();
        $robotsTxt = $settings['seo_robots_txt'] ?? "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /api/\nSitemap: https://barimanager.com/sitemap.xml";
        
        return $robotsTxt;
    }
} 