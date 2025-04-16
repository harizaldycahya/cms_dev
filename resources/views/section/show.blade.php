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

                /* Add hover effect to change the background color */
        tr.clickable-row:hover {
            background-color: #e8571e; /* Change this to your desired hover color */
            color: white;
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
    @php
        $section = DB::table('section')->where('section_id', $section_id)->get()->first();
    @endphp
    <div class="card bg-dark text-white shadow-lg position-relative overflow-hidden">
        <div class="card-body px-5 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 2rem;">Section : {{$section->section_name}}</h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('segment.show', ['project_id'=> $project->project_id, 'segment_id'=>$segment->segment_id])}}">{{$segment->segment_name}}</a>
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
    @php
        $section = DB::table('section')->where('section_id', $section_id)->get()->first();
    @endphp

    @if( $section->section_type ==  'with_sub_section')

        {{-- SUB SECTION --}}
        <div class="col-lg-12 align-items-stretch">
            <div class="card w-100 p-4">
                @if(auth()->user()->role == 'engineering')
                    <div style="text-align:right;" class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{route('sor.download', ['type'=>'section', 'project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Download Actual Sor Files</a>
                                <a href="{{route('sub_section.create', ['section_id' => $section_id])}}" display="block" class="dropdown-item">Add Sub Section</a>
                                <a href="{{route('section.edit', ['project_id' => $project->project_id, 'segment_id' => $segment->segment_id, 'section_id' => $section_id])}}" display="block" class="dropdown-item">Edit Section</a>
                                <a href="{{route('core.upload_excel', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" display="block" class="dropdown-item">Upload Excel</a>
                                <a href="{{route('section.delete', ['project_id' => $project->project_id, 'segment_id' => $segment->segment_id, 'section_id' => $section_id])}}" onclick="return confirmDelete(event);" display="block" class="dropdown-item">Delete Section</a>  
                        </div>
                    </div>
                @endif    
                @if(auth()->user()->role == 'ms')
                    <div style="text-align:right;" class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{route('sor.download', ['type'=>'section', 'project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Download Actual Sor Files</a>
                                <a href="{{route('core.upload_excel', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" display="block" class="dropdown-item">Upload Excel</a> 
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
                                            <div class="col-4">
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
                                                <div class="fw-semibold mb-3">Cable Category : {{$section->cable_category}}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-semibold mb-3">Cable Type : {{$section->cable_type}}</div>
                                                <div class="fw-semibold mb-3">First RFS : {{$section->first_rfs}}</div>
                                                <div class="fw-semibold mb-3">Site Owner Near End : {{$section->site_owner_near_end}}</div>
                                                <div class="fw-semibold mb-3">Site Owner Far End : {{$section->site_owner_far_end}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-start">
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
                                                        <table class="table table-striped table-bordered" style="width:100%;">
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
                                                                    ->where('section_id', $section_id)
                                                                    ->orderByRaw('CAST(core AS UNSIGNED) ASC')
                                                                    ->get();
                                                                @endphp
        
                                                                @foreach ($cores as $core)
                                                                <tr>
                                                                    <td>{{$core->core}}</td>
                                                                    <td>{{$core->initial_customers == '' ? 'TRIAS' : $core->initial_customers}}</td>
                                                                    <td>{{$core->actual_customers == '' ? 'TRIAS' : $core->actual_customers}}</td>
                                                                    <td>
                                                                        <?php
                                                                            if (($core->actual_customers == null || trim($core->actual_customers) == '') || 
                                                                                strcasecmp($core->actual_customers, 'TRIAS') == 0 || 
                                                                                strcasecmp($core->actual_customers, 'TRIASMITRA') == 0) {
                                                                                if ($core->actual_remarks == 'OK') {
                                                                                    echo '<span class="badge bg-success text-light">AVAILABLE</span>';
                                                                                } else {
                                                                                    echo '<span class="badge bg-danger text-light">NOT AVAILABLE</span>';
                                                                                }
                                                                            }else{
                                                                                echo '<span class="badge bg-danger text-light">NOT AVAILABLE</span>';
                                                                            }
                                                                        ?>
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
                                        <div class="card" >
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
                                                        <table class="table table-bordered" style="width:100%; overflow-y:auto;">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Initial</th>
                                                                    <th scope="col">Actual</th>
                                                                </tr>
                                                            </thead>
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
                                                            @endphp
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
                        <h5 style="text-align:right;" class="card-title fw-semibold mb-4">Sub Sections Lists</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                <thead class="bg-dark fs-4">
                                        <tr>
                                            <th style="width:10%;">
                                                <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Sub Section ID</h6>
                                            </th>
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
                                    @php
                                        $sub_sections = DB::table('sub_section')
                                        ->where('project_id', $project->project_id)
                                        ->where('segment_id', $segment->segment_id)
                                        ->where('section_id', $section->section_id)
                                        ->get();
                                    @endphp                        

                                    @foreach ($sub_sections as $sub_section)
                                        @php
                                            $number_of_requests = count(DB::table('draf_sor')->where('sub_section_id', $sub_section->sub_section_id)->where('status', 'PROCESS')->get());   
                                        @endphp
                                        <tr>
                                            <td class="px-5">
                                                <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                    {{$sub_section->sub_section_id}}
                                                </a>
                                            </td>
                                            <td class="px-5">
                                                <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                    {{$sub_section->sub_section_name}} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; 
                                                    
                                                    @if(auth()->user()->role == 'ms')
                                                        @if($number_of_requests > 0)
                                                            <span class="badge bg-danger">{{$number_of_requests}}</span>
                                                        @endif
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="px-5">
                                                <a type="button" class=" btn-notification" style="text-transform: uppercase; cursor: default; color:black;">
                                                    {{$sub_section->sub_owner}} 
                                                    
                                                </a>
                                            </td>
                                            <td style="text-align: center;">
                                                <a href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id'=> $sub_section->sub_section_id])}}" class="btn btn-primary">Detail</a>
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
    @else
        {{-- REGULAR SECTION --}}
        <div class="col-lg-12 align-items-stretch">
            <div class="card w-100 p-4">
                <div class="row">
                    @php
                         $number_of_requests = count(DB::table('draf_sor')->where('section_id', $section_id)->where('status', 'PROCESS')->get());
                    @endphp
                    <div class="col-2">
                        <a href="{{route('sor.summary', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" type="button" class="w-100 btn btn-secondary">
                            SOR Summary
                        </a>
                    </div>
                    <div class="col-3">
                        @if(auth()->user()->role == 'ms')
                            <a href="{{route('sor.index', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" type="button" class="w-100 btn btn-primary btn-notification">
                                Update SOR Requests
                                @if ($number_of_requests > 0)
                                    <span class="badge bg-danger">{{$number_of_requests}}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                    
                    <div class="col-4">
                        
                    </div>
                    <div class="col-2">
                        @if(auth()->user()->role == 'lapangan')
                            <div style="text-align:right;">
                                <a href="{{route('sor.create', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" class="w-100 btn btn-primary my-1" type="button">
                                    Upload SOR
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="col-1">
                        
                            <div style="text-align:right;" class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle my-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Menu
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a href="{{route('section.edit', ['project_id' => $project->project_id, 'segment_id' => $segment->segment_id, 'section_id' => $section_id])}}" display="block" class="dropdown-item">Edit Section</a>
                                    
                                    @if(auth()->user()->role == 'engineering')
                                        <a href="{{route('core.create', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-', 'sub_section_type' => '-', 'input_type' => 'setup' ] )}}" display="block" class="dropdown-item">Setup Core</a>
                                        <a href="{{route('core.upload_excel', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" display="block" class="dropdown-item">Upload Excel</a>
                                        <a href="{{route('section.delete', ['project_id' => $project->project_id, 'segment_id' => $segment->segment_id, 'section_id' => $section_id])}}" onclick="return confirmDelete(event);" display="block" class="dropdown-item">Delete Section</a>
                                    @endif
                                </div>
                            </div>
                        
                    </div>
                </div>
                <div class="row my-4">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-start">
                                            <div style="color:black;" class="card-title mb-9 fw-semibold"> Summary Section</div>
                                            <div style="min-height: 3rem;"></div>
                                            <div class="col-4">
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
                                                <div class="fw-semibold mb-3">Cable Category : {{$section->cable_category}}</div>
                                            </div>
                                            <div class="col-4">
                                                
                                                <div class="fw-semibold mb-3">Cable Type : {{$section->cable_type}}</div>
                                                <div class="fw-semibold mb-3">First RFS : {{$section->first_rfs}}</div>
                                                <div class="fw-semibold mb-3">Site Owner Near End : {{$section->site_owner_near_end}}</div>
                                                <div class="fw-semibold mb-3">Site Owner Far End : {{$section->site_owner_far_end}}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="card overflow-hidden text-white" style="background: linear-gradient(0deg, rgba(246,39,127,1) 0%, rgba(255,76,76,1) 100%); ; ">
                                                    <div class="card-body" style="padding: 5rem 1rem;">
                                                        @php
                                                            $core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->get());
                                                            $core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->get());
                                                            $core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());
                                                            $core_capacity = $section->core_capacity;
                                                            if($core_capacity - $core_aktif != 0){
                                                                $availability = ($core_ok / ($core_capacity - $core_aktif)) * 100;
                                                            }else{
                                                                $availability = "";
                                                            }

                                                            $x = $section->actual_max_total_loss; // Example value for $x
                                                            $b = 3.75 + $section->initial_min_total_loss; // Example value for $b
                                                            $c = $section->initial_max_total_loss; // Example value for $c

                                                            // Check conditions and set color accordingly
                                                            if ($x < $b) {
                                                                $color = '#28a745';
                                                            } elseif ($x >= $b && $x <= $c) {
                                                                $color = "#ffc107";
                                                            } else {
                                                                $color = "#dc3545";
                                                            }

                                                        @endphp
                                                        <div class="text-center">
                                                            <h5 class="card-title mb-9 fw-semibold text-white">Availibility : {{ \Illuminate\Support\Str::limit($availability, $limit = 5, $end = '.') == null ? '0' : \Illuminate\Support\Str::limit($availability, $limit = 5, $end = '.') }} %</h5>
                                                            <span class="">Availibility From Core IDLE ( {{$core_ok + $core_not_ok}} )</span>
                                                        </div>
                                                        <hr>
                                                        <div class="row align-items-center">
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-center">
                                                                
                                                                <input hidden id="available" type="text" value="{{$availability != null ? $availability : 0}}">
                                                                @if($availability > 0 && $availability <= 100)
                                                                    <input hidden id="not_available" type="text" value="<?php
                                                                        
                                                                        $data = (int)$availability;
                                                                        $hasil = 100 - $data;

                                                                        echo $hasil;
                                                                        ?>">
                                                                @else
                                                                    <input hidden id="not_available" type="text" value="{{100}}">
                                                                @endif
                                                                <div id="availibility_chart"></div>
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
                                                        <h6>Initial Data</h6>
                                                        <div class="fw-semibold mb-3">Length : {{$section->initial_length == null ? '-' : $section->initial_length}} KM</div>
                                                        <div class="fw-semibold mb-3">Min Total Loss :  {{$section->initial_min_total_loss == null ? '-' : $section->initial_min_total_loss}} dB</div>
                                                        <div class="fw-semibold mb-3">Max Total Loss : {{$section->initial_max_total_loss == null ? '-' : $section->initial_max_total_loss}} dB</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <h6>Actual Data</h6>
                                                        <div class="fw-semibold mb-3">Length : {{$section->actual_length == null ? '-' : $section->actual_length}} KM</div>
                                                        <div class="fw-semibold mb-3">Min Total Loss :  {{$section->actual_min_total_loss == null ? '-' : $section->actual_min_total_loss}} dB</div>
                                                        <div class="fw-semibold mb-3">Max Total Loss : {{$section->actual_max_total_loss == null ? '-' : $section->actual_max_total_loss}} dB</div>
                                                    </div>
                                                    <div class="col-4" style="padding-right:3rem; overflow-x:hidden;">
                                                        <div class="bg-light" style="width: 100%; border-radius: 5px; padding: 3px; position: relative;">
                                                            <div style="width: calc(100% * {{$section->actual_max_total_loss}} / {{$section->initial_max_total_loss}}); background-color: {{$color}}; height: 20px; border-radius: 5px;"></div>
                                                                <span style=" position: absolute; right: 5px; top: 150%; transform: translateY(-50%);">{{$section->actual_max_total_loss}} dB / {{$section->initial_max_total_loss}} dB</span>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <!-- Monthly Earnings -->
                                <div class="card" style="height:25rem; overflow-y:auto;">
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
                                            <div style="margin-top:1rem; width:100%; height:15rem; overflow-y:scroll;">
                                                <table class="table table-striped table-bordered" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Core</th>
                                                            <th scope="col">Initial</th>
                                                            <th scope="col">Actual</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $cores = DB::table('core')->where('section_id',$section->section_id)->orderBy(DB::raw('CAST(core AS UNSIGNED)'), 'asc')->get();
                                                        @endphp

                                                        @foreach ($cores as $core)
                                                        <tr>
                                                            <td>{{$core->core}}</td>
                                                            <td>{{$core->initial_customers}} </td>
                                                            <td>{{$core->actual_customers }}</td>
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
                                <div class="card" style="height:25rem; overflow-y:auto;">
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
                                                <table class="table table-bordered" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Initial</th>
                                                            <th scope="col">Actual</th>
                                                        </tr>
                                                    </thead>

                                                    @php
                                                        $initial_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'OK')->get());   
                                                        $initial_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'NOT OK')->get());   
                                                        $initial_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('initial_remarks', 'AKTIF')->get());   
                                                        
                                                        $actual_core_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'OK')->get());   
                                                        $actual_core_not_ok = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'NOT OK')->get());   
                                                        $actual_core_aktif = count(DB::table('core')->where('section_id', $section->section_id)->where('actual_remarks', 'AKTIF')->get());   
                                                    @endphp
                                                    <tbody>
                                                        <tr>
                                                            <td>Core Capacity : {{$section->core_capacity}}</td>
                                                            <td>Core Capacity : {{$section->core_capacity}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>SOLD Core : {{$initial_core_aktif}}</td>
                                                            <td>ACTIVE Core: {{$actual_core_aktif}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>IDLE Core : {{$section->core_capacity - $initial_core_aktif}}</td>
                                                            <td>IDLE Core : {{$section->core_capacity - $actual_core_aktif}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td style="padding-left:2rem;">* Core OK : {{$actual_core_ok}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td style="padding-left:2rem;">* Core NOT OK : {{$actual_core_not_ok}}</td>
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
                                            <a href="{{route('core.create', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => '-', 'sub_section_type' => '-', 'input_type' => 'add' ] )}}" display="block" class="dropdown-item">Add Core</a>
                                            <a href="#" type="button" style="text-transform: capitalize;" class="dropdown-item">Download Sor Files</a>
                                            <a href="{{route('core.edit', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Manage Core</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                        <thead class="text-dark fs-4 bg-dark">
                                                <tr>
                                                    <th>
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Core</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Customers</h6>
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
                                            @php
                                                $cores = DB::table('core')->where('section_id',$section->section_id)->orderBy(DB::raw('CAST(core AS UNSIGNED)'), 'asc')->get();
                                            @endphp
                                            @if (count($cores) > 0 )
                                                @foreach ($cores as $core)
                                                    <tr>
                                                        <td class="text-center" style="width:10%;">{{$core->core}}</td>
                                                        <td class="text-center">{{$core->initial_customers}}</td>
                                                        <td class="text-center">{{$core->initial_end_cable}}</td>
                                                        <td class="text-center">{{$core->initial_total_loss_db}}</td>
                                                        <td class="text-center">{{$core->initial_loss_db_km}}</td>
                                                        <td class="text-center">{{$core->initial_remarks}}</td>
                                                        @if (auth()->user()->role == "engineering")
                                                            <td class="text-center"><a onclick="return confirmDeleteCore(event)" href="{{route('core.delete', ['core_id' => $core->core_id])}}" class="btn btn-danger btn-sm">Delete</a></td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">Empty</td>
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
                                            <a href="#" type="button" style="text-transform: capitalize;" class="dropdown-item">Download Sor Files</a>
                                            <a href="{{route('core.edit', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-'])}}" type="button" style="text-transform: capitalize;" class="dropdown-item">Manage Core</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                        <thead class="text-dark fs-4 bg-dark">
                                                <tr>
                                                    <th>
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Core</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Customers</h6>
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
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Status</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Date</h6>
                                                    </th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cores = DB::table('core')->where('section_id',$section->section_id)->orderBy(DB::raw('CAST(core AS UNSIGNED)'), 'asc')->get();
                                            @endphp
                                            @if (count($cores) > 0 )
                                                @foreach ($cores as $core)
                                                    <tr style="cursor: pointer;" class="clickable-row" onclick="window.location.href='{{ route('core.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_type' => $section->section_type, 'section_id' => $section->section_id, 'sub_section_id' => '-', 'core' => $core->core]) }}';" >
                                                        <td class="text-center" style="width:10%;">{{$core->core}}</td>
                                                        <td class="text-center">{{$core->actual_customers}}</td>
                                                        <td class="text-center">{{$core->actual_end_cable}}</td>
                                                        <td class="text-center">{{$core->actual_total_loss_db}}</td>
                                                        <td class="text-center" >{{$core->actual_loss_db_km}}</td>
                                                        <td class="text-center">{{$core->actual_remarks}}</td>
                                                        <td class="text-center">{{substr($core->notes, 0, 24)}}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">Empty</td>
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

    @endif
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
    // =====================================
    // Breakup
    // =====================================
    const value1 = Number(document.getElementById("available").value);
    const value2 = Number(document.getElementById("not_available").value);
    var breakup = {
        color: "#adb5bd",
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
                startAngle: 0,
                endAngle: 360,
                donut: {
                size: '75%',
                },
            },
        },
        stroke: {
            show: false,
        },

        dataLabels: {
            enabled: false,
        },

        legend: {
            show: false,
        },
        colors: ["#7FF627", "#ecf2ff", "#ecf2ff"],

        responsive: [
        {
            breakpoint: 991,
            options: {
            chart: {
                width: 150,
            },
            },
        },
        ],
        tooltip: {
            enabled: true,
            theme: "dark",
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            y: {
                formatter: function (val) {
                    return val + "%"
                }
            }
        },
    };

    var chart = new ApexCharts(document.querySelector("#availibility_chart"), breakup);
    chart.render();
</script>
@endsection