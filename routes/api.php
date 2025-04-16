<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiAdminProjectController;
use App\Http\Controllers\api\ApiAdminSegmentController;
use App\Http\Controllers\api\ApiAdminSegmentDetailController;
use App\Http\Controllers\api\ApiAdminSectionDetailController;
use App\Http\Controllers\api\ApiAdminSectionController;
use App\Http\Controllers\api\ApiAdminSectionCustomerController;
use App\Http\Controllers\api\ApiAdminCoreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
