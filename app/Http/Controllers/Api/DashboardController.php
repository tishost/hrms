<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\TenantLedger;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $ownerId = $request->user()->owner->id;

        // Get total counts
        $totalTenants = Tenant::where('owner_id', $ownerId)->count();
        $totalUnits = Unit::whereHas('property', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->count();
        $totalProperties = Property::where('owner_id', $ownerId)->count();

        // Get financial stats
        $totalRentCollected = Invoice::where('owner_id', $ownerId)
            ->where('status', 'paid')
            ->where('invoice_type', 'rent')
            ->sum('amount');

        $totalDues = Invoice::where('owner_id', $ownerId)
            ->where('status', 'unpaid')
            ->sum('amount');

        // Get unit status counts
        $vacantUnits = Unit::whereHas('property', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'vacant')->count();

        $rentedUnits = Unit::whereHas('property', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'rented')->count();

        return response()->json([
            'stats' => [
                'total_tenants' => $totalTenants,
                'total_units' => $totalUnits,
                'total_properties' => $totalProperties,
                'rent_collected' => $totalRentCollected,
                'total_dues' => $totalDues,
                'vacant_units' => $vacantUnits,
                'rented_units' => $rentedUnits,
            ]
        ]);
    }

    public function getRecentTransactions(Request $request)
    {
        $ownerId = $request->user()->owner->id;

        $recentTransactions = TenantLedger::where('owner_id', $ownerId)
            ->with(['tenant:id,first_name,last_name', 'unit:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($ledger) {
                return [
                    'id' => $ledger->id,
                    'tenant_name' => trim(($ledger->tenant->first_name ?? '') . ' ' . ($ledger->tenant->last_name ?? '')),
                    'unit_name' => $ledger->unit ? $ledger->unit->name : 'N/A',
                    'type' => $ledger->transaction_type,
                    'amount' => $ledger->credit_amount > 0 ? $ledger->credit_amount : $ledger->debit_amount,
                    'status' => $ledger->payment_status,
                    'description' => $ledger->description,
                    'date' => $ledger->created_at->format('Y-m-d'),
                    'is_credit' => $ledger->credit_amount > 0,
                ];
            });

        return response()->json([
            'transactions' => $recentTransactions
        ]);
    }
}
