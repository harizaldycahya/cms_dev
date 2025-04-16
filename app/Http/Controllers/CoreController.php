<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Support\Str;

class CoreController extends Controller
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
    
    public function show($project_id, $segment_id, $section_type, $section_id, $sub_section_id, $core)
    {
        $project = DB::table('project')->where('project_id', $project_id)->first();
        $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
        $section = DB::table('section')->where('project_id',$project_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->first();
        $core = DB::table('core')->where('core', $core)->first();

        if ($sub_section_id == '-') {

            if(file_exists(storage_path('app/public/'.$project_id.'/'.$segment_id.'/'.$section_id.'/'.$core->core.'.sor.json'))){
                $json = file_get_contents(storage_path('app/public/'.$project_id.'/'.$segment_id.'/'.$section_id.'/'.$core->core.'.sor.json'));
                $json_data = json_decode($json,true); 

                $section = DB::table('section')->where('section_id', $section_id)->first();

                return view('core.show')
                ->with('project',$project)
                ->with('segment',$segment)
                ->with('section',$section)
                ->with('sub_section_id',$sub_section_id)
                ->with('core',$core)
                ->with('detail', $json_data);

            }else{
                return redirect()->back()->with('error', 'Sor not found');
            }   

        }else{
    
            $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->first();

            if(file_exists(storage_path('app/public/'.$project_id.'/'.$segment_id.'/'.$section_id.'/'.$sub_section_id.'/'.$core->core.'.sor.json'))){
                $json = file_get_contents(storage_path('app/public/'.$project_id.'/'.$segment_id.'/'.$section_id.'/'.$sub_section_id.'/'.$core->core.'.sor.json'));
                $json_data = json_decode($json,true); 

                $section = DB::table('section')->where('section_id', $section_id)->first();

                return view('core.show')
                ->with('project',$project)
                ->with('segment',$segment)
                ->with('section',$section)
                ->with('sub_section_id',$sub_section_id)
                ->with('core',$core)
                ->with('detail', $json_data);

            }else{
                return redirect()->back()->with('error', 'Sor not found');
            }   
        }
        


    }

    public function create($project_id, $segment_id, $section_id, $sub_section_id, $input_type)
    {

        switch($input_type){
            case 'setup':
                if($sub_section_id == '-'){
                    $project = DB::table('project')->where('project_id', $project_id)->first();
                    $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
                    $section = DB::table('section')->where('section_id', $section_id)->first();
                    $section_type = 'regular';
        
                    return view('core.create')
                    ->with('project',$project)
                    ->with('segment', $segment)
                    ->with('section', $section)
                    ->with('section_type', $section_type)
                    ->with('input_type', $input_type);
                    
                }else{
                    $project = DB::table('project')->where('project_id', $project_id)->first();
                    $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
                    $section = DB::table('section')->where('section_id', $section_id)->first();
                    $section_type = 'with_sub_section';
        
        
                    return view('core.create')
                    ->with('project',$project)
                    ->with('segment', $segment)
                    ->with('section', $section)
                    ->with('sub_section_id', $sub_section_id)
                    ->with('section_type', $section_type)
                    ->with('input_type', $input_type);
        
                }
            break;

            case 'add':
                $project = DB::table('project')->where('project_id', $project_id)->first();
                $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
                $section = DB::table('section')->where('section_id', $section_id)->first();
                
                if($sub_section_id == '-'){
                    return view('core.create')
                    ->with('project',$project)
                    ->with('segment', $segment)
                    ->with('section', $section)
                    ->with('input_type', $input_type);
                    
                }else{
                    return view('core.create')
                    ->with('project',$project)
                    ->with('segment', $segment)
                    ->with('section', $section)
                    ->with('input_type', $input_type)
                    ->with('sub_section_id', $sub_section_id);
                }
                
            break;

            default:
                if($sub_section_id == '-'){
                    $project = DB::table('project')->where('project_id', $project_id)->first();
                    $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
                    $section = DB::table('section')->where('section_id', $section_id)->first();
                    $section_type = 'regular';
        
                    return view('core.create')
                    ->with('project',$project)
                    ->with('segment', $segment)
                    ->with('section', $section)
                    ->with('section_type', $section_type)
                    ->with('input_type', $input_type);
                    
                }else{
                    $project = DB::table('project')->where('project_id', $project_id)->first();
                    $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
                    $section = DB::table('section')->where('section_id', $section_id)->first();
                    $section_type = 'with_sub_section';
        
        
                    return view('core.create')
                    ->with('project',$project)
                    ->with('segment', $segment)
                    ->with('section', $section)
                    ->with('sub_section_id', $sub_section_id)
                    ->with('section_type', $section_type)
                    ->with('input_type', $input_type);
        
                }
        }
        
    
    }


    public function store(Request $request)
    {
        $input_type = $request->input_type;
        switch($input_type){
            case 'regular':
                    DB::table('core')->where('section_id', $request->section_id)->delete();

                    $cores =  $request->core_capacity;
            
                    for ($i=1; $i <= $cores; $i++) { 
                        DB::table('core')->insert([
                            'project_id' => $request->project_id,
                            'segment_id' => $request->segment_id,
                            'section_id' => $request->section_id,
                            'core' => $i,
                            'initial_remarks' => 'NOT OK',
                            'actual_remarks' => 'NOT OK'
                        ]); 
                        
                    }
            
                    DB::table('section')->where('section_id', $request->section_id)->update([
                        'core_capacity' => $request->core_capacity,
                    ]);
            
                    return redirect(route('section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id]));
                    
                break;
            case 'range':
                    DB::table('core')->where('sub_section_id', $request->sub_section_id)->delete();
                    
                    $from = $request->from;
                    $to = $request->to;

                    for($i = $from; $i <= $to; $i++){
                        DB::table('core')->insert([
                            'project_id'=> $request->project_id,
                            'segment_id'=> $request->segment_id,
                            'section_id'=> $request->section_id,
                            'sub_section_id'=> $request->sub_section_id,
                            'core'=>$i,
                            'initial_remarks' => 'NOT OK',
                            'actual_remarks' => 'NOT OK'
                        ]);
                    }

                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id' => $request->sub_section_id]));
                break;
            case 'custom':
                    DB::table('core')->where('sub_section_id', $request->sub_section_id)->delete();
                    
                    $loop = count($request->core);

                    for($i = 0; $i < $loop; $i++){
                        DB::table('core')->insert([
                            'project_id'=> $request->project_id,
                            'segment_id'=> $request->segment_id,
                            'section_id'=> $request->section_id,
                            'sub_section_id'=> $request->sub_section_id,
                            'core'=> $request->core[$i],
                            'initial_remarks' => 'NOT OK',
                            'actual_remarks' => 'NOT OK'
                        ]);
                    }
                    
                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id' => $request->sub_section_id]));
                break;
            case 'add':
                    if (in_array(null, $request->core, true)) {
                        return redirect()->back()->with('error', 'Core cannot be empty !');
                    }

                    if(isset($request->sub_section_id)){

                        $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                        
                        $loop = count($request->core);
                        for($i = 0; $i < $loop; $i++){
                            
                            if($request->customer[$i] == null || $request->customer[$i] == ''){
                                if($request->length[$i] >= $sub_section->sub_initial_length ){
                                    if($request->total_loss_db[$i] <= $sub_section->sub_initial_max_total_loss){
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

                            DB::table('core')->insert([
                                'project_id'=> $request->project_id,
                                'segment_id'=> $request->segment_id,
                                'section_id'=> $request->section_id,
                                'sub_section_id'=> $request->sub_section_id,
                                'core'=> $request->core[$i],
                                'initial_customers'=> $request->customer[$i],
                                'initial_end_cable'=> $request->length[$i],
                                'initial_total_loss_db'=> $request->total_loss_db[$i],
                                'initial_loss_db_km'=> $request->loss_db_km[$i],
                                'initial_remarks' => $initial_remarks ,
                                'actual_remarks' => 'NOT OK'
                            ]);
                            
                        }
                        
                        return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id' => $request->sub_section_id]));
                    }else{

                        $section = DB::table('section')->where('section_id', $request->section_id)->first();
                        
                        $loop = count($request->core);
    
                        for($i = 0; $i < $loop; $i++){

                            if($request->customer[$i] == null || $request->customer[$i] == ''){
                                if($request->length[$i] != null || $request->length[$i] != ''){
                                    if($request->length[$i] >= $section->initial_length ){
                                        if($request->total_loss_db[$i] <= $section->initial_max_total_loss){
                                            $initial_remarks = 'OK';
                                        }else{
                                            $initial_remarks = 'NOT OK';
                                        }
                                    }else{
                                        $initial_remarks = 'NOT OK';
                                    }
                                }else{
                                    $initial_remarks = 'NOT OK';
                                }
                            }else{
                                $initial_remarks = 'AKTIF';
                            }

                            

                            DB::table('core')->insert([
                                'project_id'=> $request->project_id,
                                'segment_id'=> $request->segment_id,
                                'section_id'=> $request->section_id,
                                'core'=> $request->core[$i],
                                'initial_customers'=> $request->customer[$i],
                                'initial_end_cable'=> $request->length[$i],
                                'initial_total_loss_db'=> $request->total_loss_db[$i],
                                'initial_loss_db_km'=> $request->loss_db_km[$i],
                                'initial_remarks' => $initial_remarks ,
                                'actual_remarks' => 'NOT OK'
                            ]);
                        }
                        
                        return redirect(route('section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id' => '-']));
                        
                    }
                break;
            default:
                return redirect()->back()->with('error', 'cannot find input type');
        }
    }

    public function edit($project_id, $segment_id, $section_type, $section_id, $sub_section_id)
    {
        $project = DB::table('project')->where('project_id', $project_id)->first();
        $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
        $section = DB::table('section')->where('section_id', $section_id)->first();

        if($section_type == 'with_sub_section'){
            return view('core.edit')
            ->with('project',$project)
            ->with('segment', $segment)
            ->with('section', $section)
            ->with('section_type', $section_type)
            ->with('sub_section_id', $sub_section_id);
        }else{
            return view('core.edit')
            ->with('project',$project)
            ->with('segment', $segment)
            ->with('section', $section)
            ->with('section_type', $section_type);  
        }
    
    }

    public function update(Request $request)
    {

        switch(auth()->user()->role){
            case 'engineering':
                switch($request->section_type){
                    case 'regular':
                        // return $request;
                        $loop = count($request->core);
                        $section = DB::table('section')->where('section_id', $request->section_id)->first();
                        $initial_length =  $section->initial_length;
                        $initial_max_total_loss =  $section->initial_max_total_loss;
                        $initial_min_total_loss =  $section->initial_min_total_loss;

                        for($i=0; $i < $loop; $i++){
                                if($request->initial_customers[$i] == null || $request->initial_customers[$i] == ''){
                                    if($initial_length != null || $initial_length != ''){
                                        if($request->initial_end_cable[$i] >= $initial_length){
                                            if($request->initial_total_loss_db[$i] <= $initial_max_total_loss){
                                                $initial_remarks = 'OK';
                                            }else{
                                                $initial_remarks = 'NOT OK';
                                            }
                                        }else{
                                            $initial_remarks = 'NOT OK';
                                        }
                                    }else{
                                        $initial_remarks = 'NOT OK';
                                    }

                                    
                                }else{
                                    $initial_remarks = 'AKTIF';
                                }
                            
                            DB::table('core')->where('section_id', $request->section_id)->where('core', $request->core[$i])->update([
                                'initial_customers' => $request->initial_customers[$i],
                                'initial_end_cable' => $request->initial_end_cable[$i],
                                'initial_total_loss_db' => $request->initial_total_loss_db[$i],
                                'initial_loss_db_km' => $request->initial_loss_db_km[$i],
                                'initial_remarks' => $initial_remarks
                            ]);
                        };

                        return redirect(route('section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id]));

                    break;
                    case 'with_sub_section':
                        $loop = count($request->core);
                        $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                        $sub_initial_length =  $sub_section->sub_initial_length;
                        $sub_initial_max_total_loss =  $sub_section->sub_initial_max_total_loss;
                        $sub_initial_min_total_loss =  $sub_section->sub_initial_min_total_loss;

                        for($i=0; $i < $loop; $i++){

                                $initial_booked = $request->initial_booked[$i];


                                if($request->initial_customers[$i] == null || $request->initial_customers[$i] == ''){
                                    if($sub_initial_length != null || $sub_initial_length != ''){
                                        if($request->initial_end_cable[$i] >= $sub_initial_length){
                                            if($request->initial_total_loss_db[$i] <= $sub_initial_max_total_loss){
                                                $initial_remarks = 'OK';
                                            }else{
                                                $initial_remarks = 'NOT OK';
                                            }
                                        }else{
                                            $initial_remarks = 'NOT OK';
                                        }
                                    }else{
                                        $initial_remarks = 'NOT OK';
                                    }
                                }else{

                                    if ($initial_booked == 'YES'){
                                        if($sub_initial_length != null || $sub_initial_length != ''){
                                            if($request->initial_end_cable[$i] >= $sub_initial_length){
                                                if($request->initial_total_loss_db[$i] <= $sub_initial_max_total_loss){
                                                    $initial_remarks = 'OK';
                                                }else{
                                                    $initial_remarks = 'NOT OK';
                                                }
                                            }else{
                                                $initial_remarks = 'NOT OK';
                                            }
                                        }else{
                                            $initial_remarks = 'NOT OK';
                                        }
                                    } else {
                                        $initial_remarks = "AKTIF";
                                    }
                                }
                            
                            DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core', $request->core[$i])->update([
                                'initial_customers' => $request->initial_customers[$i],
                                'initial_end_cable' => $request->initial_end_cable[$i],
                                'initial_total_loss_db' => $request->initial_total_loss_db[$i],
                                'initial_loss_db_km' => $request->initial_loss_db_km[$i],
                                'initial_remarks' => $initial_remarks,
                                'initial_booked' => $initial_booked,
                            ]);
                        };

                        return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id'=> $request->sub_section_id]));
                    break;
                }
            break;
            case 'ms':
                switch($request->section_type){
                    case 'regular':
                        // return $request;
                        $loop = count($request->core);
                        $section = DB::table('section')->where('section_id', $request->section_id)->first();
                        $actual_length =  $section->actual_length;
                        $actual_max_total_loss =  $section->actual_max_total_loss;
                        $actual_min_total_loss =  $section->actual_min_total_loss;

                        for($i=0; $i < $loop; $i++){
                                if($request->actual_customers[$i] == null || $request->actual_customers[$i] == ''){
                                    if($actual_length != null || $actual_length != ''){
                                        if($request->actual_end_cable[$i] >= $actual_length){
                                            if($request->actual_total_loss_db[$i] <= $actual_max_total_loss){
                                                $actual_remarks = 'OK';
                                            }else{
                                                $actual_remarks = 'NOT OK';
                                            }
                                        }else{
                                            $actual_remarks = 'NOT OK';
                                        }
                                    }else{
                                        $actual_remarks = 'NOT OK';
                                    }

                                    
                                }else{
                                    $actual_remarks = 'AKTIF';
                                }
                            
                            DB::table('core')->where('section_id', $request->section_id)->where('core', $request->core[$i])->update([
                                'actual_customers' => $request->actual_customers[$i],
                                'actual_end_cable' => $request->actual_end_cable[$i],
                                'actual_total_loss_db' => $request->actual_total_loss_db[$i],
                                'actual_loss_db_km' => $request->actual_loss_db_km[$i],
                                'actual_remarks' => $actual_remarks
                            ]);
                        };

                        return redirect(route('section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id]));

                    break;
                    case 'with_sub_section':
                        $loop = count($request->core);
                        $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                        $sub_actual_length =  $sub_section->sub_actual_length;
                        $sub_actual_max_total_loss =  $sub_section->sub_actual_max_total_loss;
                        $sub_actual_min_total_loss =  $sub_section->sub_actual_min_total_loss;

                        for($i=0; $i < $loop; $i++){
                                $actual_booked = $request->actual_booked[$i];
                                if($request->actual_customers[$i] == null || $request->actual_customers[$i] == ''){
                                    if($sub_actual_length != null || $sub_actual_length != ''){
                                        if($request->actual_end_cable[$i] >= $sub_actual_length){
                                            if($request->actual_total_loss_db[$i] <= $sub_actual_max_total_loss){
                                                $actual_remarks = 'OK';
                                            }else{
                                                $actual_remarks = 'NOT OK';
                                            }
                                        }else{
                                            $actual_remarks = 'NOT OK';
                                        }
                                    }else{
                                        $actual_remarks = 'NOT OK';
                                    }
                                }else{
                                    if ($actual_booked == 'YES'){
                                        if($sub_actual_length != null || $sub_actual_length != ''){
                                            if($request->actual_end_cable[$i] >= $sub_actual_length){
                                                if($request->actual_total_loss_db[$i] <= $sub_actual_max_total_loss){
                                                    $actual_remarks = 'OK';
                                                }else{
                                                    $actual_remarks = 'NOT OK';
                                                }
                                            }else{
                                                $actual_remarks = 'NOT OK';
                                            }
                                        }else{
                                            $actual_remarks = 'NOT OK';
                                        }
                                    } else {
                                        $actual_remarks = "AKTIF";
                                    }
                                }
                            
                            
                            DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core', $request->core[$i])->update([
                                'actual_customers' => $request->actual_customers[$i],
                                'actual_end_cable' => $request->actual_end_cable[$i],
                                'actual_total_loss_db' => $request->actual_total_loss_db[$i],
                                'actual_loss_db_km' => $request->actual_loss_db_km[$i],
                                'actual_remarks' => $actual_remarks,
                                'actual_booked' => $actual_booked,
                            ]);
                        };

                        return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'sub_section_id'=> $request->sub_section_id]));
                    break;
                }
            break;
        }

    }

    public function delete($core_id){
        
        DB::table('core')->where('core_id', $core_id)->delete();

        return redirect()->back()->with('success', 'Core is successfully deleted');
    }

    public function upload_excel($project_id, $segment_id, $section_id, $sub_section_id){
        if($sub_section_id == '-'){
            return view('core.upload_excel')
            ->with('project_id', $project_id)
            ->with('segment_id', $segment_id)
            ->with('section_id', $section_id);
        }else{
            return view('core.upload_excel')
            ->with('project_id', $project_id)
            ->with('segment_id', $segment_id)
            ->with('section_id', $section_id)
            ->with('sub_section_id', $sub_section_id);
        }
    }
    
    public function store_upload_excel(Request $request){
        
        $section = DB::table('section')->where('section_id', $request->section_id)->first();

        if($file = $request->file('file')) {
            $project_id = $request->project_id;
            $segment_id = $request->segment_id;
            $section_id = $request->section_id;
            $section = DB::table('section')->where('section_id', $section_id)->first();
            
            $app_path = storage_path('app/public/excel/'.$project_id.'/'.$segment_id.'/'.$section_id);

            DB::table('core')->where('section_id', $section_id)->delete();
        
            $file_original_name = $file->getClientOriginalName();
            $original_filename = pathinfo($file_original_name, PATHINFO_FILENAME);
            $filename = preg_replace('/[^A-Za-z0-9_.]+/', '_', $original_filename);
            $extension = pathinfo($file_original_name, PATHINFO_EXTENSION);

            $file_upload = $filename ."_". Str::random(10).".".$extension;

            $file->move($app_path, $file_upload);
            
            $spreadsheet = IOFactory::load(storage_path('app/public/excel/'.$project_id.'/'.$segment_id.'/'.$section_id.'/'.$file_upload));
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 4, $row_limit );
            $column_range = range( 'A', $column_limit );
            $startcount = 4;
            $data = array();

            $sub_sections = DB::table('sub_section')->where('section_id', $section_id)->get();

            foreach ( $row_range as $row ) { 
                foreach($sub_sections as $sub_section){
                    if($sub_section->sub_section_id == $sheet->getCell( 'A' . $row )->getValue()){
                        $length = $sub_section->sub_initial_length;
                        $min_total_loss = $sub_section->sub_initial_min_total_loss;
                        $max_total_loss = $sub_section->sub_initial_max_total_loss;

                        $core = $sheet->getCell( 'B' . $row )->getValue();
                        $customers = $sheet->getCell( 'C' . $row )->getValue();
                        $end_cable = $sheet->getCell( 'D' . $row )->getValue();
                        $total_loss_db = $sheet->getCell( 'E' . $row )->getValue();
                        $loss_db_km = $sheet->getCell( 'F' . $row )->getValue();
                        $booked = $sheet->getCell( 'G' . $row )->getValue();
                        
                        if($customers == null || $customers == " "){
                            $booked = "NO";
                            if($end_cable >= $length ){
                                if( $total_loss_db <= $max_total_loss ){
                                    $remarks = "OK";
                                }else{
                                    $remarks = "NOT OK";
                                }
                            }else{
                                $remarks = 'NOT OK';
                            }
                        }else{
                            
                            
                            if (strcasecmp($booked, 'BOOKED') == 0) {
                                $booked = "YES";
                                if($end_cable >= $length ){
                                    if( $total_loss_db <= $max_total_loss ){
                                        $remarks = "OK";
                                    }else{
                                        $remarks = "NOT OK";
                                    }
                                }else{
                                    $remarks = 'NOT OK';
                                }
                            } else {
                                $booked = "NO";
                                $remarks = "AKTIF";
                            }
                        }

                        DB::table('core')->insert([
                            'project_id' => $project_id,
                            'segment_id' => $segment_id,
                            'section_id' => $section_id,
                            'sub_section_id' => $sub_section->sub_section_id,
                            'core' => $core,
                            'initial_customers' => $customers,
                            'initial_total_loss_db' => $total_loss_db,
                            'initial_end_cable' => $end_cable,
                            'initial_loss_db_km' => $loss_db_km,
                            'initial_remarks' => $remarks,
                            'initial_booked' => $booked,
                        ]);        
                    }
                }
                $startcount++;
            }

            return redirect()->back()->with('success', 'Great! Data has been successfully uploaded.');
    

        }else{
            return redirect()->back()->with('error', 'File is not found !!.');
        }

    }

    



}
