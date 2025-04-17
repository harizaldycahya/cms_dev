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

@section('breadcrumb')
    <div class="card bg-dark text-white shadow-lg position-relative overflow-hidden">
        <div class="card-body px-5 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold text-white" style="font-size: 1.5rem;"> #{{$project->project_id}} PROJECT </h3>
                    <h3 class="fw-semibold text-white" style="font-size: 1.8rem;">  {{$project->project_name}} ( {{$project->project_description}} )</h3>
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
    @php
        $number_of_segment = count(DB::table('segment')->where('project_id', $project->project_id)->groupBy('segment_id')->get());
        
        $route_id = request()->segment(4) == '-' ? '2' : request()->segment(4);

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

        $segments = DB::table('segment')->where('project_id', $project->project_id)->where('route_id', $route_id)->orderByRaw('CAST(segment_id AS UNSIGNED)')->get();       
        $all_segments = DB::table('segment')->where('project_id', $project->project_id)->orderByRaw('CAST(segment_id AS UNSIGNED)')->get();       
        $check_section = DB::table('section')->where('project_id', $project->project_id)->where('route_id', $route_id)->get();
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-8">
                            <h5 class="card-title mb-9 fw-semibold"> Summary Cable Project</h5>
                            <div class="fw-semibold mb-3">Project ID : {{$project->project_id}}</div>
                            <div class="fw-semibold mb-3">Cable Project Name : {{$project->project_name}} ({{$project->project_description}})</div>
                            <div class="fw-semibold mb-3">Number Of Segment :  {{$number_of_segment}}</div>
                            <div class="fw-semibold mb-3">Number Of Section : NOT YET</div>
                            @php
                                $section = DB::table('section')->where('project_id', $project->project_id)->first();
                            @endphp
                            <div class="fw-semibold mb-3">Cable Type: NOT YET</div>
                        </div>
                        <div class="col-4">
                            @if(auth()->user()->role == 'engineering')
                                <div class="d-flex justify-content-end">
                                    <div style="text-align:right;" class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            MANAGE PROJECT
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a href="{{route('project.edit', $project->project_id)}}" display="block" class="dropdown-item">Edit Project</a>
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
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card bg-light">
                <div style="height:6rem;"></div>
                @if(count($all_segments) > 0)
                    @foreach ($all_segments as $segment_item)
                        @php
                            $project_id = $project->project_id;
                            $segment_id = $segment_item->segment_id;
                            $segment_name = $segment_item->segment_name;
                            list($near_end, $far_end) = explode(" - ", $segment_name);
                            $project = DB::table('project')->where('project_id', $project->project_id)->get()->first();
                        
                            switch($segment_item->route_id){
                                case '1':
                                    $route_name = 'SUBMARINE';
                                    break;
                                case '2':
                                    $route_name = 'INLAND';
                                    break;
                                case '3':
                                    $route_name = 'LASTMILE';
                                    break;
                            }
                        
                            $route_id = $segment_item->route_id;
                        
                            $check_section = DB::table('section')->where('project_id', $project->project_id)->where('route_id', $route_id)->where('segment_id', $segment_item->segment_id)->get();
                            $get_sections = DB::table('section')->where('project_id', $project->project_id)->where('route_id', $route_id)->where('segment_id', $segment_item->segment_id)->get();
                            
                            $segment_availability_core_idle = 0;
                            $segment_availability_core_capacity = 0;
                            $lowest_section_id_idle = null;
                            
                        
                            foreach ($get_sections as $item_section) {
                                $main_core_capacity = $item_section->core_capacity;   
                            
                                // Fetch core data
                                $cores = DB::table('core')
                                    ->where('project_id', $project_id)
                                    ->where('route_id', $route_id)
                                    ->where('segment_id', $segment_id)
                                    ->where('section_id', $item_section->section_id)
                                    ->orderByRaw('CAST(core AS UNSIGNED) ASC')
                                    ->get();
                        
                                $uniqueCores = collect($cores)
                                ->groupBy(fn($core) => $core->core . '-' . $core->customer_id)
                                ->map(function ($group) {
                                    $allOk = $group->every(fn($core) => isset($core->actual_remarks) && strtoupper($core->actual_remarks) === 'OK'); // Ensure case sensitivity
                                    $finalRemark = $allOk ? 'OK' : 'NOT OK';
                                    return (object) [
                                        'core' => $group->first()->core,
                                        'customer_id' => $group->first()->customer_id,
                                        'status' => $group->first()->status,
                                        'actual_remarks' => $finalRemark,
                                    ];
                                })
                                ->values(); // Reset array keys
                        
                                // Calculate core capacities
                                $core_capacity = $uniqueCores->count();
                                // $sold_core = $uniqueCores->where('status', 'SOLD')->count();
                                $core_active = $uniqueCores->where('status', 'ACTIVE')->count();
                                $core_mismatch = $uniqueCores->where('status', 'MISMATCH')->count();
                                $core_booked = $uniqueCores->where('status', 'BOOKED')->count();
                                $sold_core = $core_active + $core_mismatch + $core_booked;
                                    
                                // Booked OK and NOT OK based on remarks, not status
                                $actual_core_booked_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
                                $actual_core_booked_not_ok = $uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();
                        
                                $core_used = $uniqueCores->where('status', 'USED')->count();
                                $idle_core = $uniqueCores->where('status', 'IDLE')->count();
                                $idle_general = $idle_core + $core_used;
                                
                                $actual_core_idle_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
                                $actual_core_idle_not_ok = $uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();
                        
                                $availability_core_idle = 0;
                                $availability_core_capacity = 0;
                        
                                if(($core_capacity - $core_active) != 0){
                                    $availability_core_idle = ($idle_general != 0) ? (($actual_core_idle_ok + $actual_core_booked_ok) / ( $idle_general + $core_booked)) * 100 : 0;
                                    $availability_core_capacity = ($core_capacity !=0 ) ? (($actual_core_idle_ok + $actual_core_booked_ok) / $core_capacity) * 100 : 0;
                                }else{
                                    $availability_core_idle = 0;
                                    $availability_core_capacity = 0;
                                }
                        
                                if (($availability_core_idle != 0 && $availability_core_idle < $segment_availability_core_idle) || $segment_availability_core_idle == 0) {
                                    $segment_availability_core_idle = $availability_core_idle;
                                    $lowest_section_id_idle = $item_section->section_id;
                                }
                        
                                if (($availability_core_capacity != 0 && $availability_core_capacity < $segment_availability_core_capacity) || $segment_availability_core_capacity == 0) {
                                    $segment_availability_core_capacity = $availability_core_capacity;
                                }
                            }

                            // start
                                // Fetch core data
                                $segment_cores = DB::table('core')
                                    ->where('project_id', $project_id)
                                    ->where('route_id', $route_id)
                                    ->where('segment_id', $segment_id)
                                    ->where('section_id', $lowest_section_id_idle)
                                    ->orderByRaw('CAST(core AS UNSIGNED) ASC')
                                    ->get();
                            
                                $segment_uniqueCores = collect($segment_cores)
                                ->groupBy(fn($core) => $core->core . '-' . $core->customer_id)
                                ->map(function ($group) {
                                    $allOk = $group->every(fn($core) => isset($core->actual_remarks) && strtoupper($core->actual_remarks) === 'OK'); // Ensure case sensitivity
                                    $finalRemark = $allOk ? 'OK' : 'NOT OK';
                                    return (object) [
                                        'core' => $group->first()->core,
                                        'customer_id' => $group->first()->customer_id,
                                        'status' => $group->first()->status,
                                        'actual_remarks' => $finalRemark,
                                    ];
                                })
                                ->values(); // Reset array keys
                        
                                // Calculate core capacities
                                $segment_core_capacity = $segment_uniqueCores->count();
                                // $sold_core = $segment_uniqueCores->where('status', 'SOLD')->count();
                                $segment_core_active = $segment_uniqueCores->where('status', 'ACTIVE')->count();
                                $segment_core_mismatch = $segment_uniqueCores->where('status', 'MISMATCH')->count();
                                $segment_core_booked = $segment_uniqueCores->where('status', 'BOOKED')->count();
                                $segment_sold_core = $segment_core_active + $segment_core_mismatch + $segment_core_booked;
                                    
                                // Booked OK and NOT OK based on remarks, not status
                                $actual_segment_core_booked_ok = $segment_uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'OK')->count();
                                $actual_segment_core_booked_not_ok = $segment_uniqueCores->where('status', 'BOOKED')->where('actual_remarks', 'NOT OK')->count();
                        
                                $segment_core_used = $segment_uniqueCores->where('status', 'USED')->count();
                                $segment_idle_core = $segment_uniqueCores->where('status', 'IDLE')->count();
                                $segment_idle_general = $segment_idle_core + $segment_core_used;
                                
                                $actual_segment_core_idle_ok = $segment_uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'OK')->count();
                                $actual_segment_core_idle_not_ok = $segment_uniqueCores->where('status', 'IDLE')->where('actual_remarks', 'NOT OK')->count();
                        
                            // end
                        
                        @endphp
                        {{-- here --}}
                        <div style="display:flex; height:80px; margin: 0rem 5rem; justify-content:left;">
                            <div style="width:30px; height:30px; background:rgb(22 163 74); position:relative;">
                                <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$near_end}}</p>
                            </div>
                            <div style="width:480px; height:30px; display:flex;">
                                <a style=" position:relative; width:100%; height:10%; {{ $segment_availability_core_idle < 50 ? 'background:rgb(239 68 68)' : 'background:rgb(22 163 74)' }};" class="line my-auto" href="{{route('segment.show', ['project_id'=>$project->project_id, 'route_id'=>$segment_item->route_id, 'segment_id'=> $segment_item->segment_id])}}">
                                    <div class="card hide" style=" position:absolute; bottom:2rem; left:2rem; min-width:40rem;">
                                        <div class="card-body">
                                            <div class="row align-items-start">
                                                <div class="row">
                                                    <table class="border-dark table table-bordered " style="width:100%; overflow-y:auto;">
                                                        <thead class="text-dark" style="font-size:.8rem;">
                                                            <tr>
                                                                <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core idle : {{ \Illuminate\Support\Str::limit($segment_availability_core_idle, 5, '') }} %</th>
                                                            </tr>
                                                            <tr>
                                                                <th scope="col" colspan="2" style="text-transform: capitalize;"> Availability based on core capacity : {{ \Illuminate\Support\Str::limit($segment_availability_core_capacity, 5, '') }} %</th>
                                                            </tr>
                                                        </thead>
                                                        <thead class="bg-dark text-white" style="font-size:.8rem;">
                                                            <tr>
                                                                <th scope="col">Initial Details</th>
                                                                <th scope="col">Actual Details</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-weight:bold">
                                                            <tr>
                                                                <td>Main Core Capacity : {{$segment_core_capacity}}</td>
                                                                <td>Main Core Capacity : {{$main_core_capacity}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Core Capacity : {{$core_capacity}}</td>
                                                                <td>Core Capacity : {{$core_capacity}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    SOLD Core : {{$sold_core}}
                                                                </td>
                                                                <td>
                                                                    SOLD Core : {{$sold_core}}
                                                                    <br>
                                                                    <ul style="padding:1rem 2rem;">
                                                                        <li style="list-style-type:square" > Active : {{$core_active}}</li>
                                                                        <li style="list-style-type:square" > Mismatch : {{$core_mismatch}}</li>
                                                                        <li style="list-style-type:square" > 
                                                                            Booked :{{$core_booked}}
                                                                            <br> 
                                                                            <ul style="padding:0.2rem 2rem;">
                                                                                <li style="list-style-type:circle" > OK : {{$actual_core_booked_ok}}</li>
                                                                                <li style="list-style-type:circle" > NOT OK : {{$actual_core_booked_not_ok}}</li>
                                                                            </ul>
                                                                        </li>
                                                                        
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>IDLE Core : {{$idle_general}}</td>
                                                                <td>
                                                                    IDLE Core : {{$idle_general}}
                                                                    <br>
                                                                    <ul style="padding:1rem 2rem;">
                                                                        <li style="list-style-type:square" > Used : {{$core_used}}</li>
                                                                        <li style="list-style-type:square" > 
                                                                            IDLE :{{$idle_core}}
                                                                            <br> 
                                                                            <ul style="padding:0.2rem 2rem;">
                                                                                <li style="list-style-type:circle" > OK : {{$actual_core_idle_ok}}</li>
                                                                                <li style="list-style-type:circle" > NOT OK : {{$actual_core_idle_not_ok}}</li>
                                                                            </ul>
                                                                        </li>
                                                                        
                                                                    </ul>
                                                                </td>
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
                                <p style="position:absolute; top: -2.5rem; font-size:.7rem; font-weight:bold; color:black; width:200px;" class="absolute -top-10 text-xs font-bold">{{$far_end}}</p>
                            </div>
                        </div>
                        <div style="height:10px;"></div>
                        {{-- here --}}
                    @endforeach`
                @else
                    <h5 style="font-weight: bold; font-size:1rem;" class="text-danger">NO DATA</h5>
                @endif
            </div>    
        </div>
    </div>

    
    <div class="row card p-5">
       
        <div class="row">
            <div class="col-8">
                <ul class="nav nav-pills mb-3 px-auto">
                    <div class="d-flex" style="margin-right: 1rem;">
                        <div style="text-align:right;" class="dropdown">
                            <a href="{{route('project.show', ['project_id'=>$project->project_id, 'route_id'=> '2'])}}" class="btn btn-{{$route_id == '2' ? 'primary' : 'light'}} px-5" type="button">
                                INLAND
                                <span style="font-size: 1.2rem; margin-left: 0.5rem;"><i class="ti ti-shovel"></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex" style="margin-right: 1rem;">
                        <div style="text-align:right;" class="dropdown">
                            <a href="{{route('project.show', ['project_id'=>$project->project_id, 'route_id'=> '1'])}}" class="btn btn-{{$route_id == '1' ? 'primary' : 'light'}} px-5" type="button">
                                SUBMARINE
                                <span style="font-size: 1.2rem; margin-left: 0.5rem;"><i class="ti ti-submarine"></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex" style="margin-right: 1rem;">
                        <div style="text-align:right;" class="dropdown">
                            <a href="{{route('project.show', ['project_id'=>$project->project_id, 'route_id'=> '3'])}}" class="btn btn-{{$route_id == '3' ? 'primary' : 'light'}} px-5" type="button">
                                LASTMILE
                                <span style="font-size: 1.2rem; margin-left: 0.5rem;"><i class="ti ti-truck"></i></span>
                            </a>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="col-4">
                @if(auth()->user()->role == 'engineering')
                    <div class="d-flex justify-content-end">
                        <div style="text-align:right;" class="dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                MANAGE SEGMENT {{$route_name}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a href="{{route('segment.create', ['project_id'=>$project->project_id, 'route_id'=> $route_id ])}}" display="block" class="dropdown-item">Create Segment</a>
                                <a href="{{route('segment.edit', ['project_id'=>$project->project_id, 'route_id'=> $route_id ])}}" display="block" class="dropdown-item">Manage Segment</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
            
        <div>
            <div class="row">
                <div class="col-lg-12 align-items-stretch">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4" style="text-transform: capitalize">
                                Segment List {{ ucwords(strtolower($route_name)) }}
                            </h5>
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                    <thead class="bg-dark fs-4">
                                        <tr>
                                            <th class="">
                                                <h6 style="display:inline-block; color:white;" class="fw-semibold mb-0">Segment ID</h6>
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
                                            <tr>
                                                <td style="width:10%;">{{$segment->segment_id}}</td>                                               
                                                <td style="width:70%;">{{$segment->segment_name}}</td>                                               
                                                <td style="width:30%; text-align: center;">
                                                    <a class="btn btn-primary" href="{{ route('segment.show', ['project_id' => $project->project_id, 'route_id' => $route_id, 'segment_id' => $segment->segment_id]) }}">
                                                        DETAIL
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