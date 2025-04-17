<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{

    public function index(){
        $projects = DB::table('project')->get();
        
        return view('project.index')->with('projects', $projects);
    }
    
    public function show($project_id, $route_id)
    {
        $project = DB::table('project')->where('project_id', $project_id)->first();
        $segments = DB::table('segment')->where('project_id', $project_id)->get();
        $sections = DB::table('section')->where('project_id',$project_id)->get();
        
        return view('project.show')
            ->with('project', $project)
            ->with('segments', $segments)
            ->with('sections', $sections)
            ->with('route_id', $route_id);
    }


    public function create()
    {
        return view('project.create');
    }

    public function store(Request $request)
    {
        $jumlah_project =  count($request->project_name);
        
        if($jumlah_project < 0){
            return 'Add at least 1 project !';
            // return redirect('engineering/project')->with('error', 'Add at least 1 project !');
        }else{
            try {
                for ($i = 0; $i < $jumlah_project; $i++) {
                    // Check if the `id` already exists
                    $exists = DB::table('project')->where('project_id', $request->project_id[$i])->exists();
    
                    if ($exists) {
                        return redirect()->back()->with('error', "Project with ID {$request->project_id[$i]} already exists.");
                    }
                    
                    // Insert the project if it doesn't exist
                    DB::table('project')->insert([
                        'project_id' => $request->project_id[$i],
                        'project_name' => $request->project_name[$i],
                        'project_description' => $request->project_description[$i],
                    ]);
                }
    
                return redirect('/')->with('success', 'Data is successfully stored!');
            } catch (\Exception $e) {
                // Handle any other database exceptions
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    }


    public function edit($project_id)
    {
        $project = DB::table('project')->where('project_id', $project_id)->first();
        return view('project.edit')->with('project', $project);
    }

 
    public function update(Request $request)
    {

        $project_id = $request->project_id;
        
        DB::table('project')->where('project_id', $project_id)->update([
            'project_name' => $request->project_name,
            'project_description' => $request->project_description,
        ]);
        
        return redirect(route('project.show', ['project_id'=>$project_id, 'route_id'=>'-']))->with('success', 'Data is successfully updated !');
    }


    public function delete($project_id){
        DB::table('project')->where('project_id', $project_id)->delete();
        DB::table('segment')->where('project_id', $project_id)->delete();
        DB::table('section')->where('project_id', $project_id)->delete();
        DB::table('sub_section')->where('project_id', $project_id)->delete();
        DB::table('core')->where('project_id', $project_id)->delete();
        DB::table('sor_request')->where('project_id', $project_id)->delete();

        return redirect()->route('dashboard')->with('success', 'Data is successfully deleted !');
    
    }

}
