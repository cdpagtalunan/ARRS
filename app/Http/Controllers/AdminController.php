<?php

namespace App\Http\Controllers;

use Helpers;
use DataTables;
use App\Models\RapidxUser;
use App\Models\UserAccess;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use App\Models\Reconciliation;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
// use App\Helpers\AppHelper; // This is a global helper

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CommonController;
use Illuminate\Contracts\Encryption\DecryptException;

class AdminController extends Controller
{
    protected $mailSender;

    public function __construct(CommonController $mailSender) {
      $this->mailSender = $mailSender;
    }
    
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
            else if($user_data->user_type == 2){
                $result = "User";
            }
            else if($user_data->user_type == 3){
                $result = "Viewer";
            }
            return $result;
        })
        ->addColumn('category', function($user_data){
            // $result = "";
            $result = array();
            // $result .= $user_data->category_id;
            if($user_data->category_id == 0){
                // $result .= "Admin";
                array_push($result, "Admin");

            }
            // else{
                $cat_details = DB::connection('mysql')->table('user_categories')
                ->whereIn('id', explode(",",$user_data->category_id))
                ->select('*')
                ->get();
                for($x = 0; $x < count($cat_details); $x++){
                    // $result .= $cat_details[$x]->classification."-".$cat_details[$x]->department;
                    array_push($result, $cat_details[$x]->classification."-".$cat_details[$x]->department);

                }

                // $result .= $user_data->category_details->classification."-".$user_data->category_details->department;

            // }
            $results = implode("<br>", $result);
            return $results;
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
                'rapidx_emp_no' => $request->empDetails['id'],
                'category_id'   => implode(",",$request->uCat),
                'user_type'     => $request->uType,
                'user_desig'    => $request->uDesig,
                'is_auth'       => $request->auth,
                'is_superior'   => $request->supp
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
                // return UserAccess::whereNull('deleted_at')->where('rapidx_emp_no',$request->empDetails['id'])->exists();
                if(!UserAccess::whereNull('deleted_at')->where('rapidx_emp_no',$request->empDetails['id'])->exists()){
                    // return "test";
                    $user_access_array['created_by'] = $_SESSION['rapidx_username'];
                    $user_access_array['created_at'] = NOW();
                    UserAccess::insert($user_access_array);
                    DB::commit();

                    // return $user_access_array;
                    return response()->json(['result' => 1, 'msg' => "User Successfully Created!"]);
                }
                else{
                    return response()->json([
                        'msg'       => 'User already exist!'
                    ],405);
                }
              
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
        $for_sel_cat = array_map('intval',explode(",",$user_data->category_id));
        return response()->json(['userData' => $user_data, 'forSelCat' => $for_sel_cat, 'emp' => $request->emp]);
    }

    public function update_user_stat(Request $request){
        date_default_timezone_set('Asia/Manila');
        $decrypted_id = Helpers::decryptId($request->emp);
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
        ->select('id', 'classification', 'department', 'deleted_at')
        ->get();
        return $cat;
        // return response()->json(['res' => $cat]);
    }

    public function update_open_recon(Request $request){
        // return $request->all();
        DB::beginTransaction();
        try{
            Reconciliation::where('recon_date_from', $request->from)
            ->where('recon_date_to', $request->to)
            ->where('classification', $request->classification)
            ->where('pr_num', 'LIKE',"%$request->dept%")
            ->update([
                'final_recon_status' => 0,
                'final_recon_date'   => NULL
            ]);

            $get_cat = UserCategory::where('classification', $request->classification)
            ->where('department', $request->dept)
            ->whereNull('deleted_at')
            ->first('id');
    
            $get_admin_user = UserAccess::with([
                'rapidx_user_details'
            ])
            ->whereNull('deleted_at')
            ->where('user_type', 1)
            ->get();
            
            $get_user = UserAccess::with([
                'rapidx_user_details'
            ])
            ->whereNull('deleted_at')
            ->whereRaw('FIND_IN_SET("'.$get_cat->id.'", category_id)')
            ->where('user_type', 2)
            ->get();

            $admin_email = collect($get_admin_user)->pluck('rapidx_user_details.email')->flatten(0)->filter()->toArray();
            $user_email = collect($get_user)->pluck('rapidx_user_details.email')->flatten(0)->filter()->toArray();

            $subject = "Open For Reconciliation <ARRS Generated Email Do Not Reply>";
            $data = [
                'dept'           => $request->dept,
                'classification' => $request->classification,
                'from'           => $request->from,
                'to'             => $request->to
            ];

            $this->mailSender->send_mail('open_recon', $data, $request, $admin_email, $user_email, $subject);
            

            DB::commit();

            return response()->json([
                'result' => true,
                'msg' => 'Transaction Successful!'
            ]);
            
            
        }
        catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }
}
