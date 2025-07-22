<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\TenantLedger;
use App\Models\TenantRent;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    // List all tenants for the authenticated owner
    public function index(Request $request)
    {
        $ownerId = $request->user()->owner->id;
        $tenants = Tenant::where('owner_id', $ownerId)
            ->with(['unit', 'property'])
            ->get();
        $tenantsTransformed = $tenants->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
                'gender' => $tenant->gender,
                'mobile' => $tenant->mobile,
                'alt_mobile' => $tenant->alt_mobile,
                'email' => $tenant->email,
                'nid_number' => $tenant->nid_number,
                'address' => $tenant->address,
                'country' => $tenant->country,
                'total_family_member' => $tenant->total_family_member,
                'occupation' => $tenant->occupation,
                'company_name' => $tenant->company_name,
                'is_driver' => $tenant->is_driver,
                'driver_name' => $tenant->driver_name,
                'unit_id' => $tenant->unit_id,
                'unit_name' => $tenant->unit ? $tenant->unit->name : null,
                'property_id' => $tenant->building_id,
                'property_name' => $tenant->property ? $tenant->property->name : null,
                'status' => $tenant->status,
                'check_in_date' => $tenant->check_in_date,
                'check_out_date' => $tenant->check_out_date,
                'security_deposit' => $tenant->security_deposit,
                'remarks' => $tenant->remarks,
                'family_types' => $tenant->family_types,
                'child_qty' => $tenant->child_qty,
                'city' => $tenant->city,
                'state' => $tenant->state,
                'zip' => $tenant->zip,
                'college_university' => $tenant->college_university,
                'business_name' => $tenant->business_name,
                'frequency' => $tenant->frequency,
                'nid_picture' => $tenant->nid_picture,
            ];
        });
        return response()->json(['tenants' => $tenantsTransformed]);
    }

    // Show a single tenant
    public function show(Request $request, $id)
    {
        $ownerId = $request->user()->owner->id;
        $tenant = Tenant::where('id', $id)
            ->where('owner_id', $ownerId)
            ->with(['unit', 'property'])
            ->firstOrFail();
        return response()->json($tenant);
    }

    // Store a new tenant
    public function store(Request $request)
    {
        // Debug: log all input
        \Log::info('Tenant store request', $request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:tenants,mobile',
            'nid_number' => 'required|string|max:50|unique:tenants,nid_number',
            'total_family_member' => 'required|integer|min:1',
            'property_id' => 'required|exists:properties,id',
            'unit_id' => 'required|exists:units,id',
            'advance_amount' => 'required|numeric|min:0',
            'start_month' => 'required|date',
            'frequency' => 'required|string',
        ]);

        $ownerId = $request->user()->owner->id;

        // Check if unit is available
        $unit = Unit::where('id', $request->unit_id)
            ->whereHas('property', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->firstOrFail();

        if ($unit->status === 'rented') {
            return response()->json(['error' => 'Unit is already rented'], 400);
        }

        DB::beginTransaction();
        try {
            // Create tenant
            $tenant = new Tenant();
            // Name split
            $fullName = $request->name;
            $nameParts = explode(' ', $fullName, 2);
            $tenant->first_name = $nameParts[0] ?? '';
            $tenant->last_name = $nameParts[1] ?? '';
            $tenant->gender = $request->gender;
            $tenant->mobile = $request->phone ?? $request->mobile;
            $tenant->alt_mobile = $request->alt_phone;
            $tenant->email = $request->email;
            $tenant->nid_number = $request->nid_number;
            $tenant->address = $request->street_address;
            $tenant->country = $request->country;
            $tenant->total_family_member = $request->total_family_member;
            $tenant->occupation = $request->occupation;
            $tenant->company_name = $request->company_name;
            $tenant->is_driver = $request->is_driver === 'true' || $request->is_driver === true;
            $tenant->driver_name = $request->driver_name;
            $tenant->building_id = $request->property_id;
            $tenant->unit_id = $request->unit_id;
            $tenant->owner_id = $ownerId;
            $tenant->status = 'active';
            $tenant->check_in_date = $request->start_month;
            $tenant->security_deposit = $request->advance_amount;
            $tenant->remarks = $request->remarks;
            $tenant->family_types = $request->family_types;
            $tenant->child_qty = $request->child_qty;
            $tenant->city = $request->city;
            $tenant->state = $request->state;
            $tenant->zip = $request->zip;
            $tenant->college_university = $request->college_university;
            $tenant->business_name = $request->business_name;
            $tenant->frequency = $request->frequency;

            // Handle NID picture upload
            if ($request->hasFile('nid_picture')) {
                $file = $request->file('nid_picture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/nid_pictures', $fileName);
                $tenant->nid_picture = $fileName;
            }

            $tenant->save();

            // Create TenantRent record
            $tenantRent = new TenantRent();
            $tenantRent->tenant_id = $tenant->id;
            $tenantRent->unit_id = $tenant->unit_id;
            $tenantRent->owner_id = $ownerId;
            $tenantRent->start_month = $request->start_month;
            $tenantRent->due_day = 5; // Default due day
            $tenantRent->advance_amount = $request->advance_amount;
            $tenantRent->frequency = $request->frequency;
            $tenantRent->remarks = $request->remarks;
            $tenantRent->save();

            // Update unit status and assign tenant
            $unit->tenant_id = $tenant->id;
            $unit->status = 'rented';
            $unit->save();

            // Generate Advance Invoice
            $advanceBreakdown = [
                [
                    'name' => 'Security Deposit',
                    'amount' => $request->advance_amount,
                    'type' => 'advance'
                ]
            ];

            $advanceInvoice = new Invoice();
            $advanceInvoice->tenant_id = $tenant->id;
            $advanceInvoice->unit_id = $tenant->unit_id;
            $advanceInvoice->owner_id = $ownerId;
            $advanceInvoice->invoice_type = 'advance';
            $advanceInvoice->amount = $request->advance_amount;
            $advanceInvoice->issue_date = now();
            $advanceInvoice->due_date = $request->start_month;
            $advanceInvoice->rent_month = date('Y-m', strtotime($request->start_month));
            $advanceInvoice->status = 'unpaid';
            $advanceInvoice->description = 'Security Deposit';
            $advanceInvoice->breakdown = json_encode($advanceBreakdown);
            $advanceInvoice->save();

            // Generate First Month Rent Invoice
            $rentBreakdown = [];

            // Add base rent
            if ($unit->rent > 0) {
                $rentBreakdown[] = [
                    'name' => 'Base Rent',
                    'amount' => $unit->rent,
                    'type' => 'rent'
                ];
            }

            // Add unit charges
            if ($unit->charges && $unit->charges->count() > 0) {
                foreach ($unit->charges as $charge) {
                    $rentBreakdown[] = [
                        'name' => $charge->label,
                        'amount' => $charge->amount,
                        'type' => 'charge'
                    ];
                }
            }

            $totalAmount = $unit->rent + ($unit->charges ? $unit->charges->sum('amount') : 0);

            $firstMonthInvoice = new Invoice();
            $firstMonthInvoice->tenant_id = $tenant->id;
            $firstMonthInvoice->unit_id = $tenant->unit_id;
            $firstMonthInvoice->owner_id = $ownerId;
            $firstMonthInvoice->invoice_type = 'rent';
            $firstMonthInvoice->amount = $totalAmount;
            $firstMonthInvoice->issue_date = now();
            $firstMonthInvoice->due_date = $request->start_month;
            $firstMonthInvoice->rent_month = date('Y-m', strtotime($request->start_month));
            $firstMonthInvoice->status = 'unpaid';
            $firstMonthInvoice->description = 'First Month Rent';
            $firstMonthInvoice->breakdown = json_encode($rentBreakdown);
            $firstMonthInvoice->save();

            // Create Ledger Entries
            // Advance Ledger Entry
            $advanceLedger = new TenantLedger();
            $advanceLedger->tenant_id = $tenant->id;
            $advanceLedger->unit_id = $tenant->unit_id;
            $advanceLedger->owner_id = $ownerId;
            $advanceLedger->transaction_type = 'security_deposit';
            $advanceLedger->debit_amount = $request->advance_amount;
            $advanceLedger->credit_amount = 0;
            $advanceLedger->balance = $request->advance_amount; // Initial balance
            $advanceLedger->description = 'Security Deposit';
            $advanceLedger->payment_status = 'pending';
            $advanceLedger->transaction_date = $request->start_month;
            $advanceLedger->save();

            // First Month Rent Ledger Entry
            $rentLedger = new TenantLedger();
            $rentLedger->tenant_id = $tenant->id;
            $rentLedger->unit_id = $tenant->unit_id;
            $rentLedger->owner_id = $ownerId;
            $rentLedger->transaction_type = 'rent_due';
            $rentLedger->debit_amount = $totalAmount;
            $rentLedger->credit_amount = 0;
            $rentLedger->balance = $request->advance_amount + $totalAmount; // Running balance
            $rentLedger->description = 'First Month Rent';
            $rentLedger->payment_status = 'pending';
            $rentLedger->transaction_date = $request->start_month;
            $rentLedger->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'tenant' => $tenant,
                'advance_invoice_id' => $advanceInvoice->id,
                'rent_invoice_id' => $firstMonthInvoice->id,
                'message' => 'Tenant added successfully with invoices generated'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Tenant creation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create tenant: ' . $e->getMessage()], 500);
        }
    }
}
