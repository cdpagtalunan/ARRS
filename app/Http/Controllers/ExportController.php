<?php

namespace App\Http\Controllers;

use Helpers;
use Carbon\Carbon;
use App\Exports\Recon;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use App\Models\Reconciliation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; 

class ExportController extends Controller
{
    public function export(Request $request, $id, $date_range, $access){

        $decrypt_id = Helpers::decryptId($id);

        $exploded_cutoff = explode('to', $date_range);
        $from = Carbon::createFromFormat('m-d-Y', trim($exploded_cutoff[0]));
        $rec_from = $from->format('Y-m-d');
        $to = Carbon::createFromFormat('m-d-Y',  trim($exploded_cutoff[1]));
        $rec_to = $to->format('Y-m-d');
        // return $rec_to;
        $exploded_access = explode(',',$access);
        $user_cat = UserCategory::whereIn('id', $exploded_access)
        ->whereNull('deleted_at')
        ->orderBy('classification', 'ASC')
        ->get(['classification', 'department']);

        $recon_details = array();
        $recon_cat = array();
        for($x = 0; $x < count($user_cat); $x++){
            array_push($recon_cat, $user_cat[$x]->classification."-".$user_cat[$x]->department);


            // return  $user_cat[$x]->department;
            // $recon_data = DB::connection('mysql')
            // ->select("
            //     SELECT * FROM 
            //     reconciliations 
            //     WHERE `classification` = '".$user_cat[$x]->classification."' 
            //     AND `pr_num` LIKE '%".$user_cat[$x]->department."%'
            //     AND `deleted_at` IS NULL
            //     AND recon_date_from >= '".$rec_from."' AND recon_date_to <= '".$rec_to."'
            // ");

            // $recon_data = DB::connection('mysql')
            // ->table('reconciliations')
            // ->select("*")
            // ->where('classification', $user_cat[$x]->classification)
            // ->where('pr_num', 'LIKE', "%{$user_cat[$x]->department}%")
            // ->whereNull('deleted_at')
            // ->where('recon_date_from', '>=', $rec_from)
            // ->where('recon_date_to', '<=', $rec_to)
            // ->get();


            if(strtoupper($user_cat[$x]->department) == 'STAMPING'){
                $recon_data = DB::connection('mysql')
                ->table('reconciliations')
                ->whereNull('deleted_at')
                ->where('logdel', 0)
                ->where('classification', $user_cat[$x]->classification)
                ->where('recon_date_from', '>=', $rec_from)
                ->where('recon_date_to', '<=', $rec_to)
                ->where('allocation', 'LIKE', '%stamping%')
                ->orderBy('supplier', 'ASC')
                ->select('*')
                ->get();
            }
            else{
                if($user_cat[$x]->department == 'PPD-GRIN'){
                    $recon_data = DB::connection('mysql')
                    ->table('reconciliations')
                    ->whereNull('deleted_at')
                    ->where('logdel', 0)
                    ->where('requisitioner', "Carlo Olanga")
                    ->where('classification', $user_cat[$x]->classification)
                    ->where('recon_date_from', '>=', $rec_from)
                    ->where('recon_date_to', '<=', $rec_to)
                    ->where('allocation', 'NOT LIKE', '%stamping%')
                    ->orderBy('supplier', 'ASC')
                    ->select('*')
                    ->get();
                }
                else{
                    $recon_data = DB::connection('mysql')
                    ->table('reconciliations')
                    ->whereNull('deleted_at')
                    ->where('logdel', 0)
                    ->where('pr_num', 'LIKE', "%{$user_cat[$x]->department}%")
                    ->where('classification', $user_cat[$x]->classification)
                    ->where('requisitioner',"<>", "Carlo Olanga")
                    ->where('recon_date_from', '>=', $rec_from)
                    ->where('recon_date_to', '<=', $rec_to)
                    ->where('allocation', 'NOT LIKE', '%stamping%')
                    ->orderBy('supplier', 'ASC')
                    ->select('*')
                    ->get();
                }
            }
          

            $recon_data_usd = collect($recon_data)->where('currency', 'USD')->flatten(0);
            $recon_data_usd_supplier = collect($recon_data_usd)->pluck('supplier')->unique()->flatten(0);

            $recon_data_php = collect($recon_data)->where('currency', 'PHP')->flatten(0);
            $recon_data_php_supplier = collect($recon_data_php)->pluck('supplier')->unique()->flatten(0);

            $recon_data_valid_supplier = collect($recon_data)->pluck('recon_status')->unique()->flatten(0);

            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['valid'] = $recon_data_valid_supplier;


            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['usd']['supplier'] = $recon_data_usd_supplier;
            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['usd']['recons'] = $recon_data_usd;

            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['php']['supplier'] = $recon_data_php_supplier;
            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['php']['recons'] = $recon_data_php;

        }
        
        // return $recon_details;


        $date = date('Ymd',strtotime(NOW()));
        return Excel::download(new Recon($date, $recon_details, $user_cat, $rec_to, $rec_from), 'Reconciliation.xlsx');
    }

    public function export_admin(Request $request, $date){
        $user_cat = UserCategory::whereNull('deleted_at')
        ->orderBy('classification', 'ASC')
        ->get(['classification', 'department']);

        $exploded_cutoff = explode('to', $date);
        $from = Carbon::createFromFormat('m-d-Y', trim($exploded_cutoff[0]));
        $rec_from = $from->format('Y-m-d');
        $to = Carbon::createFromFormat('m-d-Y',  trim($exploded_cutoff[1]));
        $rec_to = $to->format('Y-m-d');

        $recon_details = array();
        $recon_cat = array();

        for($x = 0; $x < count($user_cat); $x++){
            array_push($recon_cat, $user_cat[$x]->classification."-".$user_cat[$x]->department);
            // $recon_data = DB::connection('mysql')
            // ->select("
            //     SELECT * FROM 
            //     reconciliations 
            //     WHERE `classification` = '".$user_cat[$x]->classification."' 
            //     AND `pr_num` LIKE '%".$user_cat[$x]->department."%'
            //     AND `deleted_at` IS NULL
            //     AND recon_date_from >= '".$rec_from."' AND recon_date_to <= '".$rec_to."'
            // ");
            if(strtoupper($user_cat[$x]->department) == 'STAMPING'){
                $recon_data = DB::connection('mysql')
                ->table('reconciliations')
                ->whereNull('deleted_at')
                ->where('logdel', 0)
                ->where('classification', $user_cat[$x]->classification)
                ->where('recon_date_from', '>=', $rec_from)
                ->where('recon_date_to', '<=', $rec_to)
                ->where('allocation', 'LIKE', '%stamping%')
                ->orderBy('supplier', 'ASC')
                ->select('*')
                ->get();
            }
            else{
                // ! Remove IfElse and uncomment the query below when carlo olanga is already using the new user with section ppd-grinding
                if($user_cat[$x]->department == 'PPD-GRIN'){
                    $recon_data = DB::connection('mysql')
                    ->table('reconciliations')
                    ->where('logdel', 0)
                    ->whereNull('deleted_at')
                    ->where('requisitioner', "Carlo Olanga")
                    ->where('classification', $user_cat[$x]->classification)
                    ->where('recon_date_from', '>=', $rec_from)
                    ->where('recon_date_to', '<=', $rec_to)
                    ->where('allocation', 'NOT LIKE', '%stamping%')
                    ->orderBy('supplier', 'ASC')
                    ->select('*')
                    ->get();
                }
                else{
                    $recon_data = DB::connection('mysql')
                    ->table('reconciliations')
                    ->whereNull('deleted_at')
                    ->where('logdel', 0)
                    ->where('pr_num', 'LIKE', "%{$user_cat[$x]->department}%")
                    ->where('classification', $user_cat[$x]->classification)
                    ->where('requisitioner',"<>", "Carlo Olanga")
                    ->where('recon_date_from', '>=', $rec_from)
                    ->where('recon_date_to', '<=', $rec_to)
                    ->where('allocation', 'NOT LIKE', '%stamping%')
                    ->orderBy('supplier', 'ASC')
                    ->select('*')
                    ->get();
                }
                // ! Uncomment this
                // $recon_data = DB::connection('mysql')
                // ->table('reconciliations')
                // ->whereNull('deleted_at')
                // ->where('pr_num', 'LIKE', "%{$user_cat[$x]->department}%")
                // ->where('classification', $user_cat[$x]->classification)
                // ->where('requisitioner',"<>", "Carlo Olanga")
                // ->where('recon_date_from', '>=', $rec_from)
                // ->where('recon_date_to', '<=', $rec_to)
                // ->where('allocation', 'NOT LIKE', '%stamping%')
                // ->select('*')
                // ->get();
            }
            $recon_data_usd = collect($recon_data)->where('currency', 'USD')->flatten(0);
            $recon_data_usd_supplier = collect($recon_data_usd)->pluck('supplier')->unique()->flatten(0);

            $recon_data_php = collect($recon_data)->where('currency', 'PHP')->flatten(0);
            $recon_data_php_supplier = collect($recon_data_php)->pluck('supplier')->unique()->flatten(0);

            // $recon_data_valid_supplier = collect($recon_data)->pluck('recon_status')->unique()->flatten(0);

            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['valid'] = '[1]';


            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['usd']['supplier'] = $recon_data_usd_supplier;
            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['usd']['recons'] = $recon_data_usd;

            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['php']['supplier'] = $recon_data_php_supplier;
            $recon_details[$user_cat[$x]->classification."-".$user_cat[$x]->department]['php']['recons'] = $recon_data_php;

        }

        // return $recon_details;
        return Excel::download(new Recon($date, $recon_details, $user_cat, $rec_to, $rec_from), 'Reconciliation.xlsx');

    }
}
