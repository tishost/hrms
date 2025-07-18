<?php

namespace App\Http\Controllers\Admin;

use App\Models\Owner;
use App\Http\Controllers\Controller;


class DashboardController extends Controller
{
    public function index()
    {
        $ownerCount = Owner::count();
        return view('admin.dashboard',compact('ownerCount'));
    }
}