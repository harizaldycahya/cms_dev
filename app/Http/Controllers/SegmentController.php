<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegmentController extends Controller
{
    

    public function index($id)
    {
        
    }

    public function show($project_id, $route_id, $segment_id){
        $project = DB::table('project')->where('project_id', $project_id)->first();
        $segment = DB::table('segment')->where('route_id', $route_id)->where('segment_id', $segment_id)->first();

        return view('segment.show')
        ->with('project', $project)
        ->with('segment', $segment);
    }

    public function create($project_id, $route_id){
        return view('segment.create')->with('project_id', $project_id)->with('route_id', $route_id);
    }


    public function store(Request $request)
    {
        $project_id = $request->project_id;
        $jumlah_segment = count($request->segment_name);

        // Check if the first input is empty
        if ($request->segment_name[0] == null) {
            return redirect('project/show/' . $project_id)
                ->with('error', 'Error, Input is empty');
        }

        try {
            // Loop through the inputs and insert into the database
            for ($i = 0; $i < $jumlah_segment; $i++) {
                DB::table('segment')->insert([
                    'unique_id' => $project_id . $request->route_id . $request->segment_id[$i] ,
                    'project_id' => $project_id,
                    'segment_id' => $request->segment_id[$i],
                    'route_id' => $request->route_id,
                    'segment_name' => $request->segment_name[$i],
                ]);
            }

            return redirect('project/show/' . $project_id . '/'. $request->route_id)
                ->with('success', 'Data successfully inserted!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Check for duplicate entry error
            if ($e->getCode() == 23000) { // SQLSTATE 23000 for integrity constraint violation
                return redirect('project/show/' . $project_id . '/'. $request->route_id )
                    ->with('error', 'Segment ID is already exist !.');
            }

            // Handle other database errors (optional)
            return redirect('project/show/' . $project_id . '/'. $request->route_id )
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function edit($project_id, $route_id)
    {
        return view('segment.edit')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id);
    }

    public function update(Request $request) 
    {
        // return $request;

        // echo $segment_id;
        $jumlah_segment = count($request->segment_id);
        
        for($i = 0; $i < $jumlah_segment; $i++){
            DB::table('segment')->where('project_id', $request->project_id)->where('route_id', $request->route_id)->where('segment_id', $request->segment_id[$i])->update([
                'segment_name' => $request->segment_name[$i],
            ]);
        }
        
        return redirect(route('project.show', ['project_id'=> $request->project_id, 'route_id'=> $request->route_id]))->with('success', 'Data is successfully updated !');


    }

    public function delete($project_id, $route_id, $segment_id){

        DB::table('segment')->where('segment_id', $segment_id)->delete();
        DB::table('section')->where('segment_id', $segment_id)->delete();
        DB::table('sub_section')->where('segment_id', $segment_id)->delete();
        DB::table('core')->where('segment_id', $segment_id)->delete();
        DB::table('sor_request')->where('segment_id', $segment_id)->delete();

        return redirect()->route('project.show', ['project_id'=>$project_id, 'route_id'=> '-'])->with('success', 'Data is successfully deleted !');
        
    }

}
