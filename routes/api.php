<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CutoffController;

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
    Route::get('get_cutoff', [CutoffController::class, 'get_cutoff']);
    Route::post('save_cutoff', [CutoffController::class, 'save_cutoff']);

});
    
    
// });


