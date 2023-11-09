<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CommonController;

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

/*
    * THIS ROUTE WILL CHECK IF RAPIDX SESSION STILL EXIST
    * IF NO SESSION EXIST, SYSTEM WILL REDIRECT USER TO LOGIN 
*/
Route::get('check_user', function (Request $request) {
    session_start();
    if($_SESSION){
        return true;
    }
    else{
        return false;

    }
});

Route::middleware('CheckSessionExist')->group(function(){

    /*
        ! THIS ROUTE WILL BE ACCESS FIRST AND WILL CHECK IF USER HAS ACCESS ON THIS MODULE
        ! IF USER DONT HAVE ACCESS ON THE SYSTEM, EDI WALA 
    */
    // Route::get('check_access', function (Request $request) {
    //     $found_key = array_search('32', array_column($_SESSION['rapidx_user_accesses'], 'module_id')); // * 32 is the id of this module on RAPIDX
    //     if($found_key == ""){
    //         return response()->json(['msg' => 'User Dont Have Access'], 401);
    //     }
    // });
    Route::get('check_access', [CommonController::class, 'check_access']);

    /*
        * THIS WILL ACCEPT ANY KIND OF URL
    */
    Route::get('/{any}', function (Request $request) {
        return view('welcome');
    })->where('any', '.*');
});


