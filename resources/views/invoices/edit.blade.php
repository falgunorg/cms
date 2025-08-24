@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="d-flex justify-content-between pt-4">
            <h2>EDIT INVOICE</h2>
            <a href="{{ route('invoices.index') }}" class="btn btn-danger mb-2">Back</a>
        </div>
        <hr>
        <br>
        <form action="{{ route('invoices.update',$invoice) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('invoices.form')
        </form>
    </div>
</div>
@endsection
