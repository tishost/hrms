<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Check if user has super_admin role
        if (!$user->hasRole('super_admin')) {
            // Check if user is super admin through owner relationship
            if ($user->owner && $user->owner->is_super_admin) {
                return $next($request);
            }

            return redirect('/dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        return $next($request);
    }
}
