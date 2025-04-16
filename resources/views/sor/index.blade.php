
@extends('layouts.app')

@php
    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('section_id', $section_id)->get()->first();
    if(isset($sub_section)){
        $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();
        $requests_sor = DB::table('draf_sor')->where('status', 'PROCESS')->where('sub_section_id', $sub_section_id)->orderBy('date', 'desc')->get();
    }else{
        $requests_sor = DB::table('draf_sor')->where('status', 'PROCESS')->where('section_id', $section_id)->orderBy('date', 'desc')->get();
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
                    @if (isset($sub_section_id))
                        @php
                            $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();
                        @endphp
                        <h3 class="fw-semibold" style="font-size: 2rem;"> Setup Core : {{$sub_section->sub_section_name}}</h3>
                    @else
                        <h3 class="fw-semibold" style="font-size: 2rem;"> Setup Core : {{$section->section_name}}</h3>
                    @endif
                    <hr>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('segment.show', ['project_id'=> $project->project_id, 'segment_id'=>$segment->segment_id])}}">{{$segment->segment_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('section.show', ['project_id'=> $project->project_id, 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id])}}">{{$section->section_name}}</a>
                            </li>
                            @if (isset($sub_section_id))
                                @php
                                    $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();
                                @endphp
                                <li class="breadcrumb-item" aria-current="page">
                                    <a class="text-decoration-none" href="{{route('sub_section.show', ['project_id'=> $project->project_id, 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id, 'sub_section_id'=>$sub_section->sub_section_id])}}">
                                        {{ $sub_section->sub_section_name}}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item">
                                SOR Update Request
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
        <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
            <thead class="bg-dark fs-4">
                    <tr>
                        <th>
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">No</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Requestor NIK</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Requestor Name</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Request Date</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Status</h6>
                        </th>
                        <th class="text-center">
                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Action</h6>
                        </th>
                    </tr>
            </thead>
            <tbody>
                @if(count($requests_sor) > 0)
                    @foreach ($requests_sor as $request_sor)
                        @php
                            $requestor_name = DB::table('users')->where('nik', $request_sor->requestor)->get()->first()->name;   
                        @endphp
                        <tr>
                            <td class="text-center" style="width:10%;">{{$loop->index +1 }}</td>
                            <td class="text-center">{{$request_sor->requestor}}</td>
                            <td class="text-center">{{$requestor_name}}</td>
                            <td class="text-center">{{$request_sor->date}}</td>
                            <td class="text-center">
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
                            <td class="text-center">
                                <a href="{{route('sor.show', ['request_id' => $request_sor->request_id])}}" class="btn btn-primary" >Detail</a>
                            </td>
                        </tr>              
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center" >Empty</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div style="min-height: 5rem;"></div> 
    </div>
@endsection
