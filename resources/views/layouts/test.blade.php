{{-- Inland --}}

@for($y=1; $y < 3; $y++)

@php
  switch ($y) {
    case 1:
      $route_name = 'SUBMARINE';
      break;
    case 2:
      $route_name = 'INLAND';
      break;
    case 3:
      $route_name = 'LASTMILE';
      break;
    
    default:
      $route_name = 'UNDEFINED';
      break;
  }
@endphp
<li class="sidebar-item" style="margin-left:1rem;">
  @php
      $number_of_requests_project = count(DB::table('sor_request')->where('project_id', $project->project_id)->where('status', 'PROCESS')->get());   
  @endphp
  <a class="has-arrow" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px;" href="#" aria-expanded="false">
    <div class="row"> 
      <span class="col-2">
        <i class="ti ti-shovel" style="font-size:1.2rem;"></i>
      </span>
      <span class="hide-menu col-10" style="font-size:.8rem;">{{$route_name}}</span>
    </div>
  </a>
  @php
    $segments = DB::table('segment')->where('project_id', $project->project_id)->where('route_id', $y)->get();
  @endphp

  <ul aria-expanded="false" class="collapse two-level" style=" margin-left:1rem;">
    @foreach ($segments as $segment)
      @php
        $check_section_cable_type = DB::table('section')->where('segment_id', $segment->segment_id)->where('cable_category', 'INLAND')->get();
      @endphp
      
      @if($check_section_cable_type->isNotEmpty())
        <li class="border-with-dot sidebar-item">
          <a href="{{route('segment.show', ['project_id'=>$project->project_id, 'route_id'=>$segment->route_id, 'segment_id'=>$segment->segment_id])}}" class="{{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? 'link-custom' : '') : 'link-custom' }}" style="padding:.6rem .6rem; color:black; display:inline; display:block; border-radius:8px; {{$segment->segment_id == request()->segment(4) ? ( gettype(request()->segment(5)) == 'string' ? '' : 'background-color:#4C6EF5; color:#ffff;') : '' }};" aria-expanded="false">
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
@endfor