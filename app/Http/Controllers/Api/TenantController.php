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
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'mobile' => $tenant->mobile,
                    'email' => $tenant->email,
                    'property_name' => $tenant->unit->property->name ?? 'No Property',
                    'unit_name' => $tenant->unit->name ?? 'No Unit',
                    'rent' => $tenant->unit->rent ?? 0,
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
                'nid_front_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'nid_back_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

            // Handle NID front image upload
            $nidFrontPicturePath = null;
            if ($request->hasFile('nid_front_picture')) {
                try {
                    $file = $request->file('nid_front_picture');
                    $fileName = 'nid_front_' . time() . '_' . $user->owner_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('public/tenants/nid', $fileName);
                    $nidFrontPicturePath = str_replace('public/', 'storage/', $filePath);
                    \Log::info('NID front image uploaded: ' . $nidFrontPicturePath, []);
                } catch (\Exception $e) {
                    \Log::error('Error uploading NID front image: ' . $e->getMessage());
                }
            }

            // Handle NID back image upload
            $nidBackPicturePath = null;
            if ($request->hasFile('nid_back_picture')) {
                try {
                    $file = $request->file('nid_back_picture');
                    $fileName = 'nid_back_' . time() . '_' . $user->owner_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('public/tenants/nid', $fileName);
                    $nidBackPicturePath = str_replace('public/', 'storage/', $filePath);
                    \Log::info('NID back image uploaded: ' . $nidBackPicturePath, []);
                } catch (\Exception $e) {
                    \Log::error('Error uploading NID back image: ' . $e->getMessage());
                }
            }

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
                'nid_front_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'nid_back_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle NID front image upload
            $nidFrontPicturePath = $tenant->nid_front_picture;
            if ($request->hasFile('nid_front_picture')) {
                try {
                    $file = $request->file('nid_front_picture');
                    $fileName = 'nid_front_' . time() . '_' . $user->owner_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('public/tenants/nid', $fileName);
                    $nidFrontPicturePath = str_replace('public/', 'storage/', $filePath);
                    \Log::info('NID front image uploaded: ' . $nidFrontPicturePath);
                } catch (\Exception $e) {
                    \Log::error('Error uploading NID front image: ' . $e->getMessage());
                }
            }

            // Handle NID back image upload
            $nidBackPicturePath = $tenant->nid_back_picture;
            if ($request->hasFile('nid_back_picture')) {
                try {
                    $file = $request->file('nid_back_picture');
                    $fileName = 'nid_back_' . time() . '_' . $user->owner_id . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('public/tenants/nid', $fileName);
                    $nidBackPicturePath = str_replace('public/', 'storage/', $filePath);
                    \Log::info('NID back image uploaded: ' . $nidBackPicturePath);
                } catch (\Exception $e) {
                    \Log::error('Error uploading NID back image: ' . $e->getMessage());
                }
            }

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
}
