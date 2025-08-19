@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="d-flex justify-content-between pt-4">
            <h2>All Staffs</h2>
            <a href="{{ route('staffs.create') }}" class="btn btn-success mb-2">+ Add new</a>
        </div>
        <hr>
        <br>
        <form method="GET" action="{{ route('staffs.index') }}" class="mb-4">
            <div class="row">
                <div class="col">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or number" value="{{ request('search') }}">
                </div>
                <div class="col">
                    <select name="company_id" class="form-control">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->nickname }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="department_id" class="form-control select2">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="designation_id" class="form-control select2">
                        <option value="">All Designations</option>
                        @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ request('designation_id') == $designation->id ? 'selected' : '' }}>
                            {{ $designation->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="In-Service" {{ request('status') == 'In-Service' ? 'selected' : '' }}>In-Service</option>
                        <option value="Left" {{ request('status') == 'Left' ? 'selected' : '' }}>Left</option>
                    </select>
                </div>

                <div class="col">
                    <select name="assign_status" class="form-control">
                        <option value="">All</option>
                        <option value="assigned" {{ request('assign_status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="not_assigned" {{ request('assign_status') == 'not_assigned' ? 'selected' : '' }}>Not Assigned</option>

                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Filter</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('staffs.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>


        <div class="mb-3 text-end">
            <button id="exportBtn" class="btn btn-sm btn-success">Export to Excel</button>
            <button onclick="printTable()" class="btn btn-sm btn-info">Print</button>

        </div>

        <table id="staffsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Company</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $index => $staff)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $staff->staff_id }}</td>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->number->number ?? '-' }}</td>
                    <td>{{ $staff->company->nickname ?? '-' }}</td>
                    <td>{{ $staff->department->title ?? '-' }}</td>
                    <td>{{ $staff->designation->title ?? '-' }}</td>
                    <td>{{$staff->balance_limit}}</td>
                    <td>{{ $staff->status }}</td>
                    <td>
                        <a href="{{ route('staffs.edit', $staff->id) }}" class="btn btn-sm btn-info">Edit</a>
                        <!-- Delete Button -->
                        <button 
                            type="button" 
                            class="btn btn-sm btn-danger"
                            onclick="confirmDelete({{ $staff->id }}, {{ $staff->number ? 'true' : 'false' }})">
                            Del
                        </button>



                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal -->
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="modalBody">
                            <!-- JS will render this -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(staffId, hasNumber) {
    const form = document.getElementById('deleteForm');
    const modalBody = document.getElementById('modalBody');
    const route = `{{ route('staffs.destroy', ':id') }}`.replace(':id', staffId); ;
    form.action = route;
    if (hasNumber) {
    modalBody.innerHTML = `
            <p>This staff has a number assigned. Choose action before deleting:</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="status_action" value="inhoused" id="inhoused">
                <label class="form-check-label" for="inhoused">Mark number as Inhoused</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="status_action" value="suspended" id="suspended">
                <label class="form-check-label" for="suspended">Mark number as Suspended</label>
            </div>
        `;
    } else {
    modalBody.innerHTML = `<p>Are you sure you want to delete this staff?</p>`;
    }

    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
    }
</script>




<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function () {
    const originalTable = document.getElementById('staffsTable');
    // Clone the table to avoid modifying the original
    const clonedTable = originalTable.cloneNode(true);
    // Remove the last cell (Actions) from each row
    for (const row of clonedTable.rows) {
    if (row.cells.length > 0) {
    row.deleteCell(row.cells.length - 1);
    }
    }

    const wb = XLSX.utils.table_to_book(clonedTable, {sheet: "Numbers"});
    XLSX.writeFile(wb, 'staffs.xlsx');
    });</script>

<script>
    function printTable() {
    const originalTable = document.getElementById('staffsTable');
    const clonedTable = originalTable.cloneNode(true);
    // Remove last 2 columns from thead
    const theadRow = clonedTable.querySelector('thead tr');
    if (theadRow && theadRow.cells.length >= 2) {
    theadRow.deleteCell(theadRow.cells.length - 1); // "Actions"
    theadRow.deleteCell(theadRow.cells.length - 1); // "Limit"
    }

    // Remove last 2 columns from tbody
    clonedTable.querySelectorAll('tbody tr').forEach(row => {
    if (row.cells.length >= 2) {
    row.deleteCell(row.cells.length - 1);
    row.deleteCell(row.cells.length - 1);
    }
    });
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Staffs</title>');
    printWindow.document.write(`
            <style>
                @media print {
                    @page {
                        size: A4 landscape;
                        margin: 0 20px 0 20px; /* top right bottom left */
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                }

                body {
                   margin: 0 20px 0 20px;
                    padding: 0;
                    font-family: Arial, sans-serif;
                }

                h2 {
                    text-align: center;
                    margin: 20px 0;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 12px;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 6px;
                    text-align: left;
                }
            </style>
        `);
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h2>Staff List</h2>');
    printWindow.document.write(clonedTable.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
    }
</script>

<script>
    $(document).ready(function () {
    $('.select2').select2({
    placeholder: "Select an option",
            allowClear: true
    });
    // Auto-remove alerts after 5 seconds
    setTimeout(function () {
    document.querySelectorAll('.alert').forEach(el => el.remove());
    }, 5000);
    });

</script>





@endsection
