<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Helpers\CountryHelper;


class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = auth()->user()->owner;
        $buildingCount = $owner->properties()->count();
        $unitCount = $owner->properties()->withCount('units')->get()->sum('units_count');
        // Owner-wise tenant count using owner_id
        $tenantCount = \App\Models\Tenant::where('owner_id', $owner->id)->count();
        
        // Get SMS credit information
        $smsCredits = 0;
        $usedSmsCredits = 0;
        $subscription = $owner->subscription;
        if ($subscription) {
            $smsCredits = $subscription->sms_credits ?? 0;
            $usedSmsCredits = $subscription->used_sms_credits ?? 0;
        }
        
        // Get financial data
        $financialData = $this->getFinancialData($owner->id);
        
        $data = [
            'ordersCount' => 1284,
            'ordersGrowth' => 12.5,
            'revenue' => 24780,
            'revenueGrowth' => 8.3,
            'visitorsData' => [1200, 1900, 1700, 2100, 2400, 2200, 2600],
            'customerSegments' => [35, 25, 20, 10, 5, 5]
        ];

        return view('owner.dashboard', compact('buildingCount', 'unitCount', 'tenantCount', 'data', 'smsCredits', 'usedSmsCredits', 'subscription', 'financialData'));
    }
    
    /**
     * Get financial data for owner dashboard
     */
    private function getFinancialData($ownerId)
    {
        // Get all invoices for this owner
        $invoices = \App\Models\Invoice::where('owner_id', $ownerId)->get();
        
        // Calculate monthly received amount (current month)
        $currentMonth = now()->format('Y-m');
        $monthlyReceived = $invoices->where('status', 'Paid')
            ->where('paid_date', '>=', now()->startOfMonth())
            ->where('paid_date', '<=', now()->endOfMonth())
            ->sum('paid_amount');
        
        // Calculate due amount (unpaid invoices)
        $dueAmount = $invoices->where('status', 'Unpaid')
            ->sum('amount');
        
        // Calculate all time paid amount
        $allTimePaid = $invoices->where('status', 'Paid')
            ->sum('paid_amount');
        
        // Calculate total invoiced amount
        $totalInvoiced = $invoices->sum('amount');
        
        // Calculate partial payments
        $partialAmount = $invoices->where('status', 'Partial')
            ->sum('paid_amount');
        
        // Calculate pending amount (due + partial remaining)
        $pendingAmount = $dueAmount + $invoices->where('status', 'Partial')
            ->map(function($invoice) {
                return $invoice->amount - $invoice->paid_amount;
            })->sum();
        
        return [
            'monthly_received' => $monthlyReceived,
            'due_amount' => $dueAmount,
            'all_time_paid' => $allTimePaid,
            'total_invoiced' => $totalInvoiced,
            'partial_amount' => $partialAmount,
            'pending_amount' => $pendingAmount,
            'total_invoices' => $invoices->count(),
            'paid_invoices' => $invoices->where('status', 'Paid')->count(),
            'unpaid_invoices' => $invoices->where('status', 'Unpaid')->count(),
            'partial_invoices' => $invoices->where('status', 'Partial')->count(),
        ];
    }
}
