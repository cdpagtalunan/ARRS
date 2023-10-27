<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\RapidxUser;
use App\Models\UserAccess;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
// use App\Helpers\AppHelper; // This is a global helper

use Helpers;

class AdminController extends Controller
{
    public function get_user(Request $request){
        $user_data = UserAccess::with([
            'rapidx_user_details'
        ])
        ->get(); 

        return DataTables::of($user_data)
        ->addColumn('action', function($user_data){

            // $id_encrypt = Crypt::encryptString($user_data->id);
            $id_encrypt = Helpers::encryptId($user_data->id);
            // return $id_encrypt;
            $result = "";
            $result .= "<center>";
            // $result .= $id_encrypt;
            $result .= "<button class='btn btn-secondary btn-sm actionEditSystemDevelopment' dev-id='$id_encrypt'>Edit</button>";
            $result .= "</center>";
            return $result;
        })
        ->addColumn('user_type', function($user_data){
            $result = "";
            if($user_data->user_type == 1){
                $result = "Admin";
            }
            else{
                $result = "User";

            }
            return $result;
        })
        ->rawColumns(['action', 'user_type'])
        ->make(true);
        // return response()->json(['data' => $user_data]);

    }

    public function save_user(UserRequest $request){
        date_default_timezone_set('Asia/Manila');
        // return $request->all();
        $fields = $request->validated();
        // $test = Crypt::encryptString('1');
        // $decrypted = Crypt::decryptString($test);
        // return $decrypted;
        // return $_SESSION;
        DB::beginTransaction();
        try{
            // return $request->empDetails['id'];
            $user_access_array = array(
                'rapidx_emp_no'   => $request->empDetails['id'],
                'category_id'     => $request->uCat,
                'user_type'       => $request->uType
            );
            if(isset($request->id)){ // UPDATE
                $user_access_array['updated_by'] = $_SESSION['rapidx_username'];
                $decrypted_id = Helpers::decryptId($request->id);

                UserAccess::where('id',$decrypted_id)
                ->update($user_access_array);
                DB::commit();

                return response()->json(['result' => 2, 'msg' => 'User Successfully Edited!']);
                
            }
            else{ // CREATE
                $user_access_array['created_by'] = $_SESSION['rapidx_username'];
                $user_access_array['created_at'] = NOW();
                // $user_access_array['user_type'] = $request->uType;
                UserAccess::insert($user_access_array);
                DB::commit();

                return response()->json(['result' => 1, 'msg' => "User Successfully Created!"]);
            }
        }
        catch(Exception $e){
            DB::rollback();
            return $e;
        }
       
        
    }

    public function get_rapidx_employee(Request $request){
        $rapidx_users = DB::connection('mysql_rapidx')
        ->select('SELECT * FROM `users` WHERE user_stat = 1');

        return $rapidx_users;
        // return response()->json([
        //     'empNo' => $rapidx_users
        // ]);
    }

    public function get_user_details(Request $request){

        // $decrypted_id = Crypt::decryptString($request->emp);
        $decrypted_id = Helpers::decryptId($request->emp);
        $user_data = UserAccess::with([
            'rapidx_user_details'
        ])
        ->where('id', $decrypted_id)
        ->select('*')
        ->first();
        
        return response()->json(['userData' => $user_data, 'emp' => $request->emp]);
        
    }
}
