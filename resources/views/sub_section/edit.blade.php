@extends('layouts.app')

@php
    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('section_id', $section_id)->get()->first();
    $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();
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
                                Edit sub section
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
                <div style="min-height:2rem;"></div>
                <form action="{{ route('sub_section.update') }}" method="POST">
                    {{ csrf_field() }}
                    <input hidden type="text" name="project_id" value="{{$project_id}}">
                    <input hidden type="text" name="segment_id" value="{{$segment_id}}">
                    <input hidden type="text" name="section_id" value="{{$section_id}}">
                    <input hidden type="text" name="sub_section_id" value="{{$sub_section_id}}">
                    
                    <div class="col-12">
                        <div class="card w-100 position-relative overflow-hidden mb-0">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Sub Saction Name</label>
                                            <input type="text" class="form-control mb-2" name="sub_section_name" required value="{{$sub_section->sub_section_name}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Near End </label>
                                            <td style="min-width:10rem;"><input type="text" class="form-control mb-2" name="sub_near_end" required value="{{$sub_section->sub_near_end}}"></td>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Far End </label>
                                            <input type="text" class="form-control mb-2" name="sub_far_end" required value="{{$sub_section->sub_far_end}}">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Sub Section Owner</label>
                                        <select name="sub_owner" class="form-select">
                                            <option {{ $sub_section->sub_owner == 'trias' ? 'selected' : '' }} value="trias">TRIASMITRA</option>
                                            <option {{ $sub_section->sub_owner == 'BIZNET' ? 'selected' : '' }} value="BIZNET">BIZNET</option>
                                            <option {{ $sub_section->sub_owner == 'FIBERSTAR' ? 'selected' : '' }} value="FIBERSTAR">FIBERSTAR</option>
                                            <option {{ $sub_section->sub_owner == 'H3I' ? 'selected' : '' }} value="H3I">H3I</option>
                                            <option {{ $sub_section->sub_owner == 'HSP' ? 'selected' : '' }} value="HSP">HSP</option>
                                            <option {{ $sub_section->sub_owner == 'IFORTE' ? 'selected' : '' }} value="IFORTE">IFORTE</option>
                                            <option {{ $sub_section->sub_owner == 'INDOSAT' ? 'selected' : '' }} value="INDOSAT">INDOSAT</option>
                                            <option {{ $sub_section->sub_owner == 'IPLUS' ? 'selected' : '' }} value="IPLUS">IPLUS</option>
                                            <option {{ $sub_section->sub_owner == 'JKLD' ? 'selected' : '' }} value="JKLD">JKLD</option>
                                            <option {{ $sub_section->sub_owner == 'LINKNET' ? 'selected' : '' }} value="LINKNET">LINKNET</option>
                                            <option {{ $sub_section->sub_owner == 'LINTASARTA' ? 'selected' : '' }} value="LINTASARTA">LINTASARTA</option>
                                            <option {{ $sub_section->sub_owner == 'MORATEL' ? 'selected' : '' }} value="MORATEL">MORATEL</option>
                                            <option {{ $sub_section->sub_owner == 'PDM' ? 'selected' : '' }} value="PDM">PDM</option>
                                            <option {{ $sub_section->sub_owner == 'REMALA' ? 'selected' : '' }} value="REMALA">REMALA</option>
                                            <option {{ $sub_section->sub_owner == 'SDI' ? 'selected' : '' }} value="SDI">SDI</option>
                                            <option {{ $sub_section->sub_owner == 'SOLNET' ? 'selected' : '' }} value="SOLNET">SOLNET</option>
                                            <option {{ $sub_section->sub_owner == 'SSU' ? 'selected' : '' }} value="SSU">SSU</option>
                                            <option {{ $sub_section->sub_owner == 'TELKOM' ? 'selected' : '' }} value="TELKOM">TELKOM</option>
                                            <option {{ $sub_section->sub_owner == 'TIS' ? 'selected' : '' }} value="TIS">TIS</option>
                                            <option {{ $sub_section->sub_owner == 'TM' ? 'selected' : '' }} value="TM">TM</option>
                                            <option {{ $sub_section->sub_owner == 'XL' ? 'selected' : '' }} value="XL">XL</option>
                                            <option {{ $sub_section->sub_owner == 'AGORA' ? 'selected' : '' }} value="AGORA">AGORA</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Site Owner Near End </label>
                                            <td style="min-width:10rem;"><input type="text" class="form-control mb-2" name="sub_site_owner_near_end" value="{{$sub_section->sub_site_owner_near_end}}"></td>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Site Owner Far End </label>
                                            <td style="min-width:10rem;"><input type="text" class="form-control mb-2" name="sub_site_owner_far_end" value="{{$sub_section->sub_site_owner_far_end}}"></td>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Initial Length</label>
                                        <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$sub_section->sub_initial_length}}" name="sub_initial_length" value="">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Initial Min Total Loss</label>
                                        <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$sub_section->sub_initial_min_total_loss}}" name="sub_initial_min_total_loss" value="">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Initial Max Total Loss</label>
                                        <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$sub_section->sub_initial_max_total_loss}}" name="sub_initial_max_total_loss" value="">
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
        @case('ms')
                <div style="min-height:2rem;"></div>
                <form action="{{ route('sub_section.update') }}" method="POST">
                    {{ csrf_field() }}
                    <input hidden type="text" name="project_id" value="{{$project_id}}">
                    <input hidden type="text" name="segment_id" value="{{$segment_id}}">
                    <input hidden type="text" name="section_id" value="{{$section_id}}">
                    <input hidden type="text" name="sub_section_id" value="{{$sub_section_id}}">
                    
                    <div class="col-12">
                        <div class="card w-100 position-relative overflow-hidden mb-0">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-6 mb-4">
                                        <label class="form-label fw-semibold">Actual Length</label>
                                        <input type="number" step="0.0001" min="0" class="form-control mb-2" value="{{$sub_section->sub_actual_length}}" name="sub_actual_length" value="">
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
