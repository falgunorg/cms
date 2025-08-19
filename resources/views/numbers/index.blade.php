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
            <h2>AlL Numbers</h2>
            <a href="{{ route('numbers.create') }}" class="btn btn-success mb-2">+ Add new</a>
        </div>
        <hr>
        <br>
        <form method="GET" action="{{ route('numbers.index') }}" id="formArea" class="mb-4">
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
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inhouse" {{ request('status') == 'Inhouse' ? 'selected' : '' }}>Inhouse</option>
                        <option value="Suspend" {{ request('status') == 'Suspend' ? 'selected' : '' }}>Suspend</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                    <a href="{{ route('numbers.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>


        <div class="mb-3 text-end">
            <button id="exportBtn" class="btn btn-sm btn-success">Export to Excel</button>
            <button onclick="printTable()" class="btn btn-sm btn-info">Print</button>
        </div>
        <table id="numbersTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Number</th>
                    <th>Staff</th>
                    <th>Limit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($numbers as $index => $number)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>{{ $number->number }}</td>
                    <td>
                        @if($number->staff)
                        {{ $number->staff->name ?? '-' }}<br>
                        {{ $number->staff->company->nickname ?? '' }}
                        @if($number->staff->department)
                        , {{ $number->staff->department->title }}
                        @endif
                        @if($number->staff->designation)
                        , {{ $number->staff->designation->title }}
                        @endif
                        @else
                        <button class="btn btn-sm btn-primary" onclick="openAssignModal({{ $number->id }})">
                            Assign
                        </button>
                        @endif
                    </td>

                    <td>{{$number->staff->balance_limit?? '-'}}</td>

                    <td>
                        @php
                        switch ($number->status) {
                        case 'Active':
                        $textClass = 'text-success'; // Green
                        break;
                        case 'Inhouse':
                        $textClass = 'text-primary'; // Blue
                        break;
                        case 'Suspend':
                        $textClass = 'text-danger'; // Red
                        break;
                        case 'Inactive':
                        $textClass = 'text-secondary'; // Gray
                        break;
                        default:
                        $textClass = 'text-dark'; // Default
                        }
                        @endphp

                        <span class="{{ $textClass }}">{{ $number->status }}</span>
                    </td>

                    <td>
                        <button class="btn btn-info btn-sm"
                                onclick="openHistoryModal(this)"
                                data-number="{{ $number->number }}"
                                data-histories='@json($number->histories->map(function($h) {
                                return [
                                'start_date' => \Carbon\Carbon::parse($h->start_date)->format('d-m-Y'),
                            'end_date' => $h->end_date ? \Carbon\Carbon::parse($h->end_date)->format('d-m-Y') : 'Present',
                            'staff_name' => $h->staff->name ?? $h->staff_name
                            ];
                            }))'>
                            History
                        </button>

                        <a href="{{ route('numbers.edit', $number) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('numbers.destroy', $number) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Assign Staff Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="assignForm" method="POST" action="{{ route('numbers.assign') }}">
            @csrf
            <input type="hidden" name="number_id" id="assignNumberId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Staff to Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="staff_id">Select Staff:</label>
                    <br>
                    <select style="width:100%" class="form-control select2" name="staff_id" id="staff_id" required>
                        <option value="">-- Select Staff --</option>
                        @foreach($allStaffs as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->staff_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- JS will fill this dynamically -->
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
                                document.getElementById('exportBtn').addEventListener('click', function () {
                                const originalTable = document.getElementById('numbersTable');
                                // Clone the table to avoid modifying the original
                                const clonedTable = originalTable.cloneNode(true);
                                // Remove the last cell (Actions) from each row
                                for (const row of clonedTable.rows) {
                                if (row.cells.length > 0) {
                                row.deleteCell(row.cells.length - 1);
                                }
                                }

                                const wb = XLSX.utils.table_to_book(clonedTable, {sheet: "Numbers"});
                                XLSX.writeFile(wb, 'numbers.xlsx');
                                });</script>
<script>
    
    

    function printTable() {
    const originalTable = document.getElementById('numbersTable');
    const clonedTable = originalTable.cloneNode(true);
    // Remove last 2 columns from thead
    const theadRow = clonedTable.querySelector('thead tr');
    if (theadRow && theadRow.cells.length >= 2) {
    theadRow.deleteCell(theadRow.cells.length - 1); // "Actions"

    }

    // Remove last 2 columns from tbody
    clonedTable.querySelectorAll('tbody tr').forEach(row => {
    if (row.cells.length >= 2) {
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
                    font-size: 16px;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 6px;
                    text-align: left;
                }
            </style>
        `);
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h2>Numbers List</h2>');
    printWindow.document.write(clonedTable.outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
    }
</script>


<script type="text/javascript">
    $(document).ready(function () {

    $('.select2').select2({
    placeholder: "Select an option",
            dropdownParent: $('#formArea')
    });
    // Re-initialize for modal if needed separately
    $('#assignModal .select2').select2({
    dropdownParent: $('#assignModal')
    });
    // Auto-remove alerts after 5 seconds
    setTimeout(function () {
    document.querySelectorAll('.alert').forEach(el => el.remove());
    }, 5000);
    });
    function openAssignModal(numberId) {
    $('#assignNumberId').val(numberId);
    $('#assignModal').modal('show');
    }


    function openHistoryModal(button) {
    const number = button.getAttribute('data-number');
    const histories = JSON.parse(button.getAttribute('data-histories'));
    const modalTitle = document.querySelector('#historyModal .modal-title');
    const modalBody = document.querySelector('#historyModal .modal-body');
    modalTitle.innerText = `History for ${number}`;
    if (histories.length > 0) {
    let content = '';
    histories.forEach(h => {
    content += `
                <div class="mb-2">
                    ${h.start_date} - ${h.end_date}<br>
                    <strong>Staff:</strong> ${h.staff_name}
                </div>
                <hr>
            `;
    });
    modalBody.innerHTML = content;
    } else {
    modalBody.innerHTML = '<p>No history found.</p>';
    }

    // Show the modal
    $('#historyModal').modal('show');
    }
</script>





@endsection
