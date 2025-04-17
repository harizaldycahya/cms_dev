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
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem;"> Master Data Project</h3>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb" style="font-size: 1rem;">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                Master Data Project
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
        <div class="col-lg-12 align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="card-title fw-semibold mb-4 text-start">Cable Project List</h5>
                        </div>
                        <div class="col-6 text-end">
                            <a href="{{route('project.create')}}" class="btn btn-primary" type="button">
                                Create New Cable Project
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                            <thead class="bg-dark fs-4">
                                    <tr>
                                        <th style="width:10%;" class="text-center">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0">Project ID</h6>
                                        </th>
                                        <th style="width:30%;">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Project Name</h6>
                                        </th>
                                        <th style="width:50%;">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Project Description</h6>
                                        </th>
                                        <th style="width:10%;">
                                            <h6 style="color:white; display:inline-block;" class="fw-semibold mb-0 px-4">Action</h6>
                                        </th>
                                    </tr>
                            </thead>
                            <tbody>
                                @foreach ($projects as $project)
                                    <tr>
                                        <td style="text-align: center;">{{$project->project_id}}</td>
                                        
                                        <td class="px-5">
                                            <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                {{$project->project_name}} 
                                            </a>
                                        </td>
                                        <td class="px-5">
                                            <a type="button" class=" btn-notification" style="cursor: default; color:black;">
                                                {{$project->project_description}} 
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="{{route('project.edit', $project->project_id)}}" class="btn btn-secondary">
                                                <i style="font-size:1.3rem;" class="ti ti-pencil"></i>
                                            </a>
                                            <a href="{{route('project.delete', $project->project_id)}}" class="btn btn-danger">
                                                <i style="font-size:1.3rem;" class="ti ti-square-x"></i>
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
        function confirmDelete() {
            if (confirm("Are you sure you want to delete this segment ?")) {
                return true;
            } else {
                return false;
            }
        }
    </script>
@endsection
