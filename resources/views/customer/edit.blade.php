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
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-5">
            <div class="row align-items-center">
                <div class="col-9">
                    <h3 class="fw-semibold" style="font-size: 2rem;">Edit Customer</h3>
                    <hr>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-decoration-none" href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                Edit Customer
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
        $customer = DB::table('customer')->where('customer_id', $customer_id)->get()->first();
    @endphp
    <div class="card">
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('customer.update') }}" method="POST" enctype=multipart/form-data>
                        {{ csrf_field() }}
                        <div class="table-responsive">
                            <table class="table table-stripped text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th>
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Customer ID</h6>
                                        <th>
                                            <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">
                                                Customer Name</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableToModify">
                                    <tr id="rowToClone">
                                        <td style="width:10%;">
                                            <div style="text-align:center; font-size:1rem;">{{$customer->customer_id}}</div>
                                            <input type="hidden" name="customer_id" value="{{$customer->customer_id}}">
                                        </td>
                                        <td style="width:90%;">
                                            <input class="form-control" value="{{$customer->customer_name}}" name="customer_name" type="text">
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
    </script>
@endsection
