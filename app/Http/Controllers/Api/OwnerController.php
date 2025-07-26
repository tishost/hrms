<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
        // Download invoice PDF file (API version for mobile)
    public function downloadInvoicePDF(Request $request, $id)
    {
        try {
            $user = $request->user();

                                    // Handle token from query parameter for WebView compatibility
            if (!$user && $request->has('token')) {
                $token = $request->get('token');
                \Log::info("Token from query parameter: " . substr($token, 0, 20) . "...");

                // Parse token format: ID|TOKEN
                $parts = explode('|', $token);
                if (count($parts) == 2) {
                    $tokenId = $parts[0];
                    $tokenValue = $parts[1];

                    \Log::info("Token ID: $tokenId, Token Value: " . substr($tokenValue, 0, 20) . "...");

                    $tokenModel = \Laravel\Sanctum\PersonalAccessToken::find($tokenId);
                    if ($tokenModel) {
                        \Log::info("Token model found, stored hash: " . substr($tokenModel->token, 0, 20) . "...");
                        \Log::info("Calculated hash: " . substr(hash('sha256', $tokenValue), 0, 20) . "...");

                        if (hash('sha256', $tokenValue) === $tokenModel->token) {
                            $user = $tokenModel->tokenable;
                            \Log::info("User authenticated via token: " . ($user ? $user->name : 'No user'));
                            \Log::info("User owner_id: " . ($user ? $user->owner_id : 'No owner_id'));
                        } else {
                            \Log::error("Token hash mismatch!");
                            // Try alternative approach - use the token value directly
                            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenValue);
                            if ($tokenModel) {
                                $user = $tokenModel->tokenable;
                                \Log::info("User authenticated via direct token lookup: " . ($user ? $user->name : 'No user'));
                            }
                        }
                    } else {
                        \Log::error("Token model not found for ID: $tokenId");
                        // Try direct token lookup
                        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenValue);
                        if ($tokenModel) {
                            $user = $tokenModel->tokenable;
                            \Log::info("User authenticated via direct token lookup: " . ($user ? $user->name : 'No user'));
                        }
                    }
                } else {
                    \Log::error("Invalid token format, parts count: " . count($parts));
                    // Try direct token lookup
                    $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                    if ($tokenModel) {
                        $user = $tokenModel->tokenable;
                        \Log::info("User authenticated via direct token: " . ($user ? $user->name : 'No user'));
                    }
                }
            }

            // Check if user is owner
            if (!$user || !$user->owner) {
                \Log::error("Authentication failed - User: " . ($user ? $user->name : 'No user') . ", Has owner: " . ($user && $user->owner ? 'Yes' : 'No'));
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an owner'
                ], 403);
            }

            $ownerId = $user->owner->id;

            // Get invoice for this owner (same as web version but with owner permission)
            $invoice = \App\Models\Invoice::where('id', $id)
                ->where('owner_id', $ownerId)
                ->with(['tenant', 'unit', 'property'])
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            // Optimized PDF generation for mobile
            $pdf = \PDF::loadView('owner.invoices.pdf', compact('invoice'));

            // Configure PDF for smaller size
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('dpi', 72); // Lower DPI for smaller file size
            $pdf->setOption('image-dpi', 72);
            $pdf->setOption('image-quality', 60); // Lower image quality
            $pdf->setOption('enable-local-file-access', false);
            $pdf->setOption('isRemoteEnabled', false);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isFontSubsettingEnabled', true);

            // Set proper headers for PDF download (mobile compatible)
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice-' . $invoice->invoice_number . '.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            \Log::error('Owner invoice PDF error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get owner profile (for user type detection)
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an owner'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'owner' => [
                        'id' => $user->owner->id,
                        'first_name' => $user->owner->first_name,
                        'last_name' => $user->owner->last_name,
                        'mobile' => $user->owner->mobile,
                        'email' => $user->owner->email,
                    ],
                    'tenant' => null, // Owner doesn't have tenant
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Owner profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
