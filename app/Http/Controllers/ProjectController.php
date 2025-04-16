<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
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
    

    public function show($id)
    {
        $project = DB::table('project')->where('project_id', $id)->first();
        $segments = DB::table('segment')->where('project_id', $id)->get();
        $sections = DB::table('section')->where('project_id',$id)->get();

        $core_aktif = DB::table('core')->where('project_id',$id)->where('initial_remarks', 'aktif')->count();
        $core_ok = DB::table('core')->where('project_id',$id)->where('initial_remarks', 'ok')->count();
        $core_not_ok = DB::table('core')->where('project_id',$id)->where('initial_remarks', 'NOT OK')->count();
        $core_capacity = DB::table('core')->where('project_id',$id)->count();

        $loop_sections = DB::table('section')->where('project_id',$id)->count();
        $all_sections = DB::table('section')->where('project_id',$id)->get();

        $all_availability = [];
        for($i = 0; $i < $loop_sections; $i++){
            $current_section_id =  $all_sections[$i]->section_id;
            $current_core_aktif = DB::table('core')->where('project_id',$id)->where('section_id', $current_section_id)->where('initial_remarks', 'aktif')->count();
            $current_core_ok = DB::table('core')->where('project_id',$id)->where('section_id', $current_section_id)->where('initial_remarks', 'ok')->count();
            $current_core_not_ok = DB::table('core')->where('project_id',$id)->where('section_id', $current_section_id)->where('initial_remarks', 'NOT OK')->count();
            $current_core_capacity = DB::table('core')->where('project_id',$id)->where('section_id', $current_section_id)->count();

            if($current_core_capacity - $current_core_aktif != 0){
                $current_availability = ($current_core_ok / ($current_core_capacity - $current_core_aktif)) * 100;
                array_push($all_availability, $current_availability);
            }else{
                $current_availability = 0;
            }
            
        }
        
        if(empty($all_availability)){
            $availability =  0;
        }else{
            $availability =  min($all_availability);
        }
        
        return view('project.show')
            ->with('project', $project)
            ->with('segments', $segments)
            ->with('sections', $sections)
            ->with('core_aktif',$core_aktif)
            ->with('core_ok', $core_ok)
            ->with('core_not_ok', $core_not_ok)
            ->with('core_capacity', $core_capacity)
            ->with('availability', $availability);
    }


    public function create()
    {
        return view('project.create');
    }

    public function store(Request $request)
    {
        $jumlah_project =  count($request->project_name);
        
        if($jumlah_project < 0){
            return redirect('engineering/project')->with('error', 'Add at least 1 project !');
        }else{
            for($i=0; $i<$jumlah_project; $i++){
                DB::table('project')->insert([
                    'project_name' => $request->project_name[$i],
                    'project_description' => $request->project_description[$i],
                ]);
            }
            
            return redirect('/')->with('success', 'Data is successfully stored !');
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
        
        return redirect('project/show/'.$project_id)->with('success', 'Data is successfully deleted !');
    }


    public function delete($project_id){
        DB::table('project')->where('project_id', $project_id)->delete();
        DB::table('segment')->where('project_id', $project_id)->delete();
        DB::table('section')->where('project_id', $project_id)->delete();
        DB::table('sub_section')->where('project_id', $project_id)->delete();
        DB::table('core')->where('project_id', $project_id)->delete();
        DB::table('draf_sor')->where('project_id', $project_id)->delete();

        return redirect()->route('dashboard')->with('success', 'Data is successfully deleted !');
    
    }

}
