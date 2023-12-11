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
        DB::beginTransaction();

        try{
            if($request->adminDisRemarks == "" || $request->adminDisRemarks == null){ // approve
                ReconRequest::with([
                    'recon_remarks'
                ])
                ->where('ctrl_num', $request->dtParams['ctrl_number'])
                ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                ->update([
                    'status' => 1
                ]);
        
                // ReconRequestRemarks::where('recon_request_ctrl_num',$request->dtParams['ctrl_number'])
                // ->where('recon_request_ctrl_num_ext', $request->dtParams['ctrl_ext'])
                // ->update([
                //     'approver_remarks' => $request->adminDisRemarks
                // ]);
    
                DB::commit();
    
                return response()->json([
                    'result'    => 1,
                    'msg'       => 'Successfully Approved'
                ]);
            }
            else{ // disapprove
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
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
       
    }
}
