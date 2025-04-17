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
    </style>
@endsection

@php

    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('route_id', $route_id)->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->get()->first();
    $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();

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
        <div class="card-body px-4 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 2rem;"> Manage Core : 
                            {{$sub_section->sub_section_name}} ({{DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name}})
                    </h3>
                    <hr>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id,'route_id'=> '-', 'project_type'=>'inland'])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('segment.show', ['project_id'=> $project->project_id,'route_id'=> $route_id, 'project_type'=>'inland', 'segment_id'=>$segment->segment_id])}}">{{$segment->segment_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('section.show', ['project_id'=> $project->project_id,'route_id'=> $route_id, 'project_type'=>'inland', 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id])}}">{{$section->section_name}}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a class="text-decoration-none" href="{{route('sub_section.show', ['project_id'=> $project->project_id,'route_id'=> $route_id, 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id,'customer_id'=>$customer_id,'type_id'=>$type_id, 'sub_section_id'=>$sub_section->sub_section_id])}}">{{$sub_section->sub_section_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                Manage Core
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')

    @switch(auth()->user()->role)
        @case('engineering')
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('core.update') }}" method="POST" enctype=multipart/form-data>
                                {{ csrf_field() }}
                                <input type="hidden" name="project_id" value="{{$project->project_id}}" >
                                <input type="hidden" name="route_id" value="{{$route_id}}" >
                                <input type="hidden" name="segment_id" value="{{$segment->segment_id}}" >
                                <input type="hidden" name="section_id" value="{{$section->section_id}}" >
                                <input type="hidden" name="customer_id" value="{{$customer_id}}" >
                                <input type="hidden" name="type_id" value="{{$type_id}}" >
                                <input type="hidden" name="sub_section_id" value="{{$sub_section_id}}" >
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                        <thead class="text-dark fs-4">
                                                <tr class="bg-dark">
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0 px-4">Core</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Customers</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Length</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Total Loss DB</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Loss DB KM</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Status</h6>
                                                    </th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cores = DB::table('core')->where('sub_section_id', $sub_section_id)->orderBy(DB::raw('CAST(core AS UNSIGNED)'), 'asc')->get();
                                            @endphp

                                            @if (count($cores) > 0)
                                                @foreach ($cores as $core)
                                                    <tr>
                                                        <td class="text-center bg-light" style="width:10%;">{{$core->core}}</td>
                                                        <input type="hidden" name="core[]" value="{{$core->core}}">
                                                        <td class="text-center" style="min-width: 20rem;"> <input type="text" name="initial_customers[]" value="{{$core->initial_customers}}" class="form-control"> </td>
                                                        <td class="text-center" style="min-width: 10rem;"> <input type="number" step="0.0001" min="0" name="initial_end_cable[]" value="{{$core->initial_end_cable}}" class="form-control"> </td>
                                                        <td class="text-center" style="min-width: 10rem;"> <input type="number" step="0.0001" min="0" name="initial_total_loss_db[]" value="{{$core->initial_total_loss_db}}" class="form-control"> </td>
                                                        <td class="text-center" style="min-width: 10rem;"> <input type="number" step="0.0001" min="0" name="initial_loss_db_km[]" value="{{$core->initial_loss_db_km}}" class="form-control"> </td>
                                                        <td style="min-width: 10rem;" class="text-center bg-light">  {{$core->initial_remarks}} </td>
                                                    </tr>  
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center text-danger">
                                                        Core has not been setup !
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-left mt-5">
                                    <button type="submit" style="padding:.5rem 3rem;" class="btn btn-primary">Submit</button>
                                    <a href="{{ URL::previous() }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @break
        @case('ms')
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('core.update') }}" method="POST" enctype=multipart/form-data>
                                {{ csrf_field() }}
                                <input type="hidden" name="project_id" value="{{$project->project_id}}" >
                                <input type="hidden" name="route_id" value="{{$route_id}}" >
                                <input type="hidden" name="segment_id" value="{{$segment->segment_id}}" >
                                <input type="hidden" name="section_id" value="{{$section->section_id}}" >
                                <input type="hidden" name="customer_id" value="{{$customer_id}}" >
                                <input type="hidden" name="type_id" value="{{$type_id}}" >
                                <input type="hidden" name="sub_section_id" value="{{$sub_section_id}}" >
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                        <thead class="text-dark fs-4">
                                                <tr class="bg-dark">
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0 px-4">Core</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Customers</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Length</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Total Loss DB</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Loss DB KM</h6>
                                                    </th>
                                                    <th class="text-center">
                                                        <h6 style="display:inline-block; color:white" class="fw-semibold mb-0">Status</h6>
                                                    </th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cores = DB::table('core')->where('sub_section_id', $sub_section_id)->orderBy(DB::raw('CAST(core AS UNSIGNED)'), 'asc')->get();
                                            @endphp
                                            
                                            @if (count($cores) > 0)
                                                @foreach ($cores as $core)
                                                    <tr>
                                                        <td class="text-center bg-light" style="width:10%;">{{$core->core}}</td>
                                                        <input type="hidden" name="core[]" value="{{$core->core}}">
                                                        <td class="text-center" style="min-width: 20rem;"> <input type="text" name="actual_customers[]" value="{{$core->actual_customers}}" class="form-control"> </td>
                                                        <td class="text-center" style="min-width: 10rem;"> <input type="number" step="0.0001" min="0" name="actual_end_cable[]" value="{{$core->actual_end_cable}}" class="form-control"> </td>
                                                        <td class="text-center" style="min-width: 10rem;"> <input type="number" step="0.0001" min="0" name="actual_total_loss_db[]" value="{{$core->actual_total_loss_db}}" class="form-control"> </td>
                                                        <td class="text-center" style="min-width: 10rem;"> <input type="number" step="0.0001" min="0" name="actual_loss_db_km[]" value="{{$core->actual_loss_db_km}}" class="form-control"> </td>
                                                        <td style="min-width: 10rem;" class="text-center bg-light">  {{$core->actual_remarks}} </td>
                                                    </tr>  
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center text-danger">
                                                        Core has not been setup !
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-left mt-5">
                                    <button type="submit" style="padding:.5rem 3rem;" class="btn btn-primary">Submit</button>
                                    <a href="{{ URL::previous() }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @break
        @default
            <h5 class="text-warning">Error, Can't find  user role !</h5>
    @endswitch

@endsection
