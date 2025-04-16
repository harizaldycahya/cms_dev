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

@endsection

@section('breadcrumb')
    <div class="card bg-dark text-white shadow-lg position-relative overflow-hidden">
        <div class="card-body px-5 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 2rem;">Project : {{$project->project_name}}</h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                {{$project->project_name}}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-8">
                            <h5 class="card-title mb-9 fw-semibold"> Summary Cable Project</h5>
                            <div class="fw-semibold mb-3">Cable Project Name : {{$project->project_name}} ({{$project->project_description}})</div>
                            <div class="fw-semibold mb-3">Number Of Segment :  {{count($segments)}}</div>
                            <div class="fw-semibold mb-3">Number Of Section : {{count(DB::table('section')->where('project_id', $project->project_id)->get())}}</div>
                            @php
                                $section = DB::table('section')->where('project_id', $project->project_id)->first();
                            @endphp
                            <div class="fw-semibold mb-3">Cable Type: {{ $section && $section->cable_type != null ? $section->cable_type : 'NO DATA' }}</div>
                        </div>
                        <div class="col-4">
                            @if(auth()->user()->role == 'engineering')
                                <div class="d-flex justify-content-end">
                                    <div style="text-align:right;" class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Menu
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a href="{{route('segment.create', $project->project_id)}}" display="block" class="dropdown-item">Add Segment</a>
                                            <a href="{{route('project.edit', $project->project_id)}}" display="block" class="dropdown-item">Manage Project</a>
                                            <a href="{{route('project.delete', $project->project_id)}}" onclick="return confirmDelete();" display="block" class="dropdown-item">Delete Project</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <h5 class="card-title mb-9 fw-semibold" style=""> Segment List</h5>
                <hr>
                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    @php
                        $segments = DB::table('segment')
                        ->where('project_id', $project->project_id)
                        ->orderByRaw('CAST(segment_id AS UNSIGNED) ASC')
                        ->get();
                    @endphp

                    @foreach ($segments as $segment)
                        <button style="display:inline; text-align:left;" class="nav-link {{ $loop->index == 0 ? 'active' : '' }}" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}" type="button" role="tab" >{{$segment->segment_name}}</button>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card p-5">
                    @php
                        $segments = DB::table('segment')
                        ->where('project_id', $project->project_id)
                        ->orderByRaw('CAST(segment_id AS UNSIGNED) ASC')
                        ->get();
                    @endphp
                    @foreach ($segments as $segment)
                        <div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="{{$segment->segment_id}}" role="tabpanel">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="first-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-first_route" type="button" role="tab" aria-controls="first" aria-selected="true">Main Route</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="second-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-second_route" type="button" role="tab" aria-controls="second" aria-selected="false">Diversity Route</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="third-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-third_route" type="button" role="tab" aria-controls="third" aria-selected="false">3rd Route</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="forth-tab" data-bs-toggle="pill" data-bs-target="#{{$segment->segment_id}}-forth_route" type="button" role="tab" aria-controls="forth" aria-selected="false">4th Route</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="{{$segment->segment_id}}-first_route" role="tabpanel" >
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
                                                                    $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                }else{
                                                                    $actual_availability = "";
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
                                                            <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
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
                                                                                            <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                <div class="tab-pane fade" id="{{$segment->segment_id}}-second_route" role="tabpanel" >
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
                                                                    $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                }else{
                                                                    $actual_availability = "";
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
                                                            <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
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
                                                                                            <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                <div class="tab-pane fade" id="{{$segment->segment_id}}-third_route" role="tabpanel">
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
                                                                    $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                }else{
                                                                    $actual_availability = "";
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
                                                            <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
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
                                                                                            <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                <div class="tab-pane fade" id="{{$segment->segment_id}}-forth_route" role="tabpanel">
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
                                                                    $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                }else{
                                                                    $actual_availability = "";
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
                                                            <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}">
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
                                                                                            <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[0]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $ropa_sub_section[1]->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                                                                            $actual_availability = ($actual_core_ok / ($actual_core_capacity - $actual_core_aktif)) * 100;
                                                                        }else{
                                                                            $actual_availability = "";
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
                                                                    <a style=" position:relative; width:100%; height:10%; {{ $actual_availability < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id' => $sub_section->sub_section_id])}}">
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
                                                                                                    <td style="font-size: .8rem;">Availability : {{ $actual_availability == null ? 0 : \Illuminate\Support\Str::limit($actual_availability, 5, $end=' ') }}  %</td>
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
                    @endforeach
                </div>  
            </div>
            
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 align-items-stretch">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Segment List</h5>
                    <div class="table-responsive">
                        <table id="example" class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                            <thead class="bg-dark fs-4">
                                <tr>
                                    <th class="">
                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">NO</h6>
                                    </th>
                                    <th class="">
                                        <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Segment Name</h6>
                                    </th>
                                    <th class="text-center" >
                                        <h6 style="color:white;" class="fw-semibold mb-0">Action</h6>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($segments as $segment)
                                    @php
                                        $number_of_requests = count(DB::table('draf_sor')->where('segment_id', $segment->segment_id)->where('status', 'PROCESS')->get());   
                                    @endphp
                                    <tr>
                                        <td style="width:10%; text-align: center;">{{$loop->index + 1}}</td>
                                        <td style="width:70%;" class="px-5">
                                            <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                {{$segment->segment_name}} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; 
                                                
                                                @if(auth()->user()->role == 'ms')
                                                    @if($number_of_requests > 0)
                                                        <span class="badge bg-danger">{{$number_of_requests}}</span>
                                                    @endif
                                                @endif
                                            </a>
                                        </td>
                                        <td class="text-center" style="width:20%; text-align: center;">
                                            <a href="{{route('segment.show', ['project_id'=>$project->project_id, 'segment_id'=>$segment->segment_id])}}" class="btn btn-primary m-1" type="button">
                                                Detail
                                            </a>
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
@endsection

@section('script')

    <script>
        // Using jQuery
        $('#v-pills-tab button').on('shown.bs.tab', function (e) {
            // Remove the custom class from all tab content
            $('.tab-pane').addClass('hide');

            // Add the custom class to the newly active tab content
            // $($(e.target).data('bs-target')).addClass('custom-active-class');
            $($(e.target).data('bs-target')).removeClass('hide');
            $($(e.target).data('bs-target')).removeClass('hide');
        });
    </script>

    <script>
        function confirmDelete() {
            if (confirm("Are you sure you want to delete this project?")) {
                return true;
            } else {
                return false;
            }
        }
    </script>
@endsection