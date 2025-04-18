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
        $project_id = $request->project_id;
        $route_id = $request->route_id;
        $segment_id = $request->segment_id;
        $jumlah_section = count($request->section_id);

        

        // Check if the first input is empty
        if ($request->section_id[0] == null) {
            return redirect()->back()->with('error','Data is empty.');
        }

        // Check for duplicate section_id values
        if (count($request->section_id) !== count(array_unique($request->section_id))) {
            return redirect()->back()->with('error','Section ID cannot be the same.');
        }

        try {
            // Check if any section_id already exists in the database for the given segment_id
            $existingSections = DB::table('section')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->whereIn('section_id', $request->section_id)
                ->pluck('section_id')
                ->toArray();
            
            if (!empty($existingSections)) {
                return redirect()->back()->with('error', 'Some Section IDs already exist: ' . implode(', ', $existingSections));
            }


            // Loop through the inputs and insert into the database
            for ($i = 0; $i < $jumlah_section; $i++) {
                $section_name =  $request->section_name[$i];
                $parts = explode(' - ', $section_name);
                $near_end = trim($parts[0]); 
                $far_end = trim($parts[1]); 

                DB::table('section')->insert([
                    'unique_id' => $project_id . $request->route_id . $request->segment_id . $request->section_id[$i] ,
                    'project_id' => $project_id,
                    'route_id' => $request->route_id,
                    'segment_id' => $request->segment_id,
                    'section_id' => $request->section_id[$i],
                    'section_name' => $section_name,
                    'near_end' => $near_end,
                    'far_end' => $far_end,
                    'section_route' => $request->section_route[$i],
                    'cable_type' => $request->cable_type[$i],
                    'first_rfs' => $request->first_rfs[$i],
                    'core_capacity' => $request->core_capacity[$i],
                ]);
            }

            return redirect(route('segment.show', ['project_id' => $project_id,'route_id' => $route_id,'segment_id' => $segment_id]))->with('success', 'Data successfully inserted!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for duplicate entry error
            if ($e->getCode() == 23000) { // SQLSTATE 23000 for integrity constraint violation
                return redirect(route('segment.show', ['project_id' => $project_id,'route_id' => $route_id,'segment_id' => $segment_id]))->with('error', 'Section ID is already exist !.');
            }

            // Handle other database errors (optional)
            return redirect(route('segment.show', ['project_id' => $project_id,'route_id' => $route_id,'segment_id' => $segment_id]))
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function edit($project_id, $route_id, $segment_id)
    {
        return view('section.edit')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id);
        
    }

    public function update(Request $request)
    {

        $jumlah_section = count($request->section_id);

        for ($i = 0; $i < $jumlah_section; $i++) {
            $parts = explode(' - ', $request->section_name[$i]);
            $near_end = trim($parts[0]); 
            $far_end = trim($parts[1]); 
    
            DB::table('section')
            ->where('project_id', $request->project_id)
            ->where('route_id', $request->route_id)
            ->where('segment_id', $request->segment_id)
            ->where('section_id', $request->section_id[$i])
            ->update([
                'section_name' => $request->section_name[$i],
                'near_end' => $near_end,
                'far_end' => $far_end,
                'section_route' => $request->section_route[$i],
                'core_capacity' => $request->core_capacity[$i],
                'cable_type' => $request->cable_type[$i],
                'first_rfs' => $request->first_rfs[$i],
            ]);
        }

        return redirect(route('segment.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=>$request->segment_id]))->with('success', 'Ssections is successfully updated !!');
    
    }

    public function delete($project_id, $route_id, $segment_id, $section_id){
        DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->delete();
        DB::table('sub_section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('section_id', $section_id)->delete();
        DB::table('core')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('section_id', $section_id)->delete();
        DB::table('sor_request')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->delete();
        return redirect()->route('segment.show', ['project_id'=>$project_id, 'route_id'=>$route_id, 'segment_id'=>$segment_id])->with('success', 'Data is successfully deleted !');
        
    }

}
