@extends('layouts.app')

@php

    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('route_id', $route_id)->where('segment_id', $segment_id)->get()->first();
    $sections = DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->get();
    
    switch ($route_id) {
        case 1:
            $route_name = 'SUBMARINE';
            break;
        case 2:
            $route_name = 'INLAND';
            break;
        case 3:
            $route_name = 'LASTMILE';
            break;
        
        default:
            $route_name = 'UNDEFINED';
            break;
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
    <div class="card bg-dark text-white shadow-lg position-relative overflow-hidden">
        <div class="card-body px-5 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem; text-transform: uppercase;"> #{{$segment->segment_id}} SEGMENT {{ ucwords(strtolower($segment->segment_name)) }} ({{$route_name}})</h3>
                    <h3 class="fw-semibold text-white" style="font-size: 1.8rem;"> EDIT SECTIONS </h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none"
                                    href="{{ route('project.show', ['project_id' => $segment->project_id , 'route_id' => $segment->project_id]) }}">{{ $project->project_name }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none"
                                    href="{{ route('segment.show', ['project_id' => $segment->project_id, 'route_id' => $route_id, 'segment_id' => $segment->segment_id]) }}">{{ $segment->segment_name }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                Create Section
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('section.update') }}" method="POST" enctype=multipart/form-data>
                        {{ csrf_field() }}
                        <input type="hidden" name="segment_id" value="{{$segment_id}}">
                        <input type="hidden" name="route_id" value="{{$route_id}}">
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th class="">
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Section ID</h6>
                                        </th>
                                        <th class="">
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Section Name</h6>
                                        </th>
                                        <th class="">
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Section Route</h6>
                                        </th>
                                        <th class="">
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Core Capacity</h6>
                                        </th>
                                        <th class="">
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Cable Type</h6>
                                        </th>
                                        <th class="">
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                First RFS</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableToModify">
                                    @foreach ($sections as $section)
                                        <tr>
                                            <input hidden value="{{ $project_id }}" name="project_id">
                                            <input hidden value="{{ $segment_id }}" name="segment_id">
                                            <input hidden value="{{ $route_id }}" name="route_id">
                                            <input hidden value="{{ $section->section_id }}" name="section_id[]">
                                            <td style="width: 10%;">
                                                <input disabled value="{{$section->section_id}}" class="form-control" class="outline outline-2" type="text">
                                            </td>
                                            <td >
                                                <input class="form-control" class="outline outline-2" name="section_name[]" value="{{$section->section_name}}"
                                                    required type="text">
                                            </td>
                                            <td>
                                                <select name="section_route[]" class="form-select">
                                                    <option value="1_route" {{ $section->section_route == '1_route' ? 'selected' : '' }}>Main</option>
                                                    <option value="2_route" {{ $section->section_route == '2_route' ? 'selected' : '' }}>Diversity</option>
                                                    <option value="3_route" {{ $section->section_route == '3_route' ? 'selected' : '' }}>3rd Route</option>
                                                    <option value="4_route" {{ $section->section_route == '4_route' ? 'selected' : '' }}>4th Route</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control outline outline-2" name="core_capacity[]" type="number" min="0" value="{{$section->core_capacity}}">
                                            </td>
                                            <td >
                                                <input class="form-control" class="outline outline-2" name="cable_type[]" type="text" value="{{$section->cable_type}}">
                                            </td>
                                            <td >
                                                <input class="form-control" class="outline outline-2" name="first_rfs[]" type="text" value="{{$section->first_rfs}}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="min-height: 3rem;"></div>
                        <div class="text-center">
                            <button type="submit" style="padding:.5rem 3rem;" class="btn btn-primary">Submit</button>
                            <a href="{{ URL::previous() }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
