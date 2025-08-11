<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChargeController extends Controller
{
    /**
     * Display a listing of charges
     */
    public function index()
    {
        try {
            $charges = Charge::orderBy('created_at', 'desc')->get();
            
            return view('admin.charges.index', compact('charges'));
        } catch (\Exception $e) {
            Log::error('Error in ChargeController@index: ' . $e->getMessage());
            return back()->with('error', 'Failed to load charges. Please try again.');
        }
    }

    /**
     * Show the form for creating a new charge
     */
    public function create()
    {
        return view('admin.charges.create');
    }

    /**
     * Store a newly created charge
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'label' => 'required|string|max:255|unique:charges,label',
                'amount' => 'required|numeric|min:0|max:999999.99',
            ], [
                'label.required' => 'Charge label is required.',
                'label.unique' => 'This charge label already exists.',
                'amount.required' => 'Amount is required.',
                'amount.numeric' => 'Amount must be a valid number.',
                'amount.min' => 'Amount cannot be negative.',
                'amount.max' => 'Amount cannot exceed 999,999.99.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $charge = Charge::create([
                'label' => $request->label,
                'amount' => $request->amount,
            ]);

            Log::info('Charge created successfully', [
                'charge_id' => $charge->id,
                'label' => $charge->label,
                'amount' => $charge->amount,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.charges.index')
                ->with('success', 'Charge "' . $charge->label . '" created successfully!');

        } catch (\Exception $e) {
            Log::error('Error in ChargeController@store: ' . $e->getMessage());
            return back()->with('error', 'Failed to create charge. Please try again.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified charge
     */
    public function edit(Charge $charge)
    {
        return view('admin.charges.edit', compact('charge'));
    }

    /**
     * Update the specified charge
     */
    public function update(Request $request, Charge $charge)
    {
        try {
            $validator = Validator::make($request->all(), [
                'label' => 'required|string|max:255|unique:charges,label,' . $charge->id,
                'amount' => 'required|numeric|min:0|max:999999.99',
            ], [
                'label.required' => 'Charge label is required.',
                'label.unique' => 'This charge label already exists.',
                'amount.required' => 'Amount is required.',
                'amount.numeric' => 'Amount must be a valid number.',
                'amount.min' => 'Amount cannot be negative.',
                'amount.max' => 'Amount cannot exceed 999,999.99.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $oldLabel = $charge->label;
            $oldAmount = $charge->amount;

            $charge->update([
                'label' => $request->label,
                'amount' => $request->amount,
            ]);

            Log::info('Charge updated successfully', [
                'charge_id' => $charge->id,
                'old_label' => $oldLabel,
                'new_label' => $charge->label,
                'old_amount' => $oldAmount,
                'new_amount' => $charge->amount,
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('admin.charges.index')
                ->with('success', 'Charge "' . $charge->label . '" updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error in ChargeController@update: ' . $e->getMessage());
            return back()->with('error', 'Failed to update charge. Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified charge
     */
    public function destroy(Charge $charge)
    {
        try {
            $label = $charge->label;
            
            // Check if charge is being used anywhere
            // You can add additional checks here if needed
            
            $charge->delete();

            Log::info('Charge deleted successfully', [
                'charge_id' => $charge->id,
                'label' => $label,
                'deleted_by' => auth()->id(),
            ]);

            return redirect()->route('admin.charges.index')
                ->with('success', 'Charge "' . $label . '" deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error in ChargeController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete charge. Please try again.');
        }
    }

    /**
     * Toggle charge status (active/inactive)
     */
    public function toggleStatus(Charge $charge)
    {
        try {
            $charge->update([
                'is_active' => !$charge->is_active,
            ]);

            $status = $charge->is_active ? 'activated' : 'deactivated';
            
            Log::info('Charge status toggled', [
                'charge_id' => $charge->id,
                'label' => $charge->label,
                'new_status' => $charge->is_active ? 'active' : 'inactive',
                'toggled_by' => auth()->id(),
            ]);

            return back()->with('success', 'Charge "' . $charge->label . '" ' . $status . ' successfully!');

        } catch (\Exception $e) {
            Log::error('Error in ChargeController@toggleStatus: ' . $e->getMessage());
            return back()->with('error', 'Failed to toggle charge status. Please try again.');
        }
    }

    /**
     * Get charges for API (if needed)
     */
    public function getCharges()
    {
        try {
            $charges = Charge::where('is_active', true)
                ->orderBy('label')
                ->get(['id', 'label', 'amount']);

            return response()->json([
                'success' => true,
                'data' => $charges,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ChargeController@getCharges: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch charges',
            ], 500);
        }
    }
}
