<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\ReconRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReconRequestRemarks;

class RequestController extends Controller
{
    public function get_add_request(Request $request){
        $recon_request = ReconRequest::whereNull('deleted_at')
        ->whereNull('recon_fkid')
        ->distinct()
        ->get(['ctrl_num_ext', 'status', 'ctrl_num','po_num', 'recon_fkid']);

        // $recon_request = DB::connection('mysql')->table('recon_requests')
        // ->select('ctrl_num_ext', 'status', 'ctrl_num')
        // ->whereNull('deleted_at')
        // ->get();

        return DataTables::of($recon_request)
        ->addColumn('action', function($recon_request){
            $result = "";
            $result .="<center>";
            $result .= "<button class='btn btn-sm btn-info text-light btnViewReconRequest' title='See more' 
                        data-ctrl='$recon_request->ctrl_num' 
                        data-ctrlExt='$recon_request->ctrl_num_ext'
                        data-status='$recon_request->status'
                        >
                            <i class='fas fa-eye'></i>
                        </button>";
            $result .="</center>";
            return $result;
        })
        ->addColumn('req_status', function($recon_request){
            $result = "";
            $result .= "<center>";
            if($recon_request->status == 0){
                $result .= "<span class='badge rounded-pill text-bg-warning'>For Approval</span>";
            }
            else if($recon_request->status == 1){
                $result .= "<span class='badge rounded-pill text-bg-success'>Approved</span>";
            }
            else if($recon_request->status == 2){
                $result .= "<span class='badge rounded-pill text-bg-danger'>Disapproved</span>";
            }
          
            $result .= "</center>";

            return $result;
        })
        ->addColumn('control', function($recon_request){
            return "$recon_request->ctrl_num-$recon_request->ctrl_num_ext";
        })
        ->rawColumns(['req_status', 'action', 'control'])
        ->make(true);

    }

    public function get_remove_request(Request $request){
        $recon_request = ReconRequest::with([
            'recon_details'
        ])
        ->whereNull('deleted_at')
        ->whereNotNull('recon_fkid')
        ->get();

        return DataTables::of($recon_request)
        ->addColumn('action', function($recon_request){
            $result = "";
            $result = "";
            $result .="<center>";
            $result .= "<button class='btn btn-sm btn-info text-light btnViewRequestRemove' title='See more' 
                        data-ctrl='$recon_request->ctrl_num' 
                        data-ctrlExt='$recon_request->ctrl_num_ext'
                        data-status='$recon_request->status'
                        >
                            <i class='fas fa-eye'></i>
                        </button>";
            $result .="</center>";
            return $result;
        })
        ->addColumn('status', function($recon_request){
            $result = "";
            $result .= "<center>";
            if($recon_request->status == 0){
                $result .= "<span class='badge rounded-pill text-bg-warning'>For Approval</span>";
            }
            else if($recon_request->status == 1){
                $result .= "<span class='badge rounded-pill text-bg-success'>Approved</span>";
            }
            else{
                $result .= "<span class='badge rounded-pill text-bg-danger'>Disapproved</span>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('control', function($recon_request){
            return "$recon_request->ctrl_num-$recon_request->ctrl_num_ext";
        })
        ->rawColumns(['action', 'status', 'control'])
        ->make(true);

    }

    public function view_request_details(Request $request){
        $request_details = ReconRequest::with([
            'recon_remarks'
        ])
        ->where('ctrl_num', $request->param['ctrl_number'])
        ->where('ctrl_num_ext', $request->param['ctrl_ext'])
        ->whereNull('deleted_at')
        ->get();
        return DataTables::of($request_details)
        ->make(true);
    }

    public function response_request(Request $request){
        date_default_timezone_set('Asia/Manila');
        // return $request->all();
        DB::beginTransaction();

        try{
            if($request->adminDisRemarks == "" || $request->adminDisRemarks == null){ // approve
                if($request->dtParams['type'] == 1){ // For Adding Function
                    ReconRequest::with([
                        'recon_remarks'
                    ])
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->update([
                        'status' => 1
                    ]);

                    /*
                        * Query that will copy the table data of recon_requests to reconciliations
                        * when approved by logistics
                    */
                    DB::connection('mysql')
                    ->statement('
                        INSERT INTO reconciliations (
                            `po_date`,`po_num`,`pr_num`,`prod_code`,`prod_name`,`prod_desc`,
                            `supplier`,`currency`,`uom`,`unit_price`,`received_qty`,`po_balance`,
                            `pic`,`received_date`,`delivery_date`,`received_by`,`invoice_no`,`rcv_no`,
                            `classification`,`allocation`,`po_remarks`,`hold_remarks`,
                            `recon_date_from`,`recon_date_to`
                        )
                        SELECT
                        `po_date`,`po_num`,`pr_num`,`prod_code`,`prod_name`,`prod_desc`,
                        `supplier`,`currency`,`uom`,`unit_price`,`received_qty`,`po_balance`,
                        `pic`,`received_date`,`delivery_date`,`received_by`,`invoice_no`,`rcv_no`,
                        `classification`,`allocation`,`po_remarks`,`hold_remarks`,
                        `recon_date_from`,`recon_date_to`
                        FROM recon_requests WHERE ctrl_num = "'.$request->dtParams['ctrl_number'].'" 
                        AND ctrl_num_ext = "'.$request->dtParams['ctrl_ext'].'"
                    ');
                     /*
                        ! END of QUERY
                    */

                    DB::commit();
        
                    return response()->json([
                        'result'    => 1,
                        'msg'       => 'Successfully Approved'
                    ]);
                }
                else{ // For Removing Function

                    $recon_remove_req = ReconRequest::with(['recon_details'])
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->first();

                    $recon_remove_req->status = 1;
                    $recon_remove_req->recon_details->deleted_at = NOW();
                    $recon_remove_req->push();
                    DB::commit();

                    return response()->json([
                        'result'    => 2,
                        'msg'       => 'Successfully Approved'
                    ]);
                }
              
            }
            else{ // disapprove

                if($request->dtParams['type'] == 1){ // For Adding Function
                    ReconRequest::with([
                        'recon_remarks'
                    ])
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->update([
                        'status' => 2
                    ]);
            
                    ReconRequestRemarks::where('recon_request_ctrl_num',$request->dtParams['ctrl_number'])
                    ->where('recon_request_ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->update([
                        'approver_remarks' => $request->adminDisRemarks
                    ]);
        
                    DB::commit();
        
                    return response()->json([
                        'result'    => 1,
                        'msg'       => 'Successfully Disapproved'
                    ]);
                }
                else{
                    $recon_remove_req = ReconRequest::with([
                        'recon_remarks',
                        'recon_details'
                    ])
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->first();
            
                    $recon_remove_req->status = 2;
                    $recon_remove_req->recon_details->recon_status = 0;
                    $recon_remove_req->push();
                    DB::commit();

                    return response()->json([
                        'result'    => 2,
                        'msg'       => 'Successfully Approved'
                    ]);
            
                }
              
            }
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
       
    }

    public function get_remove_recon_details(Request $request){
        $json_decode = json_decode($request->param);

        $recon_rem_details = ReconRequest::with([
            'recon_details'
        ])
        ->where('ctrl_num',  $json_decode->ctrl_number)
        ->where('ctrl_num_ext',  $json_decode->ctrl_ext)
        ->first();

        return $recon_rem_details;
    }
}
