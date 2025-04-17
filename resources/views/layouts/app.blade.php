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

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    .border-with-dot{
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
      color: #4DA3FF !important;
    }

    .select2-container {
      width: 100% !important;
    }

    .select2-selection--single {
      width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
      height: 38px; /* Match Bootstrap .form-select height */
      padding: 0.375rem 0.75rem;
      font-size: 1rem;
      border: 1px solid #ced4da;
      border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 2.1;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 38px;
      right: 10px;
    }

    
  </style>

</head>

<body>
    @yield('outside_content')
    
    @php
      $projects = DB::table('project')->get();
    @endphp

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

          <!-- Sidebar navigation-->
          <nav class="sidebar-nav scroll-sidebar " style="border-radius: 0;" data-simplebar="">
            <ul id="sidebarnav">
              <!-- Button trigger modal -->
            {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchModal">
              SEARCH 
            </button> --}}
            <li class="sidebar-item ">
              <a style="cursor: pointer; color:white;" class="sidebar-link bg-info" data-bs-toggle="modal" data-bs-target="#searchModal" aria-expanded="false">
                <span>
                  <i class="ti ti-search"></i>
                </span>
                <span class="hide-menu">SEARCH</span>
              </a>
            </li>
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
                  <span class="hide-menu">Information</span>
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
                  <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                  <span class="hide-menu">Information</span>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="{{route('customer.index')}}" aria-expanded="false">
                    <span>
                      <i class="ti ti-file-plus"></i>
                    </span>
                    <span class="hide-menu">Data Customer</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="{{route('project.index')}}" aria-expanded="false">
                    <span>
                      <i class="ti ti-file-plus"></i>
                    </span>
                    <span class="hide-menu">Data Cable Project</span>
                  </a>
                </li>
              @endif

              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Cable System</span>
              </li>
              
              
              @foreach ($projects as $project)
                <li class="sidebar-item" style="margin:1px;">
                  <a href="{{route('project.show', ['project_id'=>$project->project_id, 'route_id'=>'-'])}}" class="{{$project->project_id == request()->segment(3) ? ( request()->segment(4) == 'link-custom' ? '' : '' ) : '' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$project->project_id == request()->segment(3) ? ( request()->segment(4) == '-' ? 'background-color:#4C6EF5; color:#ffff;' : '' )  : '' }};" aria-expanded="false">
                    
                    <div class="row px-2"> 
                      <span class="col-2">
                        <i class="ti ti-box-multiple" style="font-size:1.2rem;"></i>
                      </span>
                      <span class="hide-menu col-8" style=" font-size:.8rem;">{{$project->project_name}}</span>
                    </div>
                  </a>

                  @if($project->project_id == request()->segment(3))

                    @for($y = 1; $y <= 3; $y++)
                      @php
                        switch ($y) {
                          case 1:
                            $route_icon = 'submarine';
                            $route_name = 'SUBMARINE';
                            break;
                          case 2:
                            $route_icon = 'shovel';
                            $route_name = 'INLAND';
                            break;
                          case 3:
                            $route_icon = 'truck';
                            $route_name = 'LASTMILE';
                            break;
                          
                          default:
                            echo '-';
                            break;
                        }
                      @endphp
                      <li class="sidebar-item" style="margin-left:1rem;">
                        <a class="has-arrow" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px;" href="#" aria-expanded="false">
                          <div class="row"> 
                            <span class="col-2">
                              <i class="ti ti-{{$route_icon}}" style="font-size:1.2rem;"></i>
                            </span>
                            <span class="hide-menu col-10" style="font-size:.8rem;">
                              {{$route_name}}
                            </span>
                          </div>
                        </a>
                        @php
                          $segments = DB::table('segment')->where('project_id', $project->project_id)->where('route_id', $y)->get();
                        @endphp
                        <ul aria-expanded="false" class="collapse two-level" style=" margin-left:1rem;">
                          @foreach ($segments as $segment)
                            <li class="border-with-dot sidebar-item">
                              <a href="{{route('segment.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=>$segment->segment_id])}}" class="{{$segment->segment_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$segment->segment_id == request()->segment(5) ? ( gettype(request()->segment(6)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                <span class="hide-menu " style="font-size:.8rem;">{{$segment->segment_name}}</span>
                              </a>

                              @if($segment->segment_id == request()->segment(5))
                                @php
                                  $sections = DB::table('section')
                                      ->where('project_id', $project->project_id)
                                      ->where('route_id', $segment->route_id)
                                      ->where('segment_id', $segment->segment_id)
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
                                      <a href="{{route('section.show', ['project_id'=>$project->project_id, 'route_id'=> $segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id])}}" class="{{$section->section_id == request()->segment(6) ? ( gettype(request()->segment(7)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$section->section_id == request()->segment(6) ? ( gettype(request()->segment(7)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
                                        <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                        <span class="hide-menu" style="font-size:.8rem;"> 
                                          <span style="font-size: .6rem; background: {{$badge_color}};" class="badge fw-semibold py-1 w-85 text-white">{{$section->section_route}}</span>
                                        {{$section->section_name}}</span>
                                      </a>
                                      @if($section->section_id == request()->segment(6))
                                        @php
                                            $sub_sections = DB::table('sub_section')
                                            ->where('project_id', $project->project_id)
                                            ->where('segment_id', $segment->segment_id)
                                            ->where('section_id', $section->section_id)
                                            ->orderByRaw('CAST(customer_id AS UNSIGNED) ASC')
                                            ->get();
                                        @endphp
                                        @foreach($sub_sections as $sub_section)
                                          <ul aria-expanded="false" class="border-with-dot-secondary collapse two-level in" style=" margin-left:1rem;">
                                            <li class="sidebar-item"> 
                                              @if($sub_section->type_id == null || $sub_section->type_id == '')
                                                <a href="{{route('sub_section.show', ['project_id'=>$project->project_id,'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'customer_id'=> $sub_section->customer_id, 'type_id'=> '-', 'sub_section_id'=> $sub_section->sub_section_id])}}"  style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{($sub_section->customer_id == request()->segment(7) && '-' == request()->segment(8) && $sub_section->sub_section_id == request()->segment(9) ) ? 'background-color:#4C6EF5; color:#ffff;' : '' }};" aria-expanded="false">
                                                  <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                                    <span class="hide-menu" style="font-size:.8rem;"> 
                                                      <span style="font-size: .6rem;" class="badge bg-light-primary text-primary fw-semibold py-1 w-85 text-uppercase">{{ DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name}}</span> {{$sub_section->sub_section_name}}
                                                    </span>
                                                </a>
                                              @else
                                                <a href="{{route('sub_section.show', ['project_id'=>$project->project_id,'route_id'=>$segment->route_id, 'segment_id'=> $segment->segment_id, 'section_id' => $section->section_id, 'customer_id'=> $sub_section->customer_id, 'type_id'=> $sub_section->type_id, 'sub_section_id'=> $sub_section->sub_section_id])}}"  style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{($sub_section->customer_id == request()->segment(7) && $sub_section->type_id == request()->segment(8) && $sub_section->sub_section_id == request()->segment(9) ) ? 'background-color:#4C6EF5; color:#ffff;' : '' }};" aria-expanded="false">
                                                  <div class="round-16 d-flex align-items-center justify-content-center"></div>
                                                    <span class="hide-menu" style="font-size:.8rem;"> 
                                                      <span style="font-size: .6rem;" class="badge bg-light-primary text-primary fw-semibold py-1 w-85 text-uppercase">{{ DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name}}</span> {{$sub_section->sub_section_name}}
                                                    </span>
                                                </a>
                                              @endif
                                            </li> 
                                          </ul>
                                        @endforeach
                                      @endif
                                    </li>
                                  </ul>
                                @endforeach
                              @endif
                            </li>
                          @endforeach
                        </ul>
                      </li>
                    @endfor
                  @endif
                  
                </li>
              @endforeach
            </ul>
            <div style="height: 3rem;"></div>
          </nav>
          <!-- End Sidebar navigation -->


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

            <!-- Modal -->
            <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="searchModalLabel">SEARCH</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('search') }}" method="POST" class="position-relative" enctype=multipart/form-data>
                    @csrf
                    <div class="modal-body">
                      <div class="my-3">
                        <label for="search_project" class="form-label">Cable Project</label>
                        <select name="project" id="search_project" class="form-select">
                          <option value="">Select one...</option>
                          @foreach ($projects as $project)
                            <option value="{{ $project->project_id }}">{{ $project->project_name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="my-3">
                        <label for="route" class="form-label">Cable Category</label>
                        <select name="route" id="search_route" class="form-select">
                          <option value="2">INLAND</option>
                          <option value="1">SUBMARINE</option>
                          <option value="3">LASTMILE</option>
                        </select>
                      </div>
                      <div class="my-3">
                        <label for="route" class="form-label">Segment</label>
                        <select name="segment" id="search_segment" class="form-select" disabled>
                          <option value="">Select one...</option>
                        </select>
                      </div>
                      <div class="my-3">
                        <label for="search_section" class="form-label">Sections</label>
                        <br>
                        <select name="section" id="search_section" class="form-select" disabled>
                          <option value="">Select one...</option>
                        </select>
                      </div>
                      <div class="my-3">
                        <label for="search_sub_section" class="form-label">Customer</label>
                        <select name="sub_section" id="search_sub_section" class="form-select" disabled>
                          <option value="">Select one...</option>
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary px-5">SEARCH</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            @yield('content')
        </div>
        <div style="min-height: 10rem;"></div>
      </div>
    </div>
  
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
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
      new DataTable('#example');

      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
  </script>

  <script>
    $(document).ready(function() {
      $('#search_project').select2({
        dropdownParent: $('#searchModal'),
        placeholder: 'Select a project',
        allowClear: true
      });
      $('#search_route').select2({
        dropdownParent: $('#searchModal'),
        placeholder: 'Select a route',
        allowClear: true
      });
      $('#search_segment').select2({
        dropdownParent: $('#searchModal'),
        placeholder: 'Select a segment',
        allowClear: true
      });
      $('#search_section').select2({
        dropdownParent: $('#searchModal'),
        placeholder: 'Select a section',
        allowClear: true
      });
      $('#search_sub_section').select2({
        dropdownParent: $('#searchModal'),
        placeholder: 'Select a sub section',
        allowClear: true
      });
    });
  </script>

  <script>
    $('#search_project, select[name="route"]').on('change', function () {
      let projectId = $('#search_project').val();
      let routeId = $('select[name="route"]').val();
      let segmentSelect = $('#search_segment');

      console.log('Selected projectId:', projectId);
      console.log('Selected routeId:', routeId);

      // Reset and disable segment select before AJAX
      segmentSelect.empty().append(`<option value="">Select one...</option>`);
      segmentSelect.prop('disabled', true);

      if (projectId && routeId) {
        $.ajax({
          url: `/cms_dev/public/get-segments/${projectId}/${routeId}`,
          type: 'GET',
          success: function (segments) {
            if (segments.length > 0) {
              segments.forEach(function (segment) {
                segmentSelect.append(`<option value="${segment.segment_id}">${segment.segment_name}</option>`);
              });
              segmentSelect.prop('disabled', false); // Enable if data found
            } else {
              alert('Segment for this project is not exist!');
              segmentSelect.append(`<option value="">No segments found</option>`);
            }
          },
          error: function () {
            console.error('Failed to fetch segments.');
            segmentSelect.append(`<option value="">Error loading segments</option>`);
          }
        });
      }
    });

    $('select[name="project"], select[name="route"], select[name="segment"]').on('change', function () {
      let projectId = $('#search_project').val();
      let routeId = $('select[name="route"]').val();
      let segmentId = $('select[name="segment"]').val();
      let sectionSelect = $('#search_section');

      // Reset section select
      sectionSelect.empty().append(`<option value="">Select one...</option>`);
      sectionSelect.prop('disabled', true);

      if (projectId && routeId && segmentId) {
        $.ajax({
          url: `/cms_dev/public/get-sections/${projectId}/${routeId}/${segmentId}`,
          type: 'GET',
          success: function (sections) {
            if (sections.length > 0) {
              sections.forEach(function (section) {
                sectionSelect.append(`<option value="${section.section_id}">${section.section_name}</option>`);
              });
              sectionSelect.prop('disabled', false);
            } else {
              alert('Sections for this Segment is not exist!');
              sectionSelect.append(`<option value="">No sections found</option>`);
            }
          },
          error: function () {
            console.error('Failed to fetch sections.');
            sectionSelect.append(`<option value="">Error loading sections</option>`);
          }
        });
      }
    });

    $('select[name="project"], select[name="route"], select[name="segment"], select[name="section"]').on('change', function () {
      let projectId = $('#search_project').val();
      let routeId = $('select[name="route"]').val();
      let segmentId = $('select[name="segment"]').val();
      let sectionId = $('select[name="section"]').val();
      let subSectionSelect = $('#search_sub_section');

      
      console.log('Selected projectId:', projectId);
      console.log('Selected routeId:', routeId);
      console.log('Selected segmentId:', segmentId);
      console.log('Selected sectionId:', sectionId);

      // Reset section select
      subSectionSelect.empty().append(`<option value="">Select one...</option>`);
      subSectionSelect.prop('disabled', true);

      if (projectId && routeId && segmentId && sectionId) {
        $.ajax({
          url: `/cms_dev/public/get-sub-sections/${projectId}/${routeId}/${segmentId}/${sectionId}`,
          type: 'GET',
          success: function (sub_sections) {
            if (sub_sections.length > 0) {
              sub_sections.forEach(function (sub_section) {
                subSectionSelect.append(`<option value="${sub_section.sub_section_id}"> [ <span style="font-weight:bold;" >${sub_section.customer_name}</span> ] ${sub_section.sub_section_name}</option>`);
              });
              subSectionSelect.prop('disabled', false);
            } else {
              alert('Sub Sections for this Sections is not exist!');
              subSectionSelect.append(`<option value="">No sub sections found</option>`);
            }
          },
          error: function () {
            console.error('Failed to fetch sub sections.');
            subSectionSelect.append(`<option value="">Error loading subSections</option>`);
          }
        });
      }

    });
    
    
  </script>


  @yield('script')
  
  
</body>

</html>