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


@php

    $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    $segment = DB::table('segment')->where('route_id', $route_id)->where('segment_id', $segment_id)->get()->first();
    $section = DB::table('section')->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->get()->first();
    
    if($type_id == '-'){
        $sub_section = DB::table('sub_section')->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('customer_id', $customer_id)->where('sub_section_id', $sub_section_id)->get()->first();
    }else{
        $sub_section = DB::table('sub_section')->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('customer_id', $customer_id)->where('sub_section_id', $sub_section_id)->where('type_id', $type_id)->get()->first();
    }

    $customer = DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first();
    
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
        <div class="card-body px-5 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                        <h3 class="fw-semibold text-white" style="font-size: 1.5rem; text-transform: uppercase;"> SECTION {{ ucwords(strtolower($section->section_name)) }} ({{$route_name}})</h3>
                        <h3 class="fw-semibold text-white text-capitalize" style="font-size: 2rem;"> {{$input_type}} Core Section : {{$sub_section->sub_section_name}} ({{$customer->customer_name}})</h3>
                    <hr>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('project.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id])}}">{{$project->project_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('segment.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id, 'segment_id'=>$segment->segment_id])}}">{{$segment->segment_name}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="{{route('section.show', ['project_id'=> $project->project_id, 'route_id'=> $route_id, 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id])}}">{{$section->section_name}}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a class="text-decoration-none" href="{{route('sub_section.show', ['project_id'=> $project->project_id, 'route_id'=>$route_id, 'segment_id'=>$segment->segment_id, 'section_id'=>$section->section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id, 'sub_section_id'=>$sub_section_id])}}">
                                    {{ $sub_section->sub_section_name}}
                                </a>
                            </li>
                            <li class="breadcrumb-item text-capitalize">
                                {{$input_type}}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @switch($input_type)
        @case('setup')
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Ranged Input</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Custom Input</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="card non_ropa">
                        <div class="card-body">
                            <div class="card p-5">
                                <h5>Ranged Input</h5>
                                <div class="card-body">
                                    <form action="{{ route('core.store') }}" method="POST" enctype=multipart/form-data>
                                        {{ csrf_field() }}
                                        <input type="hidden" name="input_type" value="range" >
                                        <input type="hidden" name="project_id" value="{{$project->project_id}}" >
                                        <input type="hidden" name="route_id" value="{{$route_id}}" >
                                        <input type="hidden" name="segment_id" value="{{$segment->segment_id}}" >
                                        <input type="hidden" name="section_id" value="{{$section->section_id}}" >
                                        <input type="hidden" name="customer_id" value="{{$sub_section->customer_id}}" >
                                        <input type="hidden" name="type_id" value="{{$type_id}}" >
                                        <input type="hidden" name="sub_section_id" value="{{$sub_section->sub_section_id}}" >
                                        <div class="row" style="width:60%">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <th colspan="2">
                                                        Main Core Capacity : {{$section->core_capacity}}
                                                    </th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label for="from">From Core</label>
                                                            <input class="form-control" id="from" name="from" type="text" pattern="^\d{3}$" maxlength="3" required onblur="formatInput(this)" oninput="restrictInput(this)">
                                                        </td>
                                                        <td>
                                                            <label for="to">To Core</label>
                                                            <input class="form-control" id="to" name="to" type="text" pattern="^\d{3}$" maxlength="3" required onblur="formatInput(this)" oninput="restrictInput(this)">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div style="min-height: 3rem;"></div>
                                        <div class="text-left">
                                            <button type="submit" style="padding:.5rem 3rem;" class="btn btn-primary">Submit</button>
                                            <a href="{{ URL::previous() }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                    <div class="card ropa">
                        <div class="card-body">
                            <div class="card p-5">
                                <h5>Custom Input</h5>
                                <div class="card-body">
                                    <form action="{{ route('core.store') }}" method="POST" enctype=multipart/form-data>
                                        {{ csrf_field() }}
                                        <input type="hidden" name="input_type" value="custom" >
                                        <input type="hidden" name="project_id" value="{{$project->project_id}}" >
                                        <input type="hidden" name="route_id" value="{{$route_id}}" >
                                        <input type="hidden" name="segment_id" value="{{$segment->segment_id}}" >
                                        <input type="hidden" name="section_id" value="{{$section->section_id}}" >
                                        <input type="hidden" name="customer_id" value="{{$sub_section->customer_id}}" >
                                        <input type="hidden" name="type_id" value="{{$type_id}}" >
                                        <input type="hidden" name="sub_section_id" value="{{$sub_section->sub_section_id}}" >
                                        <table class="table table-bordered" style="width:60%;">
                                            <thead>
                                                <th class="text-center">
                                                    Select
                                                </th>
                                                <th class="px-5">
                                                    Core
                                                </th>
                                            </thead>
                                            <tbody>
                                                @for($i = 1; $i <= $section->core_capacity; $i++)
                                                    <tr>
                                                        <td style="width: 20%" class="text-center">
                                                            <input class="form-check-input" style="width: 2rem; height: 2rem;" type="checkbox" name="core[]" value="{{ str_pad($i, 3, '0', STR_PAD_LEFT) }}">
                                                        </td>
                                                        <td class="px-5">
                                                            Core {{ str_pad($i, 3, '0', STR_PAD_LEFT) }}
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                        <div style="min-height: 3rem;"></div>
                                        <div class="text-left">
                                            <button type="submit" style="padding:.5rem 3rem;" class="btn btn-primary">Submit</button>
                                            <a href="{{ URL::previous() }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @break

        @case('add')
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('core.store') }}" method="POST" enctype=multipart/form-data>
                                {{ csrf_field() }}
                                <input type="hidden" name="input_type" value="add">
                                <input type="hidden" name="project_id" value="{{$project_id}}" >
                                <input type="hidden" name="route_id" value="{{$route_id}}" >
                                <input type="hidden" name="segment_id" value="{{$segment_id}}" >
                                <input type="hidden" name="section_id" value="{{$section_id}}" >
                                <input type="hidden" name="customer_id" value="{{$customer_id}}" >
                                <input type="hidden" name="type_id" value="{{$type_id}}">
                                <input type="hidden" name="sub_section_id" value="{{$sub_section->sub_section_id}}" >
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                        <thead class="text-dark fs-4">
                                            <tr>
                                                <th class="text-center">
                                                    <div style="cursor:pointer;" onclick="cloneRow()"
                                                        class="inline-block text-success">
                                                        <i style="font-size:1.5rem;" class="ti ti-square-plus"></i>
                                                    </div>
                                                </th>
                                                <th class="">
                                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                        Core
                                                    </h6>
                                                </th>
                                                <th class="">
                                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                        Length
                                                    </h6>
                                                </th>
                                                <th class="">
                                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                        Total Loss DB
                                                    </h6>
                                                </th>
                                                <th class="">
                                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                        Loss DB KM
                                                    </h6>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableToModify">
                                            <tr id="rowToClone" class="testing2">
                                                <td class="grid justify-center text-center py-4">
                                                    <div style="cursor:pointer;" onclick="hapus(this)"
                                                        class="delete_button inline-block text-danger">
                                                        <i style="font-size:1.5rem;" class="ti ti-square-x"></i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input class="form-control" class="outline outline-2" name="core[]"
                                                        type="text" pattern="^\d{3}$" maxlength="3" required onblur="formatInput(this)" oninput="restrictInput(this)">
                                                </td>
                                                <td>
                                                    <input class="form-control" class="outline outline-2" name="length[]"
                                                        type="text">
                                                </td>
                                                <td>
                                                    <input class="form-control" class="outline outline-2" name="total_loss_db[]"
                                                        type="text">
                                                </td>
                                                <td>
                                                    <input class="form-control" class="outline outline-2" name="loss_db_km[]"
                                                        type="text">
                                                </td>
                                            </tr>
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
        @break

        @default
            <div>Cannot Find Input Type !</div>
    @endswitch

@endsection

@section('script')
    <script>
        function makeid(length) {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            const charactersLength = characters.length;
            let counter = 0;
            while (counter < length) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
                counter += 1;
            }
            return result;
        }

        const hapus = function(e) {
            var row = document.getElementById(e.parentNode.parentNode.id);
            row.remove();
        }

        function cloneRow() {
            var row = document.getElementById("rowToClone");
            var table = document.getElementById("tableToModify");
            var clone = row.cloneNode(true);
            clone.id = makeid(10);
            table.appendChild(clone);
        }

        function restrictInput(input) {
            // Allow only numbers
            input.value = input.value.replace(/[^0-9]/g, '');

            // Restrict length to 3 digits
            if (input.value.length > 3) {
                input.value = input.value.slice(0, 3);
            }
        }

        function formatInput(input) {
            // When the user leaves the input field (onblur), pad with leading zeros if needed
            if (input.value.length < 3) {
                input.value = input.value.padStart(3, '0');
            }
        }

    </script>
@endsection
