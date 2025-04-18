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
    public function index($id)
    {
        
    }
    
    public function show($project_id, $route_id, $segment_id, $section_id, $sub_section_id, $core)
    {
        $project = DB::table('project')->where('project_id', $project_id)->first();
        $segment = DB::table('segment')->where('segment_id', $segment_id)->first();
        $section = DB::table('section')->where('project_id',$project_id)->where('route_id',$route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->first();
        $core = DB::table('core')->where('core', $core)->first();
        
        $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->first();
        
        if(file_exists(storage_path('app/public/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id.'/'.$sub_section_id.'/'.$core->core.'.sor.json'))){
            $json = file_get_contents(storage_path('app/public/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id.'/'.$sub_section_id.'/'.$core->core.'.sor.json'));
            $json_data = json_decode($json,true); 

            $section = DB::table('section')->where('section_id', $section_id)->first();

            return view('core.show')
            ->with('project',$project)
            ->with('segment',$segment)
            ->with('section',$section)
            ->with('sub_section',$sub_section)
            ->with('sub_section_id',$sub_section_id)
            ->with('core',$core)
            ->with('detail', $json_data);

        }else{
            return redirect()->back()->with('error', 'Sor not found'.storage_path('app/public/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id.'/'.$core->core.'.sor.json'));
        }   
        
    }

    public function create($project_id, $route_id, $segment_id, $section_id, $customer_id, $type_id, $sub_section_id, $input_type)
    {

        switch($input_type){
            case 'setup':
                return view('core.create')
                ->with('project_id',$project_id)
                ->with('route_id', $route_id)
                ->with('segment_id', $segment_id)
                ->with('section_id', $section_id)
                ->with('customer_id', $customer_id)
                ->with('type_id', $type_id)
                ->with('sub_section_id', $sub_section_id)
                ->with('input_type', $input_type);
        
            break;

            case 'add':
                return view('core.create')
                ->with('project_id',$project_id)
                ->with('route_id', $route_id)
                ->with('segment_id', $segment_id)
                ->with('section_id', $section_id)
                ->with('customer_id', $customer_id)
                ->with('type_id', $type_id)
                ->with('sub_section_id', $sub_section_id)
                ->with('input_type', $input_type);
                
            break;

            default:
                return redirect()->back()->with('error', 'Cannot find input type !!');
        }
        
    
    }


    public function store(Request $request)
    {
        $input_type = $request->input_type;
        $type_id = $request->type_id == '-' ? '' : $request->type_id;   

        switch($input_type){
            case 'regular':
                    // DB::table('core')->where('section_id', $request->section_id)->delete();
                
                    // $cores =  $request->core_capacity;
            
                    // for ($i=1; $i <= $cores; $i++) { 
                    //     DB::table('core')->insert([
                    //         'project_id' => $request->project_id,
                    //         'route_id'=> $request->route_id,
                    //         'segment_id' => $request->segment_id,
                    //         'section_id' => $request->section_id,
                    //         'core' => $i,
                    //         'initial_remarks' => 'NOT OK',
                    //         'actual_remarks' => 'NOT OK'
                    //     ]); 
                        
                    // }
            
                    // DB::table('section')->where('section_id', $request->section_id)->update([
                    //     'core_capacity' => $request->core_capacity,
                    // ]);
            
                    // return redirect(route('section.show', ['project_id'=>$request->project_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id]));
                    
                break;
            case 'range':
                    DB::table('core')
                    ->where('project_id', $request->project_id)
                    ->where('route_id', $request->route_id)
                    ->where('segment_id', $request->segment_id)
                    ->where('section_id', $request->section_id)
                    ->where('sub_section_id', $request->sub_section_id)
                    ->delete();
                    
                    $from = (int) $request->from; // Convert to integer to ensure looping works correctly
                    $to = (int) $request->to; 

                    $customer_name = DB::table('customer')->where('customer_id', $request->customer_id)->get()->first()->customer_name;
                    
                    if($request->customer_id == 000){
                        $status = 'IDLE';
                    }else{
                        $status = 'BOOKED';
                    }

                    for($i = $from; $i <= $to; $i++){
                        DB::table('core')->insert([
                            'project_id'=> $request->project_id,
                            'route_id'=> $request->route_id,
                            'segment_id'=> $request->segment_id,
                            'section_id'=> $request->section_id,
                            'customer_id'=> $request->customer_id,
                            'type_id'=> $type_id,
                            'sub_section_id'=> $request->sub_section_id,
                            'core'=> str_pad($i, 3, '0', STR_PAD_LEFT),
                            'initial_customers'=> $customer_name,
                            'initial_remarks' => 'NOT OK',
                            'actual_remarks' => 'NOT OK',
                            'status' => $status
                        ]);
                    }
                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id' => $request->customer_id, 'type_id' => $request->type_id, 'sub_section_id' => $request->sub_section_id]));
                break;
            case 'custom':
                    DB::table('core')
                    ->where('project_id', $request->project_id)
                    ->where('route_id', $request->route_id)
                    ->where('segment_id', $request->segment_id)
                    ->where('section_id', $request->section_id)
                    ->where('sub_section_id', $request->sub_section_id)
                    ->delete();
                    
                    $loop = count($request->core);
                    $customer_name = DB::table('customer')->where('customer_id', $request->customer_id)->get()->first()->customer_name;

                    if($request->customer_id == 000){
                        $status = 'IDLE';
                    }else{
                        $status = 'BOOKED';
                    }

                    for($i = 0; $i < $loop; $i++){
                        DB::table('core')->insert([
                            'project_id'=> $request->project_id,
                            'route_id'=> $request->route_id,
                            'segment_id'=> $request->segment_id,
                            'section_id'=> $request->section_id,
                            'customer_id'=> $request->customer_id,
                            'type_id'=> $type_id,
                            'sub_section_id'=> $request->sub_section_id,
                            'core'=> $request->core[$i],
                            'initial_customers'=> $customer_name,
                            'initial_remarks' => 'NOT OK',
                            'actual_remarks' => 'NOT OK',
                            'status' => $status,
                        ]);
                    }
                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id' => $request->customer_id, 'type_id' => $request->type_id, 'sub_section_id' => $request->sub_section_id]));

                break;
            case 'add':
                    if (in_array(null, $request->core, true)) {
                        return redirect()->back()->with('error', 'Core cannot be empty !');
                    }
                    
                    if (count($request->core) !== count(array_unique($request->core))) {
                        return redirect()->back()->with('error', 'Core values must be unique!');
                    }

                    $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                    $customer_name = DB::table('customer')->where('customer_id', $sub_section->customer_id)->get()->first()->customer_name;
                    
                    if($sub_section->customer_id === '000'){
                        $status = 'IDLE';
                    }else{
                        $status = 'BOOKED';
                    }
                    
                    $loop = count($request->core);
                    for($i = 0; $i < $loop; $i++){

                        $sub_initial_length = $sub_section->sub_initial_length;
                        $sub_initial_max_total_loss = $sub_section->sub_initial_max_total_loss;
                        
                        if($sub_initial_length != null || $sub_initial_length != ''){
                            if($request->length[$i] >= $sub_initial_length){
                                if($request->total_loss_db[$i] <= $sub_initial_max_total_loss){
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

                        

                        DB::table('core')->insert([
                            'project_id'=> $request->project_id,
                            'route_id'=> $request->route_id,
                            'segment_id'=> $request->segment_id,
                            'section_id'=> $request->section_id,
                            'customer_id'=> $sub_section->customer_id,
                            'type_id'=> $sub_section->type_id,
                            'sub_section_id'=> $request->sub_section_id,
                            'core'=> $request->core[$i],
                            'initial_customers'=> $customer_name,
                            'initial_end_cable'=> $request->length[$i],
                            'initial_total_loss_db'=> $request->total_loss_db[$i],
                            'initial_loss_db_km'=> $request->loss_db_km[$i],
                            'initial_remarks' => $initial_remarks,
                            'actual_remarks' => 'NOT OK',
                            'status' => $status,
                        ]);
                        
                    }
                    
                    return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id' => $request->customer_id, 'type_id' => $request->type_id, 'sub_section_id' => $request->sub_section_id]));
                    
                break;
            default:
                return redirect()->back()->with('error', 'cannot find input type');
        }

        // return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id' => $request->customer_id, 'type_id' => '-', 'sub_section_id' => $request->sub_section_id]));
        
    }

    public function edit($project_id, $route_id, $segment_id, $section_id, $customer_id, $type_id, $sub_section_id)
    {
        return view('core.edit')
        ->with('project_id',$project_id)
        ->with('route_id',$route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id)
        ->with('customer_id', $customer_id)
        ->with('type_id', $type_id)
        ->with('sub_section_id', $sub_section_id);
    
    }

    public function update(Request $request)
    {

        switch(auth()->user()->role){
            case 'engineering':
                $loop = count($request->core);
                $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                $sub_initial_length =  $sub_section->sub_initial_length;
                $sub_initial_max_total_loss =  $sub_section->sub_initial_max_total_loss;
                $sub_initial_min_total_loss =  $sub_section->sub_initial_min_total_loss;

                for($i=0; $i < $loop; $i++){

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

                    $actual_customers = DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core', $request->core[$i])->get()->first()->actual_customers;
                    
                    if($actual_customers == '' || $actual_customers == null){
                        // ACTUAL NULL
                        if($request->initial_customers[$i] == '' || $request->initial_customers[$i] == null){
                            // ACTUAL & INITIAL NULL
                            $status = 'IDLE';
                        }else{
                            // ACTUAL NULL & INITIAL ADA
                            if($sub_section->customer_id === '000'){
                                // ACTUAL NULL & INITIAL TRIASMITRA
                                $status = 'IDLE';
                            }else{
                                // ACTUAL NULL & INITIAL BUKAN TRIASMITRA
                                $status = 'BOOKED';
                            }
                        }
                    }else{
                        // ACTUAL ADA
                        if($actual_customers == $request->initial_customers[$i]){
                            // ACTUAL & INITIAL SAMA
                            $status = 'ACTIVE';
                        }else{
                            // ACTUAL & INITIAL TIDAK SAMA
                            if($request->initial_customers[$i] == '' || $request->initial_customers[$i] == null){
                                // ACTUAL ADA & INITIAL TIDAK ADA
                                $status = 'USED';
                            }else{
                                 // ACTUAL ADA & INITIAL ADA
                                if($sub_section->customer_id === '000'){
                                    // ACTUAL ADA & INITIAL TRIASMITRA
                                    $status = 'USED';
                                }else{
                                    // ACTUAL ADA & INITIAL BUKAN TRIASMITRA
                                    $status = 'MISMATCH';
                                }
                            }
                        }
                    }
                        
                    DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core', $request->core[$i])->update([
                        'initial_customers' => $request->initial_customers[$i],
                        'initial_end_cable' => $request->initial_end_cable[$i],
                        'initial_total_loss_db' => $request->initial_total_loss_db[$i],
                        'initial_loss_db_km' => $request->initial_loss_db_km[$i],
                        'initial_remarks' => $initial_remarks,
                        'status' => $status
                    ]);

                    if($request->type_id != '-' || $request->type_id != null){

                        DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('customer_id', $request->customer_id)->where('core', $request->core[$i])->update([
                            'initial_customers' => $request->initial_customers[$i],
                            'status' => $status
                        ]);
                    }

                };

                return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id'=> $request->customer_id, 'type_id'=> $request->type_id, 'sub_section_id'=> $request->sub_section_id]));
            break;
            case 'ms':
                $loop = count($request->core);
                $sub_section = DB::table('sub_section')->where('sub_section_id', $request->sub_section_id)->first();
                $sub_actual_length =  $sub_section->sub_actual_length;
                $sub_actual_max_total_loss =  $sub_section->sub_actual_max_total_loss;
                $sub_actual_min_total_loss =  $sub_section->sub_actual_min_total_loss;

                for($i=0; $i < $loop; $i++){
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


                    $initial_customer = DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core', $request->core[$i])->get()->first()->initial_customers;
                    
                    if($initial_customer == '' || $initial_customer == null){
                        // INITIAL NULL
                        if($request->actual_customers[$i] == '' || $request->actual_customers[$i] == null){
                            // INITIAL NULL & ACTUAL NULL
                            $status = 'IDLE';
                        }else{
                            // INTIAL NULL DAN ACTUAL ADA 
                            $status = 'USED';
                        }
                    }else{
                        // INITIAL ADA
                        if($initial_customer == $request->actual_customers[$i]){
                            // INITIAL & ACTUAL SAMA
                            $status = 'ACTIVE';
                        }else{
                            // INTIAL & ACTUAL TIDAK SAMA 
                            if($request->actual_customers[$i] == '' || $request->actual_customers[$i] == null){
                                // INITIAL ADA & ACTUAL NULL
                                if($sub_section->customer_id === '000'){
                                    // INITIAL TRIASMITRA & ACTUAL NULL
                                    $status = 'IDLE';
                                }else{
                                    // INITIAL BUKAN TRIASMITRA & ACTUAL NULL
                                    $status = 'BOOKED';
                                }
                            }else{
                                // INITIAL ADA & ACTUAL ADA
                                $status = 'MISMATCH';
                            }
                        }
                    }
                    
                    DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('core', $request->core[$i])->update([
                        'actual_customers' => $request->actual_customers[$i],
                        'actual_end_cable' => $request->actual_end_cable[$i],
                        'actual_total_loss_db' => $request->actual_total_loss_db[$i],
                        'actual_loss_db_km' => $request->actual_loss_db_km[$i],
                        'actual_remarks' => $actual_remarks,
                        'status' => $status,
                    ]);

                    if($request->type_id != '-' || $request->type_id != null){

                        DB::table('core')->where('sub_section_id', $request->sub_section_id)->where('customer_id', $request->customer_id)->where('core', $request->core[$i])->update([
                            'actual_customers' => $request->actual_customers[$i],
                            'status' => $status
                        ]);
                    }
                };

                return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id'=> $request->customer_id, 'type_id'=> $request->type_id, 'sub_section_id'=> $request->sub_section_id]));
            
            break;
            default :
                return redirect(route('sub_section.show', ['project_id'=>$request->project_id, 'route_id'=>$request->route_id, 'segment_id'=> $request->segment_id, 'section_id' => $request->section_id, 'customer_id'=> $request->customer_id, 'type_id'=> $request->type_id, 'sub_section_id'=> $request->sub_section_id]))->with('error', 'Cannot find user role !');
        }

    }

    public function delete($core_id){
        
        DB::table('core')->where('core_id', $core_id)->delete();

        return redirect()->back()->with('success', 'Core is successfully deleted');
    }
    
    public function upload_excel($project_id, $route_id, $segment_id, $section_id){
        return view('core.upload_excel')
        ->with('project_id', $project_id)
        ->with('route_id', $route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id);
    }
    
    public function store_upload_excel(Request $request){
        

        $project_id = $request->project_id;
        $route_id = $request->route_id;
        $segment_id = $request->segment_id;
        $section_id = $request->section_id;

        if($file = $request->file('file')) {
            
            $app_path = storage_path('app/public/excel/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id);
            $file_original_name = $file->getClientOriginalName();
            $original_filename = pathinfo($file_original_name, PATHINFO_FILENAME);
            $filename = preg_replace('/[^A-Za-z0-9_.]+/', '_', $original_filename);
            $extension = pathinfo($file_original_name, PATHINFO_EXTENSION);

            $file_upload = $filename ."_". Str::random(10).".".$extension;

            $file->move($app_path, $file_upload);
            
            $spreadsheet = IOFactory::load(storage_path('app/public/excel/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id.'/'.$file_upload));
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit = $sheet->getHighestRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'A', $column_limit );
            $startcount = 4;
            $data = array();


            foreach ( $row_range as $row ) { 

                $core = str_pad(trim($sheet->getCell('A' . $row)->getValue()), 3, '0', STR_PAD_LEFT);
                $customers = trim($sheet->getCell('B' . $row)->getValue());
                $type_id = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $end_cable = trim($sheet->getCell('D' . $row)->getValue());
                $total_loss_db = trim($sheet->getCell('E' . $row)->getValue());
                $loss_db_km = trim($sheet->getCell('F' . $row)->getValue());

                $customer = DB::table('customer')->where('customer_name', $customers)->first();

                if (!$customer) {
                    return redirect()->back()->with('error', 'Core '.$core.' Customer '.$sheet->getCell('B' . $row)->getValue().' not found.');
                }

                $customer_id = $customer->customer_id;

                $record = DB::table('core')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->where('section_id', $section_id)
                ->where('customer_id', $customer_id)
                ->where('type_id', $type_id)
                ->where('core', $core)
                ->get()
                ->first();

                if (!$record) {
                    return redirect()->back()->with('error', 'Core '.$core.' is not matching !!');
                }

                echo $project_id.'__'.$route_id.'__'.$segment_id.'__'.$section_id.'__'.$customer_id.'__'.$type_id.'__'.$core;
                echo '</br>';
                
                $startcount++;
            }

            foreach ( $row_range as $row ) { 

                $core = str_pad(trim($sheet->getCell('A' . $row)->getValue()), 3, '0', STR_PAD_LEFT);
                $customers = trim($sheet->getCell('B' . $row)->getValue());
                $type_id = trim($sheet->getCell('C' . $row)->getValue() ?? '');
                $end_cable = trim($sheet->getCell('D' . $row)->getValue());
                $total_loss_db = trim($sheet->getCell('E' . $row)->getValue());
                $loss_db_km = trim($sheet->getCell('F' . $row)->getValue());

                $customer = DB::table('customer')->where('customer_name', $customers)->first();

                if (!$customer) {
                    return redirect()->back()->with('error', 'Core '.$core.' Customer '.$customers.' not found.');
                }

                $customer_id = $customer->customer_id;

                $record = DB::table('core')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->where('section_id', $section_id)
                ->where('customer_id', $customer_id)
                ->where('type_id', $type_id)
                ->where('core', $core)
                ->get()
                ->first();

                $sub_section_id = $record->sub_section_id;

                $length = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first()->sub_initial_length;
                $min_total_loss = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first()->sub_initial_min_total_loss;
                $max_total_loss = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first()->sub_initial_max_total_loss;
                
                if($end_cable >= $length ){
                    if( $total_loss_db <= $max_total_loss ){
                        $remarks = "OK";
                    }else{
                        $remarks = "NOT OK";
                    }
                }else{
                    $remarks = 'NOT OK';
                }

                if ($customers == 'TRIASMITRA') {
                    $status = 'IDLE';
                } else {
                    $status = 'BOOKED';
                }

                DB::table('core')
                ->where('project_id', $project_id)
                ->where('route_id', $route_id)
                ->where('segment_id', $segment_id)
                ->where('section_id', $section_id)
                ->where('customer_id', $customer_id)
                ->where('type_id', $type_id)
                ->where('sub_section_id', $sub_section_id)
                ->where('core', $core)
                ->update([
                    'initial_customers' => $customers,
                    'initial_total_loss_db' => $total_loss_db,
                    'initial_end_cable' => $end_cable,
                    'initial_loss_db_km' => $loss_db_km,
                    'initial_remarks' => $remarks,
                    'status' => $status,
                ]);  

                $startcount++;
            }

            return redirect(route('section.show', ['project_id' => $project_id,'route_id' => $route_id, 'segment_id' => $segment_id, 'section_id' => $section_id ]))->with('success', 'Great! Data has been successfully uploaded.');
    

        }else{
            return redirect()->back()->with('error', 'File is not found !!.');
        }
        
    }

    
    public function update_database(){
        return 'hello world';
    }


}
