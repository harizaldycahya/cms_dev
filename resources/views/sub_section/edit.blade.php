@extends('layouts.app')

@php

    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->get()->first();
    
    $sub_sections = DB::table('sub_section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->orderByRaw('CAST(customer_id AS UNSIGNED) ASC')->get();
    
    $customers = DB::table('customer')->get();
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
    @php
        $project = DB::table('project')->where('project_id', $project_id)->get()->first();
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
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem; text-transform: uppercase;"> #{{$section->section_id}} SECTION {{ ucwords(strtolower($section->section_name)) }} ({{$route_name}})</h3>
                    <h3 class="fw-semibold text-white" style="font-size: 1.8rem;"> EDIT SUB SECTION </h3>
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
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none"
                                    href="{{ route('section.show', ['project_id' => $segment->project_id, 'route_id' => $route_id, 'segment_id' => $segment->segment_id, 'section_id' => $section->section_id]) }}">{{ $section->section_name }}</a>
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
                    <form action="{{ route('sub_section.update') }}" method="POST" enctype=multipart/form-data>
                        {{ csrf_field() }}
                        <input type="hidden" name="project_id" value="{{$project_id}}">
                        <input type="hidden" name="segment_id" value="{{$segment_id}}">
                        <input type="hidden" name="route_id" value="{{$route_id}}">
                        <input type="hidden" name="section_id" value="{{$section_id}}">

                        
                        @if(auth()->user()->role == 'engineering')
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    ID</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Sub Section Name</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Owner</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Type</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Site Owner <br> Near End</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Site Owner <br> Far End</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Initial Length</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Initial <br> Min Total Loss</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Initial <br> Max Total Loss</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <input hidden value="{{ $project_id }}" name="project_id">
                                        <input hidden value="{{ $route_id }}" name="route_id">
                                        <input hidden value="{{ $segment_id }}" name="segment_id">
                                        <input hidden value="{{ $section_id }}" name="section_id">

                                        @foreach ($sub_sections as $sub_section)
                                            <tr>
                                                <input hidden name="sub_section_id[]" type="text" value="{{$sub_section->sub_section_id}}">
                                                <td style="min-width:5%;">
                                                    <input disabled class="form-control" class="outline outline-2" type="text" value="{{$sub_section->sub_section_id}}">
                                                </td>
                                                <td style="min-width:20rem;">
                                                    <input  class="form-control" class="outline outline-2" name="sub_section_name[]" type="text" value="{{$sub_section->sub_section_name}}">
                                                </td>
                                                <td style="min-width:10rem;">
                                                    <select name="customer_id[]" class="form-select">
                                                        @foreach($customers as $customer)
                                                            <option {{ $customer->customer_id == $sub_section->customer_id ? 'selected' : '' }} value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td ><input class="form-control" class="outline outline-2" name="type_id[]" type="text" value="{{$sub_section->type_id}}"></td>
                                                <td style="min-width:3rem;"><input type="text" name="sub_site_owner_near_end[]" value="{{$sub_section->sub_site_owner_near_end}}" class="sub_site_owner_near_end form-control mb-2"></td>
                                                <td style="min-width:3rem;"><input type="text" name="sub_site_owner_far_end[]" value="{{$sub_section->sub_site_owner_far_end}}" class="sub_site_owner_far_end form-control mb-2"></td>
                                                <td style="min-width:3rem;"><input type="number" step="0.0001" min="0" value="{{$sub_section->sub_initial_length}}" name="sub_initial_length[]" class="sub_initial_length form-control mb-2"></td>
                                                <td style="min-width:3rem;"><input type="number" step="0.0001" min="0" value="{{$sub_section->sub_initial_min_total_loss}}" name="sub_initial_min_total_loss[]" class="sub_initial_min_total_loss form-control mb-2"></td>
                                                <td style="min-width:3rem;"><input type="number" step="0.0001" min="0" value="{{$sub_section->sub_initial_max_total_loss}}" name="sub_initial_max_total_loss[]" class="sub_initial_max_total_loss form-control mb-2"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if(auth()->user()->role == 'ms')
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th style="width:10%;">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Customer</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Sub Section Name</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Actual <br> Length</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Actual <br> Min Total Loss</h6>
                                            </th>
                                            <th class="">
                                                <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                    Actual <br> Max Total Loss</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <input hidden value="{{ $project_id }}" name="project_id">
                                        <input hidden value="{{ $route_id }}" name="route_id">
                                        <input hidden value="{{ $segment_id }}" name="segment_id">
                                        <input hidden value="{{ $section_id }}" name="section_id">

                                        @foreach ($sub_sections as $sub_section)
                                            <tr>
                                                <input hidden name="sub_section_id[]" type="text" value="{{$sub_section->sub_section_id}}">
                                                <td style="min-width:5%;">
                                                    <input disabled class="form-control" class="outline outline-2" type="text" value="{{ DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name }}">
                                                </td>
                                                <td style="min-width:20rem;">
                                                    <input disabled class="form-control" class="outline outline-2" type="text" value="{{$sub_section->sub_section_name}}">
                                                </td>
                                                <td style="min-width:3rem;"><input type="number" step="0.0001" min="0" value="{{$sub_section->sub_actual_length}}" name="sub_actual_length[]" class="sub_actual_length form-control mb-2"></td>
                                                <td style="min-width:3rem;"><input type="number" step="0.0001" min="0" value="{{$sub_section->sub_actual_min_total_loss}}" name="sub_actual_min_total_loss[]" class="sub_actual_min_total_loss form-control mb-2"></td>
                                                <td style="min-width:3rem;"><input type="number" step="0.0001" min="0" value="{{$sub_section->sub_actual_max_total_loss}}" name="sub_actual_max_total_loss[]" class="sub_actual_max_total_loss form-control mb-2"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        


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
