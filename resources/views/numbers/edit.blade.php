@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="d-flex justify-content-between pt-4">
            <h2>Edit Number</h2>
            <a href="{{ route('numbers.index') }}" class="btn btn-danger mb-2">Back</a>
        </div>
        <hr>
        <br>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('numbers.update', $number->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Number</label>
                <input type="text" name="number" value="{{ old('number', $number->number) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Staff</label>
                <select name="staff_id" class="form-control select2">
                    <option value="">-- Select Staff --</option>
                    @foreach($staffs as $staff)
                    <option value="{{ $staff->id }}"
                            {{ $number->staff && $number->staff->id == $staff->id ? 'selected' : '' }}>
                        {{ $staff->name }}
                    </option>
                    @endforeach
                </select>
            </div>





            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    @foreach (['Active', 'Inhouse', 'Suspend', 'Inactive'] as $status)
                    <option value="{{ $status }}" {{ old('status', $number->status) == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
        <br/>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });
    });
</script>
@endsection
