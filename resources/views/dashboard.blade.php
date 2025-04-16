@extends('layouts.app')
  
@section('head_script')
    <style>
      html, body {
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
              <p style="text-transform: uppercase; font-size:.8rem; font-weight: bold; color:#e8571e;">Login as {{auth()->user()->role}}</p>
            </li>
          </ul>

          <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
            <li class="nav-item dropdown">
              
              <a style="font-size:1rem;" class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                aria-expanded="false">
                {{auth()->user()->name}} &nbsp;&nbsp; <i class="ti ti-user fs-6"></i>
                
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                <div class="message-body">
                  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
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

  @section('content')
      <div style="min-height: 5rem;"></div>
      <div id="map"></div>
      <div style="min-height: 5rem;"></div>
      <script>
        var map;
        var b2js_kml = 'http://cms.triasmitra.com/B3JS_2020.kmz'+"?dummy="+(new Date()).getTime();
        var sdcs_kml = 'http://cms.triasmitra.com/SDCS.kmz'+"?dummy="+(new Date()).getTime();
        var jayabaya_kml = 'http://cms.triasmitra.com/JAYABAYA.kmz'+"?dummy="+(new Date()).getTime();
        var example_kml = 'https://developers.google.com/maps/documentation/javascript/examples/kml/westcampus.kml';
        
        var myFunction;
        function initMap(){
          map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(-19.257753, 146.823688),
            zoom: 2,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });

          var b2js = new google.maps.KmlLayer(b2js_kml, {
            suppressInfoWindows: true,
            preserveViewport: false,
            map: map
          });

          b2js.addListener('click', function(event) {
            window.location.href = 'https://cms.triasmitra.com/public/project/show/1';
          });
          myFunction = function (){
            var checkBox = document.getElementById("myCheck");
            if (checkBox.checked == true){
              b2js.setMap(map);

            } else {
              b2js.setMap(null);
            }
          }
        }

      </script>
      <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4i3Ee0SQk1TbNgbhsBfC34kDQx14133c&callback=initMap">
      </script>
  @endsection
