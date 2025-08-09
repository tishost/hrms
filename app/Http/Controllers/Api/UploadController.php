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
            'replace_old' => 'nullable|boolean', // Add this to handle old file replacement
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
                'filename' => $safeName,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete old profile picture file
     */
    public function deleteOldProfilePic(Request $request)
    {
        try {
            $oldPath = $request->input('old_path');
            if (empty($oldPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Old path is required',
                ], 400);
            }

            // Extract filename from path
            $filename = basename($oldPath);
            $profilesDir = public_path('profiles');
            
            // Only allow deletion from profiles folder for security
            if (is_file($profilesDir . '/' . $filename)) {
                unlink($profilesDir . '/' . $filename);
                return response()->json([
                    'success' => true,
                    'message' => 'Old profile picture deleted',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found or not in profiles folder',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}


