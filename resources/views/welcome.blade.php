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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        /* End Loading */


        .col-md-6:has(.dataTables_filter) {
            display: block;
        }

        .col-md-6:has(#example_length) {
            display: none;
        }

        #example_filter {
            text-align: left;
        }

        #example_filter label .form-control {
            width: 15rem;
        }

        th {
            cursor: pointer;
        }

        .dt-row {
            margin-bottom: 5rem;
        }

        aside .sidebar-link .hide-menu {
            font-size: .8rem;
        }

        h2 {
            font-size: 1.5rem;
        }

        h4 {
            font-size: 1.2rem;
        }

        h3 {
            font-size: 1.2rem;
        }

        td {
            font-size: .8rem;
        }

        tr td {
            padding: .5rem .5rem !important;
            margin: .5rem .5rem !important;
        }

        .border-with-dot {
            position: relative;
            padding-left: 2px;
            /* Adjust padding to create space for the dot */
            border-left: 1.5px solid #4C6EF5;
            /* Regular left border */
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
            padding-left: 2px;
            /* Adjust padding to create space for the dot */
            border-left: 1.5px solid #4DA3FF;
            /* Regular left border */
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
            padding-left: 2px;
            /* Adjust padding to create space for the dot */
            border-left: 1.5px solid #e8571e;
            /* Regular left border */
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

        .link-custom:hover {
            background-color: #EDF2FF;
            color: #4C6EF5 !important;
        }
    </style>

</head>

<body>
    <!--  Body Wrapper -->
    <div style="" class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100">
            <div class="position-relative z-index-5">
                
                <div class="row">
                    <div class="col-xl-7 col-xxl-8" >
                        <div class="d-none d-xl-flex align-items-center justify-content-center"
                            style="height: calc(100vh - 80px);">
                            <img src="{{ asset('layout/images/logos/logo.jpeg') }}" alt=""
                                class="img-fluid" width="500">
                        </div>
                    </div>
                    <div class="col-xl-5 col-xxl-4">
                        <div
                            class="authentication-login min-vh-100 bg-body row justify-content-center align-items-center p-4">
                            <div class="col-sm-8 col-md-6 col-xl-9">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success">
                                        <!-- <button type="button" class="close" data-dismiss="alert"> <i style="font-size:1.5rem;" class="ti ti-square-x"></i> </button>	 -->
                                        <div style="cursor:pointer; font-size:1rem;" data-dismiss="alert" class="delete_button inline-block">
                                            <i class="ti ti-square-x"></i>
                                            <strong style="font-size:.8rem;" >{{ $message }}</strong>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($message = Session::get('error'))
                                    <div class="alert alert-danger">
                                        <div style="cursor:pointer; font-size:1rem;" data-dismiss="alert" class="delete_button inline-block">
                                            <i class="ti ti-square-x"></i>
                                            <strong style="font-size:.8rem;" >{{ $message }}</strong>
                                        </div>
                                        <!-- <button type="button" class="close" data-dismiss="alert"> <i style="font-size:1.5rem;" class="ti ti-square-x"></i> </button>	 -->
                                    </div>
                                @endif
                                <h2 class="mb-3 fs-7 fw-bolder">Welcome to CMS</h2>
                                <p class=" mb-9">Core Management System</p>
                                <div class="position-relative text-center my-4">
                                    <p
                                        class="mb-0 fs-4 px-3 d-inline-block bg-white text-dark z-index-5 position-relative">
                                        SIGN IN</p>
                                    <span
                                        class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
                                </div>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1" class="form-label">NIK / Username</label>
                                        <input type="text" name="nik" value="{{ old('nik') }}" required autocomplete="nik" class="form-control" id="exampleInputEmail1"
                                            aria-describedby="emailHelp">
                                    </div>
                                    <div class="mb-4">
                                        <label for="exampleInputPassword1" class="form-label">Password</label>
                                        <input id="password" type="password"  name="password" required autocomplete="current-password" class="form-control" id="exampleInputPassword1">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Sign
                                        In</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        new DataTable('#example');
    </script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    <script src="{{ asset('layout/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('layout/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('layout/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('layout/js/app.min.js') }}"></script>
    <script src="{{ asset('layout/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('layout/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset('layout/js/dashboard.js') }}"></script>
    @yield('script')

    <script>
        element.classList.remove("mystyle");
    </script>
</body>

</html>
