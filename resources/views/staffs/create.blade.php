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
            <h2>Add Staff</h2>
            <a href="{{ route('staffs.index') }}" class="btn btn-danger mb-2">Back</a>
        </div>
        <hr>
        <br>
        <form action="{{ route('staffs.store') }}" method="POST">
            @csrf
            @include('staffs.form')
            <button class="btn btn-success">Save</button>
        </form>
        <br/>
    </div>
</div>
@endsection
