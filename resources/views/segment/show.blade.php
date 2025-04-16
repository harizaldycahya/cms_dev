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

@section('header')
    @php
        $project = DB::table('project')->where('project_id', $project->project_id)->get()->first();
    @endphp
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
                    <h3 class="fw-semibold text-white" style="font-size: 2rem;">Segment : {{$segment->segment_name}}</h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id])}}">{{$project->project_name}}</a>
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
                        <a href="{{route('section.create', ['project_id' => $segment->project_id, 'segment_id' => $segment->segment_id])}}" display="block" class="dropdown-item">Create Section</a>
                        <a href="{{route('segment.edit', $segment->segment_id)}}" display="block" class="dropdown-item">Manage Segment</a>
                        <a href="{{route('segment.delete', ['project_id' => $segment->project_id, 'segment_id' => $segment->segment_id])}}" onclick="return confirmDelete();" display="block" class="dropdown-item">Delete Segment</a>
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
                                        <div class="col-8">
                                            <div style="color:black;" class="card-title mb-9 fw-semibold"> Summary Segment</div>
                                            <div class="fw-semibold mb-3">Project Description : {{$project->project_description}}</div>
                                            <div class="fw-semibold mb-3">Segment Name : {{$segment->segment_name}}</div>
                                            <div class="fw-semibold mb-3">Number Of Section : {{count(DB::table('section')->where('segment_id', $segment->segment_id)->get())}}</div>
                                            @php
                                                $section = DB::table('section')->where('project_id', $project->project_id)->first();
                                            @endphp
                                            <div class="fw-semibold mb-3">Cable Type: {{ $section && $section->cable_type != null ? $section->cable_type : 'NO DATA' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card p-5">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="first-tab" data-bs-toggle="pill" data-bs-target="#first_route" type="button" role="tab" aria-controls="first" aria-selected="true">Main Route</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="second-tab" data-bs-toggle="pill" data-bs-target="#second_route" type="button" role="tab" aria-controls="second" aria-selected="false">Diversity Route</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="third-tab" data-bs-toggle="pill" data-bs-target="#third_route" type="button" role="tab" aria-controls="third" aria-selected="false">3rd Route</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="forth-tab" data-bs-toggle="pill" data-bs-target="#forth_route" type="button" role="tab" aria-controls="forth" aria-selected="false">4th Route</button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="first_route" role="tabpanel" >
                        <div class="card bg-light">
                            <div style="height:6rem;"></div>
                            @php
                                // Fetch data from the database
                                $sections = DB::table('section')
                                    ->where('segment_id', $segment->segment_id)
                                    ->where('section_route', '1_route')
                                    ->get();
                            @endphp

                            @if(count($sections) > 0)
                                @foreach ($sections as $section)
                                    @if($section->section_type == 'regular')
                                        <div style="display:flex; height:80px; justify-content:center;">
                                            <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                            </div>
                                            <div style="width:480px; height:30px; display:flex;">
                                                @php
                                                    $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->get());
                                                    $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->get());
                                                    $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());
                                                    $actual_core_capacity = $section->core_capacity;
                                                    if($actual_core_capacity - $actual_core_aktif != 0){
                                                        $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                    }else{
                                                        $actual_availibility = "";
                                                    }
    
    
                                                    $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->get());
                                                    $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->get());
                                                    $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());
                                                    $initial_core_capacity = $section->core_capacity;
                                                    if($initial_core_capacity - $initial_core_aktif != 0){
                                                        $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                    }else{
                                                        $initial_availability = "";
                                                    }
                                                
                                                @endphp
                                                <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                    <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                        <div class="card-body">
                                                            <div class="row align-items-start">
                                                                <div class="row">
                                                                    <table class="table table-bordered" style="width:100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                    {{$section->section_name}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($initial_availability, 5, $end=' ') }}  %</td>
                                                                                <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                    @endif
    
                                    @if($section->section_type == 'with_sub_section')
                                        @php
                                            $sub_sections = 
                                            DB::table('sub_section')
                                            ->where('section_id', $section->section_id)
                                            ->where('sub_owner', 'trias')
                                            ->groupBy('ropa_id')
                                            ->get();
                                        @endphp
                                        
                                        @foreach ($sub_sections as $sub_section)
                                            @if($sub_section->ropa_id != null)
                                                @php
                                                    $ropa_sub_section = DB::table('sub_section')->where('ropa_id', $sub_section->ropa_id)->get();
                                                @endphp
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $initial_core_capacity = count(DB::table('core')->where('section_id', $section->section_id)->get());   
                                                            $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->where('initial_booked', 'NO')->get());   
                                                            $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->where('initial_booked', 'NO')->get());   
                                                            $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());   
                                                            $initial_core_booked_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->where('initial_booked', 'YES')->get());
                                                            $initial_core_booked_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->where('initial_booked', 'YES')->get());

                                                            $actual_core_capacity = count(DB::table('core')->where('section_id', $section->section_id)->get());   
                                                            $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->where('actual_booked', 'NO')->get());   
                                                            $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->where('actual_booked', 'NO')->get());   
                                                            $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());   
                                                            $actual_core_booked_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->where('actual_booked', 'YES')->get());
                                                            $actual_core_booked_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->where('actual_booked', 'YES')->get());
                                                        
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - ($actual_core_aktif + $actual_core_booked_ok + $actual_core_booked_not_ok))) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }

                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availibility = ($initial_core_ok / ($initial_core_capacity - ($initial_core_aktif + $initial_core_booked_ok + $initial_core_booked_not_ok))) * 100;
                                                            }else{
                                                                $initial_availibility = "";
                                                            }
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[0]->sub_near_end}} - {{$ropa_sub_section[0]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>Main Core Capacity : {{$section->core_capacity}}</td>
                                                                                        <td>Main Core Capacity : {{$section->core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td>Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            SOLD Core : {{$initial_core_aktif + $initial_core_booked_ok + $initial_core_booked_not_ok}}
                                                                                        </td>
                                                                                        <td>
                                                                                            SOLD Core : {{$actual_core_aktif + $actual_core_booked_ok + $actual_core_booked_not_ok}} 
                                                                                            <br>
                                                                                            <ul style="padding:1rem 2rem;">
                                                                                                <li style="list-style-type:square" > Active : {{$actual_core_aktif}}</li>
                                                                                                <li style="list-style-type:square" > 
                                                                                                    Booked : {{$actual_core_booked_ok + $actual_core_booked_not_ok}}
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
                                                                                        <td>IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td>IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}
                                                                                            <br>
                                                                                            <ul style="padding:1rem 2rem;">
                                                                                                <li style="list-style-type:square" > OK : {{$actual_core_ok}}</li>
                                                                                                <li style="list-style-type:square" > NOT OK : {{$actual_core_not_ok}}</li>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_far_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[1]->sub_near_end}} - {{$ropa_sub_section[1]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[1]->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:480px; height:30px; display:flex;">
                                                        @php
                                                            $initial_core_capacity = count(DB::table('core')->where('section_id', $section->section_id)->get());   
                                                            $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->where('initial_booked', 'NO')->get());   
                                                            $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->where('initial_booked', 'NO')->get());   
                                                            $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());   
                                                            $initial_core_booked_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->where('initial_booked', 'YES')->get());
                                                            $initial_core_booked_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->where('initial_booked', 'YES')->get());

                                                            $actual_core_capacity = count(DB::table('core')->where('section_id', $section->section_id)->get());   
                                                            $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->where('actual_booked', 'NO')->get());   
                                                            $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->where('actual_booked', 'NO')->get());   
                                                            $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());   
                                                            $actual_core_booked_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->where('actual_booked', 'YES')->get());
                                                            $actual_core_booked_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->where('actual_booked', 'YES')->get());
                                                        
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - ($actual_core_aktif + $actual_core_booked_ok + $actual_core_booked_not_ok))) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }

                                                            if($initial_core_capacity - $actual_core_aktif != 0){
                                                                $initial_availibility = ($initial_core_ok / ($initial_core_capacity - ($initial_core_aktif + $initial_core_booked_ok + $initial_core_booked_not_ok))) * 100;
                                                            }else{
                                                                $initial_availibility = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$sub_section->sub_section_name}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>Main Core Capacity : {{$section->core_capacity}}</td>
                                                                                        <td>Main Core Capacity : {{$section->core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td>Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            SOLD Core : {{$initial_core_aktif + $initial_core_booked_ok + $initial_core_booked_not_ok}}
                                                                                        </td>
                                                                                        <td>
                                                                                            SOLD Core : {{$actual_core_aktif + $actual_core_booked_ok + $actual_core_booked_not_ok}} 
                                                                                            <br>
                                                                                            <ul style="padding:1rem 2rem;">
                                                                                                <li style="list-style-type:square" > Active : {{$actual_core_aktif}}</li>
                                                                                                <li style="list-style-type:square" > 
                                                                                                    Booked : {{$actual_core_booked_ok + $actual_core_booked_not_ok}}
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
                                                                                        <td>IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td>IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}
                                                                                            <br>
                                                                                            <ul style="padding:1rem 2rem;">
                                                                                                <li style="list-style-type:square" > OK : {{$actual_core_ok}}</li>
                                                                                                <li style="list-style-type:square" > NOT OK : {{$actual_core_not_ok}}</li>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                                <div style="height:10px;"></div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                            @endif
                        </div>                
                    </div>
                    <div class="tab-pane fade" id="second_route" role="tabpanel" >
                        <div class="card bg-light">
                            <div style="height:6rem;"></div>
                            @php
                                // Fetch data from the database
                                $sections = DB::table('section')
                                    ->where('segment_id', $segment->segment_id)
                                    ->where('section_route', '2_route')
                                    ->get();
                            @endphp

                            @if(count($sections) > 0)
                                @foreach ($sections as $section)
                                    @if($section->section_type == 'regular')
                                        <div style="display:flex; height:80px; justify-content:center;">
                                            <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                            </div>
                                            <div style="width:480px; height:30px; display:flex;">
                                                @php
                                                    $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->get());
                                                    $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->get());
                                                    $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());
                                                    $actual_core_capacity = $section->core_capacity;
                                                    if($actual_core_capacity - $actual_core_aktif != 0){
                                                        $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                    }else{
                                                        $actual_availibility = "";
                                                    }
    
    
                                                    $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->get());
                                                    $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->get());
                                                    $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());
                                                    $initial_core_capacity = $section->core_capacity;
                                                    if($initial_core_capacity - $initial_core_aktif != 0){
                                                        $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                    }else{
                                                        $initial_availability = "";
                                                    }
                                                
                                                @endphp
                                                <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                    <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                        <div class="card-body">
                                                            <div class="row align-items-start">
                                                                <div class="row">
                                                                    <table class="table table-bordered" style="width:100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                    {{$section->section_name}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                    @endif
    
                                    @if($section->section_type == 'with_sub_section')
                                        @php
                                            $sub_sections = 
                                            DB::table('sub_section')
                                            ->where('section_id', $section->section_id)
                                            ->where('sub_owner', 'trias')
                                            ->groupBy('ropa_id')
                                            ->get();
                                        @endphp
                                        
                                        @foreach ($sub_sections as $sub_section)
                                            @if($sub_section->ropa_id != null)
                                                @php
                                                    $ropa_sub_section = DB::table('sub_section')->where('ropa_id', $sub_section->ropa_id)->get();
                                                @endphp
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[0]->sub_near_end}} - {{$ropa_sub_section[0]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_far_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[1]->sub_near_end}} - {{$ropa_sub_section[1]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[1]->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:480px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$sub_section->sub_section_name}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                                <div style="height:10px;"></div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="third_route" role="tabpanel">
                        <div class="card bg-light">
                            <div style="height:6rem;"></div>
                            @php
                                // Fetch data from the database
                                $sections = DB::table('section')
                                    ->where('segment_id', $segment->segment_id)
                                    ->where('section_route', '3_route')
                                    ->get();
                            @endphp

                            @if(count($sections) > 0)
                                @foreach ($sections as $section)
                                    @if($section->section_type == 'regular')
                                        <div style="display:flex; height:80px; justify-content:center;">
                                            <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                            </div>
                                            <div style="width:480px; height:30px; display:flex;">
                                                @php
                                                    $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->get());
                                                    $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->get());
                                                    $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());
                                                    $actual_core_capacity = $section->core_capacity;
                                                    if($actual_core_capacity - $actual_core_aktif != 0){
                                                        $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                    }else{
                                                        $actual_availibility = "";
                                                    }
    
    
                                                    $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->get());
                                                    $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->get());
                                                    $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());
                                                    $initial_core_capacity = $section->core_capacity;
                                                    if($initial_core_capacity - $initial_core_aktif != 0){
                                                        $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                    }else{
                                                        $initial_availability = "";
                                                    }
                                                
                                                @endphp
                                                <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                    <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                        <div class="card-body">
                                                            <div class="row align-items-start">
                                                                <div class="row">
                                                                    <table class="table table-bordered" style="width:100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                    {{$section->section_name}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                    @endif
    
                                    @if($section->section_type == 'with_sub_section')
                                        @php
                                            $sub_sections = 
                                            DB::table('sub_section')
                                            ->where('section_id', $section->section_id)
                                            ->where('sub_owner', 'trias')
                                            ->groupBy('ropa_id')
                                            ->get();
                                        @endphp
                                        
                                        @foreach ($sub_sections as $sub_section)
                                            @if($sub_section->ropa_id != null)
                                                @php
                                                    $ropa_sub_section = DB::table('sub_section')->where('ropa_id', $sub_section->ropa_id)->get();
                                                @endphp
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[0]->sub_near_end}} - {{$ropa_sub_section[0]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_far_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[1]->sub_near_end}} - {{$ropa_sub_section[1]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[1]->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:480px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$sub_section->sub_section_name}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                                <div style="height:10px;"></div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="forth_route" role="tabpanel">
                        <div class="card bg-light">
                            <div style="height:6rem;"></div>
                            @php
                                // Fetch data from the database
                                $sections = DB::table('section')
                                    ->where('segment_id', $segment->segment_id)
                                    ->where('section_route', '4_route')
                                    ->get();
                            @endphp

                            @if(count($sections) > 0)
                                @foreach ($sections as $section)
                                    @if($section->section_type == 'regular')
                                        <div style="display:flex; height:80px; justify-content:center;">
                                            <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$section->near_end}}</p>
                                            </div>
                                            <div style="width:480px; height:30px; display:flex;">
                                                @php
                                                    $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->get());
                                                    $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->get());
                                                    $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());
                                                    $actual_core_capacity = $section->core_capacity;
                                                    if($actual_core_capacity - $actual_core_aktif != 0){
                                                        $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                    }else{
                                                        $actual_availibility = "";
                                                    }
    
    
                                                    $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->get());
                                                    $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->get());
                                                    $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());
                                                    $initial_core_capacity = $section->core_capacity;
                                                    if($initial_core_capacity - $initial_core_aktif != 0){
                                                        $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                    }else{
                                                        $initial_availability = "";
                                                    }
                                                
                                                @endphp
                                                <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
                                                    <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                        <div class="card-body">
                                                            <div class="row align-items-start">
                                                                <div class="row">
                                                                    <table class="table table-bordered" style="width:100%;">
                                                                        <thead>
                                                                            <tr>
                                                                                <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                    {{$section->section_name}}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="font-size: .8rem;"></td>
                                                                                <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                    @endif
    
                                    @if($section->section_type == 'with_sub_section')
                                        @php
                                            $sub_sections = 
                                            DB::table('sub_section')
                                            ->where('section_id', $section->section_id)
                                            ->where('sub_owner', 'trias')
                                            ->groupBy('ropa_id')
                                            ->get();
                                        @endphp
                                        
                                        @foreach ($sub_sections as $sub_section)
                                            @if($sub_section->ropa_id != null)
                                                @php
                                                    $ropa_sub_section = DB::table('sub_section')->where('ropa_id', $sub_section->ropa_id)->get();
                                                @endphp
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[0]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[0]->sub_near_end}} - {{$ropa_sub_section[0]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[0]->sub_far_end}}</p>
                                                    </div>
                                                    <div style="width:225px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $ropa_sub_section[1]->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$ropa_sub_section[1]->sub_near_end}} - {{$ropa_sub_section[1]->sub_far_end}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$ropa_sub_section[1]->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="display:flex; height:80px; justify-content:center;">
                                                    <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_near_end}}</p>
                                                    </div>
                                                    <div style="width:480px; height:30px; display:flex;">
                                                        @php
                                                            $actual_core_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'OK')->get());
                                                            $actual_core_not_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $actual_core_aktif = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $actual_core_capacity = $section->core_capacity;
                                                            if($actual_core_capacity - $actual_core_aktif != 0){
                                                                $actual_availibility = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                            }else{
                                                                $actual_availibility = "";
                                                            }
    
    
                                                            $initial_core_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'OK')->get());
                                                            $initial_core_not_ok = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'NOT OK')->get());
                                                            $initial_core_aktif = count(DB::table('core')->where('sub_section_id', $sub_section->sub_section_id)->where('initial_remarks', 'AKTIF')->get());
                                                            $initial_core_capacity = $section->core_capacity;
                                                            if($initial_core_capacity - $initial_core_aktif != 0){
                                                                $initial_availability = ($initial_core_ok / ($initial_core_capacity - $initial_core_aktif)) * 100;
                                                            }else{
                                                                $initial_availability = "";
                                                            }
                                                        
                                                        @endphp
                                                        <a style=" position:relative; width:100%; height:10%; {{ $actual_availibility < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
                                                            <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                                                <div class="card-body">
                                                                    <div class="row align-items-start">
                                                                        <div class="row">
                                                                            <table class="table table-bordered" style="width:100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th colspan="2" style="font-size: 1rem; font-weight:bold;">
                                                                                            {{$sub_section->sub_section_name}}
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Initial</th>
                                                                                        <th style="font-size: .8rem; font-weight:bold;">Actual</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $initial_availability == null ? 0 : \Illuminate\Support\Str::limit($availability, 5, $end=' ') }}  %</td>
                                                                                        <td style="font-size: .8rem;">Availability : {{ $actual_availibility == null ? 0 : \Illuminate\Support\Str::limit($actual_availibility, 5, $end=' ') }}  %</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$initial_core_capacity}}</td>
                                                                                        <td style="font-size: .8rem;">Core Capacity : {{$actual_core_capacity}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">SOLD Core : {{$initial_core_aktif}}</td>
                                                                                        <td style="font-size: .8rem;">ACTIVE Core: {{$actual_core_aktif}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$initial_core_ok + $initial_core_not_ok}}</td>
                                                                                        <td style="font-size: .8rem;">IDLE Core : {{$actual_core_ok + $actual_core_not_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="font-size: .8rem;"></td>
                                                                                        <td style="font-size: .8rem;" style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                                        <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$sub_section->sub_far_end}}</p>
                                                    </div>
                                                </div>
                                                <div style="height:10px;"></div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>  
        </div>
        <div class="col-lg-4">
        </div>
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
                                                <th style="width:20%">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Category</h6>
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
                                            ->where('section_route', '1_route')
                                            ->orderBy('cable_category', 'asc')
                                            ->get();
                                            
                                        @endphp
                                        @foreach ($sections as $section)
                                            @php
                                                $number_of_requests = count(DB::table('draf_sor')->where('section_id', $section->section_id)->where('status', 'PROCESS')->get());   
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
                                                <td style="width:20%;" class="px-5">{{$section->cable_category}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    @switch($section->section_type)
                                                        @case('regular')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @case('with_sub_section')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
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
                                                <th style="width:20%">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Category</h6>
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
                                            ->where('section_route', '2_route')
                                            ->orderBy('cable_category', 'asc')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                <td style="width:70%;" class="px-5">{{$section->section_name}}</td>
                                                <td style="width:20%;" class="px-5">{{$section->cable_category}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
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
                                                <th style="width:20%">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Category</h6>
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
                                            ->where('section_route', '3_route')
                                            ->orderBy('cable_category', 'asc')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                <td style="width:70%;" class="px-5">{{$section->section_name}}</td>
                                                <td style="width:20%;" class="px-5">{{$section->cable_category}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    @switch($section->section_type)
                                                        @case('regular')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @case('with_sub_section')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
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
                                                <th style="width:20%">
                                                    <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Section Category</h6>
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
                                            ->where('section_route', '4_route')
                                            ->orderBy('cable_category', 'asc')
                                            ->get();
                                        @endphp
                                        @foreach ($sections as $section)
                                            <tr>
                                                <td style="width:10%; text-align: center;">{{$section->section_id}}</td>
                                                <td style="width:70%;" class="px-5">{{$section->section_name}}</td>
                                                <td style="width:20%;" class="px-5">{{$section->cable_category}}</td>
                                                <td style="width:20%; text-align: center;">
                                                    @switch($section->section_type)
                                                        @case('regular')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
                                                            @break
                                                        @case('with_sub_section')
                                                            <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="btn btn-primary">Detail</a>
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
        function confirmDelete() {
            if (confirm("Are you sure you want to delete this segment ?")) {
                return true;
            } else {
                return false;
            }
        }
    </script>
@endsection
