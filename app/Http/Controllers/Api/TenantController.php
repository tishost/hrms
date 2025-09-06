<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Get properties for the authenticated owner
     */
    public function getProperties()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $properties = Property::where('owner_id', $user->owner_id)
                ->select('id', 'name', 'address')
                ->get();

            return response()->json([
                'success' => true,
                'properties' => $properties
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching properties: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch properties'
            ], 500);
        }
    }

    /**
     * Get units for a specific property
     */
    public function getUnitsByProperty($propertyId)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            // Verify property belongs to the owner
            $property = Property::where('id', $propertyId)
                ->where('owner_id', $user->owner_id)
                ->first();

            if (!$property) {
                return response()->json([
                    'error' => 'Property not found'
                ], 404);
            }

            // Get available units (not assigned to any tenant)
            $units = Unit::where('property_id', $propertyId)
                ->whereDoesntHave('tenant')
                ->select('id', 'name', 'floor', 'rent')
                ->get();

            return response()->json([
                'success' => true,
                'units' => $units,
                'property' => [
                    'id' => $property->id,
                    'name' => $property->name,
                    'address' => $property->address
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching units: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch units'
            ], 500);
        }
    }

    /**
     * Get all properties with their units for tenant entry form
     */
    public function getPropertiesWithUnits()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $properties = Property::where('owner_id', $user->owner_id)
                ->with(['units' => function($query) {
                    $query->whereDoesntHave('tenant')
                        ->select('id', 'property_id', 'name', 'floor', 'rent');
                }])
                ->select('id', 'name', 'address')
                ->get();

            return response()->json([
                'success' => true,
                'properties' => $properties
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching properties with units: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch properties with units'
            ], 500);
        }
    }

    /**
     * Get tenant dashboard data
     */
    public function getDashboard()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->tenant_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $tenant = Tenant::with(['unit.property', 'rents'])
                ->find($user->tenant_id);

            if (!$tenant) {
                return response()->json([
                    'error' => 'Tenant not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'tenant' => $tenant
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching tenant dashboard: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch dashboard data'
            ], 500);
        }
    }

    /**
     * Get all tenants for the authenticated owner
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            \Log::info('Tenant API - User: ' . ($user ? $user->email : 'null'));
            \Log::info('Tenant API - User owner_id: ' . ($user ? $user->owner_id : 'null'));

            // Get owner from user
            $owner = \App\Models\Owner::where('user_id', $user->id)->first();

            if (!$user || !$owner) {
                \Log::error('Tenant API - Unauthorized access. User: ' . ($user ? $user->email : 'null') . ', owner: ' . ($owner ? $owner->id : 'null'));
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            // Build query
            $query = Tenant::whereHas('unit.property', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })->with(['unit.property']);

            // Apply status filter
            if ($request->has('status') && $request->status !== 'all') {
                $status = $request->status;
                if ($status === 'active') {
                    $query->where('status', 'active');
                } elseif ($status === 'inactive') {
                    $query->whereIn('status', ['inactive', 'checked_out']);
                } elseif ($status === 'pending') {
                    $query->whereIn('status', ['pending', 'pending_approval']);
                }
            }

            // Apply property filter
            if ($request->has('property') && $request->property !== 'all') {
                $query->whereHas('unit.property', function($query) use ($request) {
                    $query->where('name', $request->property);
                });
            }

            $tenants = $query->get()->map(function($tenant) {
                // Calculate total rent (base rent + charges)
                $baseRent = $tenant->unit->rent ?? 0;
                $totalCharges = 0;
                
                if ($tenant->unit && $tenant->unit->charges) {
                    foreach ($tenant->unit->charges as $charge) {
                        $totalCharges += $charge->amount ?? 0;
                    }
                }
                
                $totalRent = $baseRent + $totalCharges;
                
                // Calculate due balance
                $dueBalance = 0;
                $invoices = \App\Models\Invoice::where('tenant_id', $tenant->id)
                    ->where('status', 'unpaid')
                    ->get();
                
                foreach ($invoices as $invoice) {
                    $dueBalance += $invoice->total_amount ?? 0;
                }
                
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'mobile' => $tenant->mobile,
                    'email' => $tenant->email,
                    'property_name' => $tenant->unit->property->name ?? 'No Property',
                    'unit_name' => $tenant->unit->name ?? 'No Unit',
                    'rent' => $baseRent,
                    'total_rent' => $totalRent,
                    'due_balance' => $dueBalance,
                    'status' => $tenant->status ?? 'active',
                    'created_at' => $tenant->created_at,
                ];
            });

            return response()->json([
                'tenants' => $tenants
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching tenants: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch tenants'
            ], 500);
        }
    }

    /**
     * Test endpoint for debugging
     */
    public function testEndpoint()
    {
        return response()->json([
            'success' => true,
            'message' => 'Tenant API is working',
            'user' => Auth::user()
        ]);
    }

    /**
     * Show tenant details for editing
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $tenant = Tenant::whereHas('unit.property', function($query) use ($user) {
                $query->where('owner_id', $user->owner_id);
            })
            ->with(['unit.property'])
            ->find($id);

            if (!$tenant) {
                return response()->json([
                    'error' => 'Tenant not found'
                ], 404);
            }

            // Calculate rent information
            $baseRent = $tenant->unit->rent ?? 0;
            $totalCharges = 0;
            $cleaningCharges = 0;
            $otherCharges = 0;
            $unitCharges = [];
            
            if ($tenant->unit && $tenant->unit->charges) {
                foreach ($tenant->unit->charges as $charge) {
                    $chargeAmount = $charge->amount ?? 0;
                    $totalCharges += $chargeAmount;
                    
                    // Add individual charge details
                    $unitCharges[] = [
                        'label' => $charge->label ?? 'Unknown',
                        'amount' => $chargeAmount,
                    ];
                    
                    // Categorize charges based on label
                    $label = strtolower($charge->label ?? '');
                    if (strpos($label, 'cleaning') !== false) {
                        $cleaningCharges += $chargeAmount;
                    } else {
                        $otherCharges += $chargeAmount;
                    }
                }
            }
            
            $totalRent = $baseRent + $totalCharges;
            
            // Calculate due balance
            $dueBalance = 0;
            $invoices = \App\Models\Invoice::where('tenant_id', $tenant->id)
                ->where('status', 'unpaid')
                ->get();
            
            foreach ($invoices as $invoice) {
                $dueBalance += $invoice->total_amount ?? 0;
            }

            $responseData = [
                'tenant' => [
                    'id' => $tenant->id,
                    'first_name' => $tenant->first_name,
                    'last_name' => $tenant->last_name,
                    'gender' => $tenant->gender,
                    'mobile' => $tenant->mobile,
                    'alt_mobile' => $tenant->alt_mobile,
                    'email' => $tenant->email,
                    'nid_number' => $tenant->nid_number,
                    'address' => $tenant->address,
                    'city' => $tenant->city,
                    'state' => $tenant->state,
                    'zip' => $tenant->zip,
                    'country' => $tenant->country,
                    'district' => $tenant->district,
                    'upazila' => $tenant->upazila,
                    'occupation' => $tenant->occupation,
                    'company_name' => $tenant->company_name,
                    'college_university' => $tenant->college_university,
                    'business_name' => $tenant->business_name,
                    'is_driver' => $tenant->is_driver,
                    'driver_name' => $tenant->driver_name,
                    'family_types' => $tenant->family_types,
                    'child_qty' => $tenant->child_qty,
                    'total_family_member' => $tenant->total_family_member,
                    'security_deposit' => $tenant->security_deposit,
                    'start_month' => $tenant->check_in_date ? $tenant->check_in_date->format('m-Y') : null,
                    'frequency' => $tenant->frequency,
                    'remarks' => $tenant->remarks,
                    'nid_front_picture' => $tenant->nid_front_picture,
                    'nid_back_picture' => $tenant->nid_back_picture,
                    'rent' => $baseRent,
                    'total_rent' => $totalRent,
                    'due_balance' => $dueBalance,
                    'cleaning_charges' => $cleaningCharges,
                    'other_charges' => $otherCharges,
                    'unit_charges' => $unitCharges,
                    'property_name' => $tenant->unit->property->name ?? 'No Property',
                    'unit_name' => $tenant->unit->name ?? 'No Unit',
                    'unit' => [
                        'id' => $tenant->unit->id,
                        'name' => $tenant->unit->name,
                        'unit_number' => $tenant->unit->unit_number,
                        'unit_type' => $tenant->unit->unit_type,
                        'floor' => $tenant->unit->floor,
                        'rent_amount' => $tenant->unit->rent_amount,
                        'description' => $tenant->unit->description,
                    ],
                    'property' => [
                        'id' => $tenant->unit->property->id,
                        'name' => $tenant->unit->property->name,
                        'address' => $tenant->unit->property->address,
                        'city' => $tenant->unit->property->city,
                        'state' => $tenant->unit->property->state,
                        'zip' => $tenant->unit->property->zip,
                        'country' => $tenant->unit->property->country,
                    ]
                ]
            ];

            \Log::info('Tenant show response data:', $responseData);
            \Log::info('Driver info - is_driver: ' . ($tenant->is_driver ? 'true' : 'false') . ', driver_name: ' . ($tenant->driver_name ?? 'null'));
            \Log::info('NID Images - Front: ' . ($tenant->nid_front_picture ?? 'null') . ', Back: ' . ($tenant->nid_back_picture ?? 'null'));

            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error('Error fetching tenant details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch tenant details'
            ], 500);
        }
    }

    /**
     * Get outstanding amount for a tenant
     */
    public function getOutstandingAmount($id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $tenant = Tenant::whereHas('unit.property', function($query) use ($user) {
                $query->where('owner_id', $user->owner_id);
            })->find($id);

            if (!$tenant) {
                return response()->json([
                    'error' => 'Tenant not found'
                ], 404);
            }

                        // Get all due bills (unpaid invoices and partial payments)
            $dueBills = [];

                        // Get unpaid invoices with details
            $unpaidInvoices = \DB::table('invoices')
                ->where('tenant_id', $id)
                ->where('unit_id', $tenant->unit_id)
                ->where('owner_id', $user->owner_id)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->get();

            \Log::info("Found " . $unpaidInvoices->count() . " unpaid invoices for tenant $id");

            foreach ($unpaidInvoices as $invoice) {
                \Log::info("Invoice: " . $invoice->invoice_number . " - Amount: " . $invoice->amount);
                $dueBills[] = [
                    'type' => 'invoice',
                    'description' => 'Rent Invoice - ' . $invoice->invoice_number,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->amount,
                    'due_date' => $invoice->due_date,
                ];
            }

                        // Get partial payments with details
            $partialPayments = \DB::table('rent_payments')
                ->where('tenant_id', $id)
                ->where('unit_id', $tenant->unit_id)
                ->where('owner_id', $user->owner_id)
                ->where('status', 'Partial')
                ->get();

            \Log::info("Found " . $partialPayments->count() . " partial payments for tenant $id");

            foreach ($partialPayments as $payment) {
                $remainingAmount = $payment->amount_due - $payment->amount_paid;
                \Log::info("Partial Payment: Amount Due: " . $payment->amount_due . " - Amount Paid: " . $payment->amount_paid . " - Remaining: " . $remainingAmount);
                if ($remainingAmount > 0) {
                    $dueBills[] = [
                        'type' => 'partial_payment',
                        'description' => 'Partial Payment - ' . $payment->payment_date,
                        'invoice_number' => 'N/A',
                        'amount' => $remainingAmount,
                        'due_date' => $payment->payment_date,
                    ];
                }
            }

            // Calculate total outstanding
            $totalOutstanding = collect($dueBills)->sum('amount');

            \Log::info("Outstanding calculation for tenant $id:");
            \Log::info("Total due bills: " . count($dueBills));
            \Log::info("Total outstanding: $totalOutstanding");
            \Log::info("Due bills data: " . json_encode($dueBills));

            return response()->json([
                'success' => true,
                'outstanding_amount' => $totalOutstanding,
                'due_bills' => $dueBills
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching outstanding amount: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch outstanding amount'
            ], 500);
        }
    }

    /**
     * Store a new tenant
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Tenant store method called');
            \Log::info('Request headers: ' . json_encode($request->headers->all()));
            \Log::info('Request data: ' . json_encode($request->all()));

            $user = Auth::user();

            \Log::info('Auth::user() result: ' . ($user ? 'User found with ID: ' . $user->id : 'No user found'));
            \Log::info('User owner_id: ' . ($user ? $user->owner_id : 'null'));

            if (!$user || !$user->owner_id) {
                \Log::error('Unauthorized access - User: ' . ($user ? $user->id : 'null') . ', Owner ID: ' . ($user ? $user->owner_id : 'null'));
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            // Validate request (prevent duplicate mobile under same owner only on create)
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'gender' => 'nullable|string|max:20',
                'mobile' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('tenants', 'mobile')->where(function ($q) use ($user) {
                        return $q->where('owner_id', $user->owner_id);
                    }),
                ],
                'alt_mobile' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'nid_number' => 'required|string|max:50',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'upazila' => 'nullable|string|max:100',
                'occupation' => 'nullable|string|max:100',
                'company_name' => 'nullable|string|max:255',
                'college_university' => 'nullable|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'is_driver' => 'nullable|in:true,false,0,1',
                'driver_name' => 'nullable|string|max:255',
                'family_types' => 'nullable|string|max:500',
                'child_qty' => 'nullable|integer|min:0',
                'total_family_member' => 'nullable|integer|min:1',
                'property_id' => 'required|exists:properties,id',
                'unit_id' => 'required|exists:units,id',
                'advance_amount' => 'nullable|numeric|min:0',
                'cleaning_charges' => 'nullable|numeric|min:0',
                'other_charges' => 'nullable|numeric|min:0',
                'start_month' => 'nullable|date_format:m-Y',
                'frequency' => 'nullable|string|max:50',
                'remarks' => 'nullable|string|max:1000',
                'nid_front_picture' => 'nullable|string|max:255',
                'nid_back_picture' => 'nullable|string|max:255',
            ], [
                'mobile.unique' => 'This mobile number is already registered under your account.',
            ]);

            // Check if unit is available
            $unit = Unit::where('id', $request->unit_id)
                ->where('property_id', $request->property_id)
                ->whereDoesntHave('tenant')
                ->first();

            if (!$unit) {
                return response()->json([
                    'error' => 'Unit is not available or already assigned'
                ], 400);
            }

            // Handle NID front image path (from upload)
            $nidFrontPicturePath = $request->input('nid_front_picture');

            // Handle NID back image path (from upload)
            $nidBackPicturePath = $request->input('nid_back_picture');

            // Create tenant
            $tenant = Tenant::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'gender' => $request->gender ?? 'Male',
                'mobile' => $request->mobile,
                'alt_mobile' => $request->alt_mobile ?? null,
                'email' => $request->email ?? null,
                'nid_number' => $request->nid_number,
                'address' => $request->address ?? 'Not Specified',
                'city' => $request->city ?? 'Not Specified',
                'state' => $request->state ?? 'Not Specified',
                'zip' => $request->zip ?? 'Not Specified',
                'country' => $request->country ?? 'Bangladesh',
                'district' => $request->district ?? null,
                'upazila' => $request->upazila ?? null,
                'occupation' => $request->occupation ?? 'Not Specified',
                'company_name' => $request->company_name ?? null,
                'college_university' => $request->college_university ?? null,
                'business_name' => $request->business_name ?? null,
                'is_driver' => $request->is_driver === 'true' || $request->is_driver === true || $request->is_driver === '1' || $request->is_driver === 1,
                'driver_name' => $request->driver_name ?? null,
                'family_types' => $request->family_types ?? 'Not Specified',
                'child_qty' => $request->child_qty ?? 0,
                'total_family_member' => $request->total_family_member ?? 1,
                'building_id' => $request->property_id,
                'unit_id' => $request->unit_id,
                'security_deposit' => $request->advance_amount ?? 0,
                'cleaning_charges' => $request->cleaning_charges ?? 0,
                'other_charges' => $request->other_charges ?? 0,
                'check_in_date' => $request->start_month ? \Carbon\Carbon::createFromFormat('m-Y', $request->start_month)->startOfMonth() : null,
                'frequency' => $request->frequency ?? null,
                'remarks' => $request->remarks ?? null,
                'nid_front_picture' => $nidFrontPicturePath,
                'nid_back_picture' => $nidBackPicturePath,
                'status' => 'Active',
                'owner_id' => $user->owner_id,
            ]);

            // Assign unit to tenant and update status
            $unit->update([
                'tenant_id' => $tenant->id,
                'status' => 'rented'
            ]);

            // Generate invoices for the tenant
            $this->generateInitialInvoices($tenant, $unit, $request->start_month);

            // Generate ledger transactions
            $this->generateLedgerTransactions($tenant, $unit, $request->start_month);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully with invoices',
                'tenant' => $tenant
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating tenant: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create tenant'
            ], 500);
        }
    }

        /**
     * Generate initial invoices for new tenant
     */
    private function generateInitialInvoices($tenant, $unit, $startMonth)
    {
        try {
            // Get unit charges and rent
            $charges = $unit->charges ?? collect();
            $totalRent = $unit->rent ?? 0;

            // Calculate total amount including rent and charges
            $totalAmount = $totalRent;
            foreach ($charges as $charge) {
                $totalAmount += $charge->amount ?? 0;
            }

            // 1. Generate Advance Payment Invoice (if advance_amount > 0)
            if ($tenant->security_deposit > 0) {
                $advanceBreakdown = [
                    [
                        'name' => 'Security Deposit',
                        'amount' => $tenant->security_deposit,
                        'type' => 'advance'
                    ]
                ];

                $advanceInvoice = \App\Models\Invoice::create([
                    'owner_id' => $tenant->owner_id,
                    'tenant_id' => $tenant->id,
                    'unit_id' => $unit->id,
                    'type' => 'advance',
                    'amount' => $tenant->security_deposit,
                    'paid_amount' => 0,
                    'status' => 'Unpaid',
                    'issue_date' => now(),
                    'due_date' => now(),
                    'notes' => 'Advance payment on unit assignment',
                    'breakdown' => json_encode($advanceBreakdown),
                    'rent_month' => $startMonth ? \Carbon\Carbon::createFromFormat('m-Y', $startMonth)->format('Y-m') : now()->format('Y-m'),
                ]);

                \Log::info("Generated advance invoice for tenant {$tenant->id}: Invoice ID {$advanceInvoice->id}");
            }

            // 2. Generate First Month Rent Invoice
            $currentMonth = $startMonth ? \Carbon\Carbon::createFromFormat('m-Y', $startMonth) : now();

            // Prepare breakdown for fees
            $breakdown = [];

            // Add base rent
            if ($totalRent > 0) {
                $breakdown[] = [
                    'name' => 'Base Rent',
                    'amount' => $totalRent,
                    'type' => 'rent'
                ];
            }

            // Add unit charges
            if ($charges->count() > 0) {
                foreach ($charges as $charge) {
                    $breakdown[] = [
                        'name' => $charge->label ?? $charge->name ?? 'Charge',
                        'amount' => $charge->amount ?? 0,
                        'type' => 'charge'
                    ];
                }
            }

            $rentInvoice = \App\Models\Invoice::create([
                'owner_id' => $tenant->owner_id,
                'tenant_id' => $tenant->id,
                'unit_id' => $unit->id,
                'type' => 'rent',
                'amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'Unpaid',
                'issue_date' => now(),
                'due_date' => $currentMonth->endOfMonth(),
                'notes' => 'First month rent invoice on unit assignment',
                'breakdown' => json_encode($breakdown),
                'rent_month' => $currentMonth->format('Y-m'),
            ]);

            \Log::info("Generated rent invoice for tenant {$tenant->id}: Invoice ID {$rentInvoice->id}");

        } catch (\Exception $e) {
            \Log::error('Error generating invoices: ' . $e->getMessage());
        }
    }

    /**
     * Generate ledger transactions for new tenant
     */
    private function generateLedgerTransactions($tenant, $unit, $startMonth)
    {
        try {
            // Get unit charges and rent
            $charges = $unit->charges ?? collect();
            $totalRent = $unit->rent ?? 0;

            // Calculate total amount including rent and charges
            $totalAmount = $totalRent;
            foreach ($charges as $charge) {
                $totalAmount += $charge->amount ?? 0;
            }

            // 1. Ledger entry for advance payment (if security_deposit > 0)
            if ($tenant->security_deposit > 0) {
                \App\Http\Controllers\TenantLedgerController::log([
                    'tenant_id'        => $tenant->id,
                    'unit_id'          => $unit->id,
                    'owner_id'         => $tenant->owner_id,
                    'transaction_type' => 'security_deposit',
                    'reference_type'   => 'tenant_creation',
                    'reference_id'     => $tenant->id,
                    'invoice_number'   => null,
                    'debit_amount'     => $tenant->security_deposit,
                    'credit_amount'    => 0,
                    'description'      => 'Security deposit on tenant registration',
                    'transaction_date' => now(),
                    'payment_status'   => 'pending',
                ]);
            }

            // 2. Ledger entry for first month rent
            \App\Http\Controllers\TenantLedgerController::log([
                'tenant_id'        => $tenant->id,
                'unit_id'          => $unit->id,
                'owner_id'         => $tenant->owner_id,
                'transaction_type' => 'rent_due',
                'reference_type'   => 'tenant_creation',
                'reference_id'     => $tenant->id,
                'invoice_number'   => null,
                'debit_amount'     => $totalAmount,
                'credit_amount'    => 0,
                'description'      => 'First month rent and charges due',
                'transaction_date' => now(),
                'payment_status'   => 'pending',
            ]);

            \Log::info("Generated ledger transactions for tenant {$tenant->id}");

        } catch (\Exception $e) {
            \Log::error('Error generating ledger transactions: ' . $e->getMessage());
        }
    }

    /**
     * Update tenant information
     */
    public function update(Request $request, $id)
    {
        try {
            // Debug: Log all request data
            \Log::info('Update Request Data:', $request->all());
            \Log::info('Update Request Headers:', $request->headers->all());
            \Log::info('Update Request Method: ' . $request->method(), []);
            \Log::info('Update Request URL: ' . $request->url(), []);
            \Log::info('Update Request ID: ' . $id, []);

            $user = Auth::user();
            if (!$user || !$user->owner_id) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }

            // Find tenant and verify ownership
            $tenant = Tenant::where('id', $id)
                ->where('owner_id', $user->owner_id)
                ->first();

            if (!$tenant) {
                return response()->json([
                    'error' => 'Tenant not found'
                ], 404);
            }

            // Debug: Check specific required fields
            \Log::info('First Name: ' . ($request->first_name ?? 'NULL'), []);
            \Log::info('Gender: ' . ($request->gender ?? 'NULL'), []);
            \Log::info('Mobile: ' . ($request->mobile ?? 'NULL'), []);
            \Log::info('Total Family Member: ' . ($request->total_family_member ?? 'NULL'), []);
            \Log::info('Building ID: ' . ($request->building_id ?? 'NULL'), []);
            \Log::info('Unit ID: ' . ($request->unit_id ?? 'NULL'), []);
            \Log::info('Security Deposit: ' . ($request->security_deposit ?? 'NULL'), []);
            \Log::info('Check In Date: ' . ($request->check_in_date ?? 'NULL'), []);

            // Validate request
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'gender' => 'required|string|in:Male,Female,Other',
                'mobile' => 'required|string|max:20',
                'alt_mobile' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'nid_number' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'upazila' => 'nullable|string|max:100',
                'total_family_member' => 'required|integer|min:1',
                'occupation' => 'nullable|string|max:100',
                'company_name' => 'nullable|string|max:255',
                'college_university' => 'nullable|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'is_driver' => 'nullable|in:true,false,0,1',
                'driver_name' => 'nullable|string|max:255',
                'family_types' => 'nullable|string|max:500',
                'child_qty' => 'integer|min:0',
                'building_id' => 'required|exists:properties,id',
                'unit_id' => 'required|exists:units,id',
                'security_deposit' => 'required|numeric|min:0',
                'cleaning_charges' => 'nullable|numeric|min:0',
                'other_charges' => 'nullable|numeric|min:0',
                'check_in_date' => 'required|date',
                'frequency' => 'nullable|string|max:50',
                'remarks' => 'nullable|string|max:1000',
                'nid_front_picture' => 'nullable|string|max:255',
                'nid_back_picture' => 'nullable|string|max:255',
            ]);

            // Handle NID front image path (from upload)
            $nidFrontPicturePath = $request->input('nid_front_picture') ?? $tenant->nid_front_picture;

            // Handle NID back image path (from upload)
            $nidBackPicturePath = $request->input('nid_back_picture') ?? $tenant->nid_back_picture;

            // Update tenant
            $tenant->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'gender' => $request->gender,
                'mobile' => $request->mobile,
                'alt_mobile' => $request->alt_mobile,
                'email' => $request->email,
                'nid_number' => $request->nid_number,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country' => $request->country,
                'district' => $request->district,
                'upazila' => $request->upazila,
                'total_family_member' => $request->total_family_member,
                'occupation' => $request->occupation,
                'company_name' => $request->company_name,
                'college_university' => $request->college_university,
                'business_name' => $request->business_name,
                'is_driver' => $request->is_driver === 'true' || $request->is_driver === true || $request->is_driver === '1' || $request->is_driver === 1,
                'driver_name' => $request->driver_name,
                'family_types' => $request->family_types,
                'child_qty' => $request->child_qty ?? 0,
                'building_id' => $request->building_id,
                'unit_id' => $request->unit_id,
                'security_deposit' => $request->security_deposit,
                'cleaning_charges' => $request->cleaning_charges ?? 0,
                'other_charges' => $request->other_charges ?? 0,
                'check_in_date' => $request->check_in_date,
                'frequency' => $request->frequency,
                'remarks' => $request->remarks,
                'nid_front_picture' => $nidFrontPicturePath,
                'nid_back_picture' => $nidBackPicturePath,
            ]);

            \Log::info("Tenant updated successfully: {$tenant->id}", []);

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully',
                'tenant' => $tenant->fresh()
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating tenant: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update tenant'
            ], 500);
        }
    }

    /**
     * Download invoice PDF file for tenant (API version for mobile)
     */
    public function downloadInvoicePDF(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Check if user is tenant (support both tenant relation and tenant_id field)
            $tenantId = null;
            if ($user && $user->tenant) {
                $tenantId = $user->tenant->id;
            } elseif ($user && $user->tenant_id) {
                $tenantId = $user->tenant_id;
            }
            
            if (!$tenantId) {
                \Log::error("Authentication failed - User: " . ($user ? $user->name : 'No user') . ", Tenant ID: " . ($user ? $user->tenant_id : 'No tenant_id') . ", Has tenant relation: " . ($user && $user->tenant ? 'Yes' : 'No'));
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a tenant'
                ], 403);
            }

            // Get invoice for this tenant
            $invoice = \App\Models\Invoice::where('id', $id)
                ->where('tenant_id', $tenantId)
                ->with(['tenant:id,first_name,last_name,mobile,email,address,upazila,district,zip,country', 'unit:id,name,property_id', 'unit.property:id,name,address,email,mobile'])
                ->first();

            // Debug logging to see what fields are loaded
            if ($invoice && $invoice->tenant) {
                \Log::info('Tenant fields loaded:', [
                    'address' => $invoice->tenant->address,
                    'upazila' => $invoice->tenant->upazila,
                    'district' => $invoice->tenant->district,
                    'zip' => $invoice->tenant->zip,
                    'country' => $invoice->tenant->country,
                ]);
            }

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            // Process breakdown data for PDF
            if ($invoice->breakdown) {
                try {
                    $breakdown = json_decode($invoice->breakdown, true) ?? [];

                    // Add descriptions for common fee types
                    foreach ($breakdown as &$fee) {
                        if (!isset($fee['description']) || empty($fee['description'])) {
                            $feeName = strtolower($fee['name'] ?? '');

                            // Add descriptions based on fee name
                            if (strpos($feeName, 'base rent') !== false || strpos($feeName, 'monthly rent') !== false) {
                                $fee['description'] = 'Base monthly rent payment for the rental unit';
                            } elseif (strpos($feeName, 'rent') !== false) {
                                $fee['description'] = 'Monthly rent payment for the unit';
                            } elseif (strpos($feeName, 'electricity') !== false || strpos($feeName, 'power') !== false || strpos($feeName, 'electric') !== false) {
                                $fee['description'] = 'Electricity bill charges for the month';
                            } elseif (strpos($feeName, 'gas') !== false || strpos($feeName, 'gas bill') !== false) {
                                $fee['description'] = 'Gas bill charges for the month';
                            } elseif (strpos($feeName, 'water') !== false || strpos($feeName, 'water bill') !== false) {
                                $fee['description'] = 'Water bill charges for the month';
                            } elseif (strpos($feeName, 'cleaning') !== false) {
                                $fee['description'] = 'Cleaning and maintenance charges';
                            } elseif (strpos($feeName, 'maintenance') !== false) {
                                $fee['description'] = 'Building maintenance and repair charges';
                            } elseif (strpos($feeName, 'late') !== false || strpos($feeName, 'penalty') !== false) {
                                $fee['description'] = 'Late payment penalty charges';
                            } elseif (strpos($feeName, 'security') !== false || strpos($feeName, 'deposit') !== false) {
                                $fee['description'] = 'Security deposit or related charges';
                            } elseif (strpos($feeName, 'utility') !== false) {
                                $fee['description'] = 'Utility service charges (electricity, water, gas)';
                            } elseif (strpos($feeName, 'service') !== false) {
                                $fee['description'] = 'Additional service charges';
                            } else {
                                $fee['description'] = 'Additional service or charge';
                            }
                        }
                    }

                    // Update invoice with processed breakdown
                    $invoice->breakdown = json_encode($breakdown);
                } catch (\Exception $e) {
                    // Keep original breakdown if processing fails
                }
            }

            // Fetch last payment for this invoice (to populate gateway, txn id, date)
            $lastPayment = \App\Models\RentPayment::where('invoice_id', $invoice->id)
                ->orderByDesc('payment_date')
                ->orderByDesc('id')
                ->first();

            // Generate PDF using tenant invoice view
            $pdf = \PDF::loadView('tenant.invoices.pdf', compact('invoice', 'lastPayment'));

            // Configure PDF for A4 size
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('dpi', 72);
            $pdf->setOption('image-dpi', 72);
            $pdf->setOption('image-quality', 60);
            $pdf->setOption('enable-local-file-access', false);
            $pdf->setOption('isRemoteEnabled', false);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isFontSubsettingEnabled', true);

            // Set proper headers for PDF download (mobile compatible)
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice-' . $invoice->invoice_number . '.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            \Log::error('Tenant invoice PDF error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rent agreement details for the authenticated tenant
     */
    public function getRentAgreement()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->tenant_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 401);
            }

            $tenant = Tenant::with(['property', 'unit', 'unit.charges'])
                ->where('id', $user->tenant_id)
                ->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tenant not found'
                ], 404);
            }

            // Calculate total monthly charges
            $monthlyRent = $tenant->unit->rent ?? 0;
            $totalCharges = 0;
            
            if ($tenant->unit && $tenant->unit->charges) {
                foreach ($tenant->unit->charges as $charge) {
                    $totalCharges += $charge->amount ?? 0;
                }
            }

            $monthlyTotal = $monthlyRent + $totalCharges;



            // Get payment history
            $paymentHistory = \App\Models\Invoice::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'month' => date('M Y', strtotime($invoice->rent_month)),
                        'date' => date('d M Y', strtotime($invoice->created_at)),
                        'amount' => number_format($invoice->total_amount, 2),
                        'status' => $invoice->status ?? 'unpaid'
                    ];
                });

            // Get fees from unit charges
            $fees = [];
            $totalFees = 0;
            
            if ($tenant->unit && $tenant->unit->charges) {
                foreach ($tenant->unit->charges as $charge) {
                    $feeAmount = $charge->amount ?? 0;
                    $fees[] = [
                        'name' => $charge->name ?? 'Additional Charge',
                        'amount' => number_format($feeAmount, 2),
                        'type' => $charge->type ?? 'monthly'
                    ];
                    $totalFees += $feeAmount;
                }
            }

            // Calculate total monthly amount (rent + fees)
            $totalMonthlyAmount = $monthlyRent + $totalFees;

            // Prepare rent details
            $rentDetails = [
                'monthly_rent' => number_format($monthlyRent, 2),
                'payment_method' => 'Bank Transfer / Cash',
                'payment_history' => $paymentHistory,
                'fees' => $fees,
                'total_fees' => number_format($totalFees, 2),
                'total_monthly_amount' => number_format($totalMonthlyAmount, 2)
            ];



            // Prepare agreement details
            $agreementDetails = [
                'agreement_number' => 'AG-' . str_pad($tenant->id, 6, '0', STR_PAD_LEFT),
                'start_date' => $tenant->created_at ? date('d M Y', strtotime($tenant->created_at)) : 'N/A',
                'end_date' => $tenant->created_at ? date('d M Y', strtotime('+1 year', strtotime($tenant->created_at))) : 'N/A',
                'duration' => '1 Year',
                'status' => 'Active',
                'property_name' => $tenant->property->name ?? 'N/A',
                'unit_name' => $tenant->unit->name ?? 'N/A',
                'terms_conditions' => 'This is a standard rental agreement. The tenant agrees to pay rent on time, maintain the property, and follow all building rules. Late payments may incur additional fees. The agreement is renewable annually upon mutual consent.'
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'rent_details' => $rentDetails,
                    'agreement_details' => $agreementDetails
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching rent agreement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch rent agreement data'
            ], 500);
        }
    }
}
