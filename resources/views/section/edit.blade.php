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
    @php
        $project = DB::table('project')->where('project_id', $project_id)->get()->first();
        $segment = DB::table('segment')->where('segment_id', $segment_id)->get()->first();
        $section = DB::table('section')->where('section_id', $section_id)->get()->first();
    @endphp
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold" style="font-size: 2rem;">Edit section : {{ $section->section_name }}</h3>
                    <hr>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none"
                                    href="{{ route('project.show', ['project_id' => $segment->project_id]) }}">{{ $project->project_name }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none"
                                    href="{{ route('segment.show', ['project_id' => $segment->project_id, 'segment_id' => $segment->segment_id]) }}">{{ $segment->segment_name }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none"
                                    href="{{ route('section.show', ['project_id' => $segment->project_id, 'segment_id' => $segment->segment_id, 'section_id' => $section->section_id]) }}">{{ $section->section_name }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                Edit Section
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <hr class="my-5">
    @switch(auth()->user()->role)
        @case('engineering')
                @switch($section->section_type)
                    @case('regular')
                            <div style="min-height:2rem;"></div>
                            <form action="{{ route('section.update') }}" method="POST">
                                {{ csrf_field() }}
                                <input hidden type="text" name="input_type" value="regular">
                                <input hidden type="text" name="project_id" value="{{$project_id}}">
                                <input hidden type="text" name="segment_id" value="{{$segment_id}}">
                                <input hidden type="text" name="section_id" value="{{$section_id}}">
                                <input hidden type="text" name="section_type" value="regular">
                                <input hidden type="text" name="role" value="{{auth()->user()->role}}">

                                <div class="col-12">
                                    <div class="card w-100 position-relative overflow-hidden mb-0">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-lg-12">
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Cable category</label>
                                                <select class="form-select" name="cable_category">
                                                    <option value="INLAND" {{ $section->cable_category == 'INLAND' ? 'selected' : '' }}>INLAND</option>
                                                    <option value="SUBMARINE" {{ $section->cable_category == 'SUBMARINE' ? 'selected' : '' }}>SUBMARINE</option>
                                                    <option value="LASTMILE" {{ $section->cable_category == 'LASTMILE' ? 'selected' : '' }}>LASTMILE</option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="col-lg-12">
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Saction Name</label>
                                                <input type="text" class="form-control mb-2" name="section_name" required value="{{$section->section_name}}">
                                            </div>
                                            </div>
                                            <div class="col-lg-6">
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Near End </label>
                                                <td style="min-width:10rem;"><input type="text" class="form-control mb-2" name="near_end" required value="{{$section->near_end}}"></td>
                                            </div>
                                            </div>
                                            <div class="col-lg-6">
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Far End </label>
                                                <input type="text" class="form-control mb-2" name="far_end" required value="{{$section->far_end}}">
                                            </div>
                                            </div>
                                            <div class="mb-4">
                                            <label class="form-label fw-semibold">Section Route</label>
                                            <select name="section_route" class="form-select">
                                                <option value="1_route" {{ $section->section_route == '1_route' ? 'selected' : '' }}>Main</option>
                                                <option value="2_route" {{ $section->section_route == '2_route' ? 'selected' : '' }} >Diversity</option>
                                                <option value="3_route" {{ $section->section_route == '3_route' ? 'selected' : '' }} >3rd Route</option>
                                                <option value="4_route" {{ $section->section_route == '4_route' ? 'selected' : '' }} >4th Route</option>
                                            </select>
                                            </div>
                                            <div class="mb-4">
                                            <label class="form-label fw-semibold">Owner</label>
                                            <select name="owner" class="form-select">
                                                <option {{ $section->owner == 'trias' ? 'selected' : '' }} value="trias">TRIASMITRA</option>
                                                <option {{ $section->owner == 'BIZNET' ? 'selected' : '' }} value="BIZNET">BIZNET</option>
                                                <option {{ $section->owner == 'FIBERSTAR' ? 'selected' : '' }} value="FIBERSTAR">FIBERSTAR</option>
                                                <option {{ $section->owner == 'H3I' ? 'selected' : '' }} value="H3I">H3I</option>
                                                <option {{ $section->owner == 'HSP' ? 'selected' : '' }} value="HSP">HSP</option>
                                                <option {{ $section->owner == 'IFORTE' ? 'selected' : '' }} value="IFORTE">IFORTE</option>
                                                <option {{ $section->owner == 'INDOSAT' ? 'selected' : '' }} value="INDOSAT">INDOSAT</option>
                                                <option {{ $section->owner == 'IPLUS' ? 'selected' : '' }} value="IPLUS">IPLUS</option>
                                                <option {{ $section->owner == 'JKLD' ? 'selected' : '' }} value="JKLD">JKLD</option>
                                                <option {{ $section->owner == 'LINKNET' ? 'selected' : '' }} value="LINKNET">LINKNET</option>
                                                <option {{ $section->owner == 'LINTASARTA' ? 'selected' : '' }} value="LINTASARTA">LINTASARTA</option>
                                                <option {{ $section->owner == 'MORATEL' ? 'selected' : '' }} value="MORATEL">MORATEL</option>
                                                <option {{ $section->owner == 'PDM' ? 'selected' : '' }} value="PDM">PDM</option>
                                                <option {{ $section->owner == 'REMALA' ? 'selected' : '' }} value="REMALA">REMALA</option>
                                                <option {{ $section->owner == 'SDI' ? 'selected' : '' }} value="SDI">SDI</option>
                                                <option {{ $section->owner == 'SOLNET' ? 'selected' : '' }} value="SOLNET">SOLNET</option>
                                                <option {{ $section->owner == 'SSU' ? 'selected' : '' }} value="SSU">SSU</option>
                                                <option {{ $section->owner == 'TELKOM' ? 'selected' : '' }} value="TELKOM">TELKOM</option>
                                                <option {{ $section->owner == 'TIS' ? 'selected' : '' }} value="TIS">TIS</option>
                                                <option {{ $section->owner == 'TM' ? 'selected' : '' }} value="TM">TM</option>
                                                <option {{ $section->owner == 'XL' ? 'selected' : '' }} value="XL">XL</option>
                                                <option {{ $section->owner == 'AGORA' ? 'selected' : '' }} value="AGORA">AGORA</option>
                                            </select>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Core Capacity</label>
                                                <input type="number" name="core_capacity" value="{{$section->core_capacity}}" class="form-control mb-2">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Cable type</label>
                                                <input type="text" step="any" class="form-control mb-2" value="{{$section->cable_type}}" name="cable_type" value="">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">First RFS</label>
                                                <input type="text" step="any" class="form-control mb-2" value="{{$section->first_rfs}}" name="first_rfs" value="">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Initial Length</label>
                                                <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$section->initial_length}}" name="initial_length" value="">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Initial Min Total Loss</label>
                                                <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$section->initial_min_total_loss}}" name="initial_min_total_loss" value="">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Initial Max Total Loss</label>
                                                <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$section->initial_max_total_loss}}" name="initial_max_total_loss" value="">
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div style="min-height: 1rem;"></div>
                                <button style="padding:.5rem 3rem;" type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                            </form>
                        @break
                    @case('with_sub_section')
                        <div style="min-height:2rem;"></div>
                        <form action="{{ route('section.update') }}" method="POST">
                            {{ csrf_field() }}
                            <input hidden type="text" name="input_type" value="regular">
                            <input hidden type="text" name="project_id" value="{{$project_id}}">
                            <input hidden type="text" name="segment_id" value="{{$segment_id}}">
                            <input hidden type="text" name="section_id" value="{{$section_id}}">
                            <input hidden type="text" name="section_type" value="with_sub_section">
                            <input hidden type="text" name="role" value="{{auth()->user()->role}}">

                            <div class="col-12">
                                <div class="card w-100 position-relative overflow-hidden mb-0">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold">Cable category</label>
                                                    <select class="form-select" name="cable_category">
                                                        <option value="INLAND" {{ $section->cable_category == 'INLAND' ? 'selected' : '' }}>INLAND</option>
                                                        <option value="SUBMARINE" {{ $section->cable_category == 'SUBMARINE' ? 'selected' : '' }}>SUBMARINE</option>
                                                        <option value="LASTMILE" {{ $section->cable_category == 'LASTMILE' ? 'selected' : '' }}>LASTMILE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold">Saction Name</label>
                                                    <input type="text" class="form-control mb-2" name="section_name" required value="{{$section->section_name}}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold">Near End </label>
                                                    <td style="min-width:10rem;"><input type="text" class="form-control mb-2" name="near_end" required value="{{$section->near_end}}"></td>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold">Far End </label>
                                                    <input type="text" class="form-control mb-2" name="far_end" required value="{{$section->far_end}}">
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Section Route</label>
                                                <select name="section_route" class="form-select">
                                                    <option value="1_route" {{ $section->section_route == '1_route' ? 'selected' : '' }}>Main</option>
                                                    <option value="2_route" {{ $section->section_route == '2_route' ? 'selected' : '' }} >Diversity</option>
                                                    <option value="3_route" {{ $section->section_route == '3_route' ? 'selected' : '' }} >3rd Route</option>
                                                    <option value="4_route" {{ $section->section_route == '4_route' ? 'selected' : '' }} >4th Route</option>
                                                </select>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Core Capacity</label>
                                                <input type="number" name="core_capacity" value="{{$section->core_capacity}}" class="form-control mb-2">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">Cable type</label>
                                                <input type="text" step="any" class="form-control mb-2" value="{{$section->cable_type}}" name="cable_type" value="">
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-semibold">First RFS</label>
                                                <input type="text" step="any" class="form-control mb-2" value="{{$section->first_rfs}}" name="first_rfs" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="min-height: 1rem;"></div>
                            <button style="padding:.5rem 3rem;" type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                        </form>
                    @break
                        Error, Can't find section type
                    @default
                @endswitch
            @break;
        @case('ms')
                <div style="min-height:2rem;"></div>
                <form action="{{ route('section.update') }}" method="POST">
                    {{ csrf_field() }}
                    <input hidden type="text" name="input_type" value="regular">
                    <input hidden type="text" name="project_id" value="{{$project_id}}">
                    <input hidden type="text" name="segment_id" value="{{$segment_id}}">
                    <input hidden type="text" name="section_id" value="{{$section_id}}">
                    <input hidden type="text" name="section_type" value="regular">
                    <input hidden type="text" name="role" value="{{auth()->user()->role}}">
                    <input hidden type="text" name="cable_category" value="{{$section->cable_category}}">
                    
                    <div class="col-12">
                        <div class="card w-100 position-relative overflow-hidden mb-0">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-6 mb-4">
                                        <label class="form-label fw-semibold">Actual Length</label>
                                        <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$section->actual_length}}" name="actual_length" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="min-height: 1rem;"></div>
                    <button style="padding:.5rem 3rem;" type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                </form>
            @break;
        @default
            <h5 class="text-danger">You dont have permission to edit this section ! </h5>      
    @endswitch
        
@endsection
