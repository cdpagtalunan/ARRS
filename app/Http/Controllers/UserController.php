<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth; // or use Illuminate\Support\Facades\Auth;
use DataTables;

/**
 * Import Models here
 */
use App\Models\User;
use App\Models\UserLevel;

class UserController extends Controller
{
    public function signIn(Request $request)
    {
        $data = array(
            'username' => $request->username,
            'password' => $request->password,
            // 'is_deleted' => 0
        );
        // return $data;

        $validator = Validator::make($data, [
            'username' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($validator->passes()) {
            if (Auth::attempt($data)) {
                if(Auth::user()->is_deleted == 1){
                    Auth::logout();
                    return response()->json(['isDeleted' => 1, 'error_message' => 'Your account was already deleted!']);
                }
                else if(Auth::user()->is_authenticated == 0){
                    Auth::logout();
                    return response()->json(['isAuthenticated' => 0, 'error_message' => 'Your account was already registered. Kindly wait for the approval of the Administrator']);
                }
                else if(Auth::user()->status == 0){
                    Auth::logout();
                    return response()->json(['inactive' => 0, 'error_message' => 'Your account is currently deactivated. Kindly contact the Administrator']);
                }
                // else if (Auth::user()->is_password_changed == 0) {
                //     return response()->json(['isPasswordChanged' => 0, 'error_message' => 'Change Password!']);
                // }
                else {
                    session_start();
                    $_SESSION["session_user_id"] = Auth::user()->id;
                    $_SESSION["session_user_level_id"] = Auth::user()->user_level_id;
                    $_SESSION["session_username"] = Auth::user()->username;
                    $_SESSION["session_firstname"] = Auth::user()->firstname;
                    $_SESSION["session_lastname"] = Auth::user()->lastname;
                    $_SESSION["session_email"] = Auth::user()->email;

                    return response()->json(['hasError' => 0]);
                }
            } else {
                return response()->json(['hasError' => 1,  'error_message' => 'We do not recognize your username and/or password. Please try again.']);
            }
        } else {
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function addUser(Request $request){
        date_default_timezone_set('Asia/Manila');

        $data = $request->all();
        $validator = Validator::make($data, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|alphaNum|min:8|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|alphaNum|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        } else {
            DB::beginTransaction();
            try {
                $userId = User::insertGetId([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'middle_initial' => $request->middle_initial,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'is_password_changed' => 0,
                    'user_level_id' => 3, // User
                    'created_at' => date('Y-m-d H:i:s'),
                    'is_deleted' => 0
                ]);

                // User::where('id', $userId)->update(['created_by' => $userId]);s
                DB::commit();
                return response()->json(['hasError' => 0]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['hasError' => 1, 'exceptionError' => $e]);
            }
        }
    }

    public function addUserAsAdmin(Request $request){
        date_default_timezone_set('Asia/Manila');
        session_start();

        $data = $request->all();
        if(!isset($request->user_id)){
            $validator = Validator::make($data, [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'username' => 'required|unique:users',
                'password' => 'required|alphaNum|min:8|required_with:confirm_password|same:confirm_password',
                'user_level' => 'required',
                'confirm_password' => 'required|alphaNum|min:8'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    $userId = User::insertGetId([
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'middle_initial' => $request->middle_initial,
                        'email' => $request->email,
                        'username' => $request->username,
                        'password' => Hash::make($request->password),
                        'is_password_changed' => 0,
                        'user_level_id' => $request->user_level,
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_deleted' => 0
                    ]);
    
                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                }
            }
        }else{
            /**
             * The uniqueness of the email and username should be correct logic.
             */
            $validator = Validator::make($data, [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required',
                'username' => 'required',
                'user_level' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                User::where('id', $request->user_id)->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'middle_initial' => $request->middle_initial,
                    'email' => $request->email,
                    'username' => $request->username,
                    'user_level_id' => $request->user_level,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_updated_by' => $_SESSION["session_user_id"]
                ]);
                return response()->json(['hasError' => 0, 'session'=> $_SESSION["session_user_id"]]);
            }
        }
        
    }

    public function viewUsers(){
        $userDetails = User::with('user_levels')->where('is_deleted', 0)->where('is_authenticated', 1)->get();
        
        return DataTables::of($userDetails)
            ->addColumn('status', function($userDetail){
                $result = "";
                if($userDetail->status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('is_authenticated', function($userDetail){
                $result = "";
                if($userDetail->is_authenticated == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Authorized</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Not Authorized</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($userDetail){
                if($userDetail->status == 1){
                    $result =   '<center>
                                <button type="button" class="btn btn-primary btn-xs text-center actionEditUser mr-1" user-id="' . $userDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalAddUser" title="Edit User Details">
                                    <i class="fa fa-xl fa-edit"></i> 
                                </button>
                                <button type="button" class="btn btn-danger btn-xs text-center actionEditUserStatus mr-1" user-id="' . $userDetail->id . '" user-status="' . $userDetail->status . '" data-bs-toggle="modal" data-bs-target="#modalEditUserStatus" title="Deactivate User">
                                    <i class="fa-solid fa-xl fa-ban"></i>
                                </button>
                            </center>';
                }else{
                    $result =   '<center>
                                <button type="button" class="btn btn-primary btn-xs text-center actionEditUser mr-1" user-id="' . $userDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalAddUser" title="Edit User Details">
                                    <i class="fa fa-xl fa-edit"></i> 
                                </button>
                                <button type="button" class="btn btn-warning btn-xs text-center actionEditUserStatus mr-1" user-id="' . $userDetail->id . '" user-status="' . $userDetail->status . '" data-bs-toggle="modal" data-bs-target="#modalEditUserStatus" title="Activate User">
                                    <i class="fa-solid fa-xl fa-arrow-rotate-right"></i>
                                </button>
                            </center>';
                }
                return $result;
            })
        ->rawColumns(['status', 'action', 'is_authenticated'])
        ->make(true);
    }

    public function viewPendingUsers(){
        $userDetails = User::with('user_levels')->where('is_deleted', 0)->where('is_authenticated', 0)->get();
        
        return DataTables::of($userDetails)
            ->addColumn('status', function($userDetail){
                $result = "";
                if($userDetail->status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('is_authenticated', function($userDetail){
                $result = "";
                if($userDetail->is_authenticated == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Authorized</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Not Authorized</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($userDetail){
                if($userDetail->status == 1){
                    $result =   '<center>
                                <button type="button" class="btn btn-primary btn-xs text-center actionEditUser" user-id="' . $userDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalAddUser" title="Edit User Details">
                                    <i class="fa fa-xl fa-edit"></i> 
                                </button>
                                <button type="button" class="btn btn-danger btn-xs text-center actionEditUserStatus mr-1" user-id="' . $userDetail->id . '" user-status="' . $userDetail->status . '" data-bs-toggle="modal" data-bs-target="#modalEditUserStatus" title="Deactivate User">
                                    <i class="fa-solid fa-xl fa-ban"></i>
                                </button>';
                    if($userDetail->is_authenticated == 1){
                        $result .= '<button type="button" class="btn btn-success btn-xs text-center actionEditUserAuthentication" user-id="' . $userDetail->id . '" user-authentication="' . $userDetail->is_authenticated . '" data-bs-toggle="modal" data-bs-target="#modalEditUserAuthentication" title="Approve User Request">
                                        <i class="fa-solid fa-xl fa-ban"></i>
                                    </button>';
                    }else{
                        $result .= '<button type="button" class="btn btn-success btn-xs text-center actionEditUserAuthentication" user-id="' . $userDetail->id . '" user-authentication="' . $userDetail->is_authenticated . '" data-bs-toggle="modal" data-bs-target="#modalEditUserAuthentication" title="Approve User Request">
                                        <i class="fa-solid fa-xl fa-square-check"></i>
                                    </button>';
                    }
                    $result .=  '</center>';
                }else{
                    $result =   '<center>
                                <button type="button" class="btn btn-primary btn-xs text-center actionEditUser" user-id="' . $userDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalAddUser" title="Edit User Details">
                                    <i class="fa fa-xl fa-edit"></i> 
                                </button>
                                <button type="button" class="btn btn-warning btn-xs text-center actionEditUserStatus mr-1" user-id="' . $userDetail->id . '" user-status="' . $userDetail->status . '" data-bs-toggle="modal" data-bs-target="#modalEditUserStatus" title="Activate User">
                                    <i class="fa-solid fa-xl fa-arrow-rotate-right"></i>
                                </button>';
                    if($userDetail->is_authenticated == 1){
                        $result .= '<button type="button" class="btn btn-success btn-xs text-center actionEditUserAuthentication" user-id="' . $userDetail->id . '" user-authentication="' . $userDetail->is_authenticated . '" data-bs-toggle="modal" data-bs-target="#modalEditUserAuthentication" title="Approve User Request">
                                        <i class="fa-solid fa-xl fa-ban"></i>
                                    </button>';
                    }else{
                        $result .= '<button type="button" class="btn btn-success btn-xs text-center actionEditUserAuthentication" user-id="' . $userDetail->id . '" user-authentication="' . $userDetail->is_authenticated . '" data-bs-toggle="modal" data-bs-target="#modalEditUserAuthentication" title="Approve User Request">
                                        <i class="fa-solid fa-xl fa-square-check"></i>
                                    </button>';
                    }
                    $result .=  '</center>';
                }
                return $result;
            })
        ->rawColumns(['status', 'action', 'is_authenticated'])
        ->make(true);
    }

    public function getUserById(Request $request){
        $userDetails = User::with('user_levels')->where('id', $request->userId)->get();
        // echo $userDetails;
        return response()->json(['userDetails' => $userDetails]);
    }

    public function editUserStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();

        $data = $request->all(); // collect all input fields

        $validator = Validator::make($data, [
            'user_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()){
            if($request->status == 1){
                User::where('id', $request->user_id)
                    ->update([
                            'status' => 0,
                            'last_updated_by' => $_SESSION['session_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = User::where('id', $request->user_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                User::where('id', $request->user_id)
                    ->update([
                            'status' => 1,
                            'last_updated_by' => $_SESSION['session_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = User::where('id', $request->user_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
                
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function editUserAuthentication(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();

        $data = $request->all(); // collect all input fields

        $validator = Validator::make($data, [
            'user_id' => 'required',
            'authentication' => 'required',
        ]);

        if($validator->passes()){
            if($request->authentication == 1){
                User::where('id', $request->user_id)
                    ->update([
                            'is_authenticated' => 0,
                            'last_updated_by' => $_SESSION['session_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $authentication = User::where('id', $request->user_id)->value('is_authenticated');
                return response()->json(['hasError' => 0, 'authentication' => (int)$authentication]);
            }else{
                User::where('id', $request->user_id)
                    ->update([
                            'is_authenticated' => 1,
                            'last_updated_by' => $_SESSION['session_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $authentication = User::where('id', $request->user_id)->value('is_authenticated');
                return response()->json(['hasError' => 0, 'authentication' => (int)$authentication]);
            }
                
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function getDataForDashboard(){
        // $pending_claim = CustomerClaim::where('status', 0)->get();
        $totalUsers = User::where('is_authenticated', 1)->get();
        $totalPendingUsers = User::where('is_authenticated', 0)->get();
        return response()->json(['totalUsers' => count($totalUsers), 'totalPendingUsers' => count($totalPendingUsers)]);
    }



    public function logout(){
        session_start();
        session_unset();
        session_destroy();
        Auth::logout();
        return response()->json(['result' => "1"]);
    }

    public function checkSession(){
        session_start();
        $session = $_SESSION;
        return response()->json(['session' => $session]);
    }


    public function getUserLevels(Request $request){
        $userLevels = UserLevel::where('is_deleted', 0)->get();
        return response()->json(['userLevels' => $userLevels]);
    }
}
