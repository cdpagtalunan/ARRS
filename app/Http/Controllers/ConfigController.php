<?php

namespace App\Http\Controllers;

use Helpers;
use DataTables;
use App\Models\CutOff;

use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EprpoClassification;
use App\Http\Requests\CutoffRequest;
use App\Http\Requests\CategoryRequest;

class ConfigController extends Controller
{
    public function get_cutoff(Request $request){
        $cutoff_data = CutOff::all();
        
        return response()->json(['data' => $cutoff_data]);
    }
    public function save_cutoff(CutoffRequest $request){
        date_default_timezone_set('Asia/Manila');
        $fields = $request->validated();
        // return $request->all();
        DB::beginTransaction();
        try{

            $cutoff_array = array(
                'day_from'      => $request->froms,
                'day_to'        => $request->to,
                'cut_off'       => $request->cutoff,
                'day_email'     => $request->dateEmail
            );
            if(isset($request->id)){ // EDIT
                $cutoff_array['updated_at'] = NOW();

                DB::connection('mysql')->table('cut_offs')
                ->where('id', $request->id)
                ->update($cutoff_array);
                DB::commit();
                return response()->json([
                    'result'    => 1,
                    'msg'       => 'Successfully Added!'
                ]);
            }
            else{  // ADD
                $cutoff_array['created_at'] = NOW();
                if(!CutOff::whereNull('deleted_at')->find($request->cutoff)){
                    DB::connection('mysql')->table('cut_offs')
                    ->insert($cutoff_array);
                    DB::commit();
                    return response()->json([
                        'result'    => 1,
                        'msg'       => 'Successfully Added!'
                    ]);
                }
                else{
                    return response()->json([
                        'msg'       => 'Cutoff already exist!'
                    ],405);
                }
            }
        }
        catch(Exception $e){
            DB::rollback();
            return $e;
        }

        // CutOff::insert([
        //     'day_from' => 1,
        //     'day_to' => 1,
        //     'cut_off' => 1,
        //     'day_email' => 1
        // ]);
        return response()->json(['test' => 1]);
    }
    public function get_cutoff_details(Request $request){
        $cutoff_details = DB::connection('mysql')->table('cut_offs')->where('id', $request->id)
        ->first();

        return response()->json(['cutoffDetails' => $cutoff_details]);
    }
    public function get_category(Request $request){
        // $user_cat_details = DB::connection('mysql')->table('user_categories')
        // ->select('*');

        $user_cat_details = UserCategory::select('*');

        return DataTables::of($user_cat_details)
        ->addColumn('action', function($user_cat_details){
            $encrypted_id = Helpers::encryptId($user_cat_details->id);

            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-secondary btn-sm btnEditCat mr-1' data-cat='$encrypted_id'><i class='fas fa-pen-to-square'></i></button>";
            if($user_cat_details->deleted_at == null){
                $result .= "<button class='btn btn-danger btn-sm btnUpdatecatStat' data-cat='$encrypted_id' data-name='0'><i class='fas fa-x'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-success btn-sm btnUpdatecatStat' data-cat='$encrypted_id' data-name='1'><i class='fas fa-arrow-rotate-right'></i></button>";
            }

            $result .= "</center>";
            return $result;
        })
        ->addColumn('status', function($user_cat_details){
            $result = "";
            $result .= "<center>";
            if($user_cat_details->deleted_at == null){
                $result .= "<span class='badge rounded-pill bg-success'>active</span>";
            }
            else{
                $result .= "<span class='badge rounded-pill bg-danger'>Inactive</span>";
            }
            $result .= "</center>";
            return $result;
        })
        // ->addColumn('classification', function)
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function get_dropdown_data(Request $request){
        $classification_data = DB::connection('mysql_eprpo')->table('classification_code')
        ->where('status' ,'ACTIVE')
        ->select('acct_code')
        ->get();

        $classArray = [];
        for ($i=0; $i <count($classification_data) ; $i++) { 
            array_push($classArray, $classification_data[$i]->acct_code);
        }

        // return $classArray;
        // return $classification_data;
        $sect_dept = DB::connection('mysql_eprpo')->table('section_department')
        // ->where('status', 'ACTIVE')
        ->select('section_department_code')
        ->get();

        $deptArray = [];
        for ($i=0; $i <count($sect_dept) ; $i++) { 
            array_push($deptArray, $sect_dept[$i]->section_department_code);
        }
        return response()->json(['classCode' => $classArray, 'secDept' => $deptArray]);
    }

    public function save_cat(CategoryRequest $request){
        date_default_timezone_set('Asia/Manila');
        $fields = $request->validated();

        DB::beginTransaction();
        try {
            // return $request->classification['acct_code'];
            $cat_array = array(
                'classification'    => $request->classification,
                'department'        => $request->department
            );
            if(isset($request->catId)){ // EDIT
                $cat_array['updated_by'] = $_SESSION['rapidx_username'];
                $cat_array['updated_at'] = NOW();
                $encrypted_id = Helpers::decryptId($request->catId);

                DB::connection('mysql')->table('user_categories')
                ->where('id', $encrypted_id)
                ->update($cat_array);
                
                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Successfully Edited!']);

            }
            else{ // ADD
                $cat_array['created_by'] = $_SESSION['rapidx_username'];
                $cat_array['created_at'] = NOW();
                DB::connection('mysql')->table('user_categories')
                ->insert($cat_array);
                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Successfully Added!']);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }

    }

    public function get_category_details(Request $request){
        $encrypted_id = Helpers::decryptId($request->cat);

        $category_details = UserCategory::where('id', $encrypted_id)
        ->first();

        $encrypted_id = Helpers::encryptId($encrypted_id);

        return response()->json(['catDetails' => $category_details, 'cat' => $encrypted_id]);
    }

    public function update_cat_status(Request $request){
        date_default_timezone_set('Asia/Manila');
        $encrypted_id = Helpers::decryptId($request->cat);

        DB::beginTransaction();
        try{
            $update_array = array(
                'updated_at' => NOW(),
                'updated_by' => $_SESSION['rapidx_username']
            );
            if($request->function == 0){
                $update_array['deleted_at'] = NOW();
            }
            else{
                $update_array['deleted_at'] = null;
            }

            // * REMOVAL ON USER ACCESS OF CATEGORY ID THAT WILL BE INACTIVE
            $user_with_inactive_access = DB::connection('mysql')
            ->table('user_accesses')
            ->whereRaw('FIND_IN_SET("'.$encrypted_id.'", category_id)')
            ->update([
                "category_id" => DB::raw("TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `category_id`, ','), ',$encrypted_id,', ','))")
            ]);
            // * END
          
            
            DB::connection('mysql')->table('user_categories')
            ->where('id', $encrypted_id)
            ->update($update_array);

            DB::commit();

            return response()->json([
                'result'    => 1,
                'msg'       => 'Successfully Updated!'
            ]);
                
        }catch(Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function update_status(Request $request){
        date_default_timezone_set('Asia/Manila');
        DB::beginTransaction();
        try{
            if($request->func == 0){
                DB::connection('mysql')->table('cut_offs')
                ->where('id', $request->id)
                ->update([
                    'deleted_at' => NOW()
                ]);
            }
            else{
                DB::connection('mysql')->table('cut_offs')
                ->where('id', $request->id)
                ->update([
                    'deleted_at' => null
                ]);
            }
            DB::commit();
            return response()->json([
                'msg' => 'Successfully Updated!'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return $e;
        }
    }

}
