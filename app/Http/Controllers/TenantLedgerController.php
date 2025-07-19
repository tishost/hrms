<?php

namespace App\Http\Controllers;

use App\Models\TenantLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantLedgerController extends Controller
{
    /**
     * Log a transaction to the tenant ledger.
     *
     * @param array $data
     * @return TenantLedger
     */
    public static function log(array $data)
    {
        // Calculate running balance
        $last = TenantLedger::where('tenant_id', $data['tenant_id'] ?? null)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->first();
        $prevBalance = $last ? $last->balance : 0;
        $balance = $prevBalance + ($data['credit_amount'] ?? 0) - ($data['debit_amount'] ?? 0);

        $ledger = TenantLedger::create([
            'tenant_id'        => $data['tenant_id'],
            'unit_id'          => $data['unit_id'] ?? null,
            'owner_id'         => $data['owner_id'] ?? null,
            'transaction_type' => $data['transaction_type'],
            'reference_type'   => $data['reference_type'] ?? null,
            'reference_id'     => $data['reference_id'] ?? null,
            'invoice_number'   => $data['invoice_number'] ?? null,
            'debit_amount'     => $data['debit_amount'] ?? 0,
            'credit_amount'    => $data['credit_amount'] ?? 0,
            'balance'          => $balance,
            'description'      => $data['description'] ?? '',
            'notes'            => $data['notes'] ?? null,
            'transaction_date' => $data['transaction_date'] ?? now(),
            'due_date'         => $data['due_date'] ?? null,
            'payment_method'   => $data['payment_method'] ?? null,
            'payment_reference'=> $data['payment_reference'] ?? null,
            'payment_status'   => $data['payment_status'] ?? 'completed',
            'created_by'       => Auth::id() ?? 1,
        ]);
        return $ledger;
    }
}
