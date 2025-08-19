<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Number;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use Carbon\Carbon;
use App\Models\NumberHistory;

class StaffController extends Controller {

    public function index(Request $request) {
        $query = Staff::with(['number', 'company', 'department', 'designation']);

        // Search by staff name or number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                        ->orWhereHas('number', function ($q2) use ($search) {
                            $q2->where('number', 'like', "%$search%");
                        });
            });
        }

        if ($request->filled('assign_status') && $request->assign_status == 'assigned') {
            $query->whereHas('number');
        }

        if ($request->filled('assign_status') && $request->assign_status == 'not_assigned') {
            $query->whereDoesntHave('number');
        }


        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company_id
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by department_id
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by designation_id
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }

        $staffs = $query->get();
        $companies = Company::all();
        $departments = Department::all();
        $designations = Designation::all();

        return view('staffs.index', compact('staffs', 'companies', 'departments', 'designations'));
    }

    public function create() {
        $numbers = Number::doesntHave('staff')->get();
        $companies = Company::all();
        $departments = Department::all();
        $designations = Designation::all();

        return view('staffs.create', compact('numbers', 'companies', 'departments', 'designations'));
    }

    public function store_OLD(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'staff_id' => 'nullable|unique:staffs,staff_id|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'number_id' => 'nullable|exists:numbers,id',
            'status' => 'required|in:In-Service,Left',
            'balance_limit' => 'required|numeric',
        ]);

        Staff::create($validated);

        return redirect()->route('staffs.index')->with('success', 'Staff created successfully.');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'staff_id' => 'nullable|unique:staffs,staff_id|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'number_id' => 'nullable|exists:numbers,id',
            'status' => 'required|in:In-Service,Left',
            'balance_limit' => 'required|numeric',
        ]);

        $staff = Staff::create($validated);

        // If assigned a number, create history
        if ($staff->number_id) {
            NumberHistory::create([
                'number_id' => $staff->number_id,
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'start_date' => Carbon::now()->toDateString(),
            ]);

            // Ensure number status is Active
            $staff->number->update(['status' => 'Active']);
        }

        return redirect()->route('staffs.index')->with('success', 'Staff created successfully.');
    }

    public function edit(Staff $staff) {
        $numbers = Number::whereDoesntHave('staff')
                ->orWhere('id', $staff->number_id)
                ->get();
        $companies = Company::all();
        $departments = Department::all();
        $designations = Designation::all();

        return view('staffs.edit', compact('numbers', 'companies', 'departments', 'designations', 'staff'));
    }

    public function update_OLD(Request $request, Staff $staff) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'staff_id' => 'nullable|unique:staffs,staff_id,' . $staff->id,
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'number_id' => 'nullable|exists:numbers,id',
            'status' => 'required|in:In-Service,Left',
            'balance_limit' => 'required|numeric',
        ]);

        $staff->update($validated);

        return redirect()->route('staffs.index')->with('success', 'Staff updated successfully.');
    }

    public function update(Request $request, Staff $staff) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'staff_id' => 'nullable|unique:staffs,staff_id,' . $staff->id,
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'number_id' => 'nullable|exists:numbers,id',
            'status' => 'required|in:In-Service,Left',
            'balance_limit' => 'required|numeric',
        ]);

        $oldNumberId = $staff->number_id;
        $staff->update($validated);

        // Handle Number History
        if ($oldNumberId != $staff->number_id) {
            // 1. Close old history if exists
            if ($oldNumberId) {
                NumberHistory::where('number_id', $oldNumberId)
                        ->where('staff_id', $staff->id)
                        ->whereNull('end_date')
                        ->update(['end_date' => Carbon::now()->toDateString()]);
            }

            // 2. Create new history if new number assigned
            if ($staff->number_id) {
                NumberHistory::create([
                    'number_id' => $staff->number_id,
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->name,
                    'start_date' => Carbon::now()->toDateString(),
                ]);

                // Ensure number status is Active
                $staff->number->update(['status' => 'Active']);
            }
        }

        return redirect()->route('staffs.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy_OLD(Request $request, Staff $staff) {
        if ($staff->number) {
            $status = $request->status_action;

            if ($status == 'inhoused') {
                $staff->number->update(['status' => 'Inhouse']);
            } elseif ($status == 'suspended') {
                $staff->number->update(['status' => 'Suspend']);
            }
        }

        $staff->delete();

        return redirect()->route('staffs.index')->with('success', 'Staff deleted.');
    }

    public function destroy(Request $request, Staff $staff) {
        if ($staff->number) {
            $status = $request->status_action;

            // Close history
            NumberHistory::where('number_id', $staff->number_id)
                    ->where('staff_id', $staff->id)
                    ->whereNull('end_date')
                    ->update(['end_date' => Carbon::now()->toDateString()]);

            // Update number status based on selection
            if ($status == 'inhoused') {
                $staff->number->update(['status' => 'Inhouse']);
            } elseif ($status == 'suspended') {
                $staff->number->update(['status' => 'Suspend']);
            }

            // Unassign number
            $staff->update(['number_id' => null]);
        }

        $staff->delete();

        return redirect()->route('staffs.index')->with('success', 'Staff deleted and history updated.');
    }
}
