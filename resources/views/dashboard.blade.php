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



<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }} 👋</h2>
                <p class="text-gray-600">Here is a quick summary of your system.</p>
            </div>


            {{-- First Row --}}
            <div class="row">
                <div class="col-md-2 mb-2">
                    <a href="{{route('numbers.index')}}">
                        <x-dashboard-card color="blue" label="Total Numbers" :value="$totalNumbers" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('numbers.index', ['status' => 'Active']) }}">
                        <x-dashboard-card color="green" label="Active" :value="$activeNumbers" />
                    </a>
                </div>

                <div class="col-md-2 mb-2">
                    <a href="{{ route('numbers.index', ['status' => 'Inactive']) }}">
                        <x-dashboard-card color="yellow" label="Inactive" :value="$inactiveNumbers" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('numbers.index', ['status' => 'Suspend']) }}">
                        <x-dashboard-card color="red" label="Suspended" :value="$suspendedNumbers" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('numbers.index', ['assign_status' => 'not_assigned']) }}">
                        <x-dashboard-card color="gray" label="Unused" :value="$unusedNumbers" />
                    </a>
                </div>
            </div>
            <br>
            <hr>
            <br>
            {{-- Second Row --}}
            <div class="row">
                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index') }}">
                        <x-dashboard-card color="blue" label="Total Staffs" :value="$totalStaffs" />
                    </a>
                </div>


                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index', ['company_id' =>'1']) }}">
                        <x-dashboard-card color="green" label="JMS" :value="$jmsStaffs" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index', ['company_id' =>'2']) }}">
                        <x-dashboard-card color="indigo" label="MCL" :value="$mclStaffs" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index', ['company_id' =>'3']) }}">
                        <x-dashboard-card color="purple" label="MBL" :value="$mblStaffs" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index', ['company_id' =>'4']) }}">
                        <x-dashboard-card color="orange" label="HO" :value="$hoStaffs" />
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index', ['company_id' =>'5']) }}">
                        <x-dashboard-card color="teal" label="ITA" :value="$itaStaffs" /> 
                    </a>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('staffs.index', ['assign_status' =>'not_assigned']) }}">
                        <x-dashboard-card color="red" label="Unsigned" :value="$unsignedNumberStaffs" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection























