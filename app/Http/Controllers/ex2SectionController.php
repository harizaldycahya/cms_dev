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
        // return $request;
        // function generateRandomString($length = 10) {
        //     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //     $charactersLength = strlen($characters);
        //     $randomString = '';
        //     for ($i = 0; $i < $length; $i++) {
        //         $randomString .= $characters[random_int(0, $charactersLength - 1)];
        //     }
        //     return $randomString;
        // }
    
        $unique_id = $request->project_id . $request->route_id . $request->segment_id . $request->section_id;

        echo $unique_id;
        echo '<br>';
        echo $request->project_id;
        echo '<br>';
        echo $request->segment_id;
        echo '<br>';
        echo $request->route_id;
        echo '<br>';
        echo $request->section_id;
        echo '<br>';
        echo $unique_id;
        echo '<br>';
        echo $request->section_name;
        echo '<br>';
        echo $request->section_route;
        echo '<br>';
        echo $request->cable_type;
        echo '<br>';
        echo $request->first_rfs;
        echo '<br>';
        echo $request->section_core_capacity;

        // Insert into the section table
        $section_id = DB::table('section')->insertGetId([
            'project_id' => $request->project_id,
            'segment_id' => $request->segment_id,
            'route_id' => $request->route_id,
            'section_id' => $request->section_id,
            'unique_id' => $unique_id,
            'section_name' => $request->section_name,
            'near_end' => $request->near_end,
            'far_end' => $request->far_end,
            'section_route' => $request->section_route,
            'cable_type' => $request->cable_type,
            'first_rfs' => $request->first_rfs,
            'core_capacity' => $request->section_core_capacity,
        ]);

        
        // if (!is_null($request->sub_section_name)) {
        //     $loop_sub_section = count($request->sub_section_name);
        //     for ($x = 0; $x < $loop_sub_section; $x++) {
        //         DB::table('sub_section')->insert([
        //             'project_id' => $request->project_id,
        //             'segment_id' => $request->segment_id,
        //             'section_id' => $request->head_section_id,
        //             'route_id' => $request->route_id,
        //             'sub_section_name' => $request->sub_section_name[$x],
        //             'sub_near_end' => $request->sub_near_end[$x],
        //             'sub_far_end' => $request->sub_far_end[$x],
        //             'customer_id' => $request->sub_owner[$x],
        //             'sub_site_owner_near_end' => $request->sub_site_owner_near_end[$x],
        //             'sub_site_owner_far_end' => $request->sub_site_owner_far_end[$x],
        //             'sub_initial_length' => $request->sub_initial_length[$x],
        //             'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss[$x],
        //             'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss[$x],
        //         ]);
        //     }
        // }
    
        // return redirect()->route('segment.show', [
        //     'project_id' => $request->project_id,
        //     'route_id' => $request->route_id,
        //     'segment_id' => $request->segment_id
        // ])->with('success', 'Section added successfully');
        
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
        DB::table('section')->where('project_id', $request->project_id)->where('route_id', $request->route_id)->where('segment_id', $request->segment_id)->where('section_id', $request->section_id)->update([
            'section_name' => $request->section_name,
            'cable_type' => $request->cable_type,
            'first_rfs' => $request->first_rfs,
            'near_end' => $request->near_end,
            'far_end' => $request->far_end,
            'section_route' => $request->section_route,
            'core_capacity' => $request->section_core_capacity,
        ]);
        
        $jumlah_sub_section = count($request->sub_section_id);

        for ($i = 0; $i < $jumlah_sub_section; $i++) {
            DB::table('sub_section')->where('project_id', $request->project_id)->where('route_id', $request->route_id)->where('segment_id', $request->segment_id)->where('section_id', $request->section_id)->where('sub_section_id', $request->sub_section_id[$i])->update([
                'sub_section_name' => $request->sub_section_name[$i],
                'sub_near_end' => $request->sub_near_end[$i],
                'sub_far_end' => $request->sub_far_end[$i],
                'customer_id' => $request->sub_owner[$i],
                'sub_site_owner_near_end' => $request->sub_site_owner_near_end[$i],
                'sub_site_owner_far_end' => $request->sub_site_owner_far_end[$i],
                'sub_initial_length' => $request->sub_initial_length[$i],
                'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss[$i],
                'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss[$i],
            ]);

            $cores = DB::table('core')
            ->where('project_id', $request->project_id)
            ->where('route_id', $request->route_id)
            ->where('segment_id', $request->segment_id)
            ->where('section_id', $request->section_id)
            ->where('sub_section_id', $request->sub_section_id[$i])
            ->get();

            foreach($cores as $core){
                $length = $request->sub_initial_length[$i];
                $min_total_loss = $request->sub_initial_min_total_loss[$i];
                $max_total_loss = $request->sub_initial_max_total_loss[$i];
                
                if($length == '' || $length == null){
                    $initial_remarks = "NOT OK";
                }else{
                    if($core->initial_end_cable >= $length ){
                        if( $core->initial_total_loss_db <= $max_total_loss ){
                            $initial_remarks = "OK";
                        }else{
                            $initial_remarks = "NOT OK";
                        }
                    }else{
                        $initial_remarks = 'NOT OK';
                    }
                }

                DB::table('core')
                ->where('project_id', $request->project_id)
                ->where('route_id', $request->route_id)
                ->where('segment_id', $request->segment_id)
                ->where('section_id', $request->section_id)
                ->where('sub_section_id', $request->sub_section_id[$i])
                ->where('core', $core->core)
                ->update([
                    'initial_remarks' => $initial_remarks,
                ]);

            }

        }

        return redirect(route('section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id]))->with('success', 'Data is successfully updated !!');
    
    }

    public function delete($project_id, $route_id, $segment_id, $section_id){
        DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->delete();
        DB::table('sub_section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('section_id', $section_id)->delete();
        DB::table('core')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('section_id', $section_id)->delete();
        DB::table('sor_request')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->delete();
        return redirect()->route('segment.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=>$segment_id])->with('success', 'Data is successfully deleted !');
        
    }

}
