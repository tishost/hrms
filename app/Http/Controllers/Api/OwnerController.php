<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    /**
     * Update authenticated owner's profile (API for mobile)
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Find linked owner
            $owner = \App\Models\Owner::where('user_id', $user->id)->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner profile not found',
                ], 404);
            }

            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'district' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'gender' => 'nullable|in:male,female,other',
                'profile_pic' => 'nullable|string|max:255',
            ]);

            // Handle profile picture update - delete old file if new one is provided
            $oldProfilePic = $owner->profile_pic;
            if (!empty($validated['profile_pic']) && $validated['profile_pic'] !== $oldProfilePic) {
                // Delete old profile picture file if it exists
                if (!empty($oldProfilePic)) {
                    try {
                        $oldPath = public_path(ltrim($oldProfilePic, '/'));
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old profile picture: ' . $e->getMessage());
                    }
                }
            }

            // Prepare updates for owner
            $ownerUpdates = [
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'district' => $validated['district'] ?? null,
                'country' => $validated['country'] ?? ($owner->country ?: 'Bangladesh'),
                'gender' => $validated['gender'] ?? null,
                'profile_pic' => $validated['profile_pic'] ?? $owner->profile_pic,
            ];

            // Update owner
            $owner->fill($ownerUpdates);
            $owner->save();

            // Keep user record in sync for name/email (optional, best-effort)
            $user->name = $validated['name'];
            if (!empty($validated['email'])) {
                $user->email = $validated['email'];
            }
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $owner->phone,
                    'address' => $owner->address,
                    'district' => $owner->district,
                    'country' => $owner->country,
                    'gender' => $owner->gender,
                    'profile_pic' => $owner->profile_pic,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Owner profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage(),
            ], 500);
        }
    }

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

            // Resolve owner id robustly (support legacy users missing relation)
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $ownerId = null;
            if ($user->owner) {
                $ownerId = $user->owner->id;
            } elseif (!empty($user->owner_id)) {
                $ownerId = $user->owner_id;
            } else {
                $owner = \App\Models\Owner::where('user_id', $user->id)->first();
                if ($owner) {
                    $ownerId = $owner->id;
                    try {
                        if (empty($user->owner_id)) {
                            $user->update(['owner_id' => $ownerId]);
                        }
                    } catch (\Exception $e) {}
                }
            }

            if (!$ownerId) {
                \Log::error("Owner resolve failed for user ID: " . ($user->id ?? 'n/a'));
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an owner'
                ], 403);
            }

            // Get invoice for this owner (same as web version but with owner permission)
            $invoice = \App\Models\Invoice::where('id', $id)
                ->where('owner_id', $ownerId)
                ->with(['tenant:id,first_name,last_name,mobile,email', 'unit:id,name', 'property:id,name,address,email,mobile'])
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            // Process breakdown data for PDF
            if ($invoice->breakdown) {
                try {
                    $breakdown = json_decode($invoice->breakdown, true) ?? [];

                    // Add descriptions for common fee types
                    foreach ($breakdown as &$fee) {
                        if (!isset($fee['description']) || empty($fee['description'])) {
                            $feeName = strtolower($fee['name'] ?? '');

                            // Add descriptions based on fee name
                            if (strpos($feeName, 'base rent') !== false || strpos($feeName, 'monthly rent') !== false) {
                                $fee['description'] = 'Base monthly rent payment for the rental unit';
                            } elseif (strpos($feeName, 'rent') !== false) {
                                $fee['description'] = 'Monthly rent payment for the unit';
                            } elseif (strpos($feeName, 'electricity') !== false || strpos($feeName, 'power') !== false || strpos($feeName, 'electric') !== false) {
                                $fee['description'] = 'Electricity bill charges for the month';
                            } elseif (strpos($feeName, 'gas') !== false || strpos($feeName, 'gas bill') !== false) {
                                $fee['description'] = 'Gas bill charges for the month';
                            } elseif (strpos($feeName, 'water') !== false || strpos($feeName, 'water bill') !== false) {
                                $fee['description'] = 'Water bill charges for the month';
                            } elseif (strpos($feeName, 'cleaning') !== false) {
                                $fee['description'] = 'Cleaning and maintenance charges';
                            } elseif (strpos($feeName, 'maintenance') !== false) {
                                $fee['description'] = 'Building maintenance and repair charges';
                            } elseif (strpos($feeName, 'late') !== false || strpos($feeName, 'penalty') !== false) {
                                $fee['description'] = 'Late payment penalty charges';
                            } elseif (strpos($feeName, 'security') !== false || strpos($feeName, 'deposit') !== false) {
                                $fee['description'] = 'Security deposit or related charges';
                            } elseif (strpos($feeName, 'utility') !== false) {
                                $fee['description'] = 'Utility service charges (electricity, water, gas)';
                            } elseif (strpos($feeName, 'service') !== false) {
                                $fee['description'] = 'Additional service charges';
                            } else {
                                $fee['description'] = 'Additional service or charge';
                            }
                        }
                    }

                    // Update invoice with processed breakdown
                    $invoice->breakdown = json_encode($breakdown);
                } catch (Exception $e) {
                    // Keep original breakdown if processing fails
                }
            }

            // Fetch last payment for this invoice (to populate gateway, txn id, date)
            $lastPayment = \App\Models\RentPayment::where('invoice_id', $invoice->id)
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->first();

            // Optimized PDF generation for mobile
            $pdf = \PDF::loadView('owner.invoices.pdf', compact('invoice', 'lastPayment'));

            // Configure PDF for A4 size
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('dpi', 72);
            $pdf->setOption('image-dpi', 72);
            $pdf->setOption('image-quality', 60);
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

    public function getSubscription(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $owner = \App\Models\Owner::where('user_id', $user->id)->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found'
                ], 404);
            }

            // Get active subscription (end_date may be null for lifetime/free)
            $subscription = \App\Models\OwnerSubscription::where('owner_id', $owner->id)
                ->where('status', 'active')
                ->where(function($q){
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
                })
                ->with('plan')
                ->first();

            if ($subscription) {
                return response()->json([
                    'success' => true,
                    'subscription' => [
                        'id' => $subscription->id,
                        'plan_name' => $subscription->plan_name ?? optional($subscription->plan)->name,
                        'status' => $subscription->status,
                        'start_date' => $subscription->start_date,
                        'end_date' => $subscription->end_date, // may be null for lifetime
                        'auto_renew' => $subscription->auto_renew,
                        'sms_credits' => $subscription->sms_credits,
                        'plan' => $subscription->plan ? [
                            'id' => $subscription->plan->id,
                            'name' => $subscription->plan->name,
                            'price' => $subscription->plan->price,
                            'duration' => $subscription->plan->duration,
                            'properties_limit' => $subscription->plan->properties_limit,
                            'units_limit' => $subscription->plan->units_limit,
                            'tenants_limit' => $subscription->plan->tenants_limit,
                        ] : null
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'subscription' => null,
                    'message' => 'No active subscription found'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Owner subscription error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to get subscription: ' . $e->getMessage()
            ], 500);
        }
    }
}
