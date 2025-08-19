



<div class="row">

    <div class="col-lg-6">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $staff->name ?? '') }}" required>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-3">
            <label>Staff ID</label>
            <input type="text" name="staff_id" class="form-control"
                   value="{{ old('staff_id', $staff->staff_id ?? '') }}">
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-3">
            <label>Company</label>
            <select name="company_id" class="form-control select2">
                <option value="">-- Select Company --</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id', $staff->company_id ?? '') == $company->id ? 'selected' : '' }}>
                    {{ $company->nickname }}
                </option>
                @endforeach                
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label>Department</label>
            <select name="department_id" class="form-control select2">
                <option value="">-- Select Department --</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $staff->department_id ?? '') == $department->id ? 'selected' : '' }}>
                    {{ $department->title }}
                </option>

                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label>Designation</label>
            <select name="designation_id" class="form-control select2">
                <option value="">-- Select Designation --</option>
                @foreach($designations as $designation)
                <option value="{{ $designation->id }}" {{ old('designation_id', $staff->designation_id ?? '') == $designation->id ? 'selected' : '' }}>
                    {{ $designation->title }}
                </option>

                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label>Assign Number</label>
            <select name="number_id" class="form-control select2">
                <option value="">-- None --</option>
                @foreach($numbers as $number)
                <option value="{{ $number->id }}" {{ old('number_id', $staff->number_id ?? '') == $number->id ? 'selected' : '' }}>
                    {{ $number->number }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="In-Service" {{ (old('status', $staff->status ?? '') == 'In-Service') ? 'selected' : '' }}>In-Service</option>
                <option value="Left" {{ (old('status', $staff->status ?? '') == 'Left') ? 'selected' : '' }}>Left</option>
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <label>Balance Limit</label>
            <input type="text" name="balance_limit" class="form-control"
                   value="{{ old('balance_limit', $staff->balance_limit ?? '') }}" required>
        </div>
    </div>
</div>











<br>


<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });
    });
</script>


