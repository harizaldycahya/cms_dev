<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

use ZipArchive;

class SorController extends Controller
{
    
    public function index($project_id, $route_id, $segment_id, $section_id, $sub_section_id)
    {
        if($sub_section_id == '-'){
            return view('sor.index')
            ->with('project_id', $project_id)
            ->with('route_id', $route_id)
            ->with('segment_id', $segment_id)
            ->with('section_id', $section_id);
        }else{
            return view('sor.index')
            ->with('project_id', $project_id)
            ->with('route_id', $route_id)
            ->with('segment_id', $segment_id)
            ->with('section_id', $section_id)
            ->with('sub_section_id', $sub_section_id);
        }
    }

    public function create($project_id, $route_id, $segment_id, $section_id, $customer_id, $type_id, $sub_section_id)
    { 
        return view('sor.create')
        ->with('project_id',$project_id)
        ->with('route_id',$route_id)
        ->with('segment_id', $segment_id)
        ->with('section_id', $section_id)
        ->with('customer_id', $customer_id)
        ->with('type_id', $type_id)
        ->with('sub_section_id', $sub_section_id);
    }

    public function store(Request $request){

        $project_id = $request->project_id;
        $route_id = $request->route_id;
        $segment_id = $request->segment_id;
        $section_id = $request->section_id;
        $customer_id = $request->customer_id;
        $type_id = $request->type_id;
        $sub_section_id = $request->sub_section_id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
    
            $allowedMimeTypes = [
                'application/zip',
            ];

            if (!in_array($mimeType, $allowedMimeTypes)) {
                return redirect()->back()->with('error', 'Error, System only accepts ZIP files !!');
            } else {
                $currentDateTime = date('YmdHis');
                $nik = auth()->user()->nik;
                $randomNumber = mt_rand(100000, 999999);
                
                $request_id = $nik.'.'.$project_id.'.'.$route_id.'.'.$segment_id.'.'.$section_id.'.'.$sub_section_id.'.'.$currentDateTime. '.' . $randomNumber;
                $app_path = storage_path('app/public/sor_request/'.$nik.'/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id.'/'.$sub_section_id.'/'.$request_id);
                $file_original_name = $file->getClientOriginalName();
                $extension = pathinfo($file_original_name, PATHINFO_EXTENSION);
                
                $file_upload = $request_id.".".$extension;
                if ($file->move($app_path, $file_upload)) {
                    $sor_request = DB::table('sor_request')->insert([
                        'request_id' => $request_id,
                        'requestor' => $nik,
                        'project_id' => $request->project_id,
                        'route_id' => $request->route_id,
                        'segment_id' => $request->segment_id,
                        'section_id' => $request->section_id,
                        'sub_section_id' => $request->sub_section_id,
                        'date' => now(),
                        'status' => 'PROCESS',
                    ]);
                    
                    return redirect()->route('section.show', ['project_id'=>$request->project_id ,'route_id'=>$request->route_id, 'segment_id'=>$request->segment_id, 'section_id'=>$request->section_id])->with('success', 'Data is successfully updated !');
                    
                } else {
                    return redirect()->back()->with('error', 'Upload failed');
                }
                
            }
           
        } else {
            return redirect()->back()->with('error', 'Error, File is not found !!');
        }
    }

    public function show($request_id){
        $request = DB::table('sor_request')
        ->where('request_id', $request_id)
        ->first();

        return view('sor.show')
        ->with('request', $request);
    }

    public function process($request_id){
        $sor_request = DB::table('sor_request')->where('request_id', $request_id)->first();
        
        
        $file_path = storage_path('app/public/sor_request/'.$sor_request->requestor.'/'.$sor_request->project_id.'/'.$sor_request->route_id.'/'.$sor_request->segment_id.'/'.$sor_request->section_id.'/'.$sor_request->sub_section_id.'/'.$sor_request->request_id.'/'.$sor_request->request_id);
        
        if (file_exists($file_path . '.zip')) {
            $zip = new ZipArchive();
            if ($zip->open($file_path . '.zip') === TRUE) {
                $folderPath = storage_path('app/public/sor_request/'.$sor_request->requestor . '/' . $sor_request->project_id .'/'.$sor_request->route_id. '/' . $sor_request->segment_id . '/' . $sor_request->section_id.'/'.$sor_request->sub_section_id.'/'. $sor_request->request_id);

                $zip->extractTo($folderPath);
                $zip->close();

                $files = glob($folderPath . '/*.sor');
                foreach ($files as $file) {
                    $fileName = basename($file);
                    $path = storage_path('app/public/'.$sor_request->project_id.'/'.$sor_request->route_id.'/'.$sor_request->segment_id.'/'.$sor_request->section_id.'/'.$sor_request->sub_section_id.'/'.$fileName.'.sor');
                    
                    // $output = exec('cd ./jsotdr/lib/ && node test_drive.js '.$folderPath.'/'.$fileName);
                    $escapedFolderPath = escapeshellarg($folderPath);
                    $escapedFileName = escapeshellarg($fileName);
                    exec('cd ./jsotdr/lib/ && node test_drive.js ' . $escapedFolderPath . '/' . $escapedFileName, $output, $returnValue);
                    // exec('cd ./jsotdr/lib/ && node test_drive.js '.$folderPath.'/'.$fileName, $output, $returnValue);
                    if ($returnValue != 0){
                        return redirect()->back()->with('error', 'Error! File SOR is not valid !!');
                    }
                }
                
                $files = glob($folderPath . '/*.json');
                foreach ($files as $file) {
                    $fileNames[] = basename($file);

                    // Start Process JSON
                        if(file_exists($file)){
                            $json = file_get_contents($file);
                            $json_data = json_decode($json,true);
                            $total_loss = $json_data["KeyEvents"]["Summary"]["total loss"];
                            if ($json_data["KeyEvents"]["Summary"]["ORL finish"] != 0) {
                                $loss_db_km = $json_data["KeyEvents"]["Summary"]["total loss"] / $json_data["KeyEvents"]["Summary"]["ORL finish"];
                            } else {
                                $loss_db_km = 0;
                            }

                            DB::table('draf_core')->insert([
                                    'request_id' => $request_id,
                                    'project_id' => $sor_request->project_id,
                                    'route_id' => $sor_request->route_id,
                                    'segment_id' => $sor_request->segment_id,
                                    'section_id' => $sor_request->section_id,
                                    'sub_section_id' => $sor_request->sub_section_id,
                                    'core' => str_pad(preg_replace('/[^0-9]/', '', $json_data["GenParams"]["fiber ID"]), 3, '0', STR_PAD_LEFT),
                                    'total_loss_db' => $json_data["KeyEvents"]["Summary"]["total loss"],
                                    'end_cable' => $json_data["KeyEvents"]["Summary"]["ORL finish"],
                                    'loss_db_km' => $loss_db_km,
                                    'tanggal' => $json_data["FxdParams"]["date/time"]
                            ]);
                        }
                    // End Process JSON
                }

                $files = glob($folderPath . '/*');
                foreach ($files as $file) {
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    if ($extension !== 'sor' && $extension !== 'json') {
                        unlink($file);
                    }
                }

                return redirect()->back()->with('success', 'Great! Data has been successfully processed.');

            } else {
                return redirect()->back()->with('error', 'File zip is not found.');
               
            }

        } else {
            return redirect()->back()->with('error', 'File not found.'.storage_path('app/public/sor_request/'.$sor_request->requestor.'/'.$sor_request->project_id.'/'.$sor_request->route_id.'/'.$sor_request->segment_id.'/'.$sor_request->section_id.'/'.$sor_request->sub_section_id.'/'.$sor_request->request_id.'/'.$sor_request->request_id));
            // return redirect()->back()->with('error', 'File not found.'.$sor_request->project_id);
        }
    }
   
    public function approval($request_id, $status){
        set_time_limit(0);

        
        $sor_request = DB::table('sor_request')->where('request_id', $request_id)->first();
        $project_id = $sor_request->project_id;
        $route_id = $sor_request->route_id;
        $segment_id = $sor_request->segment_id;
        $section_id = $sor_request->section_id;
        $sub_section_id = $sor_request->sub_section_id;

        $cores = DB::table('core')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->where('sub_section_id', $sub_section_id)->get();
        $path = storage_path('app/public/sor_request/' . $sor_request->requestor . '/' . $sor_request->project_id .'/'.$sor_request->route_id. '/' . $sor_request->segment_id . '/' . $sor_request->section_id .'/' . $sor_request->sub_section_id . '/' . $sor_request->request_id);
        $live_folder = storage_path('app/public/' . $sor_request->project_id .'/'.$sor_request->route_id. '/' . $sor_request->segment_id . '/' . $sor_request->section_id. '/' . $sor_request->sub_section_id);
        
        $draf_cores = DB::table('draf_core')->where('request_id', $request_id)->get();

        if($status == 'APPROVE'){

            foreach($draf_cores as $draf_core){
                foreach($cores as $core){
                    if ($draf_core->core == $core->core) {

                            $sub_section = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->get()->first();
                            // Get all .json files in the directory
                            $json_files = glob($path . '/*.json');

                            // Loop through the .json files
                            foreach ($json_files as $json_file) {
                                // Check if the current item is a file
                                if (is_file($json_file)) {
                                   
                                    // Read the .json file content
                                    $json = file_get_contents($json_file);
                                    $json_data = json_decode($json, true);
                                    $fiber_id = preg_replace('/[^0-9]/', '', $json_data["GenParams"]["fiber ID"]);
                                    
                                    // Check if the fiber_id matches the core value
                                    if ($fiber_id == $core->core) {
                                        
                                        // Define the new file name using core_id
                                        $new_file_name = str_pad($fiber_id, 3, '0', STR_PAD_LEFT);
                                        
                                        // Define paths for the .json and .sor files
                                        $new_json_file = $path . '/' . $new_file_name . '.sor.json';
                                        $sor_file = preg_replace('/\.json$/', '', $json_file);
                                        $new_sor_file = $path . '/' . $new_file_name . '.sor';

                                        // Rename the .json file
                                        if (rename($json_file, $new_json_file)) {
                                            echo "File renamed to " . $new_file_name . ".json<br>";
                                        } else {
                                            echo "Failed to rename file " . basename($json_file) . "<br>";
                                        }

                                        // Rename the corresponding .sor file if it exists
                                        if (is_file($sor_file)) {
                                            if (rename($sor_file, $new_sor_file)) {
                                                echo "File renamed to " . $new_file_name . ".sor<br>";
                                            } else {
                                                echo "Failed to rename file " . basename($sor_file) . "<br>";
                                            }
                                        } else {
                                            echo "Corresponding .sor file not found for " . basename($json_file) . "<br>";
                                        }

                                        // Ensure the live folder and its parent directories exist
                                        if (!file_exists($live_folder)) {
                                            mkdir($live_folder, 0755, true);  // Creates the directory structure with 0755 permissions
                                        }

                                        // Move the .json file to the live folder
                                        $live_json_file = $live_folder . '/' . basename($new_json_file);
                                        
                                        if (rename($new_json_file, $live_json_file)) {

                                            echo "Moved " . basename($new_json_file) . " to live folder<br>";
                                        } else {
                                            echo "Failed to move " . basename($new_json_file) . " to live folder<br>";
                                        }

                                        // ^correct
                                        // Move the .sor file to the live folder if it exists
                                        if (is_file($new_sor_file)) {
                                            $live_sor_file = $live_folder . '/' . basename($new_sor_file);
                                            if (rename($new_sor_file, $live_sor_file)) {
                                                if($draf_core->end_cable != null || $draf_core->end_cable != ''){
                                                    if($draf_core->end_cable >= $sub_section->sub_actual_length ){
                                                        if($draf_core->total_loss_db <= $sub_section->sub_actual_max_total_loss){
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

                                                $update_sor = DB::table('core')->where('sub_section_id',$sub_section_id)->where('core', $core->core)->update([
                                                    'actual_end_cable' => $draf_core->end_cable,
                                                    'actual_total_loss_db' => $draf_core->total_loss_db,
                                                    'actual_loss_db_km' => $draf_core->loss_db_km,
                                                    'notes' => $draf_core->tanggal,
                                                    'actual_remarks' => $actual_remarks
                                                    // 'actual_remarks' => "TEST"
                                                ]);
                                                
                                                if($update_sor){
                                                    echo 'data berhasil masuk-'.$core->core_id.'-testing';
                                                }else{
                                                    echo 'data gagal masuk';
                                                }
                                                echo "Moved " . basename($new_sor_file) . " to live folder<br>";
                                            } else {
                                                echo "Failed to mox`ve " . basename($new_sor_file) . " to live folder<br>";
                                            }
                                        }

                                    }
                                }
                            }
                    }
                    
                }
            }
            

            DB::table('sor_request')->where('request_id', $request_id)->update([
                'status' => 'APPROVE',
            ]);

            return redirect()
            ->route('section.show', ['project_id'=>$sor_request->project_id, 'route_id'=>$sor_request->route_id, 'segment_id'=> $sor_request->segment_id, 'section_id' => $sor_request->section_id])
            ->with('success', 'Request is successfully approved !');   

        }else{
            DB::table('sor_request')->where('request_id', $request_id)->update([
                'status' => 'REJECT',
            ]);

            return redirect()
            ->route('section.show', ['project_id'=>$sor_request->project_id, 'route_id'=>$sor_request->route_id, 'segment_id'=> $sor_request->segment_id, 'section_id' => $sor_request->section_id])
            ->with('success', 'Request is successfully rejected !');   
            
        }
    }

    public function summary($project_id,$route_id, $segment_id, $section_id, $sub_section_id){

        function findIndex($array, $key, $value) {
            foreach ($array as $index => $item) {
                if (isset($item[$key]) && $item[$key] == $value) {
                    return $index; // Return the index if the key-value pair is found
                }
            }
            return null; // Return null if not found
        }

        $section = DB::table('section')->where('project_id', $project_id)->where('route_id', $route_id)->where('segment_id', $segment_id)->where('section_id', $section_id)->first();

        $path_raw = 'app/public/'.$project_id.'/'.$route_id.'/'.$segment_id.'/'.$section_id.'/'.$sub_section_id.'/';
        $path = storage_path($path_raw);

        if (File::exists($path)) {
            $files = File::files($path);

            $jsonSorFiles = array_filter($files, function ($file) {
                return substr($file->getFilename(), -9) === '.sor.json';
            });

            foreach ($jsonSorFiles as $file) {
                $json = file_get_contents(storage_path($path_raw.$file->getFilename()));
                $json_data = json_decode($json,true); 

                array_shift($json_data["KeyEvents"]);

                foreach ($json_data['KeyEvents'] as $eventName => $eventData) {
                    if ($eventName !== "Summary") {
                        // $eventData['fiber_id'] = $json_data['GenParams']['fiber ID'];
                        $eventData['fiber_id'] = preg_replace('/[^0-9]/', '', $json_data['GenParams']['fiber ID']);
                        $eventData['wavelength'] = $json_data['GenParams']['wavelength'];
                        $eventData['total loss'] = $json_data['KeyEvents']['Summary']['total loss'];
                        $eventData['loss end'] = $json_data['KeyEvents']['Summary']['loss end'];
                        $allData[] = $eventData;
                    }
                }
            }

            // Sort the data by distance in ascending order
            // usort($allData, function ($a, $b) {
            //     return $a['distance'] <=> $b['distance'];
            // });
            // Ensure $allData is defined
            $allData = $allData ?? [];  // This ensures $allData is initialized as an empty array if it's null or undefined

            // Check if $allData is an array and not empty
            if (is_array($allData) && !empty($allData)) {
                usort($allData, function ($a, $b) {
                    return $a['distance'] <=> $b['distance'];
                });
            } else {
                // Return or echo an error message
                return redirect()->back()->with('error', 'Error, Data is empty.');
            }

            // Group distances based on their values with a difference less than 0.1
            $output = [];
            $group = []; 
            $prevDistance = null;

            foreach ($allData as $item) {
               $distance = $item['distance'];
                $spliceLoss = $item['splice loss'];
                $fiberId = $item['fiber_id'];
                $wavelength = $item['wavelength'];
                $total_loss = $item['total loss'];
                $loss_end = $item['loss end'];

                if ($prevDistance === null || $distance - $prevDistance <= 0.1) {
                    // Add the distance, splice loss, fiber ID, and wavelength to the current group if it's the first distance or the difference is less than 0.1
                    $group['distances'][] = $distance;
                    $group['splice_losses'][] = $spliceLoss;
                    $group['fiber_ids'][] = $fiberId;
                    $group['wavelengths'][] = $wavelength;
                    $group['total_loss'][] = $total_loss;
                    $group['loss_end'][] = $loss_end;
                } else {
                    // Start a new group
                    $output[] = $group;
                    $group = [
                        'distances' => [$distance],
                        'splice_losses' => [$spliceLoss],
                        'fiber_ids' => [$fiberId],
                        'wavelengths' => [$wavelength],
                        'total_loss' => [$total_loss],
                        'loss_end' => [$loss_end]
                    ];
                }
                $prevDistance = $distance;

            }

            // Add the last group
            if (!empty($group)) {
                $output[] = $group;
            }

            // Calculate the average of each index, splice loss, and fiber ID for each group
            $averages = array_map(function ($group) {
                $distanceSum = array_sum($group['distances']);
                $spliceLossSum = array_sum($group['splice_losses']);
                $count = count($group['distances']);
                $averageDistance = $distanceSum / $count;
                $averageSpliceLoss = $spliceLossSum / $count;
                // Combine fiber IDs, wavelengths, and splice losses into a single array
                $combinedData = [];
                for ($i = 0; $i < $count; $i++) {
                    $combinedData[] = [
                        'fiber_id' => $group['fiber_ids'][$i],
                        'wavelength' => $group['wavelengths'][$i],
                        'splice_loss' => $group['splice_losses'][$i],
                        'total_loss' => $group['total_loss'][$i],
                        'loss_end' => $group['loss_end'][$i]
                    ];
                }

                // Sort combined_data based on fiber_id
                usort($combinedData, function ($a, $b) {
                    return $a['fiber_id'] <=> $b['fiber_id'];
                });

                return [
                    'distances' => $group['distances'],
                    'average_distance' => $averageDistance,
                    'combined_data' => $combinedData, // Combined fiber IDs, wavelengths, and splice losses
                    'average_splice_loss' => $averageSpliceLoss,
                ];
            }, $output);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $styleArray = [
                'font' => [
                    'bold'  =>  true,
                    'size'  =>  14,
                    'name'  =>  'Arial'
                ]
            ];

            // $sheet->setCellValue('B5', 'File');
            $sheet->setCellValue('C5', 'Fiber');
            $sheet->setCellValue('D5', 'Wavelength');
            $sheet->setCellValue('E5', 'Loss dB');
            $sheet->setCellValue('F5', 'Length, km');
            $sheet->setCellValue('G5', 'Attenuation');

            $loopCount = count($averages); // Number of times you want to loop
            $lastRow = count($jsonSorFiles);
            $startColumn = 'H'; // Starting column

            // Function to get the next column
            function getNextColumn($col) {
                return ++$col;
            }
            
            // Loop for the desired number of iterations
            for ($loop = 0; $loop < $loopCount; $loop++) {
                $currentColumn = $startColumn; // Initialize current column
                // Loop through columns and rows
                for ($col = $startColumn; $col != getNextColumn($startColumn); $col++) {
                    $sheet->setCellValue($col.'5', number_format((float) $averages[$loop]['average_distance'], 3, '.', '' )); // Set value for each cell in the column
                }
                // Calculate the next starting column
                $startColumn = getNextColumn($currentColumn);
            }

            $jumlah_averages = count($averages);
            $jumlah_file = count($jsonSorFiles);
            $awalColumn = 'C'; // Starting column
            $kun = -5;
            for($x = 0; $x < $jumlah_averages + 5; $x++){
                $sekarangColumn = $awalColumn;
                
                for ($y = $awalColumn; $y != getNextColumn($awalColumn); $y++) {
                    $test = 0;
                    for ($row = 6; $row <= $jumlah_file + 5; $row++) {
                        $cell = $y . $row; // Form the cell reference
                        switch ($y) {
                            case 'C':
                                $sheet->setCellValue($cell, 'Core'.$averages[0]['combined_data'][$test]["fiber_id"]); // Set value for each cell in the column
                                break;
                            case 'D':
                                $sheet->setCellValue($cell, $averages[0]['combined_data'][$test]["wavelength"]); // Set value for each cell in the column
                                break;
                            case 'E':
                                $sheet->setCellValue($cell, number_format((float) $averages[0]['combined_data'][$test]["total_loss"], 3, '.', '' )); // Set value for each cell in the column
                                break;
                            case 'F':
                                $sheet->setCellValue($cell, number_format((float) $averages[0]['combined_data'][$test]["loss_end"], 3, '.', '' )); // Set value for each cell in the column
                                break;
                            case 'G':
                                $sheet->setCellValue($cell, $averages[0]['combined_data'][$test]["loss_end"] == 0 ? '-' : number_format((float) ($averages[0]['combined_data'][$test]["total_loss"] / $averages[0]['combined_data'][$test]["loss_end"]), 3, '.', '' )); // Set value for each cell in the column
                                break;
                            default:
                                    $current_row_fiber_id = $spreadsheet->getActiveSheet()->getCell('C'.$row)->getValue();
                                    $index = findIndex($averages[$kun]['combined_data'], 'fiber_id',  preg_replace('/[^0-9]/', '', $current_row_fiber_id));
                                    
                                    if($index != ""){
                                        $sheet->setCellValue($cell, $averages[$kun]['combined_data'][$index]['splice_loss']); // Set value for each cell in the column
                                        
                                    }
                                
                            break;
                        }
                        $test++;
                        
                    }
                    
                }
    
                $awalColumn = getNextColumn($sekarangColumn);
                $kun++;
            }

            $writer = new Xlsx($spreadsheet); // Create a new Xlsx object

            $writer->save($section->section_name.".xlsx");
            header("Content-Type: application/vnd.ms-excel");
            return redirect($section->section_name.".xlsx");
        } else {
            return redirect()->back()->with('error', 'No file sor is found !');
        }
    }

    public function sor_request(){
        return view('sor.requests');
    }

    public function delete_request($request_id){

        $deleted = DB::table('sor_request')->where('request_id', $request_id)->delete();


        if ($deleted > 0) {
            return redirect(route('sor.sor_request'))->with('success', 'Request deleted successfully.');
        } else {
            return redirect(route('sor.sor_request'))->with('error', 'Failed to delete request or no matching record found.');
        }
    }

    public function download_sor($project_id, $route_id, $segment_id, $section_id, $sub_section_id){
        
        // if($type == 'section'){
        //     $zip_name = DB::table('section')->where('section_id', $section_id)->first()->section_name;
        //     // Define the folder path
        //     $folderPath = storage_path('app/public/' . $project_id . '/'. $route_id . '/' . $segment_id . '/' . $section_id);
        // }else{
        //     $zip_name = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->first()->sub_section_name;
        //     // Define the folder path
        //     $folderPath = storage_path('app/public/' . $project_id . '/'. $route_id . '/' . $segment_id . '/' . $section_id . '/'. $sub_section_id);
        // }

        $zip_name = DB::table('sub_section')->where('sub_section_id', $sub_section_id)->first()->sub_section_name;
        // Define the folder path
        $folderPath = storage_path('app/public/' . $project_id . '/'. $route_id . '/' . $segment_id . '/' . $section_id . '/'. $sub_section_id);

        // Check if the folder exists
        if (!is_dir($folderPath)) {
            return redirect()->back()->with('error', 'Folder sor not found.');
        }

        // Create a temporary file for the zip
        $zipFileName = $zip_name .'_'. time() . '.zip';
        $zipFilePath = storage_path('app/public/temp/' . $zipFileName);
        
        // Ensure the temp directory exists
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Recursively add files and subdirectories
            $this->addFolderToZip($folderPath, $zip, '');

            $zip->close();

            // Flash a success message for the next request
            Session::flash('downloadComplete', true);

            // Return the zip file as a download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'Could not create zip file.');
        }
    }

    private function addFolderToZip($folderPath, ZipArchive $zip, $parentFolder)
    {
        $files = scandir($folderPath);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $filePath = $folderPath . '/' . $file;
            $relativePath = $parentFolder . $file;

            if (is_dir($filePath)) {
                // Add directory to zip (create folder entry)
                $zip->addEmptyDir($relativePath);

                // Recursively add subdirectory
                $this->addFolderToZip($filePath, $zip, $relativePath . '/');
            } else {
                // Exclude .json files
                if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
                    // Add file to zip
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }

}
