@extends('layouts.app')

@php
    
    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('route_id', $route_id)->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->get()->first();
    
    $cores = DB::table('core')
    ->where('route_id', $route_id)
    ->where('project_id', $project_id)
    ->where('segment_id', $segment_id)
    ->where('section_id', $section_id)
    ->where('customer_id', $customer_id)
    ->where('sub_section_id', $sub_section_id)
    ->orderByRaw('CAST(core AS UNSIGNED) ASC')
    ->get();
    
    if ($type_id == '-') {
        $sub_section = DB::table('sub_section')
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->where('section_id', $section_id)
        ->where('sub_section_id', $sub_section_id)
        ->where('customer_id', $customer_id)
        ->get()
        ->first();
    } else {
        $sub_section = DB::table('sub_section')
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->where('section_id', $section_id)
        ->where('sub_section_id', $sub_section_id)
        ->where('customer_id', $customer_id)
        ->where('type_id', $type_id)
        ->get()
        ->first();
    }
    
    
    switch ($route_id) {
        case '1':
            $route_name = 'SUBMARINE';
            break;
        case '2':
            $route_name = 'INLAND';
            break;
        case '3':
            $route_name = 'LASTMILE';
            break;
        case '-':
            $route_name = 'INLAND';
            break;
        default:
            $route_name = 'UNDIFINED';
            break;
    }

    $main_core_capacity = $section->core_capacity;   

    $idle_core = $cores->where('status', 'IDLE')->count();
    $core_used = $cores->where('status', 'USED')->count();
 
    $idle_general = $idle_core + $core_used;
    $core_capacity = $cores->count();
    $core_active = $cores->where('status', 'ACTIVE')->count();   
    $core_mismatch = $cores->where('status', 'MISMATCH')->count();   
    $core_booked = $cores->where('status', 'BOOKED')->count();   
    $core_used = $cores->where('status', 'USED')->count();   

    $initial_core_sold = $cores->whereNotNull('initial_customers')->where('initial_customers', '!=', '')->count();
    $actual_core_sold = $cores->whereNotNull('actual_customers')->where('actual_customers', '!=', '')->count();

    $actual_core_booked_ok = $cores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();   
    $actual_core_booked_not_ok = $cores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();   
    
    $actual_core_idle_ok = $cores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();   
    $actual_core_idle_not_ok = $cores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();   
    $actual_core_used_ok = $cores->where('actual_remarks', 'OK')->where('status', 'USED')->count();   
         
    $sold_core = $core_active + $core_mismatch + $core_booked;
    

    if(($core_capacity - $core_active) != 0){
        $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
        $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity ) * 100 : 0;
    }else{
        $availability_core_idle = "0";
        $availability_core_capacity = "0";
    }

    $x = $sub_section->sub_actual_max_total_loss; // Example value for $x
    $b = 3.75 + $sub_section->sub_initial_min_total_loss; // Example value for $b
    $c = $sub_section->sub_initial_max_total_loss; // Example value for $c

    // Check conditions and set color accordingly
    if ($x < $b) {
        $color = '#28a745';
    } elseif ($x >= $b && $x <= $c) {
        $color = "#ffc107";
    } else {
        $color = "#dc3545";
    }


@endphp


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

        .custom-table tbody tr:hover {
            background-color: #e8571e; /* Hover color */
            color: white; /* Text color on hover */
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
                    <h3 class="fw-semibold text-white" style="font-size: 2rem;">Sub Section : {{$sub_section->sub_section_name}} <span style="text-transform: uppercase;">({{DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name}} {{$sub_section->type_id}})</span> </h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id, 'route_id'=> '-' ])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('segment.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id , 'segment_id'=>$segment->segment_id])}}">{{$segment->segment_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('section.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id , 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id])}}">{{$section->section_name}}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                    {{$sub_section->sub_section_name}}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="col-lg-12 align-items-stretch">
        <div class="card w-100 p-4">
            <div class="row">
                <div class="col-2">
                    <a href="{{route('sor.summary', ['project_id'=>$project->project_id,'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section_id])}}" type="button" class="w-100 btn btn-secondary">
                        SOR Summary
                    </a>
                </div>
                <div class="col-8">
                </div>
                <div class="col-2">
                    @if(auth()->user()->role == 'engineering')
                        <div style="text-align:right;" class="dropdown">
                            <button class="w-100 btn btn-primary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Menu
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                {{-- <a href="{{route('core.upload_excel', ['project_id'=>$project_id,'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id])}}" display="block" class="dropdown-item">Upload Excel</a> --}}
                                <a href="{{route('core.create', ['project_id'=>$project_id,'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id, 'sub_section_id'=>$sub_section_id, 'input_type' => 'setup' ] )}}" display="block" class="dropdown-item">Setup Core</a>
                                {{-- <a href="{{route('sub_section.edit', ['project_id'=>$project_id,'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id] )}}" display="block" class="dropdown-item">Edit Sub Section</a> --}}
                                <a onclick="return confirmDelete(event)" href="{{route('sub_section.delete', ['project_id'=>$project_id,'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id'=> $section_id, 'sub_section_id' => $sub_section_id] )}}" display="block" class="dropdown-item">Delete Sub Section</a>
                            </div>
                        </div>
                    @endif
                    @if(auth()->user()->role == 'lapangan')
                        <div style="text-align:right;">
                            <a href="{{route('sor.create', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id, 'customer_id'=> $sub_section->customer_id, 'type_id'=> $type_id, 'sub_section_id'=> $sub_section->sub_section_id])}}" class="btn btn-secondary my-1">
                                Upload SOR
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row my-4">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-start">
                                        <div style="color:black;" class="card-title mb-9 fw-semibold"> Summary Sub Section</div>
                                        <div style="min-height: 3rem;"></div>
                                        <div class="col-6">
                                            <div class="fw-semibold mb-3">Project Name : {{$project->project_description}} ({{$project->project_name}})</div>
                                            <div class="fw-semibold mb-3">Segment Name : {{$segment->segment_name}}</div>
                                            <div class="fw-semibold mb-3">Section Name : {{$section->section_name}}</div>
                                            <div class="fw-semibold mb-3">Main Core Capacity : {{$section->core_capacity}}</div>

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
                                            <div class="fw-semibold mb-3">Cable Category : {{$route_name}}</div>
                                            <div class="fw-semibold mb-3">Cable Type : {{$section->cable_type}}</div>
                                            <div class="fw-semibold mb-3">First RFS : {{$section->first_rfs}}</div>
                                            <div class="fw-semibold mb-3">Site Owner Near End : {{$sub_section->sub_site_owner_near_end}}</div>
                                            <div class="fw-semibold mb-3">Site Owner Far End : {{$sub_section->sub_site_owner_far_end}}</div>
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
                                    <div class="row align-items-start">
                                        <div class="col-12">
                                            <hr>
                                            <div class="row mt-5">
                                                <div class="col-4">
                                                    <h6 style="font-weight: bold;">Initial Data</h6>
                                                    <div class="fw-semibold mb-3">Length : {{$sub_section->sub_initial_length == null ? '-' : $sub_section->sub_initial_length}} KM</div>
                                                    <div class="fw-semibold mb-3">Min Total Loss :  {{$sub_section->sub_initial_min_total_loss == null ? '-' : $sub_section->sub_initial_min_total_loss}} dB</div>
                                                    <div class="fw-semibold mb-3">Max Total Loss : {{$sub_section->sub_initial_max_total_loss == null ? '-' : $sub_section->sub_initial_max_total_loss}} dB</div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 style="font-weight: bold;">Actual Data</h6>
                                                    <div class="fw-semibold mb-3">Length : {{$sub_section->sub_actual_length == null ? '-' : $sub_section->sub_actual_length}} KM</div>
                                                    <div class="fw-semibold mb-3">Min Total Loss :  {{$sub_section->sub_actual_min_total_loss == null ? '-' : $sub_section->sub_actual_min_total_loss}} dB</div>
                                                    <div class="fw-semibold mb-3">Max Total Loss : {{$sub_section->sub_actual_max_total_loss == null ? '-' : $sub_section->sub_actual_max_total_loss}} dB</div>
                                                </div>
                                                <div class="col-4" style="padding-right:3rem; overflow-x:hidden;">
                                                    <div class="bg-light" style="width: 100%; border-radius: 5px; padding: 3px; position: relative;">
                                                        <div style="width: calc(100% * {{$sub_section->sub_actual_max_total_loss}} / {{$sub_section->sub_initial_max_total_loss}}); background-color: {{$color}}; height: 20px; border-radius: 5px;"></div>
                                                            <span style=" position: absolute; right: 5px; top: 150%; transform: translateY(-50%);">{{$sub_section->sub_actual_max_total_loss}} dB / {{$sub_section->sub_initial_max_total_loss}} dB</span>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                            <table class=" table table-striped table-bordered" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Core</th>
                                                                        <th scope="col">Initial</th>
                                                                        <th scope="col">Actual</th>
                                                                        <th scope="col">Actual Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($cores as $core)
                                                                    <tr>
                                                                        <td>{{$core->core}}</td>
                                                                        <td>{{$core->initial_customers}}</td>
                                                                        <td>{{$core->actual_customers}}</td>
                                                                        <td style="font-weight:bold;">
                                                                            {{$core->status}}
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
                                        <div class="col-lg-7 ">
                                            <!-- Monthly Earnings -->
                                            <div class="card border-dark" >
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
                                                            <div
                                                                class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-file-description fs-6"></i>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <table class="border-dark table table-bordered " style="width:100%; overflow-y:auto;">
                                                                <thead class="bg-dark text-white">
                                                                    <tr>
                                                                        <th scope="col">Initial Details</th>
                                                                        <th scope="col">Actual Details</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody style="font-weight:bold">
                                                                    <tr>
                                                                        <td>Main Core Capacity : {{$main_core_capacity}}</td>
                                                                        <td>Main Core Capacity : {{$main_core_capacity}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Core Capacity : {{$core_capacity}}</td>
                                                                        <td>Core Capacity : {{$core_capacity}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            SOLD Core : {{$sold_core}}
                                                                        </td>
                                                                        <td>
                                                                            SOLD Core : {{$sold_core}}
                                                                            <br>
                                                                            <ul style="padding:1rem 2rem;">
                                                                                <li style="list-style-type:square" > Active : {{$core_active}}</li>
                                                                                <li style="list-style-type:square" > Mismatch : {{$core_mismatch}}</li>
                                                                                <li style="list-style-type:square" > 
                                                                                    Booked :{{$core_booked}}
                                                                                    <br> 
                                                                                    <ul style="padding:0.2rem 2rem;">
                                                                                        <li style="list-style-type:circle" > OK : {{$actual_core_booked_ok}}</li>
                                                                                        <li style="list-style-type:circle" > NOT OK : {{$actual_core_booked_not_ok}}</li>
                                                                                    </ul>
                                                                                </li>
                                                                                
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>IDLE Core : {{$idle_general}}</td>
                                                                        <td>
                                                                            IDLE Core : {{$idle_general}}
                                                                            <br>
                                                                            <ul style="padding:1rem 2rem;">
                                                                                <li style="list-style-type:square" > Used : {{$core_used}}</li>
                                                                                <li style="list-style-type:square" > 
                                                                                    IDLE :{{$idle_core}}
                                                                                    <br> 
                                                                                    <ul style="padding:0.2rem 2rem;">
                                                                                        <li style="list-style-type:circle" > OK : {{$actual_core_idle_ok}}</li>
                                                                                        <li style="list-style-type:circle" > NOT OK : {{$actual_core_idle_not_ok}}</li>
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
            </div>
        </div>
    </div>
    <div class="row">
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
                                <div style="text-align:right;" class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Select Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="{{route('core.create', ['project_id'=>$project->project_id,'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id, 'sub_section_id'=>$sub_section_id , 'input_type' => 'add'])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Add Cores</a>
                                        <a href="{{route('sor.download', ['project_id'=>$project->project_id,'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id'=>$sub_section_id ])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Download Actual Sor Files</a>
                                        <a href="{{route('core.edit', ['project_id'=>$project->project_id,'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id, 'sub_section_id'=>$sub_section_id])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Manage Core</a>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table  table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
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
                                                @if (auth()->user()->role == "engineering")
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Action</h6>
                                                    </th>
                                                @endif
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($cores) > 0 )
                                            @foreach ($cores as $core)
                                                <tr>
                                                    <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core}}</td>
                                                    <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core.$core->customer_id.$core->type_id}}</td>
                                                    <td class="text-center" style="width:10%;">{{$core->core}}</td>
                                                    <td class="text-center">{{$core->initial_customers}}</td>
                                                    <td class="text-center">{{$core->actual_customers}}</td>
                                                    <td style="cursor: default;" class="text-center" data-toggle="tooltip" data-placement="top" title="Minimum Length : {{$sub_section->sub_initial_length}}">{{$core->initial_end_cable}}</td>
                                                    <td style="cursor: default;" class="text-center" data-toggle="tooltip" data-placement="top" title="Max Total Loss : {{$sub_section->sub_initial_max_total_loss}}">{{$core->initial_total_loss_db}}</td>
                                                    <td class="text-center">{{$core->initial_loss_db_km}}</td>
                                                    <td class="text-center">
                                                        @php
                                                            if ($core->initial_remarks == 'OK') {
                                                                echo '<span class="badge bg-success text-light">'.$core->initial_remarks.'</span>';
                                                            } else {
                                                                echo '<span style="background:rgba(246,39,127,1);" class="badge text-light">'.$core->initial_remarks.'</span>';
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td class="text-center" style="font-weight: bold;">
                                                        {{$core->status}}
                                                    </td>
                                                    @if (auth()->user()->role == "engineering")
                                                        <td class="text-center"><a onclick="return confirmDeleteCore(event)" href="{{route('core.delete', ['core_id' => $core->core_id])}}" class="btn btn-danger btn-sm">Delete</a></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" class="text-center">Empty</td>
                                            </tr>
                                        @endif
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
                                <div style="text-align:right;" class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Select Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="{{route('sor.download', ['project_id'=>$project->project_id,'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id'=> $sub_section_id])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Download Sor Files</a>
                                        @if(auth()->user()->role == 'engineering' || auth()->user()->role == 'ms')
                                            <a href="{{route('core.edit', ['project_id'=>$project->project_id,'route_id'=>$route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id, 'sub_section_id'=>$sub_section_id])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Manage Core</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table custom-table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
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
                                        @if (count($cores) > 0 )
                                            @foreach ($cores as $core)
                                                <tr style="cursor: pointer;" class="clickable-row" onclick="window.location.href='{{ route('core.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'core' => $core->core, 'sub_section_id' => $sub_section_id]) }}';" >
                                                    <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core}}</td>
                                                    <td class="text-center">{{$core->project_id.$core->route_id.$core->segment_id.$core->section_id.$core->core.$core->customer_id.$core->type_id}}</td>
                                                    <td class="text-center" style="width:10%;">{{$core->core}}</td>
                                                    <td class="text-center">{{$core->initial_customers}}</td>
                                                    <td class="text-center">{{$core->actual_customers}}</td>
                                                    <td class="text-center">{{$core->actual_end_cable}}</td>
                                                    <td class="text-center">{{$core->actual_total_loss_db}}</td>
                                                    <td class="text-center">{{$core->actual_loss_db_km}}</td>
                                                    <td class="text-center">
                                                        @php
                                                            if ($core->actual_remarks == 'OK') {
                                                                echo '<span class="badge bg-success text-light">'.$core->actual_remarks.'</span>';
                                                            } else {
                                                                echo '<span style="background:rgba(246,39,127,1);" class="badge text-light">'.$core->actual_remarks.'</span>';
                                                            }
                                                        @endphp
                                                    </td>
                                                    <td class="text-center" style="font-weight: bold;">
                                                        {{$core->status}}
                                                    </td>
                                                    <td class="text-center">{{substr($core->notes, 0, 24)}}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11" class="text-center">Empty</td>
                                            </tr>
                                        @endif
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
    <script>
        function confirmDelete(event) {
            if (confirm("Are you sure you want to delete this sub section ?")) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        }
        function confirmDeleteCore(event) {
            if (confirm("Are you sure you want to delete this core ?")) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        }
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
