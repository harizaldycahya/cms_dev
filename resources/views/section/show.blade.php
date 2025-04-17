@extends('layouts.app')

@section('head_script')
    <style>
        html,
        body {
            /* height: 1000px; */
            padding: 0;
            margin: 0;
        }

        #map {
            height: 80vh;
            width: 100%;
            overflow: hidden;
            float: left;
            border: thin solid #333;
        }

        #capture {
            height: 360px;
            width: 480px;
            overflow: hidden;
            float: left;
            background-color: #ECECFB;
            border: thin solid #333;
            border-left: none;
        }

        .custom-table tbody tr:nth-child(odd) {
            background-color: #eaeff4; /* Light gray for odd rows */
        }

        .custom-table tbody tr:nth-child(even) {
            background-color: #ffffff; /* White for even rows */
        }
        .breadcrumb-item a{
            color: lightblue;
        }


        .breadcrumb-item a{
            color: lightblue;
        }

        .btn-notification {
            position: relative;
        }
        .btn-notification .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }

    </style>
@endsection

@php
    $project = DB::table('project')->where('project_id', $project_id)->first();
    $segment = DB::table('segment')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->first();
    $section = DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->first();
    
    $sub_sections = DB::table('sub_section')
        ->where('project_id', $project_id)
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->where('section_id', $section_id)
        ->orderByRaw('CAST(customer_id AS UNSIGNED) ASC')
        ->get();

    $route_name = match($route_id) {
        '1' => 'SUBMARINE',
        '2' => 'INLAND',
        '3' => 'LASTMILE',
        default => 'UNKNOWN'
    };

    $main_core_capacity = $section->core_capacity;   
    
    $cores = DB::table('core')
        ->where('project_id', $project_id)
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->where('section_id', $section_id)
        ->orderByRaw('CAST(core AS UNSIGNED) ASC')
        ->get();

    $uniqueCores = collect($cores)
    ->groupBy(fn($core) => $core->core . '-' . $core->customer_id)
    ->map(function ($group) {
        $allOk = $group->every(fn($core) => isset($core->actual_remarks) && strtoupper($core->actual_remarks) === 'OK'); // Ensure case sensitivity
        $finalRemark = $allOk ? 'OK' : 'NOT OK';
        return (object) [
            'core' => $group->first()->core,
            'customer_id' => $group->first()->customer_id,
            'status' => $group->first()->status,
            'actual_remarks' => $finalRemark,
        ];
    })
    ->values(); // Reset array keys

    // Calculate core capacities
    $core_capacity = $uniqueCores->count();
    // $sold_core = $uniqueCores->where('status', 'SOLD')->count();
    $core_active = $uniqueCores->where('status', 'ACTIVE')->count();
    $core_mismatch = $uniqueCores->where('status', 'MISMATCH')->count();
    $core_booked = $uniqueCores->where('status', 'BOOKED')->count();
    $sold_core = $core_active + $core_mismatch + $core_booked;
    
    $core_used = $uniqueCores->where('status', 'USED')->count();
    $idle_core = $uniqueCores->where('status', 'IDLE')->count();
    $idle_general = $idle_core + $core_used;

    $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
    $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();
    
    $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
    $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();

    $availability_core_idle = 0;
    $availability_core_capacity = 0;

    if(($core_capacity - $core_active) != 0){
        $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
        $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
    }else{
        $availability_core_idle = "0";
        $availability_core_capacity = "0";
    }

@endphp

@section('header')
    <!--  Header Start -->
    <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item d-block d-xl-none">
                    <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
            <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                <ul class="navbar-nav quick-links d-none d-lg-flex mt-3">
                    <li class="nav-item dropdown hover-dd d-none d-lg-block">
                        <p style="text-transform: uppercase; font-size:.8rem; font-weight: bold; color:#e8571e;">Login as
                            {{ auth()->user()->role }}</p>
                    </li>
                </ul>

                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                    <li class="nav-item dropdown">

                        <a style="font-size:1rem;" class="nav-link" href="javascript:void(0)" id="drop2"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }} &nbsp;&nbsp; <i class="ti ti-user fs-6"></i>

                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                            <div class="message-body">
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!--  Header End -->
@endsection

@section('breadcrumb')
    <div class="card bg-dark text-white shadow-lg position-relative overflow-hidden">
        <div class="card-body px-5 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem;"> #{{$section->section_id}} SECTION ({{$route_name}})</h3>
                    <h3 class="fw-semibold text-white" style="font-size: 1.8rem;">  {{$section->section_name}}</h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('segment.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id, 'segment_id'=>$segment->segment_id])}}">{{$segment->segment_name}}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                {{$section->section_name}}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
    {{-- SUB SECTION --}}
    <div class="col-lg-12 align-items-stretch">
        <div class="card w-100 p-4">
            @if(auth()->user()->role == 'engineering')
                <div style="text-align:right;" class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage Section
                    </button>
                    <div class="dropdown-menu" aria-abelledby="dropdownMenuButton">
                            <a href="{{route('sub_section.create', ['project_id'=>$project->project_id, 'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" display="block" class="dropdown-item">Create Sub Section</a>
                            @if (count($sub_sections) > 0)
                                <a href="{{route('sub_section.edit', ['project_id'=>$project->project_id, 'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" display="block" class="dropdown-item">Edit Sub Section</a>
                            @endif
                            <a href="{{route('core.upload_excel', ['project_id'=>$project->project_id, 'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" display="block" class="dropdown-item">Upload Excel</a>
                            <a href="{{route('section.delete', ['project_id' => $project->project_id, 'route_id' => $route_id, 'segment_id' => $segment->segment_id, 'section_id' => $section_id])}}" onclick="return confirmDelete(event);" display="block" class="dropdown-item">Delete Section</a>  
                    </div>
                </div>
            @endif    
            @if(auth()->user()->role == 'ms')
                <div style="text-align:right;" class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Manage Section
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a href="{{route('sub_section.edit', ['project_id'=>$project->project_id, 'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Manage Section</a>
                    </div>
                </div>
            @endif    
            <div class="row my-4">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-start">
                                        <div style="color:black;" class="card-title mb-9 fw-semibold"> Summary Section</div>
                                        <div class="col-6">
                                            <div class="fw-semibold mb-3">Project Name : {{$project->project_description}} ({{$project->project_name}})</div>
                                            <div class="fw-semibold mb-3">Segment Name : {{$segment->segment_name}}</div>
                                            <div class="fw-semibold mb-3">Section Name : {{$section->section_name}}</div>
                                            <div class="fw-semibold mb-3">Core Capacity : {{$section->core_capacity}}</div>

                                            @switch($section->section_route)
                                                @case('1_route')
                                                        <div class="fw-semibold mb-3">Section Route : MAIN</div>
                                                    @break
                                                @case('2_route')
                                                        <div class="fw-semibold mb-3">Section Route : DIVERSITY</div>
                                                    @break
                                                @case('3_route')
                                                        <div class="fw-semibold mb-3">Section Route : THIRD ROUTE</div>
                                                    @break
                                                @case('4_route')
                                                        <div class="fw-semibold mb-3">Section Route : FORTH ROUTE</div>
                                                    @break
                                                @default
                                                    
                                            @endswitch
                                            <div class="fw-semibold mb-3">Cable Type : {{$section->cable_type}}</div>
                                            <div class="fw-semibold mb-3">First RFS : {{$section->first_rfs}}</div>
                                        </div>
                                        <!-- First Pie Chart -->
                                        <div class="col-3">
                                            <div class="card overflow-hidden text-white" style="background: linear-gradient(0deg, rgba(246,39,127,1) 0%, rgba(255,76,76,1) 100%); ">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <h5 class="card-title fw-semibold text-white">
                                                            Availability : {{ \Illuminate\Support\Str::limit($availability_core_idle, 5, '.') ?? '0' }} %
                                                        </h5>
                                                        <span>From Core IDLE + BOOKED ( {{$idle_general + $core_booked}} )</span>
                                                    </div>
                                                    <hr>
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-center">
                                                                <input hidden id="idle_available_1" type="text" value="{{$availability_core_idle ?? 0}}">
                                                                <input hidden id="idle_not_available_1" type="text" value="{{ 100 - ($availability_core_idle ?? 0) }}">
                                                                <div id="idle_availibility_chart_1"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Second Pie Chart -->
                                        <div class="col-3">
                                            <div class="card overflow-hidden text-white" style="background: linear-gradient(0deg,#49BEFF 0%,  #5D87FF 100%); ">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <h5 class="card-title fw-semibold text-white">
                                                            Availability : {{ \Illuminate\Support\Str::limit($availability_core_capacity, 5, '.') ?? '0' }} %
                                                        </h5>
                                                        <span>From Core Capacity ( {{$core_capacity}} )</span>
                                                    </div>
                                                    <hr>
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-center">
                                                                <input hidden id="idle_available_2" type="text" value="{{$availability_core_capacity ?? 0}}">
                                                                <input hidden id="idle_not_available_2" type="text" value="{{ 100 - ($availability_core_capacity ?? 0) }}">
                                                                <div id="idle_availibility_chart_2"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-start">
                                <div style="min-height: 5rem;"></div>
                                <div style="min-height: 5rem;"></div>
                                <div class="col-lg-5">
                                    <!-- Monthly Earnings -->
                                    <div class="card" style="overflow-y:auto;">
                                        <div class="card-body">
                                            <div class="row align-items-start">
                                                <div class="col-8">
                                                    <h5 class="card-title mb-9 fw-semibold">Customer Core Allocations</h5>
                                                </div>
                                                <div class="col-4">
                                                    <div class="d-flex justify-content-end">
                                                        <div
                                                            class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-file-description fs-6"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="margin-top:3rem; width:100%; height:26rem; overflow-y:scroll;">
                                                    <table class="table custom-table table-bordered" style="width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Core</th>
                                                                <th scope="col">Initial</th>
                                                                <th scope="col">Actual</th>
                                                                <th scope="col">Actual Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $cores = DB::table('core')
                                                                    ->where('project_id', $project->project_id)
                                                                    ->where('route_id', $segment->route_id)
                                                                    ->where('segment_id', $segment->segment_id)
                                                                    ->where('section_id', $section->section_id)
                                                                    ->orderByRaw('CAST(core AS UNSIGNED) ASC')
                                                                    ->get();
                                                    
                                                                $coreGroups = []; // Group cores by their values
                                                                $coreCounts = []; // Store count of each core
                                                    
                                                                foreach ($cores as $core) {
                                                                    $coreGroups[$core->core][] = $core;
                                                                    $coreCounts[$core->core] = isset($coreCounts[$core->core]) ? $coreCounts[$core->core] + 1 : 1;
                                                                }
                                                    
                                                                $coreDisplayed = []; // Track displayed cores to apply rowspan
                                                            @endphp
                                                    
                                                            @foreach ($cores as $core)
                                                                @php
                                                                    $customer = DB::table('customer')->where('customer_id', $core->customer_id)->first();
                                                                    $customer_name = $customer ? $customer->customer_name : 'Unknown';
                                                    
                                                                    $is_duplicate = count($coreGroups[$core->core]) > 1; // More than one occurrence
                                                                    $has_different_owners = count(array_unique(array_map(fn($c) => $c->customer_id, $coreGroups[$core->core]))) > 1; // Different owners?
                                                                @endphp
                                                    
                                                                @if (!isset($coreDisplayed[$core->core]))  
                                                                    <tr>
                                                                        <td 
                                                                            style="{{ $is_duplicate && $has_different_owners ? 'background-color: red; color: white;' : '' }}">
                                                                            {{ $core->core }}
                                                                        </td>
                                                                        <td>{{ $core->initial_customers }}</td>
                                                                        <td>{{ $core->actual_customers }}</td>
                                                                        <td style="font-weight:bold;">{{ $core->status }}</td>
                                                                    </tr>
                                                                    @php $coreDisplayed[$core->core] = true; @endphp
                                                                @elseif ($has_different_owners)
                                                                    <tr>
                                                                        <td>{{ $customer_name }}</td>
                                                                        <td>{{ $core->initial_customers }}</td>
                                                                        <td>{{ $core->actual_customers }}</td>
                                                                        <td style="font-weight:bold;">{{ $core->status }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <!-- Monthly Earnings -->
                                    <div class="card border-dark">
                                        <div class="card-body">
                                            <div class="row align-items-start">
                                                <div class="col-8">
                                                    <div class="row mb-5">
                                                        <div class="col-7">
                                                            <h5 class="card-title mb-9 fw-semibold"> Core Details</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-file-description fs-6"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <table class="border-dark table table-bordered" style="width:100%; overflow-y:auto;">
                                                        <thead class="bg-dark text-white">
                                                            <tr>
                                                                <th scope="col">Initial Details</th>
                                                                <th scope="col">Actual Details</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-weight:bold">
                                                            <tr>
                                                                <td>
                                                                    Core Capacity: 
                                                                    {{$core_capacity == $section->core_capacity ? $core_capacity : '( '.$core_capacity.' / '.$section->core_capacity.' )'}}
                                                                </td>
                                                                <td>
                                                                    Core Capacity: 
                                                                    {{$core_capacity == $section->core_capacity ? $core_capacity : '( '.$core_capacity.' / '.$section->core_capacity.' )'}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>SOLD Core: {{$sold_core}}</td>
                                                                <td>
                                                                    SOLD Core: {{$sold_core}}
                                                                    <br>
                                                                    <ul style="padding:1rem 2rem;">
                                                                        <li style="list-style-type:square"> Active: {{$core_active}}</li>
                                                                        <li style="list-style-type:square"> Mismatch: {{$core_mismatch}}</li>
                                                                        <li style="list-style-type:square">
                                                                            Booked: {{$core_booked}}
                                                                            <br>
                                                                            <ul style="padding:0.2rem 2rem;">
                                                                                <li style="list-style-type:circle"> OK: {{$actual_core_booked_ok}}</li>
                                                                                <li style="list-style-type:circle"> NOT OK: {{$actual_core_booked_not_ok}}</li>
                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>IDLE Core: {{$idle_general}}</td>
                                                                <td>
                                                                    IDLE Core: {{$idle_general}}
                                                                    <br>
                                                                    <ul style="padding:1rem 2rem;">
                                                                        <li style="list-style-type:square"> Used: {{$core_used}}</li>
                                                                        <li style="list-style-type:square">
                                                                            IDLE: {{$idle_core}}
                                                                            <br>
                                                                            <ul style="padding:0.2rem 2rem;">
                                                                                <li style="list-style-type:circle"> OK: {{$actual_core_idle_ok}}</li>
                                                                                <li style="list-style-type:circle"> NOT OK: {{$actual_core_idle_not_ok}}</li>
                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-6">
                            <h5 style="text-align:left;" class="card-title fw-semibold mb-4">Sub Sections Lists</h5>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                            <thead class="bg-dark fs-4">
                                    <tr>
                                        <th style="width:40%;">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Sub Section Name</h6>
                                        </th>
                                        <th style="width:30%;">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Owner</h6>
                                        </th>
                                        <th style="width:20%;" class="text-center">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                                        </th>
                                    </tr>
                            </thead>
                            <tbody>
                                @foreach ($sub_sections as $sub_section)
                                    @php
                                        $type_id = $sub_section->type_id === null ? '-' : $sub_section->type_id;
                                    @endphp
                                    <tr>
                                        <td class="px-5">
                                            <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                {{$sub_section->sub_section_name}}
                                            </a>
                                        </td>
                                        <td class="px-5">
                                            <a type="button" class=" btn-notification" style="text-transform: uppercase; cursor: default; color:black;">
                                                {{ DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name }} {{$sub_section->type_id === null ? '' : '('.$sub_section->type_id.')'}}
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="{{route('sub_section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id, 'customer_id'=> $sub_section->customer_id, 'type_id'=> $type_id, 'sub_section_id'=> $sub_section->sub_section_id])}}" class="btn btn-primary">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach                                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row card p-3">
        <div class="col-lg-12 align-items-stretch">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-5  {{ auth()->user()->role == 'engineering' ? 'active' : ''}}" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Initial Data</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-5 {{ auth()->user()->role == 'ms' || auth()->user()->role == 'lapangan' ? 'active' : ''}} " id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Actual Data</button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade  {{ auth()->user()->role == 'engineering' ? 'show active' : ''}} " id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            <div style="display:grid; grid-template-columns:2fr 1fr;">
                                <h5 style="text-align:left;" class=" fw-semibold mb-4">Initial Cores Information</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="text-dark fs-4 bg-dark">
                                            <tr>
                                                <th>
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Link ID</h6>
                                                </th>
                                                <th>
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">CID</h6>
                                                </th>
                                                <th>
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Core</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Initial Customers</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Actual Customers</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Length</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Total Loss DB</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Loss DB KM</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">OK / NOT OK</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Status</h6>
                                                </th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        <tbody>
                                            @if (count($cores) > 0)
                                                @php
                                                    $coreCounts = []; // Store core occurrences
                                                    $coreDisplayed = []; // Track which cores have been displayed
                                                    $coreRemarks = []; // Track remarks for each core
                                                    $coreFirstValues = []; // Store first row values for merging columns

                                                    foreach ($cores as $core) {
                                                        // Count occurrences of each core
                                                        $coreCounts[$core->core] = isset($coreCounts[$core->core]) ? $coreCounts[$core->core] + 1 : 1;

                                                        // Determine final remarks for each core
                                                        if (!isset($coreRemarks[$core->core])) {
                                                            $coreRemarks[$core->core] = $core->initial_remarks;
                                                        } elseif ($core->initial_remarks !== 'OK') {
                                                            $coreRemarks[$core->core] = 'NOT OK'; // If any value is NOT OK, set the final remark to NOT OK
                                                        }

                                                        // Store first values for merging columns
                                                        if (!isset($coreFirstValues[$core->core])) {
                                                            $coreFirstValues[$core->core] = [
                                                                'actual_customers' => $core->actual_customers,
                                                                'initial_customers' => $core->initial_customers,
                                                                'status' => $core->status
                                                            ];
                                                        }
                                                    }
                                                @endphp

                                                @foreach ($cores as $core)
                                                    <tr>
                                                        <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core}}</td>
                                                        <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core.$core->customer_id.$core->type_id}}</td>

                                                        {{-- Merge core column --}}
                                                        @if (!isset($coreDisplayed[$core->core]))
                                                            <td class="text-center" style="width:10%;" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$core->core}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core] = true; @endphp
                                                        @endif

                                                        {{-- Merge initial_customers column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_initial_customers']))
                                                            <td class="text-center" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$coreFirstValues[$core->core]['initial_customers']}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_initial_customers'] = true; @endphp
                                                        @endif

                                                        {{-- Merge actual_customers column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_actual_customers']))
                                                            <td class="text-center" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$coreFirstValues[$core->core]['actual_customers']}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_actual_customers'] = true; @endphp
                                                        @endif

                                                        <td class="text-center">{{$core->initial_end_cable}}</td>
                                                        <td class="text-center">{{$core->initial_total_loss_db}}</td>
                                                        <td class="text-center">{{$core->initial_loss_db_km}}</td>

                                                        {{-- Merge remarks column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_remarks']))
                                                            <td class="text-center" rowspan="{{ $coreCounts[$core->core] }}">
                                                                @if ($coreRemarks[$core->core] == 'OK')
                                                                    <span class="badge bg-success text-light">OK</span>
                                                                @else
                                                                    <span style="background:rgba(246,39,127,1);" class="badge text-light">NOT OK</span>
                                                                @endif
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_remarks'] = true; @endphp
                                                        @endif

                                                        {{-- Merge status column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_status']))
                                                            <td class="text-center" style="font-weight: bold;" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$coreFirstValues[$core->core]['status']}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_status'] = true; @endphp
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">Empty</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ auth()->user()->role == 'ms' || auth()->user()->role == 'lapangan' ? 'show active' : ''}} " id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            <div style="display:grid; grid-template-columns:2fr 1fr;">
                                <h5 style="text-align:left;" class=" fw-semibold mb-4">Actual Cores Information</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="text-dark fs-4 bg-dark">
                                            <tr>
                                                <th>
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Link ID</h6>
                                                </th>
                                                <th>
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">CID</h6>
                                                </th>
                                                <th>
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Core</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Initial Customers</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Actual Customers</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Length</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Total Loss DB</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Loss DB KM</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">OK / NOT OK</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Status</h6>
                                                </th>
                                                <th class="text-center">
                                                    <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Date</h6>
                                                </th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        <tbody>
                                            @if (count($cores) > 0)
                                                @php
                                                    $coreCounts = []; // Store core occurrences
                                                    $coreDisplayed = []; // Track which cores have been displayed
                                                    $coreRemarks = []; // Track remarks for each core
                                                    $coreFirstValues = []; // Store first row values for merging columns

                                                    foreach ($cores as $core) {
                                                        // Count occurrences of each core
                                                        $coreCounts[$core->core] = isset($coreCounts[$core->core]) ? $coreCounts[$core->core] + 1 : 1;

                                                        // Determine final remarks for each core
                                                        if (!isset($coreRemarks[$core->core])) {
                                                            $coreRemarks[$core->core] = $core->actual_remarks;
                                                        } elseif ($core->actual_remarks !== 'OK') {
                                                            $coreRemarks[$core->core] = 'NOT OK'; // If any value is NOT OK, set the final remark to NOT OK
                                                        }

                                                        // Store first values for merging columns
                                                        if (!isset($coreFirstValues[$core->core])) {
                                                            $coreFirstValues[$core->core] = [
                                                                'actual_customers' => $core->actual_customers,
                                                                'initial_customers' => $core->initial_customers,
                                                                'status' => $core->status
                                                            ];
                                                        }
                                                    }
                                                @endphp

                                                @foreach ($cores as $core)
                                                    <tr>
                                                        <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core}}</td>
                                                        <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core.$core->customer_id.$core->type_id}}</td>

                                                        {{-- Merge core column --}}
                                                        @if (!isset($coreDisplayed[$core->core]))
                                                            <td class="text-center" style="width:10%;" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$core->core}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core] = true; @endphp
                                                        @endif

                                                        {{-- Merge initial_customers column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_initial_customers']))
                                                            <td class="text-center" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$coreFirstValues[$core->core]['initial_customers']}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_initial_customers'] = true; @endphp
                                                        @endif
                                                        
                                                        {{-- Merge actual_customers column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_actual_customers']))
                                                            <td class="text-center" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$coreFirstValues[$core->core]['actual_customers']}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_actual_customers'] = true; @endphp
                                                        @endif

                                                        

                                                        <td class="text-center">{{$core->actual_end_cable}}</td>
                                                        <td class="text-center">{{$core->actual_total_loss_db}}</td>
                                                        <td class="text-center">{{$core->actual_loss_db_km}}</td>

                                                        {{-- Merge remarks column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_remarks']))
                                                            <td class="text-center" rowspan="{{ $coreCounts[$core->core] }}">
                                                                @if ($coreRemarks[$core->core] == 'OK')
                                                                    <span class="badge bg-success text-light">OK</span>
                                                                @else
                                                                    <span style="background:rgba(246,39,127,1);" class="badge text-light">NOT OK</span>
                                                                @endif
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_remarks'] = true; @endphp
                                                        @endif

                                                        {{-- Merge status column --}}
                                                        @if (!isset($coreDisplayed[$core->core . '_status']))
                                                            <td class="text-center" style="font-weight: bold;" rowspan="{{ $coreCounts[$core->core] }}">
                                                                {{$coreFirstValues[$core->core]['status']}}
                                                            </td>
                                                            @php $coreDisplayed[$core->core . '_status'] = true; @endphp
                                                        @endif

                                                        <td class="text-center">{{substr($core->notes, 0, 24)}}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">Empty</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection


@section('script')

<script type="text/javascript">


    function confirmDeleteCore(event) {
        if (confirm("Are you sure you want to delete this core ?")) {
            return true;
        } else {
            event.preventDefault();
            return false;
        }
    }

    function confirmDeleteSection(event) {
        if (confirm("Are you sure you want to delete this section?")) {
            return true;
        } else {
            return false;
        }
    }


    function confirmDelete(event) {
        if (confirm("Are you sure you want to delete this section ?")) {
            return true; // Allow the form submission to proceed
        } else {
            event.preventDefault();
            return false; // Cancel the form submission
        }
    }

    $('#master').on('click', function(e) {
        if($(this).is(':checked',true)){
            $(".sub_chk").prop('checked', true);
        }else{ 
            $(".sub_chk").prop('checked',false);
        }  
    });
</script>

<script>
    function renderChart(containerId, availableId, notAvailableId) {
        const value1 = Number(document.getElementById(availableId).value);
        const value2 = Number(document.getElementById(notAvailableId).value);

        var chartOptions = {
            series: [value1, value2],
            labels: ["Available", "Not Available"],
            chart: {
                width: 260,
                type: "donut",
                fontFamily: "Plus Jakarta Sans', sans-serif",
                foreColor: "#adb0bb",
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                    },
                },
            },
            stroke: { show: false },
            dataLabels: { enabled: false },
            legend: { show: false },
            colors: ["#7FF627", "#ecf2ff"],
            responsive: [{
                breakpoint: 991,
                options: { chart: { width: 150 } }
            }],
            tooltip: {
                enabled: true,
                theme: "dark",
                y: { formatter: function (val) { return val + "%" } }
            }
        };

        var chart = new ApexCharts(document.querySelector(`#${containerId}`), chartOptions);
        chart.render();
    }

    // Render both pie charts
    renderChart("idle_availibility_chart_1", "idle_available_1", "idle_not_available_1");
    renderChart("idle_availibility_chart_2", "idle_available_2", "idle_not_available_2");
</script>


@endsection