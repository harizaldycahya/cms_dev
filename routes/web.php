<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SegmentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubSectionController;
use App\Http\Controllers\CoreController;
use App\Http\Controllers\SorController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\AdditionalFeaturesController;

use App\Http\Controllers\HashMicroController;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
  

Auth::routes();

Route::get('/', function () {
    if(auth()->user()){
        return redirect()->route('dashboard');
    }else{
        return view('welcome');
    }
})->name('home');




Route::middleware('auth')->group(function () {

    // AJAX
    Route::get('/get-segments/{project_id}/{route_id}', function ($project_id, $route_id)  {
        $segments = DB::table('segment')
        ->where('project_id', $project_id)
        ->where('route_id', $route_id)
        ->get();
        return response()->json($segments);
    });
    
    Route::get('/get-sections/{project_id}/{route_id}/{segment_id}', function ($project_id, $route_id, $segment_id)  {
        $sections = DB::table('section')
        ->where('project_id', $project_id)
        ->where('route_id', $route_id)
        ->where('segment_id', $segment_id)
        ->get();

        return response()->json($sections);
    });

    Route::get('/get-sub-sections/{project_id}/{route_id}/{segment_id}/{section_id}', function ($project_id, $route_id, $segment_id, $section_id) {
        $sub_sections = DB::table('sub_section')
            ->join('customer', 'sub_section.customer_id', '=', 'customer.customer_id')
            ->where('sub_section.project_id', $project_id)
            ->where('sub_section.route_id', $route_id)
            ->where('sub_section.segment_id', $segment_id)
            ->where('sub_section.section_id', $section_id)
            ->select(
                'sub_section.sub_section_id',
                'sub_section.sub_section_name',
                'customer.customer_name'
            )
            ->get();
    
        return response()->json($sub_sections);
    });

    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/project/index', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/project/show/{project_id}/{route_id}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/project/store', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/project/edit/{project_id}', [ProjectController::class, 'edit'])->name('project.edit');
    Route::post('/project/update', [ProjectController::class, 'update'])->name('project.update');
    Route::get('/project/delete/{project_id}', [ProjectController::class, 'delete'])->name('project.delete');
    
    Route::get('/segment/show/{project_id}/{route_id}/{segment_id}', [SegmentController::class, 'show'])->name('segment.show');
    Route::get('/segment/create/{project_id}/{route_id}', [SegmentController::class, 'create'])->name('segment.create');
    Route::post('/segment/store', [SegmentController::class, 'store'])->name('segment.store');
    Route::get('/segment/edit/{project_id}/{route_id}', [SegmentController::class, 'edit'])->name('segment.edit');
    Route::post('/segment/update', [SegmentController::class, 'update'])->name('segment.update');
    Route::get('/segment/delete/{project_id}/{route_id}/{segment_id}', [SegmentController::class, 'delete'])->name('segment.delete');
    
    Route::get('/section/show/{project_id}/{route_id}/{segment_id}/{section_id}', [SectionController::class, 'show'])->name('section.show');
    Route::get('/section/create/{project_id}/{route_id}/{segment_id}', [SectionController::class, 'create'])->name('section.create');
    Route::post('/section/store', [SectionController::class, 'store'])->name('section.store');
    Route::get('/section/edit/{project_id}/{route_id}/{segment_id}', [SectionController::class, 'edit'])->name('section.edit');
    Route::post('/section/update', [SectionController::class, 'update'])->name('section.update');
    Route::get('/section/delete/{project_id}/{route_id}/{segment_id}/{section_id}', [SectionController::class, 'delete'])->name('section.delete');
    
    Route::get('/sub_section/show/{project_id}/{route_id}/{segment_id}/{section_id}/{customer_id}/{type_id}/{sub_section_id}', [SubSectionController::class, 'show'])->name('sub_section.show');
    Route::get('/sub_section/create/{project_id}/{route_id}/{segment_id}/{section_id}', [SubSectionController::class, 'create'])->name('sub_section.create');
    Route::post('/sub_section/store', [SubSectionController::class, 'store'])->name('sub_section.store');
    Route::get('/sub_section/edit/{project_id}/{route_id}/{segment_id}/{section_id}', [SubSectionController::class, 'edit'])->name('sub_section.edit');
    Route::post('/sub_section/update', [SubSectionController::class, 'update'])->name('sub_section.update');
    Route::get('/sub_section/delete/{project_id}/{route_id}/{segment_id}/{section_id}/{sub_section_id}', [SubSectionController::class, 'delete'])->name('sub_section.delete');
    
    Route::get('/core/show/{project_id}/{route_id}/{segment_id}/{section_id}/{sub_section_id}/{core}', [CoreController::class, 'show'])->name('core.show');
    Route::get('/core/create/{project_id}/{route_id}/{segment_id}/{section_id}/{customer_id}/{type_id}/{sub_section_id}/{input_type}', [CoreController::class, 'create'])->name('core.create');
    Route::get('/core/delete/{core_id}', [CoreController::class, 'delete'])->name('core.delete');
    Route::post('/core/-', [CoreController::class, 'store'])->name('core.store');
    Route::get('/core/edit/{project_id}/{route_id}/{segment_id}/{section_id}/{customer_id}/{type_id}/{sub_section_id}', [CoreController::class, 'edit'])->name('core.edit');
    Route::post('/core/update', [CoreController::class, 'update'])->name('core.update');
    Route::get('/core/upload_excel/{project_id}/{route_id}/{segment_id}/{section_id}', [CoreController::class, 'upload_excel'])->name('core.upload_excel');
    Route::post('/core/store_upload_excel/', [CoreController::class, 'store_upload_excel'])->name('core.store_upload_excel');
    
    Route::get('/sor/index/{project_id}/{segment_id}/{section_id}/{customer_id}/{type_id}', [SorController::class, 'index'])->name('sor.index');
    Route::get('/sor/create/{project_id}/{route_id}/{segment_id}/{section_id}/{customer_id}/{type_id}/{sub_section_id}', [SorController::class, 'create'])->name('sor.create');
    Route::post('/sor/store', [SorController::class, 'store'])->name('sor.store');
    Route::get('/sor/show/{request_id}', [SorController::class, 'show'])->name('sor.show');
    Route::get('/sor/process/{request_id}', [SorController::class, 'process'])->name('sor.process');
    Route::get('/sor/approval/{request_id}/{status}', [SorController::class, 'approval'])->name('sor.approval');
    Route::get('/sor/delete_request/{request_id}', [SorController::class, 'delete_request'])->name('sor.delete_request');
    Route::get('/sor/summary/{project_id}/{route_id}/{segment_id}/{section_id}/{sub_section_id}', [SorController::class, 'summary'])->name('sor.summary');
    Route::get('/sor/sor_request', [SorController::class, 'sor_request'])->name('sor.sor_request');
    Route::get('/sor/download/{project_id}/{route_id}/{segment_id}/{section_id}/{sub_section_id}', [SorController::class, 'download_sor'])->name('sor.download');
    
    Route::get('/customer/index', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/edit/{customer_id}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('/customer/update/', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customer/delete/{customer_id}', [CustomerController::class, 'destroy'])->name('customer.delete');
    
    Route::post('/search', [AdditionalFeaturesController::class, 'search'])->name('search');

});





// Route::middleware(['auth', 'user-access:user'])->group(function () {
//     Route::get('/home', [HomeController::class, 'index'])->name('home');
// });

// Route::middleware(['auth', 'user-access:engineering'])->group(function () {
    
// });

// Route::middleware(['auth', 'user-access:ms'])->group(function () {

// });

// Route::middleware(['auth', 'user-access:viewer'])->group(function () {
    
// });

// Route::middleware(['auth', 'user-access:lapangan'])->group(function () {
    
// });



