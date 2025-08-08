<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'folder' => 'nullable|string|max:50',
        ]);

        try {
            $file = $request->file('file');
            $folder = trim($request->input('folder', 'uploads'), '/');
            $path = $file->store("public/{$folder}");

            // Build URL
            $relativePath = str_replace('public/', 'storage/', $path);
            $url = url($relativePath);

            return response()->json([
                'success' => true,
                'path' => $relativePath,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}


