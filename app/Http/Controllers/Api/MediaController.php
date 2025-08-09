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
                // Cache for 7 days
                return response()->file($absPath, [
                    'Cache-Control' => 'public, max-age=604800',
                ]);
            }
        }

        abort(404);
    }
}


