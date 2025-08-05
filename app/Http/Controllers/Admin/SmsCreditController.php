<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\OwnerSubscription;
use App\Services\OwnerSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsCreditController extends Controller
{
    private $ownerSmsService;

    public function __construct()
    {
        $this->ownerSmsService = new OwnerSmsService();
    }

    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            abort(403, 'Access denied. Authentication required.');
        }

        $user = auth()->user();

        // Check if user has super_admin role
        if ($user->hasRole('super_admin')) {
            return;
        }

        // Check if user is super admin through owner relationship
        if ($user->owner && $user->owner->is_super_admin) {
            return;
        }

        abort(403, 'Access denied. Super admin privileges required.');
    }

    /**
     * Display SMS credit management page
     */
    public function index()
    {
        $this->checkSuperAdmin();

        $owners = Owner::with(['subscription.plan', 'user'])
            ->whereHas('subscription')
            ->get();

        return view('admin.sms.credits.index', compact('owners'));
    }

    /**
     * Show SMS usage for specific owner
     */
    public function show($ownerId)
    {
        $this->checkSuperAdmin();

        $owner = Owner::with(['subscription.plan', 'user'])->findOrFail($ownerId);
        $smsStats = $this->ownerSmsService->getOwnerSmsStats($ownerId);

        return view('admin.sms.credits.show', compact('owner', 'smsStats'));
    }

    /**
     * Add SMS credits to owner
     */
    public function addCredits(Request $request, $ownerId)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'credits' => 'required|integer|min:1|max:10000',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $result = $this->ownerSmsService->addOwnerSmsCredits($ownerId, $request->credits);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'new_balance' => $result['new_balance']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Add SMS credits failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add SMS credits'
            ]);
        }
    }

    /**
     * Send test SMS to owner
     */
    public function sendTestSms(Request $request, $ownerId)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:160'
        ]);

        try {
            $result = $this->ownerSmsService->sendOwnerSms(
                $ownerId,
                $request->phone_number,
                $request->message
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Test SMS failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test SMS'
            ]);
        }
    }

    /**
     * Get SMS usage statistics
     */
    public function getStats($ownerId)
    {
        $this->checkSuperAdmin();

        try {
            $result = $this->ownerSmsService->getOwnerSmsStats($ownerId);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Get SMS stats failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get SMS statistics'
            ]);
        }
    }

    /**
     * Send SMS using smart routing
     */
    public function sendSmartSms(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:160',
            'owner_id' => 'nullable|exists:owners,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'template_name' => 'nullable|string'
        ]);

        try {
            $result = $this->ownerSmsService->sendSmartSms(
                $request->phone_number,
                $request->message,
                $request->owner_id,
                $request->tenant_id,
                $request->template_name,
                $request->variables ?? []
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Smart SMS failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS'
            ]);
        }
    }
} 