<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegmentController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Block access if the user has the 'hashmicro' role
            if (auth()->user()->role === 'hashmicro') {
                abort(403, 'Unauthorized action.');
            }

            // Allow access for other roles
            return $next($request);
        });
    }
    

    public function index($id)
    {
        
    }

    public function show($project_id, $segment_id)
    {
        
        $project = DB::table('project')->where('project_id', $project_id)->first();
        $segment = DB::table('segment')->where('segment_id', $segment_id)->first();

        return view('segment.show')
        ->with('project', $project)
        ->with('segment', $segment);
    }

    public function create($project_id)
    {

        return view('segment.create')->with('project_id', $project_id);
    }


    public function store(Request $request)
    {
        $project_id = $request->project_id;
        $jumlah_segment = count($request->segment_name); 
        if($request->segment_name[0] == null){
            return redirect('project/show/'.$project_id)->with('error', 'Error, Input is empty');
        }else{
            $project_id = $request->project_id;
            for($i=0; $i<$jumlah_segment; $i++){
                DB::table('segment')->insert([
                    'segment_name' => $request->segment_name[$i],
                    'project_id' => $project_id,
                ]);
            }

            return redirect('project/show/'.$project_id)->with('success', 'Data successfully inserted !');
        }
    }

    public function edit($segment_id)
    {
        $segment = DB::table('segment')->where('segment_id', $segment_id)->get()->first();
        
        return view('segment.edit')
        ->with('segment', $segment);
    }

    public function update(Request $request)
    {
        DB::table('segment')->where('segment_id', $request->segment_id)->update([
            'segment_name' => $request->segment_name,
        ]);

        return redirect('segment/show/'.$request->project_id.'/'.$request->segment_id)->with('success', 'Data is successfully updated !');
    }

    public function delete($project_id, $segment_id){

        DB::table('segment')->where('segment_id', $segment_id)->delete();
        DB::table('section')->where('segment_id', $segment_id)->delete();
        DB::table('sub_section')->where('segment_id', $segment_id)->delete();
        DB::table('core')->where('segment_id', $segment_id)->delete();
        DB::table('draf_sor')->where('segment_id', $segment_id)->delete();

        return redirect()->route('project.show', ['project_id'=>$project_id])->with('success', 'Data is successfully deleted !');
        
    }

}
