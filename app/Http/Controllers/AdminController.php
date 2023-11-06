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
            'rapidx_user_details',
            'category_details'
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
            $result .= "<button class='btn btn-secondary btn-sm actionEditUser mr-1' data-id='$id_encrypt' title='Edit User'><i class='fas fa-pen-to-square'></i></button>";

            if($user_data->deleted_at != null){
                $result .= "<button class='btn btn-success btn-sm actionDelUser' data-id='$id_encrypt' data-name='0'><i class='fas fa-rotate-right'></i></button>";
            }else{
                $result .= "<button class='btn btn-danger btn-sm actionDelUser' data-id='$id_encrypt' data-name='1'><i class='fas fa-user-slash'></i></button>";

            }
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
        ->addColumn('category', function($user_data){
            $result = "";
            // $result .= $user_data->category_id;
            if($user_data->category_id == 0){
                $result .= "Admin";
            }
            else{
                $result .= $user_data->category_details->classification."-".$user_data->category_details->department;

            }
            return $result;
        })
        ->addColumn('status', function($user_data){
            $result = "";
            $result = "<center>";
            if($user_data->deleted_at != null){
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            else{
                $result .= "<span class='badge rounded-pill bg-success'>Active</span>";
            }
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['action', 'user_type', 'status', 'category'])
        ->make(true);
        // return response()->json(['data' => $user_data]);

    }

    public function save_user(UserRequest $request){
        date_default_timezone_set('Asia/Manila');
        $fields = $request->validated();
        DB::beginTransaction();
        try{
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
    }

    public function get_user_details(Request $request){

        $decrypted_id = Helpers::decryptId($request->emp);
        $user_data = UserAccess::with([
            'rapidx_user_details'
        ])
        ->where('id', $decrypted_id)
        ->select('*')
        ->first();
        
        return response()->json(['userData' => $user_data, 'emp' => $request->emp]);
    }

    public function update_user_stat(Request $request){
        date_default_timezone_set('Asia/Manila');
        $decrypted_id = Helpers::decryptId($request->emp);
        // return $decrypted_id;
        DB::beginTransaction();
        try{
            if($request->fn_name == 0){ // * Activate User
                DB::connection('mysql')->table('user_accesses')
                ->where('id', $decrypted_id)
                ->update([
                    'deleted_at' => null,
                    'updated_by' => $_SESSION['rapidx_username']
                ]);
            }
            else{ // *  Deactivate User
                
                DB::connection('mysql')->table('user_accesses')
                ->where('id', $decrypted_id)
                ->update([
                    'deleted_at' => NOW(),
                    'updated_by' => $_SESSION['rapidx_username']
                ]);
            }
            DB::commit();
            return response()->json(['result' => 1, 'msg' => 'Successfully Updated!']);
        }
        catch(Exception $e){
            DB::rollback();
            return $e;
        }
        
    }

    public function get_cat(Request $request){
        $cat = DB::connection('mysql')->table('user_categories')
        ->whereNull('deleted_at')
        ->select('id', 'classification', 'department')
        ->get();

        return $cat;
    }
}
