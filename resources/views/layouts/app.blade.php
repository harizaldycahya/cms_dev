<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Core Management System</title>

  <link rel="shortcut icon" type="image/png" href="{{ asset('layout/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('layout/css/styles.min.css') }}" />

  <!-- Datatables -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <!-- Datatables -->

  @yield('head_script')
  
  <style>

    
    /* Loading */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-screen .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* End Loading */


    .col-md-6:has(.dataTables_filter) {
        display: block;
      }

    .col-md-6:has(#example_length) {
      display: none;
    }
    #example_filter{
      text-align:left;
    }
    #example_filter label .form-control{
      width:15rem;
    }
    th{
      cursor: pointer;
    }
    .dt-row{
      margin-bottom:5rem;
    }

    aside .sidebar-link .hide-menu{
      font-size: .8rem; 
    }

    h2{
      font-size: 1.5rem;
    }

    h4{
      font-size:1.2rem;
    }

    h3{
      font-size:1.2rem;
    }

    td{
      font-size:.8rem;
    }

    tr td{
      padding: .5rem .5rem !important;
      margin: .5rem .5rem !important;
    }

    .border-with-dot {
        position: relative;
        padding-left: 2px; /* Adjust padding to create space for the dot */
        border-left: 1.5px solid #4C6EF5; /* Regular left border */
    }

    .border-with-dot::before {
      content: '';
      position: absolute;
      left: -5px;
      top: 1rem;
      width: 10px;
      height: 10px;
      background-color: #4C6EF5;
      border-radius: 50%;
      border: 2px solid #4C6EF5;
    }

    .border-with-dot-secondary {
        position: relative;
        padding-left: 2px; /* Adjust padding to create space for the dot */
        border-left: 1.5px solid #4DA3FF; /* Regular left border */
    }

    .border-with-dot-secondary::before {
      content: '';
      position: absolute;
      left: -5px;
      top: 1rem;
      width: 10px;
      height: 10px;
      background-color: #4DA3FF;
      border-radius: 50%;
      border: 2px solid #4DA3FF;
    }

    .border-with-dot-third {
        position: relative;
        padding-left: 2px; /* Adjust padding to create space for the dot */
        border-left: 1.5px solid #e8571e; /* Regular left border */
    }

    .border-with-dot-third::before {
      content: '';
      position: absolute;
      left: -5px;
      top: 1rem;
      width: 10px;
      height: 10px;
      background-color: #e8571e;
      border-radius: 50%;
      border: 2px solid #e8571e;
    }

    .link-custom:hover{
      background-color: #EDF2FF;
      color: #4C6EF5 !important;
    }

    
  </style>

</head>

<body>
    @yield('outside_content')
    
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
      data-sidebar-position="fixed" data-header-position="fixed">

      <!-- Sidebar Start -->
      <aside class="left-sidebar" >
        <!-- Sidebar scroll-->
        <div>
          <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="./index.html" class="text-nowrap logo-img pt-2">
              <img src="{{ asset('layout/images/logos/logo.jpeg') }}" width="180" alt="" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
              <i class="ti ti-x fs-8"></i>
            </div>
          </div>
          <div style="min-height: 4rem;"></div>

          @if(auth()->user()->role == 'hashmicro')
              <!-- Sidebar navigation-->
              <nav class="sidebar-nav scroll-sidebar " style="border-radius: 0;" data-simplebar="">
                <ul id="sidebarnav">
                  <li class="nav-small-cap" >
                    <i class="ti ti-dots nav-small-cap-icon fs-4" ></i>
                    <span class="hide-menu">Home</span>
                  </li> 
                  <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('hashmicro.index')}}" aria-expanded="false">
                      <span>
                        <i class="ti ti-layout-dashboard"></i>
                      </span>
                      <span class="hide-menu">List Of Inputs</span>
                    </a>
                  </li>
                </ul>
                <div style="height: 3rem;"></div>
              </nav>
              <!-- End Sidebar navigation -->

          @else
              <!-- Sidebar navigation-->
              <nav class="sidebar-nav scroll-sidebar " style="border-radius: 0;" data-simplebar="">
                <ul id="sidebarnav">
                  <li class="nav-small-cap" >
                    <i class="ti ti-dots nav-small-cap-icon fs-4" ></i>
                    <span class="hide-menu">Home</span>
                  </li> 
                  <li class="sidebar-item">
                    <a class="sidebar-link" href="{{route('dashboard')}}" aria-expanded="false">
                      <span>
                        <i class="ti ti-layout-dashboard"></i>
                      </span>
                      <span class="hide-menu">Dashboard</span>
                    </a>
                  </li>
                  
                  @if(auth()->user()->role == 'ms' || auth()->user()->role == 'lapangan')
                    <li class="nav-small-cap">
                      <i class="ti ti-dots nav-small-cap-icon"></i>
                      <span class="hide-menu">Management</span>
                    </li>
                    <li class="sidebar-item">
                      @php
                          $number_of_requests_project = count(DB::table('sor_request')->where('status', 'PROCESS')->get());   
                      @endphp
                      <a style="padding:.6rem 3rem .6rem .6rem;" class="sidebar-link" href="{{route('sor.sor_request')}}" aria-expanded="false">
                        <span class="col-2">
                          <i class="ti ti-layout-dashboard" style="font-size:1.2rem;"></i>
                        </span>
                        <span class="hide-menu col-8" style=" font-size:.8rem;">SOR Request</span>
                        @if($number_of_requests_project > 0 && auth()->user()->role == 'ms')
                          <div class="col-2 bg-danger text-white text-center" style="font-size:.8rem; border-radius: 9999rem;">{{$number_of_requests_project}}</div>
                        @endif
                      </a>
                    </li>
                  @endif
                  
                  
                  @if(auth()->user()->role == 'engineering')
                    <li class="nav-small-cap">
                      <i class="ti ti-dots nav-small-cap-icon"></i>
                      <span class="hide-menu">Management</span>
                    </li>
                    <li class="sidebar-item">
                      <a class="sidebar-link" href="{{route('project.create')}}" aria-expanded="false">
                        <span>
                          <i class="ti ti-file-plus"></i>
                        </span>
                        <span class="hide-menu">Create Project</span>
                      </a>
                    </li>
                  @endif
                  <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Information</span>
                  </li>

                  @php
                    $projects = DB::table('project')->get();
                  @endphp

                  @foreach ($projects as $project)
                    <li class="sidebar-item" style="margin:1px;">
                      <a href="{{route('project.show', ['project_id'=>$project->project_id])}}" class="{{$project->project_id == request()->segment(3) ? ( gettype(request()->segment(4)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$project->project_id == request()->segment(3) ? ( gettype(request()->segment(4)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                        
                        <div class="row px-2"> 
                          <span class="col-2">
                            <i class="ti ti-box-multiple" style="font-size:1.2rem;"></i>
                          </span>
                          <span class="hide-menu col-8" style=" font-size:.8rem;">{{$project->project_name}}</span>
                        </div>
                      </a>
                      
                      @if($project->project_id == request()->segment(3))

                        {{-- Inland --}}
                        <li class="sidebar-item" style="margin-left:1rem;">
                          @php
                              $number_of_requests_project = count(DB::table('sor_request')->where('project_id', $project->project_id)->where('status', 'PROCESS')->get());   
                          @endphp
                          <a class="has-arrow" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px;" href="#" aria-expanded="false">
                            <div class="row"> 
                              <span class="col-2">
                                <i class="ti ti-shovel" style="font-size:1.2rem;"></i>
                              </span>
                              <span class="hide-menu col-10" style="font-size:.8rem;">Inland</span>
                            </div>
                          </a>
                          @php
                            $segments = DB::table('segment')->where('project_id', $project->project_id)->get();
                          @endphp

                          <ul aria-expanded="false" class="collapse two-level" style=" margin-left:1rem;">
                            @foreach ($segments as $segment)

                              @php
                                $check_section_cable_type = DB::table('section')->where('segment_id', $segment->segment_id)->where('cable_category', 'INLAND')->get();
                              @endphp
                              
                              @if($check_section_cable_type->isNotEmpty())
                                <li class="border-with-dot sidebar-item">
                                  <a href="{{route('segment.show', ['project_id'=>$project->project_id, 'segment_id'=>$segment->segment_id])}}" class="{{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                    <span class="hide-menu " style="font-size:.8rem;">{{$segment->segment_name}}</span>
                                  </a>

                                  @if($segment->segment_id == request()->segment(4))
                                    @php
                                      $sections = DB::table('section')
                                          ->where('segment_id', $segment->segment_id)
                                          ->where('cable_category', 'INLAND')
                                          ->get();
                                    @endphp

                                    @foreach($sections as $section)
                                      <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                        <li class="sidebar-item">
                                          @php
                                            switch ($section->section_route) {
                                              case '1_route':
                                                  $section->section_route = 'MAIN';
                                                  $badge_color = '#f07a47';  // Bright Orange (remains the same)
                                                  break;

                                              case '2_route':
                                                  $section->section_route = 'DIVERSITY';
                                                  $badge_color = '#6aa84f';  // Soft Green
                                                  break;

                                              case '3_route':
                                                  $section->section_route = '3RD ROUTE';
                                                  $badge_color = '#3c78d8';  // Calm Blue
                                                  break;

                                              case '4_route':
                                                  $section->section_route = '4TH ROUTE';
                                                  $badge_color = '#f6b26b';  // Warm Peach
                                                  break;

                                              default:
                                                  $section->section_route = 'MAIN';
                                                  $badge_color = '#f07a47';  // Bright Orange (default)
                                                  break;
                                            }
                                          @endphp
                                          <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="{{$section->section_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$section->section_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                            <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                            <span class="hide-menu" style="font-size:.8rem;"> 
                                              <span style="font-size: .6rem; background: {{$badge_color}};" class="badge fw-semibold py-1 w-85 text-white">{{$section->section_route}}</span>
                                            {{$section->section_name}}</span>
                                          </a>
                                          @if($section->section_id == request()->segment(5))
                                            @php
                                                $sub_sections = DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->get();
                                            @endphp
                                            @foreach($sub_sections as $sub_section)
                                              <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                                <li class="sidebar-item"> 
                                                  <a href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id'=> $sub_section->sub_section_id])}}"  style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$sub_section->sub_section_id == request()->segment(6) ? 'background-color:#4C6EF5; color:#ffff;' : '' }};" aria-expanded="false">
                                                    <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                                      <span class="hide-menu" style="font-size:.8rem;"> 
                                                        <span style="font-size: .6rem;" class="badge bg-light-primary text-primary fw-semibold py-1 w-85 text-uppercase">{{$sub_section->sub_owner}}</span> {{$sub_section->sub_section_name}}
                                                      </span>
                                                  </a>
                                                </li> 
                                              </ul>
                                            @endforeach
                                          @endif
                                        </li>
                                      </ul>
                                    @endforeach
                                  @endif
                                </li>
                              @endif
                            @endforeach
                          </ul>
                        </li>
                        {{-- Inland --}}

                        {{-- Submarine --}}
                        <li class="sidebar-item" style="margin-left:1rem;">
                          <a class="has-arrow" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px;" href="#" aria-expanded="false">
                            <div class="row"> 
                              <span class="col-2">
                                <i class="ti ti-submarine" style="font-size:1.2rem;"></i>
                              </span>
                              <span class="hide-menu col-10" style="font-size:.8rem;">Submarine</span>
                            </div>
                          </a>
                          @php
                            $segments = DB::table('segment')->where('project_id', $project->project_id)->get();
                          @endphp

                          <ul aria-expanded="false" class="collapse two-level" style=" margin-left:1rem;">
                            @foreach ($segments as $segment)

                              @php
                                $check_section_cable_type = DB::table('section')->where('segment_id', $segment->segment_id)->where('cable_category', 'SUBMARINE')->get();
                              @endphp
                              
                              @if($check_section_cable_type->isNotEmpty())
                                <li class="border-with-dot sidebar-item">
                                  <a href="{{route('segment.show', ['project_id'=>$project->project_id, 'segment_id'=>$segment->segment_id])}}" class="{{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                    <span class="hide-menu " style="font-size:.8rem;">{{$segment->segment_name}}</span>
                                  </a>

                                  @if($segment->segment_id == request()->segment(4))
                                    @php
                                      $sections = DB::table('section')
                                          ->where('segment_id', $segment->segment_id)
                                          ->where('cable_category', 'SUBMARINE')
                                          ->get();
                                    @endphp

                                    @foreach($sections as $section)
                                      <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                        <li class="sidebar-item">
                                          @php
                                            switch ($section->section_route) {
                                              case '1_route':
                                                  $section->section_route = 'MAIN';
                                                  $badge_color = '#f07a47';  // Bright Orange (remains the same)
                                                  break;

                                              case '2_route':
                                                  $section->section_route = 'DIVERSITY';
                                                  $badge_color = '#6aa84f';  // Soft Green
                                                  break;

                                              case '3_route':
                                                  $section->section_route = '3RD ROUTE';
                                                  $badge_color = '#3c78d8';  // Calm Blue
                                                  break;

                                              case '4_route':
                                                  $section->section_route = '4TH ROUTE';
                                                  $badge_color = '#f6b26b';  // Warm Peach
                                                  break;

                                              default:
                                                  $section->section_route = 'MAIN';
                                                  $badge_color = '#f07a47';  // Bright Orange (default)
                                                  break;
                                            }
                                          @endphp
                                          <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="{{$section->section_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$section->section_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                            <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                            <span class="hide-menu" style="font-size:.8rem;"> 
                                              <span style="font-size: .6rem; background: {{$badge_color}};" class="badge fw-semibold py-1 w-85 text-white">{{$section->section_route}}</span>
                                            {{$section->section_name}}</span>
                                          </a>
                                          @if($section->section_id == request()->segment(5))
                                            @php
                                                $sub_sections = DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->get();
                                            @endphp
                                            @foreach($sub_sections as $sub_section)
                                              <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                                <li class="sidebar-item"> 
                                                  <a href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id'=> $sub_section->sub_section_id])}}"  style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$sub_section->sub_section_id == request()->segment(6) ? 'background-color:#4C6EF5; color:#ffff;' : '' }};" aria-expanded="false">
                                                    <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                                      <span class="hide-menu" style="font-size:.8rem;"> 
                                                        <span style="font-size: .6rem;" class="badge bg-light-primary text-primary fw-semibold py-1 w-85 text-uppercase">{{$sub_section->sub_owner}}</span> {{$sub_section->sub_section_name}}
                                                      </span>
                                                  </a>
                                                </li> 
                                              </ul>
                                            @endforeach   
                                          @endif
                                        </li>
                                      </ul>
                                    @endforeach
                                  @endif
                                </li>
                              @endif
                            @endforeach
                          </ul>
                        </li>
                        {{-- Submarine --}}

                        {{-- Lastmile --}}
                        <li class="sidebar-item" style="margin-left:1rem;">
                          <a class="has-arrow" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px;" href="#" aria-expanded="false">
                            <div class="row"> 
                              <span class="col-2">
                                <i class="ti ti-truck" style="font-size:1.2rem;"></i>
                              </span>
                              <span class="hide-menu col-10" style="font-size:.8rem;">Lastmile</span>
                            </div>
                          </a>
                          @php
                            $segments = DB::table('segment')->where('project_id', $project->project_id)->get();
                          @endphp

                          <ul aria-expanded="false" class="collapse two-level" style=" margin-left:1rem;">
                            @foreach ($segments as $segment)

                              @php
                                $check_section_cable_type = DB::table('section')->where('segment_id', $segment->segment_id)->where('cable_category', 'LASTMILE')->get();
                              @endphp
                              
                              @if($check_section_cable_type->isNotEmpty())
                                <li class="border-with-dot sidebar-item">
                                  <a href="{{route('segment.show', ['project_id'=>$project->project_id, 'segment_id'=>$segment->segment_id])}}" class="{{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                    <span class="hide-menu " style="font-size:.8rem;">{{$segment->segment_name}}</span>
                                  </a>

                                  @if($segment->segment_id == request()->segment(4))
                                    @php
                                      $sections = DB::table('section')
                                          ->where('segment_id', $segment->segment_id)
                                          ->where('cable_category', 'LASTMILE')
                                          ->get();
                                    @endphp

                                    @foreach($sections as $section)
                                      <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                        <li class="sidebar-item">
                                          @php
                                            switch ($section->section_route) {
                                              case '1_route':
                                                  $section->section_route = 'MAIN';
                                                  $badge_color = '#f07a47';  // Bright Orange (remains the same)
                                                  break;

                                              case '2_route':
                                                  $section->section_route = 'DIVERSITY';
                                                  $badge_color = '#6aa84f';  // Soft Green
                                                  break;

                                              case '3_route':
                                                  $section->section_route = '3RD ROUTE';
                                                  $badge_color = '#3c78d8';  // Calm Blue
                                                  break;

                                              case '4_route':
                                                  $section->section_route = '4TH ROUTE';
                                                  $badge_color = '#f6b26b';  // Warm Peach
                                                  break;

                                              default:
                                                  $section->section_route = 'MAIN';
                                                  $badge_color = '#f07a47';  // Bright Orange (default)
                                                  break;
                                            }
                                          @endphp
                                          <a href="{{route('section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="{{$section->section_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$section->section_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                            <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                            <span class="hide-menu" style="font-size:.8rem;"> 
                                              <span style="font-size: .6rem; background: {{$badge_color}};" class="badge fw-semibold py-1 w-85 text-white">{{$section->section_route}}</span>
                                            {{$section->section_name}}</span>
                                          </a>
                                          @if($section->section_id == request()->segment(5))
                                            @php
                                                $sub_sections = DB::table('sub_section')
                                                ->where('section_id', $section->section_id)
                                                ->get();
                                            @endphp
                                            @foreach($sub_sections as $sub_section)
                                              <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                                <li class="sidebar-item"> 
                                                  <a href="{{route('sub_section.show', ['project_id'=>$project->project_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'sub_section_id'=> $sub_section->sub_section_id])}}"  style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$sub_section->sub_section_id == request()->segment(6) ? 'background-color:#4C6EF5; color:#ffff;' : '' }};" aria-expanded="false">
                                                    <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                                      <span class="hide-menu" style="font-size:.8rem;"> 
                                                        <span style="font-size: .6rem;" class="badge bg-light-primary text-primary fw-semibold py-1 w-85 text-uppercase">{{$sub_section->sub_owner}}</span> {{$sub_section->sub_section_name}}
                                                      </span>
                                                  </a>
                                                </li> 
                                              </ul>
                                            @endforeach
                                          @endif
                                        </li>
                                      </ul>
                                    @endforeach
                                  @endif
                                </li>
                              @endif
                            @endforeach
                          </ul>
                        </li>
                        {{-- Lastmile --}}

                      @endif
                    </li>
                  @endforeach
                </ul>
                <div style="height: 3rem;"></div>
              </nav>
              <!-- End Sidebar navigation -->

          @endif


        </div>
        <!-- End Sidebar scroll-->
      </aside>
      <!--  Sidebar End -->
      
      <!--  Main wrapper -->
      <div class="body-wrapper" style="overflow-x:scroll;">
        <!--  Header Start -->
          <header class="app-header bg-info text-white">
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
                              <p style="text-transform: uppercase; font-size:1rem; font-weight: bold; background-color:#e8571e; padding: 5px 10px; color:white;">Login as
                                  {{ auth()->user()->role }}</p>
                          </li>
                      </ul>

                      
                      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                          <li class="nav-item dropdown">
                            <li class="nav-item dropdown">
                                <a style="font-size:1rem;" class="nav-link text-white" href="javascript:void(0)" id="drop2"
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
                          </li>
                      </ul>
                  </div>
              </nav>
          </header>
          
        <!--  Header End -->
        {{-- <div class="container-fluid"> --}}
        <div style="width:90%; margin:auto;">
          <div class="row">
            
          </div>
          
        </div>
        <div style="width:90%; margin:auto;" >
          <div style="min-height: 7rem;"></div>
            @if(url()->current() !== 'https://cms.triasmitra.com/public/hashmicro/index')
              <div class="col-2">
                  <a href="{{ url()->previous() }}" style="width:100%; background:#e8571e; color:white;" class="btn">
                      <i class="ti ti-caret-left"></i> Back
                  </a>
              </div>
            @endif
          <div style="min-height: 2rem;"></div>
            @yield('breadcrumb')
            
            @if ($message = Session::get('success'))
              <div class="alert alert-success">
                <div style="cursor:pointer; font-size:1rem;" data-dismiss="alert" class="delete_button inline-block">
                    <i class="ti ti-square-x"></i>
                    <strong style="font-size:1rem;" >{{ $message }}</strong>
                </div>
              </div>
            @endif

            @if ($message = Session::get('error'))
              <div class="alert alert-danger">
                <div style="cursor:pointer; font-size:1rem;" data-dismiss="alert" class="delete_button inline-block">
                    <i class="ti ti-square-x"></i>
                    <strong style="font-size:1rem;" >{{ $message }}</strong>
                </div>
              </div>
            @endif

            @yield('content')
        </div>
        <div style="min-height: 10rem;"></div>
      </div>

      
    </div>

    <script>
        new DataTable('#example');

        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

  <!-- Bootstrap JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <!-- End Bootstrap JavaScript -->

  <script src="{{ asset('layout/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('layout/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('layout/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('layout/js/app.min.js') }}"></script>
  <script src="{{ asset('layout/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
  <script src="{{ asset('layout/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="{{ asset('layout/js/dashboard.js') }}"></script>
  @yield('script')
  
</body>

</html>