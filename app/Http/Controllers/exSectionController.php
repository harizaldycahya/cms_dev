<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function index($id)
    {
        
    }

    public function show($project_id, $route_id, $segment_id, $section_id)
    {   
        return view('section.show')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id);
    }

    public function create($project_id, $route_id, $segment_id)
    {
        return view('section.create')->with('project_id', $project_id)->with('route_id', $route_id)->with('segment_id', $segment_id);
    }


    public function store(Request $request)
    {
        switch($request->input_type){
            case 'regular':
                    // $loop =  count($request->section_name);
                    // for ($i=0; $i < $loop; $i++) { 
                    //     DB::table('section')->insert([
                    //         'segment_id' => $request->segment_id,
                    //         'project_id' => $request->project_id,
                    //         'section_type' => 'regular',
                    //         'section_name' => $request->section_name[$i],
                    //         'near_end' => $request->near_end[$i],
                    //         'far_end' => $request->far_end[$i],
                    //         'cable_category' => $request->cable_category[$i],
                    //         'section_route' => $request->section_route[$i],
                    //         'cable_type' => $request->cable_type[$i],
                    //         'first_rfs' => $request->first_rfs[$i],
                    //         'owner' => $request->owner[$i],
                    //         'core_capacity' => $request->core_capacity[$i],
                    //         'site_owner_far_end' => $request->site_owner_far_end[$i],
                    //         'site_owner_near_end' => $request->site_owner_near_end[$i],
                    //         'initial_length' => $request->initial_length[$i],
                    //         'initial_max_total_loss' => $request->initial_max_total_loss[$i],
                    //         'initial_min_total_loss' => $request->initial_min_total_loss[$i],
                            
                    //     ]);
                    // }

                    // return redirect()->route('segment.show', ['project_id'=>$request->project_id[0], 'segment_id'=>$request->segment_id]);

                    break;
                case 'with_sub_section':
                    $loop_section = count($request->head_sub_section_name);
                
                    function generateRandomString($length = 10) {
                        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $charactersLength = strlen($characters);
                        $randomString = '';
                        for ($i = 0; $i < $length; $i++) {
                            $randomString .= $characters[random_int(0, $charactersLength - 1)];
                        }
                        return $randomString;
                    }
                
                    for ($i = 0; $i < $loop_section; $i++) {
                        $unique_id = $request->project_id . $request->route_id . $request->segment_id . $request->head_section_id[$i];
                
                        // Check if unique_id already exists
                        $existingSection = DB::table('section')->where('unique_id', $unique_id)->exists();
                        if ($existingSection) {
                            return redirect()->route('segment.show', [
                                'project_id' => $request->project_id,
                                'route_id' => $request->route_id,
                                'segment_id' => $request->segment_id
                            ])->with('error', 'Duplicate unique_id found:'.$unique_id);
                        }
                
                        // Insert into the section table
                        $section_id = DB::table('section')->insertGetId([
                            'project_id' => $request->project_id,
                            'segment_id' => $request->segment_id,
                            'route_id' => $request->route_id,
                            'section_id' => $request->head_section_id[$i],
                            'unique_id' => $unique_id,
                            'section_type' => 'with_sub_section',
                            'section_name' => $request->head_sub_section_name[$i],
                            'near_end' => $request->head_sub_near_end[$i],
                            'far_end' => $request->head_sub_far_end[$i],
                            'section_route' => $request->head_sub_section_route[$i],
                            'cable_type' => $request->head_sub_cable_type[$i],
                            'first_rfs' => $request->head_sub_first_rfs[$i],
                            'core_capacity' => $request->head_sub_section_core_capacity[$i],
                        ]);
                
                        if (!is_null($request->ropa_sub_section_name)) {
                            $loop_ropa_sub_section = count($request->ropa_sub_section_name[$request->card_id[$i]]);
                            
                            for ($x = 0; $x < $loop_ropa_sub_section; $x++) {
                                $randomString = generateRandomString(20);
                                DB::table('sub_section')->insert([
                                    'project_id' => $request->project_id,
                                    'route_id' => $request->route_id,
                                    'segment_id' => $request->segment_id,
                                    'section_id' => $request->head_section_id[$i],
                                    'customer_id' => $request->ropa_sub_owner[$request->card_id[$i]][$x],
                                    'ropa_id' => $randomString,
                                    'sub_section_name' => $request->ropa_sub_near_end[$request->card_id[$i]][$x] . ' - ' . $request->ropa_sub_ropa[$request->card_id[$i]][$x],
                                    'sub_near_end' => $request->ropa_sub_near_end[$request->card_id[$i]][$x],
                                    'sub_far_end' => $request->ropa_sub_ropa[$request->card_id[$i]][$x],
                                    'sub_site_owner_near_end' => $request->ropa_sub_site_owner_near_end[$request->card_id[$i]][$x],
                                    'sub_site_owner_far_end' => 'ROPA',
                                    'sub_initial_length' => $request->ropa_sub_near_end_initial_length[$request->card_id[$i]][$x],
                                    'sub_initial_min_total_loss' => $request->ropa_sub_near_end_initial_min_total_loss[$request->card_id[$i]][$x],
                                    'sub_initial_max_total_loss' => $request->ropa_sub_near_end_initial_max_total_loss[$request->card_id[$i]][$x],
                                ]);
                
                                DB::table('sub_section')->insert([
                                    'project_id' => $request->project_id,
                                    'route_id' => $request->route_id,
                                    'segment_id' => $request->segment_id,
                                    'section_id' => $request->head_section_id[$i],
                                    'customer_id' => $request->ropa_sub_owner[$request->card_id[$i]][$x],
                                    'ropa_id' => $randomString,
                                    'sub_section_name' => $request->ropa_sub_ropa[$request->card_id[$i]][$x] . ' - ' . $request->ropa_sub_far_end[$request->card_id[$i]][$x],
                                    'sub_near_end' => $request->ropa_sub_ropa[$request->card_id[$i]][$x],
                                    'sub_far_end' => $request->ropa_sub_far_end[$request->card_id[$i]][$x],
                                    'sub_site_owner_near_end' => 'ROPA',
                                    'sub_site_owner_far_end' => $request->ropa_sub_site_owner_far_end[$request->card_id[$i]][$x],
                                    'sub_initial_length' => $request->ropa_sub_far_end_initial_length[$request->card_id[$i]][$x],
                                    'sub_initial_min_total_loss' => $request->ropa_sub_far_end_initial_min_total_loss[$request->card_id[$i]][$x],
                                    'sub_initial_max_total_loss' => $request->ropa_sub_far_end_initial_max_total_loss[$request->card_id[$i]][$x],
                                ]);
                            }
                        }
                        
                        if (!is_null($request->sub_section_name)) {
                            $loop_sub_section = count($request->sub_section_name[$request->card_id[$i]]);
                            for ($x = 0; $x < $loop_sub_section; $x++) {
                                DB::table('sub_section')->insert([
                                    'project_id' => $request->project_id,
                                    'segment_id' => $request->segment_id,
                                    'section_id' => $request->head_section_id[$i],
                                    'route_id' => $request->route_id,
                                    'sub_section_name' => $request->sub_section_name[$request->card_id[$i]][$x],
                                    'sub_near_end' => $request->sub_near_end[$request->card_id[$i]][$x],
                                    'sub_far_end' => $request->sub_far_end[$request->card_id[$i]][$x],
                                    'customer_id' => $request->sub_owner[$request->card_id[$i]][$x],
                                    'sub_site_owner_near_end' => $request->sub_site_owner_near_end[$request->card_id[$i]][$x],
                                    'sub_site_owner_far_end' => $request->sub_site_owner_far_end[$request->card_id[$i]][$x],
                                    'sub_initial_length' => $request->sub_initial_length[$request->card_id[$i]][$x],
                                    'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss[$request->card_id[$i]][$x],
                                    'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss[$request->card_id[$i]][$x],
                                ]);
                            }
                        }
                    }
                
                    return redirect()->route('segment.show', [
                        'project_id' => $request->project_id,
                        'route_id' => $request->route_id,
                        'segment_id' => $request->segment_id
                    ])->with('success', 'Section added successfully');
                
                break;
            default:
                    return redirect()->back()->with('error', 'Input type is undefined !');
                break;
        }
    }

    public function edit($project_id, $route_id, $segment_id, $section_id)
    {
        return view('section.edit')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id);
        
    }

    public function update(Request $request)
    {
        switch($request->role){
            case 'engineering' :
                if (isset($request->section_type)) {
                    switch($request->section_type){
                        case 'regular':
                            $update = DB::table('section')->where('section_id', $request->section_id)->update([
                                'section_name' => $request->section_name,
                                'near_end' => $request->near_end,
                                'far_end' => $request->far_end,
                                'cable_category' => $request->cable_category,
                                'section_route' => $request->section_route,
                                'cable_type' => $request->cable_type,
                                'first_rfs' => $request->first_rfs,
                                'owner' => $request->owner,
                                'core_capacity' => $request->core_capacity,
                                'site_owner_far_end' => $request->site_owner_far_end,
                                'site_owner_near_end' => $request->site_owner_near_end,
                                'initial_length' => $request->initial_length,
                                'initial_max_total_loss' => $request->initial_max_total_loss,
                                'initial_min_total_loss' => $request->initial_min_total_loss,
                            ]);
        
                            $cores = DB::table('core')->where('section_id', $request->section_id)->get();
                            $section = DB::table('section')->where('section_id', $request->section_id)->first();
                            $initial_length =  $section->initial_length;
                            $initial_max_total_loss =  $section->initial_max_total_loss;
                            $initial_min_total_loss =  $section->initial_min_total_loss;
                            foreach($cores as $core){
                                if($core->initial_customers == null || $core->initial_customers == ''){
                                    if($core->initial_end_cable >= $initial_length){
                                        if($core->initial_total_loss_db <= $initial_max_total_loss){
                                            $initial_remarks = 'OK';
                                        }else{
                                            $initial_remarks = 'NOT OK';
                                        }
                                    }else{
                                        $initial_remarks = 'NOT OK';
                                    }
                                }else{
                                    $initial_remarks = 'AKTIF';
                                }
        
                                DB::table('core')->where('core_id', $core->core_id)->update([
                                    'initial_remarks' => $initial_remarks,
                                ]);
                            }
                            
                            if ($update) {
                                return redirect()->route('section.show', ['project_id'=>$request->project_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id])->with('success', 'Data is successfully updated !');
                            } else {
                                return redirect()->route('section.show', ['project_id'=>$request->project_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id])->with('error', 'Data is not updated !');            
                            }
        
        
                        break;
                        case 'with_sub_section':
                            $update = DB::table('section')->where('section_id', $request->section_id)->update([
                                'section_name' => $request->section_name,
                                'near_end' => $request->near_end,
                                'far_end' => $request->far_end,
                                'cable_category' => $request->cable_category,
                                'section_route' => $request->section_route,
                                'cable_type' => $request->cable_type,
                                'first_rfs' => $request->first_rfs,
                                'core_capacity' => $request->core_capacity
                            ]);
                            if ($update) {
                                return redirect()->route('section.show', ['project_id'=>$request->project_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id])->with('success', 'Data is successfully updated !');
                            } else {
                                return redirect()->back()->with('error', 'Error, failed to update data !');            
                            }
                        break;
                    }
                    
                } else {
                    return redirect()->back()->with('error', 'Error, Cannot find section type !');
                }
            break;
            
            case 'ms' :
                $actual_min_total_loss = (0.2 * $request->actual_length) + ( round($request->actual_length / 2 ) * 0.1) + (2 * 0.5);
                $actual_max_total_loss = number_format(( $actual_min_total_loss * (14.29 / 100) ) + $actual_min_total_loss, 2, '.', '');

                $update = DB::table('section')->where('section_id', $request->section_id)->update([
                    
                    'actual_length' => $request->actual_length,
                    'actual_max_total_loss' => $actual_min_total_loss,
                    'actual_min_total_loss' => $actual_max_total_loss,
                ]);

                $cores = DB::table('core')->where('section_id', $request->section_id)->get();
                $section = DB::table('section')->where('section_id', $request->section_id)->first();
                $actual_length =  $section->actual_length;
                $actual_max_total_loss =  $section->actual_max_total_loss;
                $actual_min_total_loss =  $section->actual_min_total_loss;
                foreach($cores as $core){
                    if($core->actual_customers == null || $core->actual_customers == ''){
                        if($core->actual_end_cable >= $actual_length){
                            if($core->actual_total_loss_db <= $actual_max_total_loss){
                                $actual_remarks = 'OK';
                            }else{
                                $actual_remarks = 'NOT OK';
                            }
                        }else{
                            $actual_remarks = 'NOT OK';
                        }
                    }else{
                        $actual_remarks = 'AKTIF';
                    }

                    DB::table('core')->where('core_id', $core->core_id)->update([
                        'actual_remarks' => $actual_remarks,
                    ]);
                }
                
                if ($update) {
                    return redirect()->route('section.show', ['project_id'=>$request->project_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id])->with('success', 'Data is successfully updated !');
                } else {
                    return redirect()->route('section.show', ['project_id'=>$request->project_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id])->with('error', 'Data is not updated !');            
                }

            break;

            default :
                return redirect()->back()->with('error', 'You dont have permission to edit this section !');
        }


    }

    public function delete($project_id, $route_id, $segment_id, $section_id){
        DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->delete();
        DB::table('sub_section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('section_id', $section_id)->delete();
        DB::table('core')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('section_id', $section_id)->delete();
        DB::table('sor_request')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->delete();
        return redirect()->route('segment.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=>$segment_id])->with('success', 'Data is successfully deleted !');
        
    }

}
