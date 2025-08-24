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
            <h2>INVOICES</h2>
            <a href="{{ route('invoices.create') }}" class="btn btn-success mb-2">+ Add new</a>
        </div>
        <hr>
        <br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>File</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->title }}</td>
                    <td>{{ $invoice->amount }}</td>
                    <td>
                        @if($invoice->file)
                        <a href="{{ asset('storage/'.$invoice->file) }}" target="_blank">View</a>
                        @endif
                    </td>
                    <td>{{ $invoice->remarks }}</td>
                    <td>
                        <a href="{{ route('invoices.show',$invoice) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('invoices.edit',$invoice) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('invoices.destroy',$invoice) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this invoice?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5">No invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>

@endsection
