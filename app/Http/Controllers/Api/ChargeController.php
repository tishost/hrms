<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Charge;

class ChargeController extends Controller
{
    public function index()
    {
        $charges = Charge::all();
        return response()->json(['charges' => $charges]);
    }
}
