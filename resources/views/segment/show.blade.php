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

        .breadcrumb-item a{
            color: lightblue;
        }

        .hide {
    
            display: none;
        }
        .line:hover{
            background: blue !important;
            cursor: pointer;
        }
        .line:hover .hide{
            display: block;
            cursor: pointer;
            font-size:.6rem;
        }
        .line:hover p{
            display: block;
            cursor: pointer;
            margin:5px;
        }
        .nav-link{
            color: black;
        }
    </style>
@endsection

@php
    $project_id = $project->project_id;
    $segment_id = $segment->segment_id;

    $project = DB::table('project')->where('project_id', $project->project_id)->get()->first();

    switch($segment->route_id){
        case '1':
            $route_name = 'SUBMARINE';
            break;
        case '2':
            $route_name = 'INLAND';
            break;
        case '3':
            $route_name = 'LASTMILE';
            break;
    }

    $route_id = $segment->route_id;

    $check_section = DB::table('section')->where('project_id', $project->project_id)->where('route_id', $route_id)->where('segment_id', $segment->segment_id)->get();
    $get_sections = DB::table('section')->where('project_id', $project->project_id)->where('route_id', $route_id)->where('segment_id', $segment->segment_id)->get();

    $segment_availability_core_idle = 0;
    $segment_availability_core_capacity = 0;


    foreach ($get_sections as $item_section) {
        $main_core_capacity = $item_section->core_capacity;   
    
        // Fetch core data
        $cores = DB::table('core')
            ->where('project_id', $project_id)
            ->where('route_id', $route_id)
            ->where('segment_id', $segment_id)
            ->where('section_id', $item_section->section_id)
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
            
        // Booked OK and NOT OK based on remarks, not status
        $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
        $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();

        $core_used = $uniqueCores->where('status', 'USED')->count();
        $idle_core = $uniqueCores->where('status', 'IDLE')->count();
        $idle_general = $idle_core + $core_used;
        
        $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
        $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();

        $availability_core_idle = 0;
        $availability_core_capacity = 0;

        if(($core_capacity - $core_active) != 0){
            $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
            $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
        }else{
            $availability_core_idle = 0;
            $availability_core_capacity = 0;
        }

        if (($availability_core_idle != 0 && $availability_core_idle < $segment_availability_core_idle) || $segment_availability_core_idle == 0) {
            $segment_availability_core_idle = $availability_core_idle;
        }

        if (($availability_core_capacity != 0 && $availability_core_capacity < $segment_availability_core_capacity) || $segment_availability_core_capacity == 0) {
            $segment_availability_core_capacity = $availability_core_capacity;
        }
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
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem;"> #{{$segment->segment_id}} SEGMENT ({{$route_name}})</h3>
                    <h3 class="fw-semibold text-white" style="font-size: 1.8rem;">  {{$segment->segment_name}}</h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id, 'route_id'=> '-'])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                {{$segment->segment_name}}
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
            @if(auth()->user()->role == 'engineering')
                <div style="text-align:right;" class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a href="{{route('section.create', ['project_id' => $segment->project_id, 'route_id' => $segment->route_id ,'segment_id' => $segment->segment_id])}}" display="block" class="dropdown-item">Create Section</a>
                        <a href="{{route('section.edit', ['project_id' => $segment->project_id, 'route_id' => $segment->route_id ,'segment_id' => $segment->segment_id])}}" display="block" class="dropdown-item">Edit Section</a>
                         <a href="{{route('segment.delete', ['project_id' => $segment->project_id,'route_id' => $segment->route_id , 'segment_id' => $segment->segment_id])}}" onclick="return confirmDelete();" display="block" class="dropdown-item">Delete Segment</a>
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
                                        <div class="col-6">
                                            <div style="color:black;" class="card-title mb-9 fw-semibold"> Summary Segment</div>
                                            <div class="fw-semibold mb-3">Project Description : {{$project->project_description}}</div>
                                            <div class="fw-semibold mb-3">Segment Name : {{$segment->segment_name}}</div>
                                            <div class="fw-semibold mb-3">Number Of Section : {{count(DB::table('section')->where('segment_id', $segment->segment_id)->get())}}</div>
                                            @php
                                                $section = DB::table('section')->where('project_id', $project->project_id)->first();
                                            @endphp
                                            <div class="fw-semibold mb-3">Cable Type: {{ $section && $section->cable_type != null ? $section->cable_type : 'NO DATA' }}</div>
                                        </div>
                                        <!-- First Pie Chart -->
                                        <div class="col-3">
                                            <div class="card overflow-hidden text-white" style="background: linear-gradient(0deg, rgba(246,39,127,1) 0%, rgba(255,76,76,1) 100%); ">
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <h5 class="card-title fw-semibold text-white">
                                                            Availability : {{ \Illuminate\Support\Str::limit((float)$segment_availability_core_idle, 5, '.') ?? '0' }} %
                                                        </h5>
                                                        <span>From Core IDLE + BOOKED ( {{$idle_general + $core_booked}} )</span>
                                                    </div>
                                                    <hr>
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-center">
                                                                <input hidden id="idle_available_1" type="text" value="{{$segment_availability_core_idle ?? 0}}">
                                                                <input hidden id="idle_not_available_1" type="text" value="{{ 100 - (float)($segment_availability_core_idle ?? 0) }}">
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
                                                            Availability : {{ \Illuminate\Support\Str::limit((float)$segment_availability_core_capacity, 5, '.') ?? '0' }} %
                                                        </h5>
                                                        <span>From Core Capacity ( {{$core_capacity}} )</span>
                                                    </div>
                                                    <hr>
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-center">
                                                                <input hidden id="idle_available_2" type="text" value="{{$segment_availability_core_capacity ?? 0}}">
                                                                <input hidden id="idle_not_available_2" type="text" value="{{ 100 - (float)($segment_availability_core_capacity ?? 0) }}">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if (count($check_section) > 0)
            <div class="col-lg-12">
                <div class="card p-5 test">
                    <div class="tab-pane fade show active" id="{{$segment->segment_id}}" role="tabpanel">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="first-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-first_route" type="button" role="tab" aria-controls="first" aria-selected="true">Main Route</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="second-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-second_route" type="button" role="tab" aria-controls="second" aria-selected="false">Diversity Route</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="third-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-third_route" type="button" role="tab" aria-controls="third" aria-selected="false">3rd Route</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="forth-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-forth_route" type="button" role="tab" aria-controls="forth" aria-selected="false">4th Route</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="{{$segment->segment_id}}-first_route" role="tabpanel" >
                                <div class="card bg-light">
                                    <div style="height:6rem;"></div>
                                    @php
                                        // Fetch data from the database
                                        $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('section_route', '1_route')
                                            ->orderByRaw('CAST(section_id AS UNSIGNED)')
                                            ->get();

                                        $section_route_id = $segment->route_id;
                                    @endphp
                                    @if(count($sections) > 0)
                                        @foreach ($sections as $section)
                                            @php
                                                $sub_sections = 
                                                DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->where('customer_id', '000')
                                                ->get();
                                            @endphp

                                            {{-- here --}}
                                            <div style="display:flex; height:80px; margin: 0rem 5rem; justify-content:left;">
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                                </div>
                                                <div style="width:480px; height:30px; display:flex;">
                                                    @php
                                                        $section_id = $section->section_id;
                                                        $route_name = match($route_id) {
                                                            '1' => 'SUBMARINE',
                                                            '2' => 'INLAND',
                                                            '3' => 'LASTMILE',
                                                            default => 'UNKNOWN'
                                                        };

                                                        $main_core_capacity = $section->core_capacity;   
    
                                                        // Fetch core data
                                                        $cores = DB::table('core')
                                                            ->where('project_id', $project_id)
                                                            ->where('route_id', $route_id)
                                                            ->where('segment_id', $segment_id)
                                                            ->where('section_id', $section->section_id)
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
                                                            
                                                        // Booked OK and NOT OK based on remarks, not status
                                                        $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();

                                                        $core_used = $uniqueCores->where('status', 'USED')->count();
                                                        $idle_core = $uniqueCores->where('status', 'IDLE')->count();
                                                        $idle_general = $idle_core + $core_used;
                                                        
                                                        $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();

                                                        $availability_core_idle = 0;
                                                        $availability_core_capacity = 0;

                                                        if(($core_capacity - $core_active) != 0){
                                                            $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
                                                            $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
                                                        }else{
                                                            $availability_core_idle = 0;
                                                            $availability_core_capacity = 0;
                                                        }


                                                    @endphp
                                                    <a style=" position:relative; width:100%; height:10%; {{ $availability_core_idle < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$section_route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                        <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                            <div class="card-body">
                                                                <div class="row align-items-start">
                                                                    <div class="row">
                                                                        <table class="border-dark table table-bordered " style="width:100%; overflow-y:auto;">
                                                                            <thead class="text-dark" style="font-size:.8rem;">
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core idle : {{ \Illuminate\Support\Str::limit($availability_core_idle, 5, '') }} %</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core capacity : {{ \Illuminate\Support\Str::limit($availability_core_capacity, 5, '') }} %</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <thead class="bg-dark text-white" style="font-size:.8rem;">
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
                                                    </a>
                                                </div>
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->far_end}}</p>
                                                </div>
                                            </div>
                                            <div style="height:10px;"></div>
                                            {{-- here --}}
                                        @endforeach
                                    @else
                                        <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                                    @endif
                                </div>                
                            </div>
                            <div class="tab-pane fade show" id="{{$segment->segment_id}}-second_route" role="tabpanel" >
                                <div class="card bg-light">
                                    <div style="height:6rem;"></div>
                                    @php
                                        // Fetch data from the database
                                        $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('section_route', '2_route')
                                            ->orderByRaw('CAST(section_id AS UNSIGNED)')
                                            ->get();

                                        $section_route_id = $segment->route_id;
                                    @endphp
                                    @if(count($sections) > 0)
                                        @foreach ($sections as $section)
                                            @php
                                                $sub_sections = 
                                                DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->where('customer_id', '000')
                                                ->get();
                                            @endphp

                                            {{-- here --}}
                                            <div style="display:flex; height:80px; margin: 0rem 5rem; justify-content:left;">
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                                </div>
                                                <div style="width:480px; height:30px; display:flex;">
                                                    @php
                                                        $section_id = $section->section_id;
                                                        $route_name = match($route_id) {
                                                            '1' => 'SUBMARINE',
                                                            '2' => 'INLAND',
                                                            '3' => 'LASTMILE',
                                                            default => 'UNKNOWN'
                                                        };

                                                        $main_core_capacity = $section->core_capacity;   
    
                                                        // Fetch core data
                                                        $cores = DB::table('core')
                                                            ->where('project_id', $project_id)
                                                            ->where('route_id', $route_id)
                                                            ->where('segment_id', $segment_id)
                                                            ->where('section_id', $section->section_id)
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
                                                            
                                                        // Booked OK and NOT OK based on remarks, not status
                                                        $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();

                                                        $core_used = $uniqueCores->where('status', 'USED')->count();
                                                        $idle_core = $uniqueCores->where('status', 'IDLE')->count();
                                                        $idle_general = $idle_core + $core_used;
                                                        
                                                        $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();

                                                        $availability_core_idle = 0;
                                                        $availability_core_capacity = 0;

                                                        if(($core_capacity - $core_active) != 0){
                                                            $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
                                                            $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
                                                        }else{
                                                            $availability_core_idle = 0;
                                                            $availability_core_capacity = 0;
                                                        }


                                                    @endphp
                                                    <a style=" position:relative; width:100%; height:10%; {{ $availability_core_idle < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$section_route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                        <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                            <div class="card-body">
                                                                <div class="row align-items-start">
                                                                    <div class="row">
                                                                        <table class="border-dark table table-bordered " style="width:100%; overflow-y:auto;">
                                                                            <thead class="text-dark" style="font-size:.8rem;">
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core idle : {{ \Illuminate\Support\Str::limit($availability_core_idle, 5, '') }} %</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core capacity : {{ \Illuminate\Support\Str::limit($availability_core_capacity, 5, '') }} %</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <thead class="bg-dark text-white" style="font-size:.8rem;">
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
                                                    </a>
                                                </div>
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->far_end}}</p>
                                                </div>
                                            </div>
                                            <div style="height:10px;"></div>
                                            {{-- here --}}
                                        @endforeach
                                    @else
                                        <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                                    @endif
                                </div>                
                            </div>
                            <div class="tab-pane fade show" id="{{$segment->segment_id}}-third_route" role="tabpanel" >
                                <div class="card bg-light">
                                    <div style="height:6rem;"></div>
                                    @php
                                        // Fetch data from the database
                                        $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('section_route', '3_route')
                                            ->orderByRaw('CAST(section_id AS UNSIGNED)')
                                            ->get();

                                        $section_route_id = $segment->route_id;
                                    @endphp
                                    @if(count($sections) > 0)
                                        @foreach ($sections as $section)
                                            @php
                                                $sub_sections = 
                                                DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->where('customer_id', '000')
                                                ->get();
                                            @endphp

                                            {{-- here --}}
                                            <div style="display:flex; height:80px; margin: 0rem 5rem; justify-content:left;">
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                                </div>
                                                <div style="width:480px; height:30px; display:flex;">
                                                    @php
                                                        $section_id = $section->section_id;
                                                        $route_name = match($route_id) {
                                                            '1' => 'SUBMARINE',
                                                            '2' => 'INLAND',
                                                            '3' => 'LASTMILE',
                                                            default => 'UNKNOWN'
                                                        };

                                                        $main_core_capacity = $section->core_capacity;   
    
                                                        // Fetch core data
                                                        $cores = DB::table('core')
                                                            ->where('project_id', $project_id)
                                                            ->where('route_id', $route_id)
                                                            ->where('segment_id', $segment_id)
                                                            ->where('section_id', $section->section_id)
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
                                                            
                                                        // Booked OK and NOT OK based on remarks, not status
                                                        $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();

                                                        $core_used = $uniqueCores->where('status', 'USED')->count();
                                                        $idle_core = $uniqueCores->where('status', 'IDLE')->count();
                                                        $idle_general = $idle_core + $core_used;
                                                        
                                                        $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();

                                                        $availability_core_idle = 0;
                                                        $availability_core_capacity = 0;

                                                        if(($core_capacity - $core_active) != 0){
                                                            $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
                                                            $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
                                                        }else{
                                                            $availability_core_idle = 0;
                                                            $availability_core_capacity = 0;
                                                        }


                                                    @endphp
                                                    <a style=" position:relative; width:100%; height:10%; {{ $availability_core_idle < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$section_route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                        <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                            <div class="card-body">
                                                                <div class="row align-items-start">
                                                                    <div class="row">
                                                                        <table class="border-dark table table-bordered " style="width:100%; overflow-y:auto;">
                                                                            <thead class="text-dark" style="font-size:.8rem;">
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core idle : {{ \Illuminate\Support\Str::limit($availability_core_idle, 5, '') }} %</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core capacity : {{ \Illuminate\Support\Str::limit($availability_core_capacity, 5, '') }} %</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <thead class="bg-dark text-white" style="font-size:.8rem;">
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
                                                    </a>
                                                </div>
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->far_end}}</p>
                                                </div>
                                            </div>
                                            <div style="height:10px;"></div>
                                            {{-- here --}}
                                        @endforeach
                                    @else
                                        <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                                    @endif
                                </div>                
                            </div>
                            <div class="tab-pane fade show" id="{{$segment->segment_id}}-forth_route" role="tabpanel" >
                                <div class="card bg-light">
                                    <div style="height:6rem;"></div>
                                    @php
                                        // Fetch data from the database
                                        $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('section_route', '4_route')
                                            ->orderByRaw('CAST(section_id AS UNSIGNED)')
                                            ->get();

                                        $section_route_id = $segment->route_id;
                                    @endphp
                                    @if(count($sections) > 0)
                                        @foreach ($sections as $section)
                                            @php
                                                $sub_sections = 
                                                DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->where('customer_id', '000')
                                                ->get();
                                            @endphp

                                            {{-- here --}}
                                            <div style="display:flex; height:80px; margin: 0rem 5rem; justify-content:left;">
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                                </div>
                                                <div style="width:480px; height:30px; display:flex;">
                                                    @php
                                                        $section_id = $section->section_id;
                                                        $route_name = match($route_id) {
                                                            '1' => 'SUBMARINE',
                                                            '2' => 'INLAND',
                                                            '3' => 'LASTMILE',
                                                            default => 'UNKNOWN'
                                                        };

                                                        $main_core_capacity = $section->core_capacity;   
    
                                                        // Fetch core data
                                                        $cores = DB::table('core')
                                                            ->where('project_id', $project_id)
                                                            ->where('route_id', $route_id)
                                                            ->where('segment_id', $segment_id)
                                                            ->where('section_id', $section->section_id)
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
                                                            
                                                        // Booked OK and NOT OK based on remarks, not status
                                                        $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();

                                                        $core_used = $uniqueCores->where('status', 'USED')->count();
                                                        $idle_core = $uniqueCores->where('status', 'IDLE')->count();
                                                        $idle_general = $idle_core + $core_used;
                                                        
                                                        $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
                                                        $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();

                                                        $availability_core_idle = 0;
                                                        $availability_core_capacity = 0;

                                                        if(($core_capacity - $core_active) != 0){
                                                            $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
                                                            $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
                                                        }else{
                                                            $availability_core_idle = 0;
                                                            $availability_core_capacity = 0;
                                                        }


                                                    @endphp
                                                    <a style=" position:relative; width:100%; height:10%; {{ $availability_core_idle < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$section_route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                        <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                            <div class="card-body">
                                                                <div class="row align-items-start">
                                                                    <div class="row">
                                                                        <table class="border-dark table table-bordered " style="width:100%; overflow-y:auto;">
                                                                            <thead class="text-dark" style="font-size:.8rem;">
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core idle : {{ \Illuminate\Support\Str::limit($availability_core_idle, 5, '') }} %</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core capacity : {{ \Illuminate\Support\Str::limit($availability_core_capacity, 5, '') }} %</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <thead class="bg-dark text-white" style="font-size:.8rem;">
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
                                                    </a>
                                                </div>
                                                <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                    <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->far_end}}</p>
                                                </div>
                                            </div>
                                            <div style="height:10px;"></div>
                                            {{-- here --}}
                                        @endforeach
                                    @else
                                        <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                                    @endif
                                </div>                
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        @endif  
    </div>

    <div class="row">
        <div class="col-lg-12 align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <h5 style="text-align:right;" class="card-title fw-semibold mb-4">Sections Lists</h5>
                    <div class="table-responsive">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="first-tab" data-bs-toggle="pill" data-bs-target="#first" type="button" role="tab" aria-controls="first" aria-selected="true">Main Route</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="second-tab" data-bs-toggle="pill" data-bs-target="#second" type="button" role="tab" aria-controls="second" aria-selected="false">Diversity Route</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="third-tab" data-bs-toggle="pill" data-bs-target="#third" type="button" role="tab" aria-controls="third" aria-selected="false">3rd Route</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="forth-tab" data-bs-toggle="pill" data-bs-target="#forth" type="button" role="tab" aria-controls="forth" aria-selected="false">4th Route</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="first" role="tabpanel" >
                                <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="bg-dark fs-4">
                                            <tr>
                                                <th style="width:10%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Section ID</h6>
                                                </th>
                                                <th style="width:50%;">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Name</h6>
                                                </th>
                                                
                                                <th style="width:20%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                                                </th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('route_id', $segment->route_id)
                                            ->where('section_route', '1_route')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            @php
                                                $number_of_requests = count(DB::table('sor_request')->where('section_id', $section->section_id)->where('status', 'PROCESS')->get());   
                                            @endphp
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                
                                                <td style="width:70%;" class="px-5">
                                                    <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                        {{$section->section_name}} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; 
                                              
                                                        @if(auth()->user()->role == 'ms')
                                                            @if($number_of_requests > 0)
                                                                <span class="badge bg-danger">{{$number_of_requests}}</span>
                                                            @endif
                                                        @endif
                                                    </a>
                                                </td>
                                                <td style="width:20%; text-align: center;">
                                                    <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=> $segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach                                                
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="second" role="tabpanel" >
                                <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="bg-dark fs-4">
                                            <tr>
                                                <th style="width:10%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Section ID</h6>
                                                </th>
                                                <th style="width:50%;">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Name</h6>
                                                </th>
                                                <th style="width:20%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                                                </th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('route_id', $segment->route_id)
                                            ->where('section_route', '2_route')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                <td style="width:70%;" class="px-5">{{$section->section_name}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach                                                
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="third" role="tabpanel">
                                <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="bg-dark fs-4">
                                            <tr>
                                                <th style="width:10%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Section ID</h6>
                                                </th>
                                                <th style="width:50%;">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Name</h6>
                                                </th>
                                                
                                                <th style="width:20%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                                                </th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('route_id', $segment->route_id)
                                            ->where('section_route', '3_route')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                <td style="width:70%;" class="px-5">{{$section->section_name}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    @switch($section->section_type)
                                                        @case('regular')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @case('with_sub_section')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @default
                                                            <a href="" class="btn btn-primary">Detail</a>
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @endforeach                                                
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="forth" role="tabpanel">
                                <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="bg-dark fs-4">
                                            <tr>
                                                <th style="width:10%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Section ID</h6>
                                                </th>
                                                <th style="width:50%;">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Name</h6>
                                                </th>
                                                
                                                <th style="width:20%;" class="text-center">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                                                </th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sections = DB::table('section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('route_id', $segment->route_id)
                                            ->where('section_route', '4_route')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                <td style="width:70%;" class="px-5">{{$section->section_name}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    @switch($section->section_type)
                                                        @case('regular')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @case('with_sub_section')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @default
                                                            <a href="" class="btn btn-primary">Detail</a>
                                                    @endswitch
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
        </div>
    </div>
@endsection

@section('script')
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
    <script>
        function confirmDelete() {
            if (confirm("Are you sure you want to delete this segment ?")) {
                return true;
            } else {
                return false;
            }
        }
    </script>
@endsection
