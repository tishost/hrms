<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OwnerSubscription;
use App\Services\OwnerSmsService;

class SmsCreditController extends Controller
{
    protected $ownerSmsService;

    public function __construct(OwnerSmsService $ownerSmsService)
    {
        $this->ownerSmsService = $ownerSmsService;
    }

    /**
     * Show SMS credits page
     */
    public function index()
    {
        $owner = auth()->user()->owner;
        $subscription = $owner->subscription;
        
        $smsCredits = 0;
        $usedSmsCredits = 0;
        $remainingCredits = 0;
        
        if ($subscription) {
            $smsCredits = $subscription->sms_credits ?? 0;
            $usedSmsCredits = $subscription->used_sms_credits ?? 0;
            $remainingCredits = $smsCredits - $usedSmsCredits;
        }

        return view('owner.sms.index', compact('smsCredits', 'usedSmsCredits', 'remainingCredits', 'subscription'));
    }

    /**
     * Add SMS credits to owner's account
     */
    public function addCredits(Request $request)
    {
        $request->validate([
            'credits_amount' => 'required|integer|min:50|max:10000',
            'payment_method' => 'required|in:bkash,nagad,rocket,card'
        ]);

        $owner = auth()->user()->owner;
        $subscription = $owner->subscription;

        if (!$subscription) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }

        $creditsAmount = $request->credits_amount;
        $paymentMethod = $request->payment_method;

        // Calculate price based on credits
        $price = $this->calculateCreditsPrice($creditsAmount);

        try {
            // Add credits to subscription
            $this->ownerSmsService->addOwnerSmsCredits($owner->id, $creditsAmount);

            // Log the transaction (you can create a separate table for this)
            \Log::info('SMS credits added', [
                'owner_id' => $owner->id,
                'credits_added' => $creditsAmount,
                'payment_method' => $paymentMethod,
                'price' => $price
            ]);

            return redirect()->back()->with('success', "Successfully added {$creditsAmount} SMS credits to your account.");

        } catch (\Exception $e) {
            \Log::error('Failed to add SMS credits: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add SMS credits. Please try again.');
        }
    }

    /**
     * Calculate price for SMS credits
     */
    private function calculateCreditsPrice($credits)
    {
        $prices = [
            50 => 500,
            100 => 900,
            200 => 1600,
            500 => 3500,
            1000 => 6000
        ];

        return $prices[$credits] ?? ($credits * 10); // Default price: ৳10 per credit
    }

    /**
     * Get SMS usage statistics
     */
    public function getStats()
    {
        $owner = auth()->user()->owner;
        $stats = $this->ownerSmsService->getOwnerSmsStats($owner->id);

        return response()->json($stats);
    }
} 