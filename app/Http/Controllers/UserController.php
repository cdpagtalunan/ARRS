<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\cn_ppts_user;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function get_user_login(Request $request){
        // return "qwe";
        // session_start();
        return $_SESSION;
        // return $request->session()->get('id');
        // $data = $request->session()->get('rapidx_user_accesses');
        // return $request->session()->all();
        // $found_key = array_search('32', array_column($_SESSION['rapidx_user_accesses'], 'module_id')); // * 32 is the id of this module on RAPIDX
        // if($found_key != ""){
        //     // $request->session()->put('emp_no', $_REQUEST['rapidx_employee_number']);
        //     // $request->session()->put('emp_no', $_REQUEST['rapidx_employee_number']);
        //     // session([
        //     //     'emp_no' => $_REQUEST['rapidx_employee_number'],
        //     //     'rapidx_uname' => $_REQUEST['rapidx_username'],
        //     //     ' '
        //     // ]);
        // }
        // else{
        //     // return redirect()->away('../RapidX');
        //     return response()->json(['msg' => 'User Dont Have Access'], 401);
        // }
    }

  
}
