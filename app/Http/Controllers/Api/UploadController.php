<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            // Save directly under public folder to avoid storage symlink issues
            $publicDir = public_path($folder);
            if (!is_dir($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            $ext = strtolower($file->getClientOriginalExtension());
            $safeName = Str::random(40) . '.' . $ext;
            $file->move($publicDir, $safeName);

            $relativePath = '/' . $folder . '/' . $safeName;
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


