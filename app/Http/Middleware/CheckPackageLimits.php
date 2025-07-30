<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PackageLimitService;
use Illuminate\Support\Facades\Auth;

class CheckPackageLimits
{
    protected $packageLimitService;

    public function __construct(PackageLimitService $packageLimitService)
    {
        $this->packageLimitService = $packageLimitService;
    }

    public function handle(Request $request, Closure $next, string $action)
    {
        $owner = Auth::user()->owner;

        if (!$this->packageLimitService->canPerformAction($owner, $action)) {
            $limit = $this->packageLimitService->getOwnerLimits($owner)->get($action);

            $message = match($action) {
                'properties' => 'Property limit exceeded. Please upgrade your plan.',
                'units' => 'Unit limit exceeded. Please upgrade your plan.',
                'tenants' => 'Tenant limit exceeded. Please upgrade your plan.',
                'sms' => 'SMS limit exceeded for this month.',
                'emails' => 'Email limit exceeded for this month.',
                default => 'Action limit exceeded. Please upgrade your plan.'
            };

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $message,
                    'limit_type' => $action,
                    'current_usage' => $limit->current_usage,
                    'max_limit' => $limit->max_limit
                ], 403);
            }

            return back()->with('error', $message);
        }

        return $next($request);
    }
}
