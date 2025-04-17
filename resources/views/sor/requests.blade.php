
@extends('layouts.app')

@php
    if(auth()->user()->role == 'lapangan'){
        $requests_sor = DB::table('sor_request')->where('requestor', auth()->user()->nik)->where('status', 'PROCESS')->orderBy('date', 'desc')->get();
    }
    if(auth()->user()->role == 'ms'){
        $requests_sor = DB::table('sor_request')->where('status', 'PROCESS')->orderBy('date', 'desc')->get();
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

        #ma- {
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
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold" style="font-size: 2rem;"> Sor Requests</h3>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div style="min-height:2rem;"></div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
            <thead class="bg-dark fs-4">
                    <tr>
                        <th>
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Date / Time</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Requestor</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Project</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Segment</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Section</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Route</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Customer & Sub Section</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Status</h6>
                        </th>
                        <th >
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                        </th>
                    </tr>
                </thead>  
            <tbody class="">
                @if(count($requests_sor) > 0)
                    @foreach ($requests_sor as $request_sor)
                        @php
                            $requestor_name = DB::table('users')->where('nik', $request_sor->requestor)->get()->first()->name;  
                            switch($request_sor->route_id){
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
                            $sub_section = DB::table('sub_section')->where('sub_section_id', $request_sor->sub_section_id)->get()->first();
                            $customer_name = DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name;
                        @endphp
                        <tr>
                            <td style="width:10%;">{{$request_sor->date }}</td>
                            <td >
                                 
                                <h6 class="fw-semibold mb-1">{{$requestor_name}}</h6>
                                <p class="fs-2 mb-0 text-muted">
                                    {{$request_sor->requestor}}
                                </p>
                            </td>
                            <td  >
                                 
                                <h6 class="fw-semibold mb-1">{{DB::table('project')->where('project_id', $request_sor->project_id)->get()->first()->project_name}}</h6>
                                <p class="fs-2 mb-0 text-muted">
                                    {{$route_name}}
                                </p>
                            </td>
                            <td >{{DB::table('segment')->where('project_id', $request_sor->project_id)->where('segment_id', $request_sor->segment_id)->get()->first()->segment_name}}</td>
                            <td >{{DB::table('section')->where('project_id', $request_sor->project_id)->where('segment_id', $request_sor->segment_id)->where('route_id', $request_sor->route_id)->where('section_id', $request_sor->section_id)->get()->first()->section_name}}</td>
                            <td >
                                @php
                                    $section_route = DB::table('section')->where('project_id', $request_sor->project_id)->where('segment_id', $request_sor->segment_id)->where('route_id', $request_sor->route_id)->where('section_id', $request_sor->section_id)->get()->first()->section_route;
                                    switch ($section_route) {
                                        case '1_route':
                                            echo 'Main';
                                            break;
                                        case '2_route':
                                            echo 'Diversity';
                                            break;
                                        case '3_route':
                                            echo '3rd Route';
                                            break;
                                        case '4_route':
                                            echo '4th Route';
                                            break;
                                        
                                        default:
                                            echo 'Main';
                                            break;
                                    }
                                @endphp
                            </td>
                            <td >
                                <h6 class="fw-semibold mb-1">{{$customer_name}}</h6>
                                <p class="fs-2 mb-0 text-muted">
                                    {{DB::table('sub_section')->where('sub_section_id', $request_sor->sub_section_id)->get()->first()->sub_section_name}}
                                </p>
                                
                            </td>
                            
                            <td >
                                @switch($request_sor->status)
                                    @case('PROCESS')
                                        <div class="bg-info text-white px-1 py-1">{{$request_sor->status}}</div>
                                        @break
                                    @case('APPROVE')
                                        <div class="bg-primary text-success px-1 py-1">{{$request_sor->status}}</div>
                                        @break
                                    @case('REJECT')
                                        <div class="bg-primary text-danger px-1 py-1">{{$request_sor->status}}</div>
                                        @break
                                    @default
                                @endswitch    
                                
                            </td>
                            <td >
                                <a href="{{route('sor.show', ['request_id' => $request_sor->request_id])}}" class="btn btn-primary" >Detail</a>
                            </td>
                        </tr>              
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" class="text-center" >Empty</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div style="min-height: 5rem;"></div> 
    </div>
@endsection
