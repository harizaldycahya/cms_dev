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
        $segment = DB::table('segment')->where('segment_id', $segment_id)->get()->first();
        $project = DB::table('project')->where('project_id', $project_id)->get()->first();
    @endphp
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold" style="font-size: 2rem;">Create section : {{ $segment->segment_name }}</h3>
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
    <hr class="my-5">
    <div>
        <div class="row px-3">
            <div class="col d-flex justify-content-start">
                <h5 style="font-weight: 800;">Form input section with sub section</h5>
            </div>
            <div class="col d-flex justify-content-end">
                <div style="cursor:pointer;" onclick="add_section_card()"
                    style="padding:.5rem 3rem; margin-right: 1rem;" class="btn btn-sm btn-success">
                    <div class="row" style="align-items: center;">
                        <div class="col-2">
                            <i class="ti ti-square-plus" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="col-10">
                            Add Section
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="min-height:2rem;"></div>
        <form action="{{ route('section.store') }}" method="POST">
            {{ csrf_field() }}
            <input hidden type="text" name="input_type" value="with_sub_section">
            <input hidden type="text" name="project_id" value="{{$project_id}}">
            <input hidden type="text" name="segment_id" value="{{$segment_id}}">
            <div id="section_card_container">

                {{-- Start Template --}}
                <div id="section_card" style="display: none;" class="card p-5 shadow-lg">
                    <div class="row px-3 mb-5 d-flex">
                        <div class="col-6 text-right">
                            <div onclick="add_sub_section('section_card')"
                                class="add_sub_section btn btn-sm btn-dark"
                                style="display:inline-block; margin: 0rem 2px;">
                                <div class="row" style="align-items: center;">
                                    <div class="col-2">
                                        <i class="ti ti-square-plus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="col-10">
                                        Add sub section
                                    </div>
                                </div>
                            </div>
                            <div onclick="add_sub_section_with_ropa('section_card')" class=" add_sub_section_with_ropa btn btn-sm btn-dark"
                                style="display:inline-block; margin: 0rem 2px;">
                                <div class="row" style="align-items: center;">
                                    <div class="col-2">
                                        <i class="ti ti-square-plus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="col-10">
                                        Add sub section with ropa
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <div onclick="confirm_delete_section_card('section_card')"
                                class="delete_section_card btn btn-sm btn-danger float-end"
                                style="margin: 0rem 2px;">
                                <div class="row" style="align-items: center;">
                                    <div class="col-2">
                                        <i class="ti ti-square-minus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="col-10">
                                        Delete Section
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="overflow-x:scroll;">
                        <table class="table table-bordered" style="min-width:100rem;">
                            <thead>
                                <input disabled type="hidden" name="card_id[]" value="" class="head head_card_id">
                                <tr>
                                    <tr>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="3">
                                            Cable Category : 
                                            <select disabled class="head form-select" name="head_sub_cable_category[]">
                                                <option value="INLAND">INLAND</option>
                                                <option value="SUBMARINE">SUBMARINE</option>
                                                <option value="LASTMILE">LASTMILE</option>
                                            </select>
                                        </th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="3">
                                            Section Route : 
                                            <select disabled name="head_sub_section_route[]" class="head form-select">
                                                <option value="1_route">Main</option>
                                                <option value="2_route">Diversity</option>
                                                <option value="3_route">3rd Route</option>
                                                <option value="4_route">4th Route</option>
                                            </select>
                                        </th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="3">
                                            Core Capacity : 
                                            <input disabled type="number" name="head_sub_section_core_capacity[]" class="head form-control">
                                        </th>
                                    </tr>
                                </tr>
                                <tr>
                                    <tr>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="2">
                                            Section Name : 
                                            <input disabled type="text" name="head_sub_section_name[]" class="head form-control mb-2">
                                        </th>
                                        <th style="font-size:.8rem;" colspan="2">Section Point</th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="2">
                                            Cable Type :
                                            <input disabled type="text" name="head_sub_cable_type[]" class="head form-control mb-2">
                                        </th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="2">
                                            First RFS :
                                            <input disabled type="text" name="head_sub_first_rfs[]" class="head form-control mb-2">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="font-size:.8rem;">
                                            Near End :
                                            <input disabled type="text" name="head_sub_near_end[]" class="head form-control mb-2">
                                        </th>
                                        <th style="font-size:.8rem;">
                                            Far End :
                                            <input disabled type="text" name="head_sub_far_end[]" class="head form-control mb-2">
                                        </th>
                                    </tr>
                                </tr>
                                <tr>
                                    <tr>
                                        <th style="font-size:.8rem;" rowspan="2">Action</th>
                                        <th style="font-size:.8rem;" rowspan="2">Sub Section Name</th>
                                        <th style="font-size:.8rem;" colspan="3">Sub Section Point</th>
                                        <th style="font-size:.8rem;" rowspan="2">Owner</th>
                                        <th style="font-size:.8rem;" colspan="2">Site Owner</th>
                                        <th style="font-size:.8rem;" rowspan="2">Length</th>
                                        <th style="font-size:.8rem;" rowspan="2">Min Total Loss</th>
                                        <th style="font-size:.8rem;" rowspan="2">Max Total Loss</th>
                                    </tr>
                                    <tr>
                                        <th style="font-size:.8rem;">Near End</th>
                                        <th style="font-size:.8rem;">Ropa</th>
                                        <th style="font-size:.8rem;">Far End</th>
                                        <th style="font-size:.8rem;">Near End</th>
                                        <th style="font-size:.8rem;">Far End</th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider" id="sub_section_container">
                                <tr class="empty">
                                    <td class="bg-light text-center" colspan="11">
                                        Empty
                                    </td>
                                </tr>
                                <tr style="display: none;" class="sub_section_field">
                                    <td>
                                        <div onclick="confirm_delete_sub_section('sub_section_field')"
                                            class="delete_sub_section text-danger text-center"><i
                                                class="ti ti-square-minus" style="font-size: 1.5rem;"></i></div>
                                    </td>
                                    <td style="min-width:20rem;"><input disabled type="text" name="sub_section_name[template][]" class="sub_section_name form-control mb-2"></td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_near_end[template][]" class="sub_near_end form-control mb-2"></td>
                                    <td style="min-width:10rem;"> </td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_far_end[template][]" class="sub_far_end form-control mb-2"></td>
                                    <td style="min-width:10rem;">
                                        <select disabled name="sub_owner[template][]" class="sub_owner form-select">
                                            <option value="TRIAS">TRIASMITRA</option>
                                            <option value="BIZNET">BIZNET</option>
                                            <option value="FIBERSTAR">FIBERSTAR</option>
                                            <option value="H3I">H3I</option>
                                            <option value="HSP">HSP</option>
                                            <option value="IFORTE">IFORTE</option>
                                            <option value="INDOSAT">INDOSAT</option>
                                            <option value="IPLUS">IPLUS</option>
                                            <option value="JKLD">JKLD</option>
                                            <option value="LINKNET">LINKNET</option>
                                            <option value="LINTASARTA">LINTASARTA</option>
                                            <option value="MORATEL">MORATEL</option>
                                            <option value="PDM">PDM</option>
                                            <option value="REMALA">REMALA</option>
                                            <option value="SDI">SDI</option>
                                            <option value="SOLNET">SOLNET</option>
                                            <option value="SSU">SSU</option>
                                            <option value="TELKOM">TELKOM</option>
                                            <option value="TIS">TIS</option>
                                            <option value="TM">TM</option>
                                            <option value="XL">XL</option>
                                            <option value="AGORA">AGORA</option>
                                        </select>
                                    </td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_site_owner_near_end[template][]" class="sub_site_owner_near_end form-control mb-2"></td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_site_owner_far_end[template][]" class="sub_site_owner_far_end form-control mb-2"></td>
                                    <td style="min-width:10rem;"><input disabled type="number" step="0.0001" min="0" name="sub_initial_length[template][]" class="sub_initial_length form-control mb-2" value=""></td>
                                    <td style="min-width:10rem;"><input disabled type="number" step="0.0001" min="0" name="sub_initial_min_total_loss[template][]" class="sub_initial_min_total_loss form-control mb-2" value=""></td>
                                    <td style="min-width:10rem;"><input disabled type="number" step="0.0001" min="0" name="sub_initial_max_total_loss[template][]" class="sub_initial_max_total_loss form-control mb-2" value=""></td>
                                </tr>
                                <tr style="display: none;" class="ropa_sub_section_field">
                                    <td rowspan="2" style="align-content : center;">
                                        <div onclick="confirm_delete_sub_section_with_ropa('ropa_sub_section_field')"
                                            class="delete_sub_section_with_ropa text-danger text-center"><i
                                                class="ti ti-square-minus" style=" font-size: 1.5rem;"></i></div>
                                    </td>
                                    <td rowspan="2" style="min-width:20rem; align-content : center;">
                                        <input disabled type="text" disabled name="ropa_sub_section_name[template][]" class="ropa_sub_section_name form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_near_end[template][]" class="ropa_sub_near_end form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_ropa[template][]" class="ropa_sub_ropa form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_far_end[template][]" class="ropa_sub_far_end form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;">
                                        <select disabled name="ropa_sub_owner[template][]" class="ropa_sub_owner form-select">
                                            <option value="TRIAS">TRIASMITRA</option>
                                            <option value="BIZNET">BIZNET</option>
                                            <option value="FIBERSTAR">FIBERSTAR</option>
                                            <option value="H3I">H3I</option>
                                            <option value="HSP">HSP</option>
                                            <option value="IFORTE">IFORTE</option>
                                            <option value="INDOSAT">INDOSAT</option>
                                            <option value="IPLUS">IPLUS</option>
                                            <option value="JKLD">JKLD</option>
                                            <option value="LINKNET">LINKNET</option>
                                            <option value="LINTASARTA">LINTASARTA</option>
                                            <option value="MORATEL">MORATEL</option>
                                            <option value="PDM">PDM</option>
                                            <option value="REMALA">REMALA</option>
                                            <option value="SDI">SDI</option>
                                            <option value="SOLNET">SOLNET</option>
                                            <option value="SSU">SSU</option>
                                            <option value="TELKOM">TELKOM</option>
                                            <option value="TIS">TIS</option>
                                            <option value="TM">TM</option>
                                            <option value="XL">XL</option>
                                            <option value="AGORA">AGORA</option>
                                        </select>
                                    </td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_site_owner_near_end[template][]" class="ropa_sub_site_owner_near_end form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_site_owner_far_end[template][]" class="ropa_sub_site_owner_far_end form-control mb-2"></td>
                                    <td style="min-width:10rem;">
                                        <label>Near end to Ropa</label>
                                        <input disabled name="ropa_sub_near_end_initial_length[template][]" type="number" step="0.0001" min="0" class="ropa_sub_near_end_initial_length form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Near end to Ropa</label>
                                        <input disabled name="ropa_sub_near_end_initial_min_total_loss[template][]" type="number" step="0.0001" min="0" class="ropa_sub_near_end_initial_min_total_loss form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Near end to Ropa</label>
                                        <input disabled type="number" step="0.0001" min="0" class="ropa_sub_near_end_initial_max_total_loss form-control mb-2" name="ropa_sub_near_end_initial_max_total_loss[template][]" value="">
                                    </td>
                                </tr>
                                <tr style="display: none;" class="ropa_sub_section_field_2" >
                                    <td style="min-width:10rem;">
                                        <label>Far end to Ropa</label>
                                        <input disabled name="ropa_sub_far_end_initial_length[template][]" type="number" step="0.0001" min="0" class="ropa_sub_far_end_initial_length form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Far end to Ropa</label>
                                        <input disabled name="ropa_sub_far_end_initial_min_total_loss[template][]" type="number" step="0.0001" min="0" class="ropa_sub_far_end_initial_min_total_loss form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Far end to Ropa</label>
                                        <input disabled name="ropa_sub_far_end_initial_max_total_loss[template][]" type="number" step="0.0001" min="0" class="ropa_sub_far_end_initial_max_total_loss form-control mb-2" value="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="min-height: 1rem;"></div>
                </div>
                {{-- End Template --}}


                {{-- Start first_card --}}
                <div id="first_card" class="card p-5 shadow-lg">
                    <div class="row px-3 mb-5 d-flex">
                        <div class="col-6 text-right">
                            <div onclick="add_sub_section('first_card')" class="add_sub_section btn btn-sm btn-dark" style="display:inline-block; margin: 0rem 2px;">
                                <div class="row" style="align-items: center;">
                                    <div class="col-2">
                                        <i class="ti ti-square-plus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="col-10">
                                        Add sub section
                                    </div>
                                </div>
                            </div>
                            <div onclick="add_sub_section_with_ropa('first_card')" class="add_sub_section_with_ropa btn btn-sm btn-dark" style="display:inline-block; margin: 0rem 2px;">
                                <div class="row" style="align-items: center;">
                                    <div class="col-2">
                                        <i class="ti ti-square-plus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="col-10">
                                        Add sub section with ropa
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <div onclick="confirm_delete_section_card('first_card')"
                                class="delete_section_card btn btn-sm btn-danger float-end"
                                style="margin: 0rem 2px;">
                                <div class="row" style="align-items: center;">
                                    <div class="col-2">
                                        <i class="ti ti-square-minus" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="col-10">
                                        Delete Section
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="overflow-x:scroll;">
                        <table class="table table-bordered" style="min-width:100rem;">
                            <thead>
                                <input type="hidden" name="card_id[]" value="first_card">
                                <tr>
                                    <tr>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="3">
                                            Cable Category : 
                                            <select class="head form-select" name="head_sub_cable_category[]">
                                                <option value="INLAND">INLAND</option>
                                                <option value="SUBMARINE">SUBMARINE</option>
                                                <option value="LASTMILE">LASTMILE</option>
                                            </select>
                                        </th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="3">
                                            Section Route : 
                                            <select name="head_sub_section_route[]" class="head form-select">
                                                <option value="1_route">Main</option>
                                                <option value="2_route">Diversity</option>
                                                <option value="3_route">3rd Route</option>
                                                <option value="4_route">4th Route</option>
                                            </select>
                                        </th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="3">
                                            Core Capacity : 
                                            <input type="number" name="head_sub_section_core_capacity[]" class="head form-control">
                                        </th>
                                    </tr>
                                </tr>
                                <tr>
                                    <tr>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="2">
                                            Section Name : 
                                            <input type="text" name="head_sub_section_name[]" class="head form-control mb-2">
                                        </th>
                                        <th style="font-size:.8rem;" colspan="2">Section Point</th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="2">
                                            Cable Type :
                                            <input type="text" name="head_sub_cable_type[]" class="head form-control mb-2">
                                        </th>
                                        <th style="font-size:.8rem;" rowspan="2" colspan="2">
                                            First RFS :
                                            <input type="text" name="head_sub_first_rfs[]" class="head form-control mb-2">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="font-size:.8rem;">
                                            Near End :
                                            <input type="text" name="head_sub_near_end[]" class="head form-control mb-2">
                                        </th>
                                        <th style="font-size:.8rem;">
                                            Far End :
                                            <input type="text" name="head_sub_far_end[]" class="head form-control mb-2">
                                        </th>
                                    </tr>
                                </tr>
                                <tr>
                                    <tr>
                                        <th style="font-size:.8rem;" rowspan="2">Action</th>
                                        <th style="font-size:.8rem;" rowspan="2">Sub Section Name</th>
                                        <th style="font-size:.8rem;" colspan="3">Sub Section Point</th>
                                        <th style="font-size:.8rem;" rowspan="2">Owner</th>
                                        <th style="font-size:.8rem;" colspan="2">Site Owner</th>
                                        <th style="font-size:.8rem;" rowspan="2">Length</th>
                                        <th style="font-size:.8rem;" rowspan="2">Min Total Loss</th>
                                        <th style="font-size:.8rem;" rowspan="2">Max Total Loss</th>
                                    </tr>
                                    <tr>
                                        <th style="font-size:.8rem;">Near End</th>
                                        <th style="font-size:.8rem;">Ropa</th>
                                        <th style="font-size:.8rem;">Far End</th>
                                        <th style="font-size:.8rem;">Near End</th>
                                        <th style="font-size:.8rem;">Far End</th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider" id="sub_section_container">
                                <tr class="empty">
                                    <td class="bg-light text-center" colspan="11">
                                        Empty
                                    </td>
                                </tr>
                                <tr style="display: none;" class="sub_section_field">
                                    <td>
                                        <div onclick="confirm_delete_sub_section('sub_section_field')"
                                            class="delete_sub_section text-danger text-center"><i
                                                class="ti ti-square-minus" style="font-size: 1.5rem;"></i></div>
                                    </td>
                                    <td style="min-width:20rem;"><input disabled type="text" name="sub_section_name[first_card][]" class="sub_section_name form-control mb-2"></td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_near_end[first_card][]" class="sub_near_end form-control mb-2"></td>
                                    <td style="min-width:10rem;"> </td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_far_end[first_card][]" class="sub_far_end form-control mb-2"></td>
                                    <td style="min-width:10rem;">
                                        <select disabled name="sub_owner[first_card][]" class="sub_owner form-select">
                                            <option value="TRIAS">TRIASMITRA</option>
                                            <option value="BIZNET">BIZNET</option>
                                            <option value="FIBERSTAR">FIBERSTAR</option>
                                            <option value="H3I">H3I</option>
                                            <option value="HSP">HSP</option>
                                            <option value="IFORTE">IFORTE</option>
                                            <option value="INDOSAT">INDOSAT</option>
                                            <option value="IPLUS">IPLUS</option>
                                            <option value="JKLD">JKLD</option>
                                            <option value="LINKNET">LINKNET</option>
                                            <option value="LINTASARTA">LINTASARTA</option>
                                            <option value="MORATEL">MORATEL</option>
                                            <option value="PDM">PDM</option>
                                            <option value="REMALA">REMALA</option>
                                            <option value="SDI">SDI</option>
                                            <option value="SOLNET">SOLNET</option>
                                            <option value="SSU">SSU</option>
                                            <option value="TELKOM">TELKOM</option>
                                            <option value="TIS">TIS</option>
                                            <option value="TM">TM</option>
                                            <option value="XL">XL</option>
                                            <option value="AGORA">AGORA</option>
                                        </select>
                                    </td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_site_owner_near_end[first_card][]" class="sub_site_owner_near_end form-control mb-2"></td>
                                    <td style="min-width:10rem;"><input disabled type="text" name="sub_site_owner_far_end[first_card][]" class="sub_site_owner_far_end form-control mb-2"></td>
                                    <td style="min-width:10rem;"><input disabled type="number" step="0.0001" min="0" name="sub_initial_length[first_card][]" class="sub_initial_length form-control mb-2" value=""></td>
                                    <td style="min-width:10rem;"><input disabled type="number" step="0.0001" min="0" name="sub_initial_min_total_loss[first_card][]" class="sub_initial_min_total_loss form-control mb-2" value=""></td>
                                    <td style="min-width:10rem;"><input disabled type="number" step="0.0001" min="0" name="sub_initial_max_total_loss[first_card][]" class="sub_initial_max_total_loss form-control mb-2" value=""></td>
                                </tr>
                                <tr style="display: none;" class="ropa_sub_section_field">
                                    <td rowspan="2" style="align-content : center;">
                                        <div onclick="confirm_delete_sub_section_with_ropa('ropa_sub_section_field')"
                                            class="delete_sub_section_with_ropa text-danger text-center"><i
                                                class="ti ti-square-minus" style=" font-size: 1.5rem;"></i></div>
                                    </td>
                                    <td rowspan="2" style="min-width:20rem; align-content : center;">
                                        <input disabled type="text" disabled name="ropa_sub_section_name[first_card][]" class="ropa_sub_section_name form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_near_end[first_card][]" class="ropa_sub_near_end form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_ropa[first_card][]" class="ropa_sub_ropa form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_far_end[first_card][]" class="ropa_sub_far_end form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;">
                                        <select disabled name="ropa_sub_owner[first_card][]" class="ropa_sub_owner form-select">
                                            <option value="TRIAS">TRIASMITRA</option>
                                            <option value="BIZNET">BIZNET</option>
                                            <option value="FIBERSTAR">FIBERSTAR</option>
                                            <option value="H3I">H3I</option>
                                            <option value="HSP">HSP</option>
                                            <option value="IFORTE">IFORTE</option>
                                            <option value="INDOSAT">INDOSAT</option>
                                            <option value="IPLUS">IPLUS</option>
                                            <option value="JKLD">JKLD</option>
                                            <option value="LINKNET">LINKNET</option>
                                            <option value="LINTASARTA">LINTASARTA</option>
                                            <option value="MORATEL">MORATEL</option>
                                            <option value="PDM">PDM</option>
                                            <option value="REMALA">REMALA</option>
                                            <option value="SDI">SDI</option>
                                            <option value="SOLNET">SOLNET</option>
                                            <option value="SSU">SSU</option>
                                            <option value="TELKOM">TELKOM</option>
                                            <option value="TIS">TIS</option>
                                            <option value="TM">TM</option>
                                            <option value="XL">XL</option>
                                            <option value="AGORA">AGORA</option>
                                        </select>
                                    </td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_site_owner_near_end[first_card][]" class="ropa_sub_site_owner_near_end form-control mb-2"></td>
                                    <td rowspan="2" style="align-content : center; min-width:10rem;"><input disabled type="text" name="ropa_sub_site_owner_far_end[first_card][]" class="ropa_sub_site_owner_far_end form-control mb-2"></td>
                                    <td style="min-width:10rem;">
                                        <label>Near end to Ropa</label>
                                        <input disabled name="ropa_sub_near_end_initial_length[first_card][]" type="number" step="0.0001" min="0" class="ropa_sub_near_end_initial_length form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Near end to Ropa</label>
                                        <input disabled name="ropa_sub_near_end_initial_min_total_loss[first_card][]" type="number" step="0.0001" min="0" class="ropa_sub_near_end_initial_min_total_loss form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Near end to Ropa</label>
                                        <input disabled type="number" step="0.0001" min="0" class="ropa_sub_near_end_initial_max_total_loss form-control mb-2" name="ropa_sub_near_end_initial_max_total_loss[first_card][]" value="">
                                    </td>
                                </tr>
                                <tr style="display: none;" class="ropa_sub_section_field_2" >
                                    <td style="min-width:10rem;">
                                        <label>Far end to Ropa</label>
                                        <input disabled name="ropa_sub_far_end_initial_length[first_card][]" type="number" step="0.0001" min="0" class="ropa_sub_far_end_initial_length form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Far end to Ropa</label>
                                        <input disabled name="ropa_sub_far_end_initial_min_total_loss[first_card][]" type="number" step="0.0001" min="0" class="ropa_sub_far_end_initial_min_total_loss form-control mb-2" value="">
                                    </td>
                                    <td style="min-width:10rem;">
                                        <label>Far end to Ropa</label>
                                        <input disabled name="ropa_sub_far_end_initial_max_total_loss[first_card][]" type="number" step="0.0001" min="0" class="ropa_sub_far_end_initial_max_total_loss form-control mb-2" value="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="min-height: 1rem;"></div>
                </div>
                {{-- End first_card --}}
                
                

                <div style="min-height: 3rem;"></div>
            </div>
            <button style="padding:.5rem 3rem;" type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
        </form>
    </div>
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

        function add_section() {
            var section_field = document.getElementById("section_field"); // find row to copy
            var section_container = document.getElementById("section_container"); // find table to append to
            var clone = section_field.cloneNode(true); // copy children too

            section_id = makeid(10);

            clone.id = section_id; // change id or other attributes/contents
            section_container.appendChild(clone); // add new row to end of table

            clone.querySelector(".delete_section").setAttribute('onclick', 'delete_section("' + section_id + '")');
        }

        function add_section_card() {
            var section_card = document.getElementById("section_card"); // find the card to copy
            var section_card_container = document.getElementById("section_card_container"); // find the container to append to
            var clone = section_card.cloneNode(true); // copy children too
            
            // Generate unique ID for section_card and sub_section_container
            var newCardId = makeid(10);
            // var newSubSectionContainerId = makeid(10);
            clone.style.display = '';
            var inputs = clone.querySelectorAll('.head');

            clone.querySelector('.head_card_id').value = newCardId;

            inputs.forEach(function(input) {
                input.disabled = false; // Enable the input fields
            });

            clone.id = newCardId; 

            clone.querySelector(".delete_section_card").setAttribute('onclick', 'confirm_delete_section_card("' +
                newCardId + '")');

            clone.querySelector(".add_sub_section").setAttribute('onclick', 'add_sub_section("' +
                newCardId + '")');
                
            clone.querySelector(".add_sub_section_with_ropa").setAttribute('onclick', 'add_sub_section_with_ropa("' +
                newCardId + '")');

            section_card_container.appendChild(clone); // Add new card to the container
        }

        function add_sub_section(subSectionContainerId) {
            parentElement = document.getElementById(subSectionContainerId);

            var empty = parentElement.getElementsByClassName("empty")[0];

            var sub_section_field = parentElement.getElementsByClassName("sub_section_field")[0]; // find the row to copy
            var div = parentElement.querySelector('tbody'); // find the container for this card's sub-section
            var clone = sub_section_field.cloneNode(true); // copy children too

            clone.style.display = ''; // Remove 'display: none' to show the row
            empty.style.display = 'none'; 


            clone.classList.remove('sub_section_field');

            id = makeid(10);
            clone.classList.add(id); // change id or other attributes/contents

            var inputs = clone.querySelectorAll('input, select');
            inputs.forEach(function(input) {
                input.disabled = false; // Enable the input fields
            });

            // Update the 'name' attribute of inputs based on their class names
            var classNames = ['sub_section_name', 'sub_near_end', 'sub_far_end', 'sub_owner', 'sub_site_owner_near_end', 'sub_site_owner_far_end', 'sub_initial_length', 'sub_initial_min_total_loss', 'sub_initial_max_total_loss'];
            classNames.forEach(function(className) {
                var elements = clone.getElementsByClassName(className);
                Array.from(elements).forEach(function(element) {
                    // Update the name attribute based on class name and newCardId
                    element.name = className + '[' + subSectionContainerId + '][]';
                });
            });

            div.appendChild(clone); // Add the new row to the corresponding sub-section container

            clone.querySelector(".delete_sub_section").setAttribute('onclick', 'confirm_delete_sub_section("' + id +'")');
        }

        function add_sub_section_with_ropa(subSectionContainerId) {
            // var empty = document.getElementById("empty"); // find the row to copy
            parentElement = document.getElementById(subSectionContainerId);
            var ropa_sub_section_field = parentElement.getElementsByClassName("ropa_sub_section_field")[0]; // find the row to copy
            var ropa_sub_section_field_2 = parentElement.getElementsByClassName("ropa_sub_section_field_2")[0]; // find the row to copy
            var empty = parentElement.getElementsByClassName("empty")[0];

            var div = parentElement.querySelector('tbody'); // find the container for this card's sub-section

            var clone = ropa_sub_section_field.cloneNode(true); // copy children too
            var clone_2 = ropa_sub_section_field_2.cloneNode(true); // copy children too

            id = makeid(10);
            clone.classList.add(id);
            clone_2.classList.add(id);


            // Update the 'name' attribute of inputs based on their class names
            var classNames_1 = ['ropa_sub_section_name', 'ropa_sub_near_end','ropa_sub_ropa', 'ropa_sub_far_end', 'ropa_sub_owner', 'ropa_sub_site_owner_near_end', 'ropa_sub_site_owner_far_end', 'ropa_sub_near_end_initial_length', 'ropa_sub_near_end_initial_min_total_loss', 'ropa_sub_near_end_initial_max_total_loss'];
            var classNames_2 = ['ropa_sub_far_end_initial_length', 'ropa_sub_far_end_initial_min_total_loss', 'ropa_sub_far_end_initial_max_total_loss'];
            classNames_1.forEach(function(className) {
                var elements = clone.getElementsByClassName(className);
                Array.from(elements).forEach(function(element) {
                    // Update the name attribute based on class name and newCardId
                    element.name = className + '[' + subSectionContainerId + '][]';
                });
            });
            classNames_2.forEach(function(className) {
                var elements = clone_2.getElementsByClassName(className);
                Array.from(elements).forEach(function(element) {
                    // Update the name attribute based on class name and newCardId
                    element.name = className + '[' + subSectionContainerId + '][]';
                });
            });

            div.appendChild(clone);
            div.appendChild(clone_2);

            clone.style.display = '';
            clone_2.style.display = '';

            clone.classList.remove('ropa_sub_section_field');
            clone_2.classList.remove('ropa_sub_section_field_2');

            empty.style.display = 'none'; 

            var inputs = clone.querySelectorAll('input, select');
            inputs.forEach(function(input) {
                input.disabled = false; // Enable the input fields
            });

            var inputs_2 = clone_2.querySelectorAll('input, select');
            inputs_2.forEach(function(input) {
                input.disabled = false; // Enable the input fields
            });
            
            // ADD HIDDEN INPUT WITH A RANDOM VALUE
            var hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "ropa_hidden[]";
            hiddenInput.value = id; // Set random value (or any value you need)
            clone.appendChild(hiddenInput);
            
            clone.querySelector(".delete_sub_section_with_ropa").setAttribute('onclick', 'confirm_delete_sub_section_with_ropa("' + id +'")');
        }

        // Show confirmation dialog before deleting a section card
        function confirm_delete_section_card(sectionCardId) {
            if (confirm("Are you sure you want to delete this section card?")) {
                delete_section_card(sectionCardId);
            }
        }

        // Show confirmation dialog before deleting a sub-section
        function confirm_delete_sub_section(subSectionId) {
            if (confirm("Are you sure you want to delete this sub-section?")) {
                delete_sub_section(subSectionId);
            }
        }

        // Show confirmation dialog before deleting a sub-section
        function confirm_delete_sub_section_with_ropa(subSectionId) {
            if (confirm("Are you sure you want to delete this sub-section?")) {
                delete_sub_section_with_ropa(subSectionId);
            }
        }


        // Delete sub-section by ID
        function delete_section(sectionId) {
            var section = document.getElementById(sectionId);
            if (sectionId == 'section_field') {
                alert('This element cannot be deleted !');
            } else {
                if (section) {
                    section.remove(); // Remove the sub-section from the DOM
                }
            }
        }

        // Delete section card by ID
        function delete_section_card(sectionCardId) {
            var sectionCard = document.getElementById(sectionCardId);
            if (sectionCardId == 'section_card') {
                alert('This element cannot be deleted !');
            } else {
                if (sectionCard) {
                    sectionCard.remove(); // Remove the sub-section from the DOM
                }
            }

        }

        // Delete sub-section by ID
        function delete_sub_section(subSectionId) {

            var subSections = document.getElementsByClassName(subSectionId);
            if (subSectionId == 'sub_section_field') {
                alert('This element cannot be deleted !');
            } else {
                if (subSections) {
                    while (subSections.length > 0) {
                        subSections[0].remove(); // Keep removing the first element until none remain
                    }
                }
            }
        }


        // Delete sub-section by ID
        function delete_sub_section_with_ropa(subSectionId) {
            var subSections = document.getElementsByClassName(subSectionId);
            if (subSectionId == 'ropa_sub_section_field') {
                alert('This element cannot be deleted !');
            } else {
                if (subSections) {
                    while (subSections.length > 0) {
                        subSections[0].remove(); // Keep removing the first element until none remain
                    }
                }
            }
        }
    </script>

    <script src="{{ asset('layout/dist/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('layout/dist/js/forms/form-wizard.js') }}"></script>
@endsection
