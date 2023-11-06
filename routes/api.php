<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ConfigController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['middleware' => ['auth:sanctum']], function () {
Route::middleware('CheckSessionExist')->group(function(){
    /*
        * START SETTINGS ROUTES
    */

    // ^ CUT-OFF ROUTE
    Route::get('get_cutoff', [ConfigController::class, 'get_cutoff']);
    Route::post('save_cutoff', [ConfigController::class, 'save_cutoff']);

    // ^ USER CATEGORY ROUTE
    Route::get('get_category', [ConfigController::class, 'get_category']);
    Route::post('save_cat', [ConfigController::class, 'save_cat']);
    Route::get('get_dropdown_data', [ConfigController::class, 'get_dropdown_data']);
    Route::get('get_category_details', [ConfigController::class, 'get_category_details']);
    Route::post('update_cat_status', [ConfigController::class, 'update_cat_status']);
    /*
        ! END SETTINGS ROUTES
    */

    // USER ROUTE
    Route::get('get_user', [AdminController::class, 'get_user']);
    Route::post('save_user', [AdminController::class, 'save_user']);
    Route::get('get_rapidx_employee', [AdminController::class, 'get_rapidx_employee']);
    Route::get('get_user_details', [AdminController::class, 'get_user_details']);
    Route::post('update_user_stat', [AdminController::class, 'update_user_stat']);
    Route::get('get_cat', [AdminController::class, 'get_cat']);
    
});
    
    
// });


