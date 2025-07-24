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

            // Check if user is owner
            if (!$user->owner) {
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

            // Use the same PDF generation as web owner (working approach)
            $pdf = \PDF::loadView('owner.invoices.pdf', compact('invoice'));

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
