<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Number;
use App\Models\Staff;

class DashboardController extends Controller {

    public function index() {
        return view('dashboard', [
            'totalNumbers' => Number::count(),
            'activeNumbers' => Number::where('status', 'active')->count(),
            'inactiveNumbers' => Number::where('status', 'inactive')->count(),
            'suspendedNumbers' => Number::where('status', 'suspended')->count(),
            'unusedNumbers' => Number::doesntHave('staff')->count(),
            'totalStaffs' => Staff::count(),
            'jmsStaffs' => Staff::where('company_id', 1)->count(), // adjust ID
            'mclStaffs' => Staff::where('company_id', 2)->count(),
            'mblStaffs' => Staff::where('company_id', 3)->count(),
            'hoStaffs' => Staff::where('company_id', 4)->count(),
            'itaStaffs' => Staff::where('company_id', 5)->count(),
            'unsignedNumberStaffs' => Staff::whereDoesntHave('number')->count(),
        ]);
    }
}
