<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ImageHelper;
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
            
            // Validate image
            $validationErrors = ImageHelper::validateImage($file, 5); // 5MB limit
            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file: ' . implode(', ', $validationErrors),
                ], 400);
            }
            
            // Compress image for NID uploads
            $compressedPath = null;
            if ($folder === 'tenants/nid') {
                $compressedPath = ImageHelper::compressImage($file, 800, 600, 80);
                if (!$compressedPath) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to compress image',
                    ], 400);
                }
            }
            
            // Save directly under public folder to avoid storage symlink issues
            $publicDir = public_path($folder);
            if (!is_dir($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            
            $ext = strtolower($file->getClientOriginalExtension());
            $safeName = Str::random(40) . '.' . $ext;
            
            // Use compressed image if available, otherwise use original
            if ($compressedPath) {
                $compressedFile = new \Illuminate\Http\File($compressedPath);
                $compressedFile->move($publicDir, $safeName);
                // Clean up temporary compressed file
                unlink($compressedPath);
            } else {
                $file->move($publicDir, $safeName);
            }

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

    /**
     * Delete old NID image file
     */
    public function deleteOldNidImage(Request $request)
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
            $nidDir = public_path('tenants/nid');
            
            // Only allow deletion from tenants/nid folder for security
            if (is_file($nidDir . '/' . $filename)) {
                unlink($nidDir . '/' . $filename);
                return response()->json([
                    'success' => true,
                    'message' => 'Old NID image deleted',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found or not in tenants/nid folder',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}


