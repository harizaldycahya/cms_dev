
@extends('layouts.app')

@php
    $project = DB::table('project')->where('project_id', $request->project_id)->get()->first();
    $segment = DB::table('segment')->where('project_id', $request->project_id)->where('route_id', $request->route_id)->where('segment_id', $request->segment_id)->get()->first();
    $route_id = $segment->route_id;
    $section = DB::table('section')->where('project_id', $request->project_id)->where('route_id', $request->route_id)->where('segment_id', $request->segment_id)->where('section_id', $request->section_id)->get()->first();
    // $request_sor = DB::table('sor_request')->where('request_id', $request->request_id)->where('section_id', $request->section_id)->get()->first();
    $request_sor = DB::table('sor_request')->where('request_id', $request->request_id)->where('sub_section_id', $request->sub_section_id)->get()->first();
    $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->get()->first();
    // if(isset($sub_section)){
    // }else{
    // }
    
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
        <div class="card-body px-4 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 2rem;">Sub Section : {{$sub_section->sub_section_name}} <span style="text-transform: uppercase;">({{DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name}} {{$sub_section->type_id}})</span> </h3>
                    <hr>
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
                            <li class="breadcrumb-item">
                                Request SOR Detail
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div style="min-height:2rem;"></div>
    <div class="table-responsive">
        @php
            $draf_core = DB::table('draf_core')->where('request_id', $request->request_id)->orderBy(DB::raw('CAST(core AS UNSIGNED)'), 'asc')->get();   

        @endphp

        @if(auth()->user()->role == 'ms')
            @if(count($draf_core) > 0 )
                <div class="row">
                    <div class="col-2">
                        <a href="{{route('sor.approval', ['request_id' => $request->request_id, 'status' => 'APPROVE' ])}}" class="w-100 btn btn-success text-white py-2">Approve</a>
                    </div>
                    <div class="col-2">
                        <a href="{{route('sor.approval', ['request_id' => $request->request_id, 'status' => 'REJECT'])}}" class="w-100 btn btn-danger text-white py-2">Reject</a>
                    </div>
                    <div class="col-8">
                    
                    </div> 
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <p class="text-danger">Data has not been processed, please click process data button to extract and convert file sor to be readable information ! </p>
                    </div>
                    <div class="col-3">
                        <a id="uploadLink" href="{{route('sor.process', ['request_id' => $request->request_id])}}" class="w-100 btn btn-info text-white py-2">Process Data</a>
                        <button id="loadingButton" class="btn btn-primary" type="button" disabled="" style="width:100%; display:none;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Extracting and converting file sor into readable data, please wait ...
                        </button>
                    </div>
                    
                    <div class="col-7"></div>
                    <div class="col-2">
                        <a onclick="return confirm('Are you sure you want to delete this request?');" href="{{route('sor.delete_request', ['request_id' => $request->request_id])}}" class="w-100 btn btn-danger text-white py-2">Delete Request</a>
                    </div>
                </div>
            @endif
        @endif

        <div style="min-height: 3rem;"></div>
        <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
            <thead>
                <tr>
                    <th colspan="3" class="">
                        @switch($request_sor->status)
                            @case('PROCESS')
                                    <h6 style="display:inline-block;" class=" text-right fw-semibold mb-0">STATUS :
                                        <span class="text-primary"> {{auth()->user()->role == 'lapangan' ? 'WAITING FOR APPROVAL' : 'PROCESS'}}</span> 
                                    </h6>
                                @break
                            @case('APPROVE')
                                    <h6 style="display:inline-block;" class=" text-right fw-semibold mb-0">STATUS : <span class="text-success">{{$request_sor->status}}</span> </h6>
                                @break
                            @case('REJECT')
                                    <h6 style="display:inline-block;" class=" text-right fw-semibold mb-0">STATUS : <span class="text-danger">{{$request_sor->status}}</span> </h6>
                                @break
                            @default
                                
                        @endswitch 
                       
                    </th>
                    <th colspan="2">
                        <h6 style="display:inline-block;" class="fw-semibold mb-0 px-4">REQUESTOR : {{DB::table('users')->where('nik', $request_sor->requestor)->get()->first()->name}}</h6>
                    </th>
                    
                </tr>
            </thead>
            <thead class="text-dark fs-4 bg-dark">
                    
                    <tr>
                        <th>
                            <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0 px-4">Core</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Length</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Total Loss DB</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Loss dB KM</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Date</h6>
                        </th>
                    </tr>
            </thead>
            <tbody>
                @if(count($draf_core) > 0 )
                    @foreach ($draf_core as $core)
                        <tr>
                            <td class="text-center" >{{$core->core}}</td>
                            <td class="text-center" >{{$core->end_cable}}</td> 
                            <td class="text-center" >{{$core->total_loss_db}}</td>
                            <td class="text-center" >{{$core->loss_db_km}}</td>
                            <td class="text-center" >{{substr($core->tanggal, 0, 25) }}</td>
                        </tr>
                    @endforeach
                @else   
                    <tr>
                        <td class="text-center" colspan="5">Empty</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div style="min-height: 5rem;"></div> 
@endsection

@section('script')
    <script>
        document.getElementById('uploadLink').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default anchor behavior
        
            document.getElementById('uploadLink').style.display = 'none';
            document.getElementById('loadingButton').style.display = 'block';
        
            // Redirect to the href URL
            window.location.href = this.href;
        });
    </script>
@endsection