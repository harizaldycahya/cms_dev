<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubSectionController extends Controller
{
    

    public function index($id)
    {
        
    }

    public function show($project_id, $route_id, $segment_id, $section_id, $sub_section_id)
    {

        return view('sub_section.show')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id)
        ->with('sub_section_id', $sub_section_id);
    }

    public function create($project_id, $route_id, $segment_id, $section_id)
    {
      return view('sub_section.create')
      ->with('project_id', $project_id)
      ->with('route_id', $route_id)
      ->with('segment_id', $segment_id)
      ->with('section_id', $section_id);
    }


    public function store(Request $request)
    {
        function generateRandomString($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        $project_id = $request->project_id;
        $route_id = $request->route_id;
        $segment_id = $request->segment_id;
        $section_id = $request->section_id;
        
        if (!is_null($request->ropa_sub_section_name)) {
            $loop_ropa_sub_section = count($request->ropa_sub_section_name);
            
            for($x = 0; $x < $loop_ropa_sub_section; $x++){
                $randomString = generateRandomString(20);
                    DB::table('sub_section')->insert([
                        'project_id' => $request->project_id,
                        'route_id' => $request->route_id,
                        'segment_id' => $request->segment_id,
                        'section_id' => $section_id,
                        'customer_id' => $request->ropa_sub_owner[$x],
                        'ropa_id' => $randomString,
                        'sub_section_name' => $request->ropa_sub_near_end[$x].' - '.$request->ropa_sub_ropa[$x],
                        'sub_near_end' => $request->ropa_sub_near_end[$x],
                        'sub_far_end' => $request->ropa_sub_ropa[$x],
                        'sub_site_owner_near_end' => $request->ropa_sub_site_owner_near_end[$x],
                        'sub_site_owner_far_end' => 'ROPA',
                        'sub_initial_length' => $request->ropa_sub_near_end_initial_length[$x],
                        'sub_initial_min_total_loss' => $request->ropa_sub_near_end_initial_min_total_loss[$x],
                        'sub_initial_max_total_loss' => $request->ropa_sub_near_end_initial_max_total_loss[$x],
                    ]);

                    DB::table('sub_section')->insert([
                        'project_id' => $request->project_id,
                        'route_id' => $request->route_id,
                        'segment_id' => $request->segment_id,
                        'section_id' => $section_id,
                        'customer_id' => $request->ropa_sub_owner[$x],
                        'ropa_id' => $randomString,
                        'sub_section_name' => $request->ropa_sub_ropa[$x].' - '.$request->ropa_sub_far_end[$x],
                        'sub_near_end' => $request->ropa_sub_ropa[$x],
                        'sub_far_end' => $request->ropa_sub_far_end[$x],
                        'sub_site_owner_near_end' => 'ROPA',
                        'sub_site_owner_far_end' => $request->ropa_sub_site_owner_far_end[$x],
                        'sub_initial_length' => $request->ropa_sub_far_end_initial_length[$x],
                        'sub_initial_min_total_loss' => $request->ropa_sub_far_end_initial_min_total_loss[$x],
                        'sub_initial_max_total_loss' => $request->ropa_sub_far_end_initial_max_total_loss[$x],
                    ]);
            }
        }


        if (!is_null($request->sub_section_name)) {
            $loop_sub_section = count($request->sub_section_name);
            for($x = 0; $x < $loop_sub_section; $x++){

                DB::table('sub_section')->insert([
                    'project_id' => $request->project_id,
                    'route_id' => $request->route_id,
                    'segment_id' => $request->segment_id,
                    'section_id' => $section_id,
                    'customer_id' => $request->sub_owner[$x],
                    'sub_section_name' => $request->sub_section_name[$x],
                    'sub_near_end' => $request->sub_near_end[$x],
                    'sub_far_end' => $request->sub_far_end[$x],
                    'sub_site_owner_near_end' => $request->sub_site_owner_near_end[$x],
                    'sub_site_owner_far_end' => $request->sub_site_owner_far_end[$x],
                    'sub_initial_length' => $request->sub_initial_length[$x],
                    'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss[$x],
                    'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss[$x],
                ]);
            }
        }

        return redirect(route('section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id]))->with('success', 'Sub Section Created Successfully');

    }
    
    public function edit($project_id, $route_id, $segment_id, $section_id, $sub_section_id)
    {
        return view('sub_section.edit')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id)
        ->with('sub_section_id', $sub_section_id);
    }

    public function update(Request $request)
    {   

        switch(auth()->user()->role){
            case 'engineering':
                $update = DB::table('sub_section')
                ->where('sub_section_id', $request->sub_section_id)
                ->update([
                    'sub_section_name' => $request->sub_section_name,
                    'sub_near_end' => $request->sub_near_end,
                    'sub_far_end' => $request->sub_far_end,
                    'sub_owner' => $request->sub_owner,
                    'sub_site_owner_near_end' => $request->sub_site_owner_near_end,
                    'sub_site_owner_far_end' => $request->sub_site_owner_far_end,
                    'sub_initial_length' => $request->sub_initial_length,
                    'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss,
                    'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss,
                ]);
                
                $cores = DB::table('core')->where('sub_section_id', $request->sub_section_id)->get();
                $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                $initial_length =  $sub_section->sub_initial_length;
                $initial_max_total_loss =  $sub_section->sub_initial_max_total_loss;
                $initial_min_total_loss =  $sub_section->sub_initial_min_total_loss;
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
            
                    DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core_id', $core->core_id)->update([
                        'initial_remarks' => $initial_remarks,
                    ]);
                }
            
                if($update){
                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id'=> $request->sub_section_id]))->with('success', 'Sub Section Updated Successfully');
                }else{
                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id'=> $request->sub_section_id]))->with('success', 'Sub Section Is Not Updated');
                }
                    
            break;
                    
            case 'ms':



                    // $sub_actual_min_total_loss = (0.2 * $request->sub_actual_length) + ( round($request->sub_actual_length / 2 ) * 0.1) + (2 * 0.5);
                    // $sub_actual_max_total_loss = number_format(( $sub_actual_min_total_loss * (14.29 / 100) ) + $sub_actual_min_total_loss, 2, '.', '');
    
                    $update = DB::table('sub_section')
                    ->where('sub_section_id', $request->sub_section_id)
                    ->update([
                        'sub_actual_length' => $request->sub_actual_length,
                        'sub_actual_min_total_loss' => $request->sub_actual_min_total_loss,
                        'sub_actual_max_total_loss' => $request->sub_actual_max_total_loss,
                    ]);
                
                    $cores = DB::table('core')->where('sub_section_id', $request->sub_section_id)->get();
                    $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                    $actual_length =  $sub_section->sub_actual_length;
                    $actual_max_total_loss =  $sub_section->sub_actual_max_total_loss;
                    $actual_min_total_loss =  $sub_section->sub_actual_min_total_loss;
                    foreach($cores as $core){
                        if($core->actual_end_cable >= $actual_length){
                            if($core->actual_total_loss_db <= $actual_max_total_loss){
                                $actual_remarks = 'OK';
                            }else{
                                $actual_remarks = 'NOT OK';
                            }
                        }else{
                            $actual_remarks = 'NOT OK';
                        }
                
                        DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core_id', $core->core_id)->update([
                            'actual_remarks' => $actual_remarks,
                        ]);
                    }
                
                    if($update){
                        return redirect(route('sub_section.show', ['project_id'=>$request->project_id,'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id'=> $request->sub_section_id]))->with('success', 'Sub Section Updated Successfully');
                    }else{
                        return redirect(route('sub_section.show', ['project_id'=>$request->project_id,'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id'=> $request->sub_section_id]))->with('success', 'Sub Section Is Not Updated');
                    }
                    
            break;
        }


    }

    public function delete($project_id, $route_id, $segment_id, $section_id, $sub_section_id){
        $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->first();

        if($sub_section->ropa_id != null){
            DB::table('sub_section')->where('ropa_id', $sub_section->ropa_id)->delete();
        }else{
            DB::table('sub_section')->where('sub_section_id', $sub_section_id)->delete();
        }

        return redirect(route('section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id]))->with('success', 'Sub Section Deleted Successfully');
    }

}
