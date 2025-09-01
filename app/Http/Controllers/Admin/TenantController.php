<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenant;
use App\Models\Owner;
use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['owner', 'unit', 'property'])
            ->select('tenants.*');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nid_number', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by district
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // Filter by owner
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $tenants = $query->paginate(15);

        // Get filter data
        $owners = Owner::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $statuses = ['active', 'inactive', 'checkout'];

        return view('admin.tenants.index', compact('tenants', 'owners', 'districts', 'statuses'));
    }

    public function show($id)
    {
        $tenant = Tenant::with(['owner', 'unit', 'property', 'rents'])->findOrFail($id);
        return view('admin.tenants.show', compact('tenant'));
    }

    public function export(Request $request)
    {
        $format = $request->get('export', 'csv');
        
        $query = Tenant::with(['owner', 'unit', 'property'])
            ->select('tenants.*');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nid_number', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        $tenants = $query->get();

        if ($format === 'csv') {
            return $this->exportToCsv($tenants);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($tenants);
        }

        return redirect()->back();
    }

    private function exportToCsv($tenants)
    {
        $filename = 'tenants_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tenants) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Name',
                'Mobile No',
                'Owner Name',
                'District',
                'Status',
                'Email',
                'NID Number',
                'Address',
                'Check In Date',
                'Security Deposit'
            ]);

            // CSV data
            foreach ($tenants as $tenant) {
                fputcsv($file, [
                    $tenant->full_name,
                    $tenant->mobile,
                    $tenant->owner->name ?? 'N/A',
                    $tenant->district ?? 'N/A',
                    ucfirst($tenant->status),
                    $tenant->email ?? 'N/A',
                    $tenant->nid_number ?? 'N/A',
                    $tenant->address ?? 'N/A',
                    $tenant->check_in_date ? $tenant->check_in_date->format('Y-m-d') : 'N/A',
                    $tenant->security_deposit ?? '0'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($tenants)
    {
        $data = [
            'tenants' => $tenants,
            'export_date' => now()->format('Y-m-d H:i:s')
        ];

        $pdf = Pdf::loadView('admin.tenants.export-pdf', $data);
        return $pdf->download('tenants_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
