<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\TenantLedger;
use App\Models\Charge;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Generate Financial Report
     */
    public function financialReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'nullable|in:rent,charges,all'
        ]);

        $ownerId = $request->user()->owner->id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $type = $request->type ?? 'all';

        try {
            // Base query for invoices
            $invoiceQuery = Invoice::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($type !== 'all') {
                $invoiceQuery->where('invoice_type', $type);
            }

            $invoices = $invoiceQuery->get();

            // Calculate totals
            $totalInvoiced = $invoices->sum('amount');
            $totalPaid = $invoices->where('status', 'paid')->sum('amount');
            $totalUnpaid = $invoices->where('status', 'unpaid')->sum('amount');
            $totalPartial = $invoices->where('status', 'partial')->sum('amount');

            // Monthly breakdown
            $monthlyData = $invoices->groupBy(function($invoice) {
                return $invoice->created_at->format('Y-m');
            })->map(function($monthInvoices) {
                return [
                    'total' => $monthInvoices->sum('amount'),
                    'paid' => $monthInvoices->where('status', 'paid')->sum('amount'),
                    'unpaid' => $monthInvoices->where('status', 'unpaid')->sum('amount'),
                    'count' => $monthInvoices->count()
                ];
            });

            // Property-wise breakdown
            $propertyData = $invoices->groupBy('property_id')->map(function($propertyInvoices) {
                $property = $propertyInvoices->first()->property;
                return [
                    'property_name' => $property ? $property->name : 'N/A',
                    'total' => $propertyInvoices->sum('amount'),
                    'paid' => $propertyInvoices->where('status', 'paid')->sum('amount'),
                    'unpaid' => $propertyInvoices->where('status', 'unpaid')->sum('amount'),
                    'count' => $propertyInvoices->count()
                ];
            });

            return response()->json([
                'success' => true,
                'report' => [
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'type' => $type
                    ],
                    'summary' => [
                        'total_invoiced' => $totalInvoiced,
                        'total_paid' => $totalPaid,
                        'total_unpaid' => $totalUnpaid,
                        'total_partial' => $totalPartial,
                        'collection_rate' => $totalInvoiced > 0 ? round(($totalPaid / $totalInvoiced) * 100, 2) : 0
                    ],
                    'monthly_breakdown' => $monthlyData,
                    'property_breakdown' => $propertyData,
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate financial report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Occupancy Report
     */
    public function occupancyReport(Request $request)
    {
        $ownerId = $request->user()->owner->id;

        try {
            // Get all properties with units
            $properties = Property::where('owner_id', $ownerId)
                ->with(['units' => function($query) {
                    $query->with('tenant');
                }])
                ->get();

            $occupancyData = $properties->map(function($property) {
                $totalUnits = $property->units->count();
                $occupiedUnits = $property->units->where('status', 'rented')->count();
                $vacantUnits = $property->units->where('status', 'vacant')->count();

                return [
                    'property_id' => $property->id,
                    'property_name' => $property->name,
                    'total_units' => $totalUnits,
                    'occupied_units' => $occupiedUnits,
                    'vacant_units' => $vacantUnits,
                    'occupancy_rate' => $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 2) : 0,
                    'units' => $property->units->map(function($unit) {
                        return [
                            'unit_id' => $unit->id,
                            'unit_name' => $unit->name,
                            'status' => $unit->status,
                            'tenant_name' => $unit->tenant ?
                                trim(($unit->tenant->first_name ?? '') . ' ' . ($unit->tenant->last_name ?? '')) :
                                'Vacant',
                            'rent_amount' => $unit->rent_amount ?? 0
                        ];
                    })
                ];
            });

            $totalProperties = $properties->count();
            $totalUnits = $properties->sum(function($property) {
                return $property->units->count();
            });
            $totalOccupied = $properties->sum(function($property) {
                return $property->units->where('status', 'rented')->count();
            });
            $overallOccupancyRate = $totalUnits > 0 ? round(($totalOccupied / $totalUnits) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'report' => [
                    'summary' => [
                        'total_properties' => $totalProperties,
                        'total_units' => $totalUnits,
                        'total_occupied' => $totalOccupied,
                        'total_vacant' => $totalUnits - $totalOccupied,
                        'overall_occupancy_rate' => $overallOccupancyRate
                    ],
                    'properties' => $occupancyData,
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate occupancy report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Tenant Report
     */
    public function tenantReport(Request $request)
    {
        $ownerId = $request->user()->owner->id;

        try {
            $tenants = Tenant::where('owner_id', $ownerId)
                ->with(['unit', 'unit.property'])
                ->get();

            $tenantData = $tenants->map(function($tenant) {
                $totalInvoices = Invoice::where('tenant_id', $tenant->id)->count();
                $paidInvoices = Invoice::where('tenant_id', $tenant->id)->where('status', 'paid')->count();
                $unpaidInvoices = Invoice::where('tenant_id', $tenant->id)->where('status', 'unpaid')->count();
                $totalAmount = Invoice::where('tenant_id', $tenant->id)->sum('amount');
                $paidAmount = Invoice::where('tenant_id', $tenant->id)->where('status', 'paid')->sum('amount');

                return [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
                    'phone' => $tenant->phone,
                    'email' => $tenant->email,
                    'property_name' => $tenant->unit && $tenant->unit->property ? $tenant->unit->property->name : 'N/A',
                    'unit_name' => $tenant->unit ? $tenant->unit->name : 'N/A',
                    'rent_amount' => $tenant->unit ? $tenant->unit->rent_amount : 0,
                    'move_in_date' => $tenant->move_in_date,
                    'status' => $tenant->status,
                    'invoice_stats' => [
                        'total_invoices' => $totalInvoices,
                        'paid_invoices' => $paidInvoices,
                        'unpaid_invoices' => $unpaidInvoices,
                        'total_amount' => $totalAmount,
                        'paid_amount' => $paidAmount,
                        'outstanding_amount' => $totalAmount - $paidAmount,
                        'payment_rate' => $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 2) : 0
                    ]
                ];
            });

            $totalTenants = $tenants->count();
            $activeTenants = $tenants->where('status', 'active')->count();
            $inactiveTenants = $tenants->where('status', 'inactive')->count();

            return response()->json([
                'success' => true,
                'report' => [
                    'summary' => [
                        'total_tenants' => $totalTenants,
                        'active_tenants' => $activeTenants,
                        'inactive_tenants' => $inactiveTenants
                    ],
                    'tenants' => $tenantData,
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate tenant report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Transaction Report
     */
    public function transactionReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'nullable|in:rent,charges,payment,all'
        ]);

        $ownerId = $request->user()->owner->id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $type = $request->type ?? 'all';

        try {
            $ledgerQuery = TenantLedger::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['tenant:id,first_name,last_name', 'unit:id,name', 'unit.property:id,name']);

            if ($type !== 'all') {
                $ledgerQuery->where('transaction_type', $type);
            }

            $transactions = $ledgerQuery->orderBy('created_at', 'desc')->get();

            $transactionData = $transactions->map(function($ledger) {
                return [
                    'id' => $ledger->id,
                    'date' => $ledger->created_at->format('Y-m-d'),
                    'tenant_name' => trim(($ledger->tenant->first_name ?? '') . ' ' . ($ledger->tenant->last_name ?? '')),
                    'property_name' => $ledger->unit && $ledger->unit->property ? $ledger->unit->property->name : 'N/A',
                    'unit_name' => $ledger->unit ? $ledger->unit->name : 'N/A',
                    'transaction_type' => $ledger->transaction_type,
                    'description' => $ledger->description,
                    'debit_amount' => $ledger->debit_amount,
                    'credit_amount' => $ledger->credit_amount,
                    'balance' => $ledger->balance,
                    'payment_status' => $ledger->payment_status
                ];
            });

            $totalDebit = $transactions->sum('debit_amount');
            $totalCredit = $transactions->sum('credit_amount');
            $netAmount = $totalCredit - $totalDebit;

            return response()->json([
                'success' => true,
                'report' => [
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'type' => $type
                    ],
                    'summary' => [
                        'total_transactions' => $transactions->count(),
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'net_amount' => $netAmount
                    ],
                    'transactions' => $transactionData,
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate transaction report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available report types
     */
    public function getReportTypes()
    {
        return response()->json([
            'success' => true,
            'report_types' => [
                [
                    'id' => 'financial',
                    'name' => 'Financial Report',
                    'description' => 'Revenue, payments, and financial summary',
                    'endpoint' => '/api/reports/financial',
                    'parameters' => ['start_date', 'end_date', 'type']
                ],
                [
                    'id' => 'occupancy',
                    'name' => 'Occupancy Report',
                    'description' => 'Property and unit occupancy status',
                    'endpoint' => '/api/reports/occupancy',
                    'parameters' => []
                ],
                [
                    'id' => 'tenant',
                    'name' => 'Tenant Report',
                    'description' => 'Tenant information and payment history',
                    'endpoint' => '/api/reports/tenant',
                    'parameters' => []
                ],
                [
                    'id' => 'transaction',
                    'name' => 'Transaction Report',
                    'description' => 'Detailed transaction ledger',
                    'endpoint' => '/api/reports/transaction',
                    'parameters' => ['start_date', 'end_date', 'type']
                ]
            ]
        ]);
    }
}
