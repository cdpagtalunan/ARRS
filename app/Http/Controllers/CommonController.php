<?php

namespace App\Http\Controllers;

use App\Models\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    public function check_access(Request $request){
        $found_key = array_search('32', array_column($_SESSION['rapidx_user_accesses'], 'module_id')); // * 32 is the id of this module on RAPIDX
        if($found_key == ""){
            return response()->json(['msg' => 'User Dont Have Access'], 401);
        }
        else{
            $user_system_access_check = DB::connection('mysql')->table('user_accesses')
            ->where('rapidx_emp_no', $_SESSION['rapidx_user_id'])
            ->whereNull('deleted_at')
            ->select('*')
            ->first();

            // return $user_system_access_check;

            if($user_system_access_check != ""){
                $exploded_category =  explode(",", $user_system_access_check->category_id);

                $uAccessArray = [];
                for ($i=0; $i <count($exploded_category) ; $i++) { 
                    array_push($uAccessArray, $exploded_category[$i]);
                }

                return response()->json(['uAccess' => $uAccessArray, 'uName' => $_SESSION['rapidx_name'], 'appid' => $user_system_access_check->id ]);
            }
            else{
                return response()->json(['msg' => 'User Dont Have Access '], 401);

            }
        

        }
    }
}