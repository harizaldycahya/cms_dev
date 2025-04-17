<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubSectionController extends Controller
{
    

    public function index($id)
    {
        
    }

    public function show($project_id, $route_id, $segment_id, $section_id, $customer_id, $type_id, $sub_section_id)
    {

        return view('sub_section.show')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id)
        ->with('customer_id', $customer_id)
        ->with('type_id', $type_id)
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
        $project_id = $request->project_id;
        $route_id = $request->route_id;
        $segment_id = $request->segment_id;
        $section_id = $request->section_id;

        $loop_sub_section = count($request->sub_section_name);
        for($x = 0; $x < $loop_sub_section; $x++){

            $sub_section_name =  $request->sub_section_name[$x];
            $parts = explode(' - ', $sub_section_name);
            $sub_near_end = trim($parts[0]); 
            $sub_far_end = trim($parts[1]); 

            DB::table('sub_section')->insert([
                'project_id' => $request->project_id,
                'route_id' => $request->route_id,
                'segment_id' => $request->segment_id,
                'section_id' => $section_id,
                'customer_id' => $request->customer_id[$x],
                'type_id' => $request->type_id[$x],
                'sub_section_name' => $request->sub_section_name[$x],
                'sub_near_end' => $sub_near_end,
                'sub_far_end' => $sub_far_end,
                'sub_site_owner_near_end' => $request->sub_site_owner_near_end[$x],
                'sub_site_owner_far_end' => $request->sub_site_owner_far_end[$x],
                'sub_initial_length' => $request->sub_initial_length[$x],
                'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss[$x],
                'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss[$x],
            ]);
        }

        return redirect(route('section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id]))->with('success', 'Sub Section Created Successfully');
    }
    
    public function edit($project_id, $route_id, $segment_id, $section_id)
    {
        return view('sub_section.edit')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id);
    }

    public function update(Request $request)
    {   
        
        switch(auth()->user()->role){
            case 'engineering':

                $jumlah_sub_section_id = count($request->sub_section_id);
                $typeIdTracker = []; // To track duplicate type_ids within the request

                for ($x = 0; $x < $jumlah_sub_section_id; $x++) {
                    // Skip if type_id is null or empty
                    if (empty($request->type_id[$x])) {
                        continue;
                    }

                    $customer_name = DB::table('customer')->where('customer_id', $request->customer_id[$x])->get()->first()->customer_name;

                    // Check for duplicates within the request
                    $key = $request->customer_id[$x] . '-' . $request->type_id[$x]; // Combine customer_id and type_id
                    if (isset($typeIdTracker[$key])) {
                        return redirect()->back()->with('error', "Duplicate Type ID " . $request->type_id[$x] . " found in request for Customer ID " . $customer_name);
                    }
                    $typeIdTracker[$key] = true; // Store the combination to track duplicates

                    // Check for duplicates in the database (excluding the current record being updated)
                    $existingType = DB::table('sub_section')
                        ->where('sub_section_id', $request->sub_section_id[$x])
                        ->where('customer_id', $request->customer_id[$x])
                        ->where('type_id', $request->type_id[$x])
                        ->where('sub_section_id', '!=', $request->sub_section_id[$x]) // Ensure it's not the same record
                        ->exists();

                    if ($existingType) {
                        return redirect()->back()->with('error', "Type ID " . $request->type_id[$x] . " already exists for Customer " . $customer_name);
                    }
                }

                // Proceed with update if no duplicates are found
                for ($i = 0; $i < $jumlah_sub_section_id; $i++) {
                    $sub_section_name = $request->sub_section_name[$i];
                    $parts = explode(' - ', $sub_section_name);
                    $sub_near_end = trim($parts[0]); 
                    $sub_far_end = trim($parts[1]);

                    DB::table('sub_section')
                        ->where('sub_section_id', $request->sub_section_id[$i])
                        ->update([
                            'sub_section_name' => $sub_section_name,
                            'sub_near_end' => $sub_near_end,
                            'sub_far_end' => $sub_far_end,
                            'customer_id' => $request->customer_id[$i],
                            'type_id' => $request->type_id[$i],
                            'sub_site_owner_near_end' => $request->sub_site_owner_near_end[$i],
                            'sub_site_owner_far_end' => $request->sub_site_owner_far_end[$i],
                            'sub_initial_length' => $request->sub_initial_length[$i],
                            'sub_initial_min_total_loss' => $request->sub_initial_min_total_loss[$i],
                            'sub_initial_max_total_loss' => $request->sub_initial_max_total_loss[$i],
                        ]);

                    $cores = DB::table('core')->where('sub_section_id', $request->sub_section_id[$i])->get();
                    $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id[$i])->first();

                    if ($sub_section) {
                        $initial_length = $sub_section->sub_initial_length;
                        $initial_max_total_loss = $sub_section->sub_initial_max_total_loss;
                        $initial_min_total_loss = $sub_section->sub_initial_min_total_loss;

                        foreach ($cores as $core) {
                            if($core->initial_end_cable == null || $core->initial_total_loss_db == null){
                                $initial_remarks = 'NOT OK';
                            }else{
                                if ($core->initial_end_cable >= $initial_length) {
                                    $initial_remarks = ($core->initial_total_loss_db <= $initial_max_total_loss) ? 'OK' : 'NOT OK';
                                } else {
                                    $initial_remarks = 'NOT OK';
                                }
                            }

                            DB::table('core')
                                ->where('sub_section_id', $request->sub_section_id[$i])
                                ->where('core_id', $core->core_id)
                                ->update([
                                    'initial_remarks' => $initial_remarks,
                                ]);
                        }
                    }
                }
                
                
                return redirect(route('section.show', ['project_id'=>$request->project_id,'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id]))->with('success', 'Sub Section Updated Successfully');
                
            break;

            case 'ms':

                $jumlah_sub_section_id = count($request->sub_section_id);

                for($i = 0; $i < $jumlah_sub_section_id; $i++){

                    $update = DB::table('sub_section')
                    ->where('sub_section_id', $request->sub_section_id[$i])
                    ->update([
                        'sub_actual_length' => $request->sub_actual_length[$i],
                        'sub_actual_min_total_loss' => $request->sub_actual_min_total_loss[$i],
                        'sub_actual_max_total_loss' => $request->sub_actual_max_total_loss[$i],
                    ]);

                    $cores = DB::table('core')->where('sub_section_id', $request->sub_section_id[$i])->get();
                    $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id[$i])->first();
                    $actual_length =  $sub_section->sub_actual_length;
                    $actual_max_total_loss =  $sub_section->sub_actual_max_total_loss;
                    $actual_min_total_loss =  $sub_section->sub_actual_min_total_loss;

                    foreach($cores as $core){
                        if($core->actual_end_cable >= $actual_length){
                            if($core->actual_total_loss_db <= $actual_max_total_loss){
                                $actual_remarks = $actual_length == null ? 'NOT OK' : 'OK';
                            }else{
                                $actual_remarks = 'NOT OK';
                            }
                        }else{
                            $actual_remarks = 'NOT OK';
                        }
                        
                        DB::table('core')->where('sub_section_id', $request->sub_section_id[$i])->where('core_id', $core->core_id)->update([
                            'actual_remarks' => $actual_remarks,
                        ]);
                    }

                }

                return redirect(route('section.show', ['project_id'=>$request->project_id,'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id]))->with('success', 'Sub Section Updated Successfully');
                
                    
            break;
        }


    }

    public function delete($project_id, $route_id, $segment_id, $section_id, $sub_section_id){
        
        $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->first();

        if($sub_section->type_id == null ){
            // TIDAK BERCABANG

            $check_trias_sub_section_id = DB::table('sub_section')
            ->where('project_id', $project_id)
            ->where('route_id', $route_id)
            ->where('segment_id', $segment_id)
            ->where('section_id', $section_id)
            ->where('customer_id', '000')
            ->get()
            ->first();

            if (!$check_trias_sub_section_id) {
                return redirect()->back()->with('error', 'Please create sub section TRIASMITRA first, before deleting sub section !');
            }else{
                $trias_sub_section_id = $check_trias_sub_section_id->sub_section_id;
                DB::table('core')
                ->where('sub_section_id', $sub_section_id)
                ->update([
                    'customer_id' => '000', 
                    'status' => 'IDLE', 
                    'initial_customers' => null, 
                    'actual_customers' => null,
                    'sub_section_id' => $trias_sub_section_id,
                ]);

                DB::table('sub_section')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->where('section_id', $section_id)
                ->where('sub_section_id', $sub_section_id)
                ->delete();
            }

            return redirect(route('section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id]))->with('success', 'Sub Section Deleted Successfully');
            
        }else{
            $check_trias_sub_section_id = DB::table('sub_section')
            ->where('project_id', $project_id)
            ->where('route_id', $route_id)
            ->where('segment_id', $segment_id)
            ->where('section_id', $section_id)
            ->where('customer_id', '000')
            ->get()
            ->first();

            if (!$check_trias_sub_section_id) {
                return redirect()->back()->with('error', 'Please create sub section TRIASMITRA first, before deleting sub section !');
            }else{

                $trias_sub_section_id = $check_trias_sub_section_id->sub_section_id;
                DB::table('core')
                ->where('sub_section_id', $sub_section_id)
                ->update([
                    'customer_id' => '000', 
                    'status' => 'IDLE', 
                    'initial_customers' => null, 
                    'actual_customers' => null,
                    'sub_section_id' => $trias_sub_section_id,
                ]);

                DB::table('core')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->where('section_id', $section_id)
                ->where('customer_id', $sub_section->customer_id)
                ->whereNotNull('type_id') // Correct way to check for NOT NULL
                ->delete();

                DB::table('sub_section')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->where('section_id', $section_id)
                ->where('customer_id', $sub_section->customer_id)
                ->whereNotNull('type_id') // Correct way to check for NOT NULL
                ->delete();

                return redirect(route('section.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=> $segment_id, 'section_id' => $section_id]))->with('success', 'Sub Section Deleted Successfully');
            }
        }

    }

}
