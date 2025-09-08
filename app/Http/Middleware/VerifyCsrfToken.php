<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Cookie;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // API routes that don't need CSRF
        'api/*',
        // CSRF token refresh route
        'csrf-token',
        'refresh-csrf',
        // Test routes
        'test-csrf',
        'test-template-save',
        'test-template-save-admin',
        // Admin notifications (forms / ajax from admin panel)
        'admin/notifications/*'
    ];

    /**
     * Add the CSRF token cookie to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    protected function addHttpCookie($request, $response)
    {
        $config = config('session');
        
        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $request->session()->token(),
                time() + 60 * $config['lifetime'],
                $config['path'],
                $config['domain'],
                $config['secure'],
                false,
                false,
                $config['same_site'] ?? null
            )
        );
        
        return $response;
    }
}
