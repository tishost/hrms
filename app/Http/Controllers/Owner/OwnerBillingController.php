<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Billing;
use Carbon\Carbon;

class OwnerBillingController extends Controller
{
    /**
     * Get owner billing overview
     */
    public function getBillingOverview(Request $request)
    {
        try {
            $owner = Auth::user();
            
            // Get total paid amount
            $totalPaid = Billing::where('owner_id', $owner->id)
                ->where('status', 'paid')
                ->sum('net_amount');
            
            // Get total pending amount
            $totalPending = Billing::where('owner_id', $owner->id)
                ->whereIn('status', ['pending', 'unpaid'])
                ->sum('net_amount');
            
            // Get total overdue amount
            $totalOverdue = Billing::where('owner_id', $owner->id)
                ->where('status', 'unpaid')
                ->where('due_date', '<', Carbon::now())
                ->sum('net_amount');
            
            $overview = [
                'total_paid' => number_format($totalPaid, 2),
                'total_pending' => number_format($totalPending, 2),
                'total_overdue' => number_format($totalOverdue, 2),
            ];
            
            return response()->json([
                'success' => true,
                'overview' => $overview
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load billing overview: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get owner payment history
     */
    public function getPaymentHistory(Request $request)
    {
        try {
            $owner = Auth::user();
            
            $payments = Billing::where('owner_id', $owner->id)
                ->where('status', 'paid')
                ->with(['subscription'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => number_format($payment->net_amount, 2),
                        'payment_method' => $payment->paymentMethod->name ?? 'Unknown',
                        'status' => $payment->status,
                        'payment_date' => $payment->paid_date ? Carbon::parse($payment->paid_date)->format('Y-m-d') : $payment->created_at->format('Y-m-d'),
                        'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                        'subscription_plan' => $payment->subscription->subscriptionPlan->name ?? 'N/A',
                    ];
                });
            
            return response()->json([
                'success' => true,
                'payments' => $payments
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load payment history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get owner subscription invoices with filters
     */
    public function getSubscriptionInvoices(Request $request)
    {
        try {
            $owner = Auth::user();
            $status = $request->get('status', 'all');
            
            $query = Billing::where('owner_id', $owner->id)
                ->with(['subscription.subscriptionPlan']);
            
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            $invoices = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'net_amount' => number_format($invoice->net_amount, 2),
                        'status' => $invoice->status,
                        'due_date' => $invoice->due_date ? Carbon::parse($invoice->due_date)->format('Y-m-d') : null,
                        'plan_name' => $invoice->subscription->subscriptionPlan->name ?? 'Subscription',
                        'created_at' => $invoice->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            
            return response()->json([
                'success' => true,
                'invoices' => $invoices
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscription invoices: ' . $e->getMessage()
            ], 500);
        }
    }
}
