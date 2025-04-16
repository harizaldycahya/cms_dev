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


@section('content')

<div class="row">
    <div class="col-lg-12 align-items-stretch">
        <div class="card w-100 p-4">
            <div style="display:grid; grid-template-columns:2fr 1fr;">
                <h4 style="text-align:left;" class=" fw-semibold mb-4">Core {{$core->core}} | {{$section->section_name}}</h4>
                
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Monthly Earnings -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-start">
                                        <h5 class="card-title mb-9 fw-semibold">
                                            Report OTDR 
                                        </h5>
                                        <div class="col-6">
                                            <div class="fw-semibold mb-3">Begin : </div>
                                            <div class="fw-semibold mb-3">Cable : </div>
                                            <div class="fw-semibold mb-3">Range : {{$detail["FxdParams"]["range"] ?? ""}} </div>
                                            <div class="fw-semibold mb-3">Wavelength : {{$detail["FxdParams"]["wavelength"] ?? ""}} </div>
                                            <div class="fw-semibold mb-3">Loss Threshold : {{$detail["FxdParams"]["loss thr"] ?? ""}}</div>
                                            <div class="fw-semibold mb-3">OTDR : {{$detail["SupParams"]["OTDR"] ?? ""}} &nbsp; &nbsp; S/N : {{$detail["SupParams"]["OTDR S/N"] ?? ""}}</div>
                                            <div class="fw-semibold mb-3">Module : {{$detail["SupParams"]["module"] ?? ""}} &nbsp; &nbsp; S/N : {{$detail["SupParams"]["module S/N"] ?? ""}}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-semibold mb-3">End : </div>
                                            <div class="fw-semibold mb-3">Fiber : {{$detail["GenParams"]["fiber ID"] ?? ""}}</div>
                                            <div class="fw-semibold mb-3">Pulse Width : {{$detail["FxdParams"]["pulse width"] ?? ""}}</div>
                                            <div class="fw-semibold mb-3">Refractive index : {{$detail["FxdParams"]["index"] ?? ""}}</div>
                                            <div class="fw-semibold mb-3">Comment : {{$detail["GenParams"]["comments"] ?? ""}}</div>
                                            <div class="fw-semibold mb-3">Date : {{$detail["FxdParams"]["date/time"] ?? ""}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!-- Yearly Breakup -->
                            <div class="card overflow-hidden">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-9 fw-semibold">Summary </h5>
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <div class="fw-semibold mb-3">Fiber Length : {{$detail["GenParams"]["wavelength"]}}</div>
                                            {{-- <div class="fw-semibold mb-3">Attenuation : {{ number_format((float)$detail["KeyEvents"]["Summary"]["total loss"] / $detail["KeyEvents"]["Summary"]["loss end"], 2, '.', '' )}}</div> --}}
                                            @if($detail["KeyEvents"]["Summary"]["loss end"] == 0)
                                                <div class="fw-semibold mb-3">Error : {{$detail["KeyEvents"]["Summary"]["loss end"]}}</div>
                                            @else
                                                <div class="fw-semibold mb-3">Attenuation : {{ number_format((float)$detail["KeyEvents"]["Summary"]["total loss"] / $detail["KeyEvents"]["Summary"]["loss end"], 2, '.', '' )}}</div>
                                            @endif
                                            <div class="fw-semibold mb-3">Total Loss: {{$detail["KeyEvents"]["Summary"]["total loss"] ?? ""}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <div style="display:grid; grid-template-columns:2fr 1fr;">
                    <h5 style="text-align:left;" class=" fw-semibold mb-4">Event Table</h5>
                    <div style="text-align:right;" class="dropdown">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="example" class="table text-nowrap mb-0 align-middle" style="margin-top:1rem;">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th class="border-bottom-0">
                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">No</h6>
                                    <svg style="width:.8rem; display:inline;" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.13347 0.0999756H2.98516L5.01902 4.79058H3.86226L3.45549 3.79907H1.63772L1.24366 4.79058H0.0996094L2.13347 0.0999756ZM2.54025 1.46012L1.96822 2.92196H3.11227L2.54025 1.46012Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M0.722656 9.60832L3.09974 6.78633H0.811638V5.87109H4.35819V6.78633L2.01925 9.60832H4.43446V10.5617H0.722656V9.60832Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M8.45558 7.25664V7.40664H8.60558H9.66065C9.72481 7.40664 9.74667 7.42274 9.75141 7.42691C9.75148 7.42808 9.75146 7.42993 9.75116 7.43262C9.75001 7.44265 9.74458 7.46304 9.72525 7.49314C9.72522 7.4932 9.72518 7.49326 9.72514 7.49332L7.86959 10.3529L7.86924 10.3534C7.83227 10.4109 7.79863 10.418 7.78568 10.418C7.77272 10.418 7.73908 10.4109 7.70211 10.3534L7.70177 10.3529L5.84621 7.49332C5.84617 7.49325 5.84612 7.49318 5.84608 7.49311C5.82677 7.46302 5.82135 7.44264 5.8202 7.43262C5.81989 7.42993 5.81987 7.42808 5.81994 7.42691C5.82469 7.42274 5.84655 7.40664 5.91071 7.40664H6.96578H7.11578V7.25664V0.633865C7.11578 0.42434 7.29014 0.249976 7.49967 0.249976H8.07169C8.28121 0.249976 8.45558 0.42434 8.45558 0.633865V7.25664Z" fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                                    </svg>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">Distance, km</h6>
                                    <svg style="width:.8rem; display:inline;" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.13347 0.0999756H2.98516L5.01902 4.79058H3.86226L3.45549 3.79907H1.63772L1.24366 4.79058H0.0996094L2.13347 0.0999756ZM2.54025 1.46012L1.96822 2.92196H3.11227L2.54025 1.46012Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M0.722656 9.60832L3.09974 6.78633H0.811638V5.87109H4.35819V6.78633L2.01925 9.60832H4.43446V10.5617H0.722656V9.60832Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M8.45558 7.25664V7.40664H8.60558H9.66065C9.72481 7.40664 9.74667 7.42274 9.75141 7.42691C9.75148 7.42808 9.75146 7.42993 9.75116 7.43262C9.75001 7.44265 9.74458 7.46304 9.72525 7.49314C9.72522 7.4932 9.72518 7.49326 9.72514 7.49332L7.86959 10.3529L7.86924 10.3534C7.83227 10.4109 7.79863 10.418 7.78568 10.418C7.77272 10.418 7.73908 10.4109 7.70211 10.3534L7.70177 10.3529L5.84621 7.49332C5.84617 7.49325 5.84612 7.49318 5.84608 7.49311C5.82677 7.46302 5.82135 7.44264 5.8202 7.43262C5.81989 7.42993 5.81987 7.42808 5.81994 7.42691C5.82469 7.42274 5.84655 7.40664 5.91071 7.40664H6.96578H7.11578V7.25664V0.633865C7.11578 0.42434 7.29014 0.249976 7.49967 0.249976H8.07169C8.28121 0.249976 8.45558 0.42434 8.45558 0.633865V7.25664Z" fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                                    </svg>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">Loss, dB</h6>
                                    <svg style="width:.8rem; display:inline;" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.13347 0.0999756H2.98516L5.01902 4.79058H3.86226L3.45549 3.79907H1.63772L1.24366 4.79058H0.0996094L2.13347 0.0999756ZM2.54025 1.46012L1.96822 2.92196H3.11227L2.54025 1.46012Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M0.722656 9.60832L3.09974 6.78633H0.811638V5.87109H4.35819V6.78633L2.01925 9.60832H4.43446V10.5617H0.722656V9.60832Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M8.45558 7.25664V7.40664H8.60558H9.66065C9.72481 7.40664 9.74667 7.42274 9.75141 7.42691C9.75148 7.42808 9.75146 7.42993 9.75116 7.43262C9.75001 7.44265 9.74458 7.46304 9.72525 7.49314C9.72522 7.4932 9.72518 7.49326 9.72514 7.49332L7.86959 10.3529L7.86924 10.3534C7.83227 10.4109 7.79863 10.418 7.78568 10.418C7.77272 10.418 7.73908 10.4109 7.70211 10.3534L7.70177 10.3529L5.84621 7.49332C5.84617 7.49325 5.84612 7.49318 5.84608 7.49311C5.82677 7.46302 5.82135 7.44264 5.8202 7.43262C5.81989 7.42993 5.81987 7.42808 5.81994 7.42691C5.82469 7.42274 5.84655 7.40664 5.91071 7.40664H6.96578H7.11578V7.25664V0.633865C7.11578 0.42434 7.29014 0.249976 7.49967 0.249976H8.07169C8.28121 0.249976 8.45558 0.42434 8.45558 0.633865V7.25664Z" fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                                    </svg>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">Reflection, dB</h6>
                                    <svg style="width:.8rem; display:inline;" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.13347 0.0999756H2.98516L5.01902 4.79058H3.86226L3.45549 3.79907H1.63772L1.24366 4.79058H0.0996094L2.13347 0.0999756ZM2.54025 1.46012L1.96822 2.92196H3.11227L2.54025 1.46012Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M0.722656 9.60832L3.09974 6.78633H0.811638V5.87109H4.35819V6.78633L2.01925 9.60832H4.43446V10.5617H0.722656V9.60832Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M8.45558 7.25664V7.40664H8.60558H9.66065C9.72481 7.40664 9.74667 7.42274 9.75141 7.42691C9.75148 7.42808 9.75146 7.42993 9.75116 7.43262C9.75001 7.44265 9.74458 7.46304 9.72525 7.49314C9.72522 7.4932 9.72518 7.49326 9.72514 7.49332L7.86959 10.3529L7.86924 10.3534C7.83227 10.4109 7.79863 10.418 7.78568 10.418C7.77272 10.418 7.73908 10.4109 7.70211 10.3534L7.70177 10.3529L5.84621 7.49332C5.84617 7.49325 5.84612 7.49318 5.84608 7.49311C5.82677 7.46302 5.82135 7.44264 5.8202 7.43262C5.81989 7.42993 5.81987 7.42808 5.81994 7.42691C5.82469 7.42274 5.84655 7.40664 5.91071 7.40664H6.96578H7.11578V7.25664V0.633865C7.11578 0.42434 7.29014 0.249976 7.49967 0.249976H8.07169C8.28121 0.249976 8.45558 0.42434 8.45558 0.633865V7.25664Z" fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                                    </svg>
                                </th>
                                <th class="border-bottom-0">
                                    <h6 style="display:inline-block; margin-right:.5rem;" class="fw-semibold mb-0">Attenuation, dB/km</h6>
                                    <svg style="width:.8rem; display:inline;" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.13347 0.0999756H2.98516L5.01902 4.79058H3.86226L3.45549 3.79907H1.63772L1.24366 4.79058H0.0996094L2.13347 0.0999756ZM2.54025 1.46012L1.96822 2.92196H3.11227L2.54025 1.46012Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M0.722656 9.60832L3.09974 6.78633H0.811638V5.87109H4.35819V6.78633L2.01925 9.60832H4.43446V10.5617H0.722656V9.60832Z" fill="currentColor" stroke="currentColor" stroke-width="0.1" />
                                        <path d="M8.45558 7.25664V7.40664H8.60558H9.66065C9.72481 7.40664 9.74667 7.42274 9.75141 7.42691C9.75148 7.42808 9.75146 7.42993 9.75116 7.43262C9.75001 7.44265 9.74458 7.46304 9.72525 7.49314C9.72522 7.4932 9.72518 7.49326 9.72514 7.49332L7.86959 10.3529L7.86924 10.3534C7.83227 10.4109 7.79863 10.418 7.78568 10.418C7.77272 10.418 7.73908 10.4109 7.70211 10.3534L7.70177 10.3529L5.84621 7.49332C5.84617 7.49325 5.84612 7.49318 5.84608 7.49311C5.82677 7.46302 5.82135 7.44264 5.8202 7.43262C5.81989 7.42993 5.81987 7.42808 5.81994 7.42691C5.82469 7.42274 5.84655 7.40664 5.91071 7.40664H6.96578H7.11578V7.25664V0.633865C7.11578 0.42434 7.29014 0.249976 7.49967 0.249976H8.07169C8.28121 0.249976 8.45558 0.42434 8.45558 0.633865V7.25664Z" fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                                    </svg>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach ($detail["KeyEvents"] as $event => $event_node)
                                    @if($loop->index > 0)
                                        <tr>
                                            <!-- <td>{{$loop->index}}</td> -->
                                            <td><?php echo preg_replace('/[^0-9]/', '', $event)?></td>
                                            <td>{{$event_node['distance'] ?? ''}}</td>
                                            <td>{{$event_node['splice loss'] ?? ''}}</td>
                                            <td>{{$event_node['refl loss'] ?? ''}}</td>
                                            <td>{{$event_node['slope'] ?? ''}}</td>
                                        </tr> 
                                    @endif
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

<script type="text/javascript">
    $('#master').on('click', function(e) {
        if($(this).is(':checked',true)){
            $(".sub_chk").prop('checked', true);
        }else{ 
            $(".sub_chk").prop('checked',false);  

        }  

    });
</script>

@endsection