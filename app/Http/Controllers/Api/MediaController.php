<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Serve public media files (currently restricted to the 'profiles' folder).
     */
    public function show(Request $request, string $path)
    {
        // Basic traversal protection
        if (str_contains($path, '..')) {
            abort(404);
        }

        // Normalize any leading slashes and possible 'storage/' prefix
        $normalizedPath = ltrim($path, '/');
        if (Str::startsWith($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        // Only allow 'profiles' folder for safety
        if (!Str::startsWith($normalizedPath, 'profiles/')) {
            abort(404);
        }

        $candidates = [
            public_path($normalizedPath),
            storage_path('app/public/' . $normalizedPath),
        ];

        foreach ($candidates as $absPath) {
            if (is_file($absPath) && is_readable($absPath)) {
                // No cache - always serve fresh content
                return response()->file($absPath, [
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                ]);
            }
        }

        abort(404);
    }
}


