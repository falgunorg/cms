<?php

namespace App\Http\Controllers;

use App\Models\Number;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\NumberHistory;
use Carbon\Carbon;

class NumberController extends Controller {

    public function index(Request $request) {
        $query = Number::with(['staff', 'staff.company', 'histories', 'staff.department', 'staff.designation']);
        $allStaffs = Staff::whereNull('number_id')->get();
        // Search by staff name or number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%$search%")
                        ->orWhereHas('staff', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        });
            });
        }

        if ($request->filled('assign_status') && $request->assign_status == 'assigned') {
            $query->whereHas('staff');
        }

        if ($request->filled('assign_status') && $request->assign_status == 'not_assigned') {
            $query->whereDoesntHave('staff');
        }


        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company_id
        if ($request->filled('company_id')) {
            $query->whereHas('staff.company', function ($q) use ($request) {
                $q->where('id', $request->company_id);
            });
        }

        // Filter by department_id
        if ($request->filled('department_id')) {
            $query->whereHas('staff.department', function ($q) use ($request) {
                $q->where('id', $request->department_id);
            });
        }

        // Filter by designation_id
        if ($request->filled('designation_id')) {
            $query->whereHas('staff.designation', function ($q) use ($request) {
                $q->where('id', $request->designation_id);
            });
        }

        $numbers = $query->get();
        $companies = Company::all();
        $departments = Department::all();
        $designations = Designation::all();

        return view('numbers.index', compact('numbers', 'companies', 'departments', 'designations', 'allStaffs'));
    }

    public function create() {
        $staffs = Staff::whereNull('number_id')->get();
        return view('numbers.create', compact('staffs'));
    }

    public function store(Request $request) {
        $request->validate([
            'number' => 'required|unique:numbers,number',
            'status' => 'required|in:Active,Inhouse,Suspend,Inactive',
            'staff_id' => 'nullable|exists:staffs,id',
        ]);

        // Step 1: Create the number
        $number = Number::create($request->only('number', 'status'));

        // Step 2: Assign to staff if provided
        if ($request->filled('staff_id')) {
            $staff = Staff::find($request->staff_id);
            $staff->number_id = $number->id;
            $staff->save();
        }

        return redirect()->route('numbers.index')->with('success', 'Number added successfully.');
    }

    public function edit(Number $number) {
        $staffs = Staff::whereNull('number_id')
                ->orWhere('id', $number->staff->id ?? 0) // allow assigned staff
                ->get();

        return view('numbers.edit', compact('number', 'staffs'));
    }

    public function update_old(Request $request, Number $number) {
        $request->validate([
            'number' => 'required|unique:numbers,number,' . $number->id,
            'status' => 'required|in:Active,Inhouse,Suspend,Inactive',
            'staff_id' => 'nullable|exists:staffs,id',
        ]);

        // Update number info
        $number->update($request->only('number', 'status'));

        // Step 1: Unassign from any staff currently using this number
        Staff::where('number_id', $number->id)->update(['number_id' => null]);

        // Step 2: Assign to the new staff if provided
        if ($request->filled('staff_id')) {
            $staff = Staff::find($request->staff_id);
            $staff->number_id = $number->id;
            $staff->save();
        }

        return redirect()->route('numbers.index')->with('success', 'Number updated successfully.');
    }

    public function update(Request $request, Number $number) {
        $request->validate([
            'number' => 'required|unique:numbers,number,' . $number->id,
            'status' => 'required|in:Active,Inhouse,Suspend,Inactive',
            'staff_id' => 'nullable|exists:staffs,id',
        ]);

        // Save old staff for history comparison
        $oldStaff = $number->staff;

        // Update number info
        $number->update($request->only('number', 'status'));

        // Step 1: Unassign any existing staff
        Staff::where('number_id', $number->id)->update(['number_id' => null]);

        // Step 2: Handle new assignment
        if ($request->filled('staff_id')) {
            $newStaff = Staff::find($request->staff_id);
            $newStaff->number_id = $number->id;
            $newStaff->save();

            // Close old history if exists
            if ($oldStaff) {
                NumberHistory::where('number_id', $number->id)
                        ->whereNull('end_date')
                        ->update(['end_date' => Carbon::now()->toDateString()]);
            }

            // Create new history for new staff
            NumberHistory::create([
                'number_id' => $number->id,
                'staff_id' => $newStaff->id,
                'staff_name' => $newStaff->name,
                'start_date' => Carbon::now()->toDateString(),
            ]);
        } else {
            // If no staff now, just close old history if exists
            if ($oldStaff) {
                NumberHistory::where('number_id', $number->id)
                        ->whereNull('end_date')
                        ->update(['end_date' => Carbon::now()->toDateString()]);
            }
        }

        return redirect()->route('numbers.index')->with('success', 'Number updated successfully.');
    }

    public function assignStaff_OLD(Request $request) {
        $request->validate([
            'number_id' => 'required|exists:numbers,id',
            'staff_id' => 'required|exists:staffs,id',
        ]);

        $number = Number::findOrFail($request->number_id);
        $staff = Staff::findOrFail($request->staff_id);

        // Assign number to staff
        $staff->number_id = $number->id;
        $staff->save();

        // Update number status
        $number->status = 'Active';
        $number->save();

        return redirect()->route('numbers.index')->with('success', 'Number assigned to staff successfully.');
    }

    public function assignStaff(Request $request) {
        $request->validate([
            'number_id' => 'required|exists:numbers,id',
            'staff_id' => 'required|exists:staffs,id',
        ]);

        $number = Number::with('staff')->findOrFail($request->number_id);
        $staff = Staff::findOrFail($request->staff_id);

        // Close previous history if exists
        if ($number->staff) {
            NumberHistory::where('number_id', $number->id)
                    ->whereNull('end_date')
                    ->update(['end_date' => Carbon::now()->toDateString()]);

            // Unassign previous staff
            $number->staff->update(['number_id' => null]);
        }

        // Assign number to new staff
        $staff->update(['number_id' => $number->id]);

        // Update number status
        $number->update(['status' => 'Active']);

        // Create new history
        NumberHistory::create([
            'number_id' => $number->id,
            'staff_id' => $staff->id,
            'staff_name' => $staff->name,
            'start_date' => Carbon::now()->toDateString(),
        ]);

        return redirect()->route('numbers.index')->with('success', 'Number assigned to staff successfully.');
    }

    public function destroy(Number $number) {
        try {
            // Check if the number is assigned
            if ($number->staff) {
                return redirect()->back()
                                ->with('error', 'Number cannot be deleted because it is assigned to ' . $number->staff->name);
            }

            $number->delete();
            return redirect()->back()->with('success', 'Number deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong while deleting the number.');
        }
    }
}
