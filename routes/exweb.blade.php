<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\AdminSegmentController;
use App\Http\Controllers\AdminSectionController;
use App\Http\Controllers\AdminCoreController;
use App\Http\Controllers\AdminTopologiController;

use App\Http\Controllers\MSProjectController;
use App\Http\Controllers\MSSegmentController;
use App\Http\Controllers\MSSectionController;
use App\Http\Controllers\MSCoreController;
use App\Http\Controllers\MSTopologiController;

use App\Http\Controllers\ViewerProjectController;
use App\Http\Controllers\ViewerSegmentController;
use App\Http\Controllers\ViewerSectionController;
use App\Http\Controllers\ViewerCoreController;
use App\Http\Controllers\ViewerTopologiController;

use App\Http\Controllers\LapanganProjectController;
use App\Http\Controllers\LapanganSegmentController;
use App\Http\Controllers\LapanganSectionController;
use App\Http\Controllers\LapanganCoreController;
use App\Http\Controllers\LapanganTopologiController;

use App\Http\Controllers\EngineeringProjectController;
use App\Http\Controllers\EngineeringSegmentController;
use App\Http\Controllers\EngineeringSectionController;
use App\Http\Controllers\EngineeringCoreController;
use App\Http\Controllers\EngineeringTopologiController;
use App\Http\Controllers\EngineeringMasterController;
  
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
  
Route::get('/', function () {

    if(auth()->user()){
        switch(auth()->user()->role){
            case 'admin':
                return redirect()->route('admin.home');
                break;
            case 'engineering':
                return redirect()->route('engineering.home');
                break;
            case 'ms':
                return redirect()->route('ms.home');
                break;
            case 'viewer':
                return redirect()->route('viewer.home');
                break;
            case 'lapangan':
                return redirect()->route('lapangan.home');
                break;
            
            default:
                return redirect()->route('ms.home');
                break;
        }
    }else{
        return view('welcome');
    }
    

});
  
Auth::routes();
  
/*------------------------------------------
--------------------------------------------
All Normal Users Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:user'])->group(function () {
  
    Route::get('/home', [HomeController::class, 'index'])->name('home');

});
  
/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:admin'])->group(function () {  
    Route::get('/admin/home', [HomeController::class, 'adminHome'])->name('admin.home');
    Route::get('/admin/user', [AdminUserController::class, 'index'])->name('admin.user');
    Route::get('/admin/segment', [AdminSegmentController::class, 'index'])->name('admin.segment');
    Route::get('/admin/segment/{project_id}/{segment_id}', [AdminSegmentController::class, 'show'])->name('admin.segment.show');
    Route::get('/admin/segment/create', [AdminSegmentController::class, 'create'])->name('admin.segment.create');

    Route::get('/admin/project', [AdminProjectController::class, 'index'])->name('admin.project');
    Route::get('/admin/project/{id}', [AdminProjectController::class, 'show'])->name('admin.project.show');
    Route::get('/admin/project/create', [AdminProjectController::class, 'create'])->name('admin.project.create');

    Route::get('/admin/section', [AdminSectionController::class, 'index'])->name('admin.section');
    Route::get('/admin/section/show', [AdminSectionController::class, 'show'])->name('admin.section.show');
    Route::get('/admin/section/create', [AdminSectionController::class, 'create'])->name('admin.section.create');
    Route::get('/admin/section/edit', [AdminSectionController::class, 'edit'])->name('admin.section.edit');
    Route::post('/admin/section/update', [AdminSectionController::class, 'update'])->name('admin.section.update');
    
    Route::get('/admin/core', [AdminCoreController::class, 'index'])->name('admin.core');
    Route::get('/admin/core/show', [AdminCoreController::class, 'show'])->name('admin.core.show');
    Route::get('/admin/core/create', [AdminCoreController::class, 'create'])->name('admin.core.create');
    Route::get('/admin/core/edit', [AdminCoreController::class, 'edit'])->name('admin.core.edit');
    Route::post('/admin/core/store', [AdminCoreController::class, 'store'])->name('admin.core.store');
    Route::post('/admin/core/destroy', [AdminCoreController::class, 'destroy'])->name('admin.core.destroy');
    Route::post('/admin/core/upload', [AdminCoreController::class, 'upload'])->name('admin.core.upload');
    Route::get('/admin/core/export', [AdminCoreController::class, 'export'])->name('admin.core.export');
    Route::get('/admin/core/import_view', [AdminCoreController::class, 'import_view'])->name('admin.core.import_view');
    Route::post('/admin/core/import', [AdminCoreController::class, 'import'])->name('admin.core.import');
    Route::delete('/admin/core/deleteAll', [AdminCoreController::class, 'deleteAll'])->name('admin.core.deleteAll');
    Route::post('/admin/core/updateMany', [AdminCoreController::class, 'updateMany'])->name('admin.core.updateMany');
    Route::get('/admin/core/update/{core_id}', [AdminCoreController::class, 'update'])->name('admin.core.update');
    Route::get('/admin/core/delete/{core_id}', [AdminCoreController::class, 'delete'])->name('admin.core.delete');
    Route::post('/admin/core/deleteMany', [AdminCoreController::class, 'deleteMany'])->name('admin.core.deleteMany');
    Route::post('/admin/core/insertUpdateCore', [AdminCoreController::class, 'insertUpdateCore'])->name('admin.core.insertUpdateCore');
    Route::post('/admin/core/insertDeleteCore', [AdminCoreController::class, 'insertDeleteCore'])->name('admin.core.insertDeleteCore');
    Route::get('/admin/core/update_requests', [AdminCoreController::class, 'updateRequests'])->name('admin.core.update_requests');
    Route::get('/admin/core/delete_requests', [AdminCoreController::class, 'deleteRequests'])->name('admin.core.delete_requests');
    Route::get('/admin/core/file_insert_requests', [AdminCoreController::class, 'fileInsertRequests'])->name('admin.core.file_insert_requests');
    Route::get('/admin/core/manual_insert_requests', [AdminCoreController::class, 'manualInsertRequests'])->name('admin.core.manual_insert_requests');
    Route::get('/admin/core/show_manual_insert_requests/{code}', [AdminCoreController::class, 'showManualInsertRequests'])->name('admin.core.show_manual_insert_requests');
    Route::post('/admin/core/approveUpdate', [AdminCoreController::class, 'approveUpdate'])->name('admin.core.approveUpdate');
    Route::post('/admin/core/rejectUpdate', [AdminCoreController::class, 'rejectUpdate'])->name('admin.core.rejectUpdate');
    Route::post('/admin/core/approveManualInsert', [AdminCoreController::class, 'approveManualInsert'])->name('admin.core.approveManualInsert');
    Route::post('/admin/core/rejectManualInsert', [AdminCoreController::class, 'rejectManualInsert'])->name('admin.core.rejectManualInsert');
    Route::get('/admin/core/show_update_requests/{code}', [AdminCoreController::class, 'showUpdateRequests'])->name('admin.core.show_update_requests');
    Route::get('/admin/core/show_delete_requests/{code}', [AdminCoreController::class, 'showDeleteRequests'])->name('admin.core.show_delete_requests');
    Route::get('/admin/core/download/{filename}', [AdminCoreController::class, 'download'])->name('admin.core.download');
    
    Route::post('/admin/core/approveInsert', [AdminCoreController::class, 'approveInsert'])->name('admin.core.approveInsert');
    Route::post('/admin/core/rejectInsert', [AdminCoreController::class, 'rejectInsert'])->name('admin.core.rejectInsert');
   
    Route::post('/admin/core/approveDelete', [AdminCoreController::class, 'approveDelete'])->name('admin.core.approveDelete');
    Route::post('/admin/core/rejectDelete', [AdminCoreController::class, 'rejectDelete'])->name('admin.core.rejectDelete');
    
    Route::get('/admin/topologi/show', [AdminTopologiController::class, 'show'])->name('admin.topologi.show');
    
});
  
/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:engineering'])->group(function () {
    Route::get('/engineering/home', [HomeController::class, 'engineeringHome'])->name('engineering.home');

    Route::get('/engineering/project', [EngineeringProjectController::class, 'index'])->name('engineering.project');
    Route::get('/engineering/project/delete/{project_id}', [EngineeringProjectController::class, 'delete'])->name('engineering.project.delete');
    Route::get('/engineering/project/create', [EngineeringProjectController::class, 'create'])->name('engineering.project.create');
    Route::get('/engineering/project/edit/{project_id}', [EngineeringProjectController::class, 'edit'])->name('engineering.project.edit');
    Route::post('/engineering/project/update', [EngineeringProjectController::class, 'update'])->name('engineering.project.update');
    Route::post('/engineering/project/store', [EngineeringProjectController::class, 'store'])->name('engineering.project.store');
    Route::get('/engineering/project/toggle_status/{project_id}', [EngineeringProjectController::class, 'toggle_status'])->name('engineering.project.toggle_status');
    Route::get('/engineering/segment/toggle_status/{segment_id}', [EngineeringSegmentController::class, 'toggle_status'])->name('engineering.segment.toggle_status');
    Route::get('/engineering/section/toggle_status/{section}', [EngineeringSectionController::class, 'toggle_status'])->name('engineering.section.toggle_status');

    Route::get('/engineering/segment/{project_id}', [EngineeringSegmentController::class, 'index'])->name('engineering.segment');
    Route::get('/engineering/segment/delete/{project_id}/{segment_id}', [EngineeringSegmentController::class, 'delete'])->name('engineering.segment.delete');
    Route::get('/engineering/segment/{project_id}/create', [EngineeringSegmentController::class, 'create'])->name('engineering.segment.create');
    Route::get('/engineering/segment/{project_id}/{segment_id}/edit', [EngineeringSegmentController::class, 'edit'])->name('engineering.segment.edit');
    Route::post('/engineering/segment/update', [EngineeringSegmentController::class, 'update'])->name('engineering.segment.update');
    Route::post('/engineering/segment/store', [EngineeringSegmentController::class, 'store'])->name('engineering.segment.store');
    Route::get('/engineering/section/{project_id}/{segment_id}', [EngineeringSectionController::class, 'index'])->name('engineering.section');
    Route::get('/engineering/section/{project_id}/{segment_id}/create', [EngineeringSectionController::class, 'create'])->name('engineering.section.create');
    Route::post('/engineering/section/store', [EngineeringSectionController::class, 'store'])->name('engineering.section.store');
    Route::get('/engineering/section/{project_id}/{segment_id}/{section_id}', [EngineeringSectionController::class, 'show'])->name('engineering.section.show');
    Route::get('/engineering/show_sub_section_list/{project_id}/{segment_id}/{multi_section_code}', [EngineeringSectionController::class, 'show_sub_section_list'])->name('engineering.section.show_sub_section_list');
    Route::get('/engineering/section/delete/{project_id}/{segment_id}/{section_id}', [EngineeringSectionController::class, 'delete'])->name('engineering.section.delete');
    Route::get('/engineering/section/setup/{section_type}/{project_id}/{segment_id}', [EngineeringSectionController::class, 'section_type'])->name('engineering.section.section_type');
    Route::get('/engineering/setup_core_capacity/{project_id}/{segment_id}/{multi_section_code}', [EngineeringSectionController::class, 'setup_core_capacity'])->name('engineering.section.setup_core_capacity');
    Route::get('/engineering/customer_section', [EngineeringSectionController::class, 'customer_section'])->name('engineering.customer_section.index');
    // Route::post('/engineering/store_core_capacity/', [EngineeringSectionController::class, 'store_core_capacity'])->name('engineering.core.store');
    
    // Route::get('/engineering/add_sub_section/{project_id}/{segment_id}/{section_id}', [EngineeringSectionController::class, 'add_sub_section'])->name('engineering.section.add_sub_section');
    // Route::post('/engineering/sub_section/store', [EngineeringSectionController::class, 'store_sub_section'])->name('engineering.section.store_sub_section');

    // APPROVAL VIEW START
    Route::get('/engineering/core/file_insert_requests', [EngineeringCoreController::class, 'fileInsertRequests'])->name('engineering.core.file_insert_requests');
    Route::get('/engineering/core/manual_insert_requests', [EngineeringCoreController::class, 'manualInsertRequests'])->name('engineering.core.manual_insert_requests');
    Route::get('/engineering/core/update_requests', [EngineeringCoreController::class, 'updateRequests'])->name('engineering.core.update_requests');
    Route::get('/engineering/core/delete_requests', [EngineeringCoreController::class, 'deleteRequests'])->name('engineering.core.delete_requests');
    Route::get('/engineering/core/show_update_requests/{code}', [EngineeringCoreController::class, 'showUpdateRequests'])->name('engineering.core.show_update_requests');
    Route::get('/engineering/core/show_delete_requests/{code}', [EngineeringCoreController::class, 'showDeleteRequests'])->name('engineering.core.show_delete_requests');
    // APPROVAL VIEW END

    // APPROVAL VIEW DETAIL START
    Route::get('/engineering/core/show_manual_insert_requests/{code}', [EngineeringCoreController::class, 'showManualInsertRequests'])->name('engineering.core.show_manual_insert_requests');
    // APPROVAL VIEW DETAIL END

    // APPROVAL ACTION START
    Route::post('/engineering/core/approveInsert', [EngineeringCoreController::class, 'approveInsert'])->name('engineering.core.approveInsert');
    Route::post('/engineering/core/rejectInsert', [EngineeringCoreController::class, 'rejectInsert'])->name('engineering.core.rejectInsert');
    
    Route::post('/engineering/core/approveManualInsert', [EngineeringCoreController::class, 'approveManualInsert'])->name('engineering.core.approveManualInsert');
    Route::post('/engineering/core/rejectManualInsert', [EngineeringCoreController::class, 'rejectManualInsert'])->name('engineering.core.rejectManualInsert');

    Route::post('/engineering/core/approveUpdate', [EngineeringCoreController::class, 'approveUpdate'])->name('engineering.core.approveUpdate');
    Route::post('/engineering/core/rejectUpdate', [EngineeringCoreController::class, 'rejectUpdate'])->name('engineering.core.rejectUpdate');

    Route::post('/engineering/core/approveDelete', [EngineeringCoreController::class, 'approveDelete'])->name('engineering.core.approveDelete');
    Route::post('/engineering/core/rejectDelete', [EngineeringCoreController::class, 'rejectDelete'])->name('engineering.core.rejectDelete');

    // APPROVAL ACTION END


    // Input Data Start
    Route::get('/engineering/core/create', [EngineeringCoreController::class, 'create'])->name('engineering.core.create');
    Route::get('/engineering/core/import_view', [EngineeringCoreController::class, 'import_view'])->name('engineering.core.import_view');
    Route::get('/engineering/core/export', [EngineeringCoreController::class, 'export'])->name('engineering.core.export');
    Route::get('/engineering/section/edit', [EngineeringSectionController::class, 'edit'])->name('engineering.section.edit');
    Route::post('/engineering/section/update', [EngineeringSectionController::class, 'update'])->name('engineering.section.update');
    Route::post('/engineering/core/store', [EngineeringCoreController::class, 'store'])->name('engineering.core.store');
    Route::post('/engineering/core/upload', [EngineeringCoreController::class, 'upload'])->name('engineering.core.upload');
    // Input Data Start

    // Individual action Start
    Route::get('/engineering/core/edit/{core_id}', [EngineeringCoreController::class, 'edit'])->name('engineering.core.edit');
    Route::get('/engineering/core/delete/{core_id}', [EngineeringCoreController::class, 'destroy'])->name('engineering.core.delete');
    Route::get('/engineering/core/download/{project_id}/{segment_id}/{section_id}/{core_id}', [EngineeringCoreController::class, 'download'])->name('engineering.core.download');
    Route::get('/engineering/core/download_all_sor/{project_id}/{segment_id}/{section_id}', [EngineeringCoreController::class, 'download_all_sor'])->name('engineering.core.download_all_sor');
    // Individual action end

    // Many item action Start
    Route::post('/engineering/core/updateMany', [EngineeringCoreController::class, 'updateMany'])->name('engineering.core.updateMany'); // Edit
    Route::get('/engineering/core/editAllCore', [EngineeringCoreController::class, 'editAllCore'])->name('engineering.core.editAllCore'); 
    Route::post('/engineering/core/insertUpdateCore', [EngineeringCoreController::class, 'insertUpdateCore'])->name('engineering.core.insertUpdateCore'); //Store
    Route::post('/engineering/core/insertDeleteCore', [EngineeringCoreController::class, 'insertDeleteCore'])->name('engineering.core.insertDeleteCore');
    // Many item action End

    // Download
    // Route::get('/engineering/core/download/{filename}', [EngineeringCoreController::class, 'download'])->name('engineering.core.download');

    // Topologi
    Route::get('/engineering/topologi/show', [EngineeringTopologiController::class, 'show'])->name('engineering.topologi.show');
    Route::get('/engineering/core/detail/{core_id}', [EngineeringCoreController::class, 'detail'])->name('engineering.core.detail');
    Route::get('/engineering/core/upload_sor/{core_id}', [EngineeringCoreController::class, 'core_detail_upload_sor'])->name('engineering.core.core_detail_upload_sor');
    Route::get('/engineering/core/upload_excel/{core_id}', [EngineeringCoreController::class, 'core_detail_upload_excel'])->name('engineering.core.core_detail_upload_excel');
    Route::post('/engineering/core/store_sor', [EngineeringCoreController::class, 'core_detail_store_sor'])->name('engineering.core.core_detail_store_sor');
    Route::post('/engineering/core/store_excel', [EngineeringCoreController::class, 'core_detail_store_excel'])->name('engineering.core.core_detail_store_excel');

    // Start Setup Core Capacity
    Route::get('/engineering/core/setup_core/{project_id}/{segment_id}/{section_id}', [EngineeringCoreController::class, 'setup_core'])->name('engineering.core.setup_core');
    Route::get('/engineering/core/setup_core_multiple/{project_id}/{segment_id}/{section_id}', [EngineeringCoreController::class, 'setup_core_multiple'])->name('engineering.core.setup_core_multiple');
    Route::post('/engineering/core/store_setup_core', [EngineeringCoreController::class, 'store_setup_core'])->name('engineering.core.store_setup_core');
    
    // End Setup Core Capacity 
    
});

Route::middleware(['auth', 'user-access:ms'])->group(function () {
    Route::get('/ms/home', [HomeController::class, 'msHome'])->name('ms.home');
    Route::get('/ms/project', [MSProjectController::class, 'index'])->name('ms.project');
    Route::get('/ms/segment/{id}', [MSSegmentController::class, 'index'])->name('ms.segment');
    Route::get('/ms/section/{project_id}/{segment_id}', [MSSectionController::class, 'index'])->name('ms.section');
    Route::get('/ms/section/{project_id}/{segment_id}/{section_id}', [MSSectionController::class, 'show'])->name('ms.section.show');
    Route::get('/ms/show_sub_section_list/{project_id}/{segment_id}/{multi_section_code}', [MSSectionController::class, 'show_sub_section_list'])->name('ms.section.show_sub_section_list');


    // APPROVAL VIEW START
    Route::get('/ms/core/file_insert_requests', [MSCoreController::class, 'fileInsertRequests'])->name('ms.core.file_insert_requests');
    Route::get('/ms/core/manual_insert_requests', [MSCoreController::class, 'manualInsertRequests'])->name('ms.core.manual_insert_requests');
    Route::get('/ms/core/update_requests', [MSCoreController::class, 'updateRequests'])->name('ms.core.update_requests');
    Route::get('/ms/core/delete_requests', [MSCoreController::class, 'deleteRequests'])->name('ms.core.delete_requests');
    Route::get('/ms/core/show_update_requests/{code}', [MSCoreController::class, 'showUpdateRequests'])->name('ms.core.show_update_requests');
    Route::get('/ms/core/show_delete_requests/{code}', [MSCoreController::class, 'showDeleteRequests'])->name('ms.core.show_delete_requests');
    // APPROVAL VIEW END

    // APPROVAL VIEW DETAIL START
    Route::get('/ms/core/show_manual_insert_requests/{code}', [MSCoreController::class, 'showManualInsertRequests'])->name('ms.core.show_manual_insert_requests');
    // APPROVAL VIEW DETAIL END

    // APPROVAL ACTION START
    Route::post('/ms/core/approveInsert', [MSCoreController::class, 'approveInsert'])->name('ms.core.approveInsert');
    Route::post('/ms/core/rejectInsert', [MSCoreController::class, 'rejectInsert'])->name('ms.core.rejectInsert');
    
    Route::post('/ms/core/approveManualInsert', [MSCoreController::class, 'approveManualInsert'])->name('ms.core.approveManualInsert');
    Route::post('/ms/core/rejectManualInsert', [MSCoreController::class, 'rejectManualInsert'])->name('ms.core.rejectManualInsert');

    Route::post('/ms/core/approveUpdate', [MSCoreController::class, 'approveUpdate'])->name('ms.core.approveUpdate');
    Route::post('/ms/core/rejectUpdate', [MSCoreController::class, 'rejectUpdate'])->name('ms.core.rejectUpdate');

    Route::post('/ms/core/approveDelete', [MSCoreController::class, 'approveDelete'])->name('ms.core.approveDelete');
    Route::post('/ms/core/rejectDelete', [MSCoreController::class, 'rejectDelete'])->name('ms.core.rejectDelete');
    // APPROVAL ACTION END


    // Input Data Start
    Route::get('/ms/core/create', [MSCoreController::class, 'create'])->name('ms.core.create');
    Route::get('/ms/core/import_view', [MSCoreController::class, 'import_view'])->name('ms.core.import_view');
    Route::get('/ms/core/export', [MSCoreController::class, 'export'])->name('ms.core.export');
    Route::get('/ms/section/edit', [MSSectionController::class, 'edit'])->name('ms.section.edit');
    Route::post('/ms/section/update', [MSSectionController::class, 'update'])->name('ms.section.update');

    Route::post('/ms/core/store', [MSCoreController::class, 'store'])->name('ms.core.store');
    Route::post('/ms/core/upload', [MSCoreController::class, 'upload'])->name('ms.core.upload');
    // Input Data Start


    // Individual action Start
    Route::get('/ms/core/edit/{core_id}', [MSCoreController::class, 'edit'])->name('ms.core.edit');
    Route::get('/ms/core/editAllCore', [MSCoreController::class, 'editAllCore'])->name('ms.core.editAllCore');
    Route::get('/ms/core/delete/{core_id}', [MSCoreController::class, 'destroy'])->name('ms.core.delete');
    // Individual action End

    // Many item action Start
        Route::post('/ms/core/updateMany', [MSCoreController::class, 'updateMany'])->name('ms.core.updateMany'); // Edit
        Route::post('/ms/core/insertUpdateCore', [MSCoreController::class, 'insertUpdateCore'])->name('ms.core.insertUpdateCore'); //Store
        Route::post('/ms/core/insertDeleteCore', [MSCoreController::class, 'insertDeleteCore'])->name('ms.core.insertDeleteCore');
    // Many item action End


    // Download
    Route::get('/ms/core/download/{filename}', [MSCoreController::class, 'download'])->name('ms.core.download');
    Route::get('/ms/core/excel_template', [MSCoreController::class, 'excel_template'])->name('ms.core.excel_template');
    Route::get('/ms/core/excel_summary', [MSCoreController::class, 'excel_summary'])->name('ms.core.excel_summary');

    // Topologi
    Route::get('/ms/topologi/show', [MSTopologiController::class, 'show'])->name('admin.topologi.show');

    Route::get('/ms/core/detail/{project_id}/{segment_id}/{section_id}/{core_id}', [MSCoreController::class, 'detail'])->name('ms.core.detail');
    Route::get('/ms/core/upload_sor/{project_id}/{segment_id}/{section_id}/{core_id}', [MSCoreController::class, 'core_detail_upload_sor'])->name('ms.core.core_detail_upload_sor');
    Route::get('/ms/core/upload_excel/{core_id}', [MSCoreController::class, 'core_detail_upload_excel'])->name('ms.core.core_detail_upload_excel');
    Route::post('/ms/core/store_sor', [MSCoreController::class, 'core_detail_store_sor'])->name('ms.core.core_detail_store_sor');
    Route::post('/ms/core/store_excel', [MSCoreController::class, 'core_detail_store_excel'])->name('ms.core.core_detail_store_excel');
    Route::get('/ms/sor_request/{project_id}/{segment_id}/{section_id}', [MSCoreController::class, 'sor_request'])->name('ms.sor_request');
    Route::get('/ms/customer_request/{project_id}/{segment_id}/{section_id}', [MSCoreController::class, 'customer_update_request'])->name('ms.customer_request');
    Route::get('/ms/customer_update_request_detail/{request_id}', [MSCoreController::class, 'customer_update_request_detail'])->name('ms.customer_update_request_detail');
    Route::get('/ms/customer_update_request_approval/{project_id}/{segment_id}/{section_id}/{request_id}/{action}', [MSCoreController::class, 'customer_update_request_approval'])->name('ms.customer_update_request_approval');
    Route::get('/ms/sor_request/{project_id}/{segment_id}/{section_id}/{request_id}', [MSCoreController::class, 'sor_request_show'])->name('ms.sor_request.show');
    Route::post('/getProcessedFiles', [MSCoreController::class, 'getProcessedFiles'])->name('get.processed.files');
    Route::post('/process_data', [MSCoreController::class, 'process_data'])->name('process_data');

    // Start Custom Calculator
        Route::get('/ms/custom_calculator', [MSCoreController::class, 'custom_calculator'])->name('ms.custom_calculator');
        // Route::post('/autocomplete', [MSCoreController::class, 'autocomplete'])->name('get-suggestions');
        Route::get('/autocomplete', [MSCoreController::class, 'autocomplete']);     
        Route::post('/calculate', [MSCoreController::class, 'calculate'])->name('ms.calculate');
    // End Custom Calculator

});

Route::middleware(['auth', 'user-access:viewer'])->group(function () {
    Route::get('/viewer/home', [HomeController::class, 'viewerHome'])->name('viewer.home');
    Route::get('/viewer/project', [ViewerProjectController::class, 'index'])->name('viewer.project');
    Route::get('/viewer/segment/{id}', [ViewerSegmentController::class, 'index'])->name('viewer.segment');
    Route::get('/viewer/section/{project_id}/{segment_id}', [ViewerSectionController::class, 'index'])->name('viewer.section');
    Route::get('/viewer/section/{project_id}/{segment_id}/{section_id}', [ViewerSectionController::class, 'show'])->name('viewer.section.show');

    // Download
    Route::get('/viewer/core/download/{filename}', [ViewerCoreController::class, 'download'])->name('viewer.core.download');
    Route::get('/viewer/core/excel_summary', [ViewerCoreController::class, 'excel_summary'])->name('viewer.core.excel_summary');

    // Topologi
    Route::get('/viewer/topologi/show', [ViewerTopologiController::class, 'show'])->name('admin.topologi.show');

    Route::get('/viewer/core/detail/{project_id}/{segment_id}/{section_id}/{core_id}', [ViewerCoreController::class, 'detail'])->name('viewer.core.detail');

});

Route::middleware(['auth', 'user-access:lapangan'])->group(function () {
    Route::get('/lapangan/home', [HomeController::class, 'lapanganHome'])->name('lapangan.home');
    Route::get('/lapangan/project', [lapanganProjectController::class, 'index'])->name('lapangan.project');
    Route::get('/lapangan/segment/{id}', [lapanganSegmentController::class, 'index'])->name('lapangan.segment');
    Route::get('/lapangan/section/{project_id}/{segment_id}', [lapanganSectionController::class, 'index'])->name('lapangan.section');
    Route::get('/lapangan/section/{project_id}/{segment_id}/{section_id}', [lapanganSectionController::class, 'show'])->name('lapangan.section.show');
    Route::get('/lapangan/show_sub_section_list/{project_id}/{segment_id}/{multi_section_code}', [lapanganSectionController::class, 'show_sub_section_list'])->name('lapangan.section.show_sub_section_list');
    Route::get('/lapangan/core/upload_all_sor/{project_id}/{segment_id}/{section_id}', [LapanganCoreController::class, 'upload_all_sor'])->name('lapangan.core.upload_all_sor');
    Route::post('/lapangan/core/store_all_sor', [LapanganCoreController::class, 'store_all_sor'])->name('lapangan.core.store_all_sor');
    Route::get('/lapangan/sor_request', [LapanganCoreController::class, 'sor_request'])->name('lapangan.sor_request');
    Route::get('/lapangan/customer_update_request', [LapanganCoreController::class, 'customer_update_request'])->name('lapangan.customer_update_request');
    Route::get('/lapangan/customer_update_request_detail/{request_id}', [LapanganCoreController::class, 'customer_update_request_detail'])->name('lapangan.customer_update_request_detail');
    Route::post('/lapangan/core/store_customer_update_request', [LapanganCoreController::class, 'store_customer_update_request'])->name('lapangan.store_customer_update_request');
    Route::get('/lapangan/core/download/{filename}', [lapanganCoreController::class, 'download'])->name('lapangan.core.download');
    Route::get('/lapangan/core/excel_summary', [lapanganCoreController::class, 'excel_summary'])->name('lapangan.core.excel_summary');
    Route::get('/lapangan/core/detail/{project_id}/{segment_id}/{section_id}/{core_id}', [lapanganCoreController::class, 'detail'])->name('lapangan.core.detail'); 
    Route::get('/lapangan/core/editAllCore', [lapanganCoreController::class, 'editAllCore'])->name('lapangan.core.editAllCore'); 
    
});



