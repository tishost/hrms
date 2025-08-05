<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\SystemHelper;

class FinancialReportController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Monthly Revenue Report
        $monthlyRevenue = $this->getMonthlyRevenue();
        
        // Property Performance
        $propertyPerformance = $this->getPropertyPerformance();
        
        // Payment Statistics
        $paymentStats = $this->getPaymentStatistics();
        
        // Revenue Growth
        $revenueGrowth = $this->getRevenueGrowth();
        
        // Top Performing Properties
        $topProperties = $this->getTopPerformingProperties();
        
        // Payment Methods Analysis
        $paymentMethods = $this->getPaymentMethodsAnalysis();
        
        return view('admin.reports.financial.index', compact(
            'monthlyRevenue',
            'propertyPerformance',
            'paymentStats',
            'revenueGrowth',
            'topProperties',
            'paymentMethods'
        ));
    }

    /**
     * Get monthly revenue data
     */
    private function getMonthlyRevenue()
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Billing::where('status', 'paid')
                ->whereYear('paid_date', $month->year)
                ->whereMonth('paid_date', $month->month)
                ->sum('amount');
            
            $months->push([
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
                'year' => $month->year,
                'month_num' => $month->month
            ]);
        }
        
        return $months;
    }

    /**
     * Get property performance data
     */
    private function getPropertyPerformance()
    {
        return Property::with(['units.tenant', 'owner'])
            ->withCount(['units as total_units'])
            ->withCount(['units as occupied_units' => function($query) {
                $query->whereHas('tenant');
            }])
            ->get()
            ->map(function($property) {
                $totalRevenue = Billing::whereHas('subscription', function($query) use ($property) {
                    $query->where('owner_id', $property->owner_id);
                })->where('status', 'paid')->sum('amount');
                
                $occupancyRate = $property->total_units > 0 
                    ? round(($property->occupied_units / $property->total_units) * 100, 2)
                    : 0;
                
                return [
                    'property' => $property,
                    'total_revenue' => $totalRevenue,
                    'occupancy_rate' => $occupancyRate,
                    'monthly_rent' => $property->units->sum('rent')
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStatistics()
    {
        $currentMonth = Carbon::now();
        
        return [
            'total_revenue' => Billing::where('status', 'paid')->sum('amount'),
            'monthly_revenue' => Billing::where('status', 'paid')
                ->whereYear('paid_date', $currentMonth->year)
                ->whereMonth('paid_date', $currentMonth->month)
                ->sum('amount'),
            'pending_amount' => Billing::where('status', 'pending')->sum('amount'),
            'failed_amount' => Billing::where('status', 'failed')->sum('amount'),
            'total_transactions' => Billing::count(),
            'successful_transactions' => Billing::where('status', 'paid')->count(),
            'pending_transactions' => Billing::where('status', 'pending')->count(),
            'failed_transactions' => Billing::where('status', 'failed')->count(),
        ];
    }

    /**
     * Get revenue growth data
     */
    private function getRevenueGrowth()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        $currentRevenue = Billing::where('status', 'paid')
            ->whereYear('paid_date', $currentMonth->year)
            ->whereMonth('paid_date', $currentMonth->month)
            ->sum('amount');
            
        $lastMonthRevenue = Billing::where('status', 'paid')
            ->whereYear('paid_date', $lastMonth->year)
            ->whereMonth('paid_date', $lastMonth->month)
            ->sum('amount');
        
        $growthRate = $lastMonthRevenue > 0 
            ? round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2)
            : 0;
        
        return [
            'current_revenue' => $currentRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'growth_rate' => $growthRate,
            'growth_amount' => $currentRevenue - $lastMonthRevenue
        ];
    }

    /**
     * Get top performing properties
     */
    private function getTopPerformingProperties()
    {
        return Property::with(['owner.user', 'units'])
            ->get()
            ->map(function($property) {
                $revenue = Billing::whereHas('subscription', function($query) use ($property) {
                    $query->where('owner_id', $property->owner_id);
                })->where('status', 'paid')->sum('amount');
                
                $occupancyRate = $property->units->count() > 0 
                    ? round(($property->units->where('tenant_id', '!=', null)->count() / $property->units->count()) * 100, 2)
                    : 0;
                
                return [
                    'property' => $property,
                    'revenue' => $revenue,
                    'occupancy_rate' => $occupancyRate,
                    'total_units' => $property->units->count(),
                    'occupied_units' => $property->units->where('tenant_id', '!=', null)->count()
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);
    }

    /**
     * Get payment methods analysis
     */
    private function getPaymentMethodsAnalysis()
    {
        return Billing::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->get()
            ->map(function($item) {
                $totalAmount = Billing::where('status', 'paid')->sum('amount');
                $percentage = $totalAmount > 0 ? round(($item->total_amount / $totalAmount) * 100, 2) : 0;
                
                return [
                    'method' => $item->payment_method ?? 'Unknown',
                    'count' => $item->count,
                    'amount' => $item->total_amount,
                    'percentage' => $percentage
                ];
            });
    }

    /**
     * Export financial report to PDF
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->input('type', 'monthly');
        $dateRange = $request->input('date_range', 'current_month');
        
        $data = $this->getReportData($reportType, $dateRange);
        
        // Generate PDF using a library like DomPDF
        // For now, return JSON response
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Report generated successfully'
        ]);
    }

    /**
     * Export financial report to Excel
     */
    public function exportExcel(Request $request)
    {
        $reportType = $request->input('type', 'monthly');
        $dateRange = $request->input('date_range', 'current_month');
        
        $data = $this->getReportData($reportType, $dateRange);
        
        // Generate Excel using a library like PhpSpreadsheet
        // For now, return JSON response
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Report exported successfully'
        ]);
    }

    /**
     * Get report data based on type and date range
     */
    private function getReportData($type, $dateRange)
    {
        switch ($type) {
            case 'monthly':
                return $this->getMonthlyRevenue();
            case 'property_performance':
                return $this->getPropertyPerformance();
            case 'payment_statistics':
                return $this->getPaymentStatistics();
            case 'revenue_growth':
                return $this->getRevenueGrowth();
            case 'top_properties':
                return $this->getTopPerformingProperties();
            case 'payment_methods':
                return $this->getPaymentMethodsAnalysis();
            default:
                return [];
        }
    }
} 