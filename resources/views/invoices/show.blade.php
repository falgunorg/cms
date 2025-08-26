@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="d-flex justify-content-between pt-4">
            <h2>INVOICE DETAILS</h2>
            <a href="{{ route('invoices.index') }}" class="btn btn-danger mb-2">Back</a>
        </div>
        <hr>
        <br>
        <div class="mb-3">
            <p><strong>Title:</strong> {{ $invoice->title }}</p>
            <p><strong>Amount:</strong> {{ number_format($invoice->amount, 2) }}</p>
            <p><strong>Remarks:</strong> {{ $invoice->remarks }}</p>
        </div>

        <div>
            <strong>File:</strong>
            @if($invoice->file)
            <h5 class="mt-2">Invoice File</h5>
            <iframe 
                src="{{ asset($invoice->file) }}" 
                width="100%" 
                height="600px" 
                style="border:1px solid #ccc; border-radius:5px;">
            </iframe>
            @else
            <p>No file uploaded.</p>
            @endif
        </div>
    </div>
</div>

@endsection
