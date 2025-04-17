<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdditionalFeaturesController extends Controller
{
    
    public function search(Request $request)
    {
        $project_id = $request->project;
        $route_id = $request->route ?? '-';
        $segment_id = $request->segment;
        $section_id = $request->section;
        $sub_section_id = $request->sub_section;

        if($request->project == null){
            return 'Please select project first !';
        }else{
            if($request->segment == null){
                return redirect(route('project.show', ['project_id'=>$project_id, 'route_id'=>$route_id]));
            }else{
                if($request->section == null){
                    return redirect(route('segment.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=>$segment_id]));
                }else{
                    if($request->sub_section == null){
                        return redirect(route('section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=>$segment_id, 'section_id'=>$section_id]));
                    }else{
                        $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();
                        $customer_id = $sub_section->customer_id;
                        $type_id = $sub_section->type_id == null ? '-' : $sub_section->type_id;
                        return redirect(route('sub_section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=>$segment_id, 'section_id'=>$section_id, 'customer_id'=>$customer_id, 'type_id'=>$type_id, 'sub_section_id'=>$sub_section_id]));
                    }
                }
            }
        }
        return $request;
    }


}
