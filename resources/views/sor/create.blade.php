
@extends('layouts.app')

@php
    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->get()->first();
    
    if ($type_id == '-') {
        $sub_section = DB::table('sub_section')
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->where('section_id', $section_id)
        ->where('sub_section_id', $sub_section_id)
        ->where('customer_id', $customer_id)
        ->get()
        ->first();

    } else {
        $sub_section = DB::table('sub_section')
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->where('section_id', $section_id)
        ->where('sub_section_id', $sub_section_id)
        ->where('customer_id', $customer_id)
        ->where('type_id', $type_id)
        ->get()
        ->first();
    }
    
    
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
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem; text-transform: uppercase;"> Sub Section : {{$sub_section->sub_section_name}} <span style="text-transform: uppercase;">({{DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name}} {{$sub_section->type_id}})</span></h3>
                    <h3 class="fw-semibold text-white" style="font-size: 1.8rem;">  UPLOAD SOR </h3>
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
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('sub_section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id, 'customer_id'=> $sub_section->customer_id, 'type_id'=> $type_id, 'sub_section_id'=> $sub_section->sub_section_id])}}">{{$sub_section->sub_section_name}}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                    Upload SOR
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
    <form id="uploadForm" action="{{ route('sor.store') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input hidden type="text" name="project_id" value="{{$project_id}}">
        <input hidden type="text" name="route_id" value="{{$route_id}}">
        <input hidden type="text" name="segment_id" value="{{$segment_id}}">
        <input hidden type="text" name="section_id" value="{{$section_id}}">
        <input hidden type="text" name="customer_id" value="{{$customer_id}}">
        <input hidden type="text" name="type_id" value="{{$type_id}}">
        <input hidden type="text" name="sub_section_id" value="{{$sub_section_id}}">
        <div class="col-12">
            <div class="card w-100 position-relative overflow-hidden mb-0">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-6 mb-4">
                            <label class="form-label fw-semibold">Upload SOR</label>
                            <br>
                            <label class="form-label fw-semibold text-danger">Format file should be .zip ( <a href="https://www.winzip.com/en/pages/download/winzip/?hd=Zip%20and%20Unzip%20Files%20Software&x-target=ppc&promo=ppc&utm_source=google&utm_medium=cpc&utm_campaign=wz-dd-all-adwordsppc&utm_content=40437508056&utm_term=zip&utm_id=760563327&gad_source=1&gclid=CjwKCAjw_4S3BhAAEiwA_64YhuhMn30S8Kz7TMN36lt51-naQTBqYb8TNSDlmJx0Ec1297uoNmOgJxoCeygQAvD_BwE">Download WinZip application</a> )</label>
                            <input type="file" class="form-control mt-5 mb-2" name="file" value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="min-height: 5rem;"></div>
        <div class="col-6">
            <div class="row">
                <div class="col-4">
                    <button id="submitButton" type="submit" class="btn btn-primary" style="width:100%;">Submit</button>
                    <button id="loadingButton" class="btn btn-primary" type="button" disabled="" style="width:100%; display:none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Uploading file please wait ...
                    </button>
                    <div id="status"></div>
                </div>
                <div class="col-2">
                    <a href="{{ URL::previous() }}" class="btn btn-danger" style="width:100%;">Cancel</a>
                </div>
                <div class="col-6"></div>
            </div>
        </div>
    </form>
    <div style="min-height: 5rem;"></div>
@endsection

@section('script')
<script>
    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        document.getElementById('submitButton').style.display = 'none';
        document.getElementById('loadingButton').style.display = 'block';
    });

    
</script>
@endsection