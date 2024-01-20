<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ReconciliationController;

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

        //^ CUT-OFF ROUTE
        Route::get('get_cutoff', [ConfigController::class, 'get_cutoff']);
        Route::post('save_cutoff', [ConfigController::class, 'save_cutoff']);
        Route::get('get_cutoff_details', [ConfigController::class, 'get_cutoff_details']);
        Route::post('update_status', [ConfigController::class, 'update_status']);

        //^ USER CATEGORY ROUTE
        Route::get('get_category', [ConfigController::class, 'get_category']);
        Route::post('save_cat', [ConfigController::class, 'save_cat']);
        Route::get('get_dropdown_data', [ConfigController::class, 'get_dropdown_data']);
        Route::get('get_category_details', [ConfigController::class, 'get_category_details']);
        Route::post('update_cat_status', [ConfigController::class, 'update_cat_status']);

        // USER ROUTE
        Route::get('get_user', [AdminController::class, 'get_user']);
        Route::post('save_user', [AdminController::class, 'save_user']);
        Route::get('get_rapidx_employee', [AdminController::class, 'get_rapidx_employee']);
        Route::get('get_user_details', [AdminController::class, 'get_user_details']);
        Route::post('update_user_stat', [AdminController::class, 'update_user_stat']);
        Route::get('get_cat', [AdminController::class, 'get_cat']);
        
        //^ RECONCILIATION ROUTE
        Route::get('get_category_of_user', [ReconciliationController::class, 'get_category_of_user']);
        Route::get('get_eprpo_data', [ReconciliationController::class, 'get_eprpo_data']);
        Route::get('get_recon', [ReconciliationController::class, 'get_recon']);
        Route::get('get_recon_details', [ReconciliationController::class, 'get_recon_details']);
        Route::post('save_recon', [ReconciliationController::class, 'save_recon']);
        Route::post('request_remove_recon', [ReconciliationController::class, 'request_remove_recon']);
        Route::get('get_recon_for_add', [ReconciliationController::class, 'get_recon_for_add']);
        Route::post('request_for_addition', [ReconciliationController::class, 'request_for_addition']);
        Route::get('get_recon_dates', [ReconciliationController::class, 'get_recon_dates']);

        //^ ADMIN REQUEST ROUTE
        Route::get('get_add_request', [RequestController::class, 'get_add_request']);
        Route::get('get_remove_request', [RequestController::class, 'get_remove_request']);
        Route::get('view_request_details', [RequestController::class, 'view_request_details']);
        Route::post('response_request', [RequestController::class, 'response_request']);
        Route::get('get_remove_recon_details', [RequestController::class, 'get_remove_recon_details']);

        //^ USER REQUEST ROUTE
        Route::get('get_request', [RequestController::class, 'get_request']);

        //^ MAILING ROUTE
        Route::get('send_email', [EmailController::class, 'send_email']);

        // ^ DECRYPTING ID
        Route::get('decrypt_id', [CommonController::class, 'decrypt_id']);

        // ^ EXPORT ROUTE
        Route::get('export/{u_id}/{date_range}/{access}', [ExportController::class, 'export']);
        Route::get('export_admin/{date_range}', [ExportController::class, 'export_admin']);


    });
        
    
// });


