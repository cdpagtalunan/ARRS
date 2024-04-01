<?php

namespace App\Http\Controllers;

use Mail;
use Helpers;
use DataTables;
use App\Models\UserAccess;
use App\Models\ReconRequest;
use App\Models\UserCategory;
use Illuminate\Http\Request;

use App\Models\Reconciliation;
use Illuminate\Support\Facades\DB;
use App\Models\ReconRequestRemarks;
use App\Http\Controllers\ReconciliationController;

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
        ->where('request_type', 1)
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
            // * Get Email recipient
            $exploded_data = explode('-', $request->dtParams['ctrl_number']);
            $get_cat = UserCategory::where('classification', $exploded_data[1])
            ->where('department', $exploded_data[0])
            ->whereNull('deleted_at')
            ->first('id');
    
            $get_all_user = UserAccess::with([
                'rapidx_user_details'
            ])
            ->whereNull('deleted_at')
            ->whereRaw('FIND_IN_SET("'.$get_cat->id.'", category_id)')
            ->get();

            $admin_email = collect($get_all_user)->where('user_type', 1)->pluck('rapidx_user_details.email')->flatten(0)->toArray();
            $user_email = collect($get_all_user)->where('user_type', 2)->pluck('rapidx_user_details.email')->flatten(0)->toArray();
            // * END

            if($request->adminDisRemarks == "" || $request->adminDisRemarks == null){ // approve
                if($request->dtParams['type'] == 1){ // For Adding Function

                    ReconRequest::with([
                        'recon_remarks'
                    ])
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->whereNull('deleted_at')
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

                    $recon_data = ReconRequest::where('status', 1)
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->get();

                    // * For Email
                    $data = array(
                        'type' => "Approved",
                        'function' => 'add',
                        'control' => $request->dtParams['ctrl_number']."-".$request->dtParams['ctrl_ext'], // change to $control-$control_ext
                        'recon_data' => $recon_data,
                    //  'user_remarks' => $request->reasons,
                        // 'cutoff_date_req' => $request->cutoff_date,
                        'requestor' => $_SESSION['rapidx_name']
                    );
                    
                    Mail::send('mail.admin_response', $data, function($message) use ($request, $admin_email, $user_email){
                        $message->to($user_email);
                        $message->cc($admin_email);
                        $message->bcc('cpagtalunan@pricon.ph');
                        $message->subject("Approved Reconciliation Request <ARRS Generated Email Do Not Reply>");
                    });
                    // * End Email

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

                    $data = array(
                        'type' => "Approved",
                        'function' => 'remove',
                        'control' => $request->dtParams['ctrl_number']."-".$request->dtParams['ctrl_ext'], // change to $control-$control_ext
                        'remove_request_data' => $recon_remove_req,
                    //  'user_remarks' => $request->reasons,
                        // 'cutoff_date_req' => $request->cutoff_date,
                        'requestor' => $_SESSION['rapidx_name']
                    );
                    // return $data;
                    Mail::send('mail.admin_response', $data, function($message) use ($request, $admin_email, $user_email){
                        $message->to($user_email);
                        $message->cc($admin_email);
                        $message->bcc('cpagtalunan@pricon.ph');
                        $message->subject("Approved Reconciliation Request <ARRS Generated Email Do Not Reply>");
                    });

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
                // return $request->all();
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

                    // * For Email
                    $recon_data = ReconRequest::with([
                        'recon_remarks'
                    ])
                    ->where('status', 2)
                    ->where('ctrl_num', $request->dtParams['ctrl_number'])
                    ->where('ctrl_num_ext', $request->dtParams['ctrl_ext'])
                    ->get();

                    $data = array(
                        'type' => "Disapproved",
                        'function' => 'add',
                        'control' => $request->dtParams['ctrl_number']."-".$request->dtParams['ctrl_ext'], // change to $control-$control_ext
                        'recon_data' => $recon_data,
                        'user_remarks' => $request->adminDisRemarks,
                        // 'cutoff_date_req' => $request->cutoff_date,
                        'requestor' => $_SESSION['rapidx_name']
                    );
                    
                    Mail::send('mail.admin_response', $data, function($message) use ($request, $admin_email, $user_email){
                        $message->to($user_email);
                        $message->cc($admin_email);
                        $message->bcc('cpagtalunan@pricon.ph');
                        $message->subject("Disapproved Reconciliation Request <ARRS Generated Email Do Not Reply>");
                    });

                    // * END
        
                    // DB::commit();
        
                    // return response()->json([
                    //     'result'    => 1,
                    //     'msg'       => 'Successfully Disapproved'
                    // ]);
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

                    // * For Email
                    $data = array(
                        'type' => "Disapproved",
                        'function' => 'remove',
                        'control' => $request->dtParams['ctrl_number']."-".$request->dtParams['ctrl_ext'], // change to $control-$control_ext
                        'remove_request_data' => $recon_remove_req,
                        'user_remarks' => $request->adminDisRemarks,
                        // 'cutoff_date_req' => $request->cutoff_date,
                        'requestor' => $_SESSION['rapidx_name']
                    );
                    
                    Mail::send('mail.admin_response', $data, function($message) use ($request, $admin_email, $user_email){
                        $message->to($user_email);
                        $message->cc($admin_email);
                        $message->bcc('cpagtalunan@pricon.ph');
                        $message->subject("Disapproved Reconciliation Request <ARRS Generated Email Do Not Reply>");
                    });

                    // * END
                    // DB::commit();

                    // return response()->json([
                    //     'result'    => 2,
                    //     'msg'       => 'Successfully Disapproved'
                    // ]);
            
                }

                DB::commit();

                return response()->json([
                    'result'    => 2,
                    'msg'       => 'Successfully Disapproved'
                ]);
              
            }
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
       
    }

    public function get_requested_recon_details(Request $request){
        $json_decode = json_decode($request->param);

        $recon_rem_details = ReconRequest::with([
            'recon_details',
            'recon_remarks'
        ])
        ->where('ctrl_num',  $json_decode->ctrl_number)
        ->where('ctrl_num_ext',  $json_decode->ctrl_ext)
        ->first();
        $encrypt_id = Helpers::encryptId($recon_rem_details->id);

        return response()->json([
            'data' => $recon_rem_details,
            'reqId' => $encrypt_id
        ]);
    }

    public function get_request(Request $request){
        if(!isset($request->access)){
            $requests = ReconRequest::where('id', 0)
            ->get();

            return DataTables::of($requests)
            ->make(true);
        }

        $category_access = DB::connection('mysql')->table('user_categories')
        ->select(DB::raw("CONCAT(department,'-',classification) AS ctrl"))
        ->whereIn('id', $request->access)
        ->get();
        $category_access = collect($category_access)->pluck('ctrl');

        $requests = ReconRequest::with([
            'recon_remarks',
            'recon_details'
        ])
        ->whereIn('status', $request->param)
        ->whereIn('ctrl_num', $category_access)
        ->whereNull('deleted_at')
        // ->orderBy('status', 'asc')
        ->distinct()
        ->get(['ctrl_num','ctrl_num_ext','status', 'request_type','po_num','recon_fkid']);

        return DataTables::of($requests)
        ->addColumn('action', function($requests){
            $result = "";
            $result .="<center>";
            $result .= "<button class='btn btn-sm btn-info text-light btnViewReconRequest' title='See more' 
                        data-ctrl='$requests->ctrl_num' 
                        data-ctrlExt='$requests->ctrl_num_ext'
                        data-status='$requests->status'
                        data-type='$requests->request_type'
                        >
                            <i class='fas fa-eye'></i>
                        </button>";
            $result .="</center>";
            return $result;
        })
        ->addColumn('req_status', function($requests){
            $result = "";
            $result .= "<center>";
            if($requests->request_type == 0){
                $result .= "<span class='badge rounded-pill text-bg-info'>For Addition</span>";
            }
            else if($requests->request_type == 1){
                $result .= "<span class='badge rounded-pill text-bg-info'>For Removal</span>";
            }
            else if($requests->request_type == 2){
                $result .= "<span class='badge rounded-pill text-bg-info'>For Edit</span>";
            }

            $result .= "<br>";

            if($requests->status == 0){

                $result .= "<span class='badge rounded-pill text-bg-warning'>For Approval</span>";
            }
            else if($requests->status == 1){
                $result .= "<span class='badge rounded-pill text-bg-success'>Approved</span>";
            }
            else if($requests->status == 2){
                $result .= "<span class='badge rounded-pill text-bg-danger'>Disapproved</span>";
                $result .= "<br>";
                $result .= "Remarks: $request->recon_remarks->approver_remarks";
            }


            $result .= "</center>";
            
            return $result;
        })
        ->addColumn('control', function($requests){
            return "$requests->ctrl_num-$requests->ctrl_num_ext";
        })
        ->addColumn('po', function($requests){
            $result = "";
            if($requests->po_num == null){
                $result .= $requests->recon_details->po_num;
            }
            else{
                $result .= $requests->po_num;
            }
            return $result;
        })
        ->rawColumns(['action', 'req_status', 'control', 'po'])
        ->make(true);
    }

    public function get_edit_request(Request $request){
        $recon_request = ReconRequest::with([
            'recon_details'
        ])
        ->whereNull('deleted_at')
        ->whereNotNull('recon_fkid')
        ->where('request_type', 2)
        ->get();

        return DataTables::of($recon_request)
        ->addColumn('action', function($recon_request){
            $result = "";
            $result = "";
            $result .="<center>";
            $result .= "<button class='btn btn-sm btn-info text-light btnViewDetails' title='See more' 
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

    public function update_request_data(Request $request){
        // return $request->all();
        DB::beginTransaction();
        
        try{
            $decrypt_id = Helpers::decryptId($request->req_id);

            $request_data = DB::connection('mysql')
            ->table('recon_requests')
            ->join('reconciliations', 'recon_requests.recon_fkid', '=', 'reconciliations.id')
            ->where('recon_requests.id', $decrypt_id)
            ->select('recon_requests.*', 'reconciliations.*')
            ->first();

            $eprpo_data = DB::connection('mysql_eprpo')
            ->select('
                SELECT 
                item.item_name as item_name1,
                item.long_description as long_description1,
                receiving_details.item_name,
                receiving_details.long_description,
                receiving_details.receiving_number,
                receiving_header.received_date,
                receiving_header.received_by,
                receiving_header.actual_delivery_date,
                supplier_name,
                item_code,
                receiving_details.unit_price,
                receiving_details.quantity_received,
                currency_code,
                reference_po_number,
                other_reference,
                unit_of_measure_id,
                unit_of_measure_code,
    
                (SELECT purchase_order_details.`classification_code` FROM `purchase_order_details` WHERE purchase_order_details.`order_number` = receiving_header.reference_po_number AND purchase_order_details.item_id= receiving_details.item_id LIMIT 0,1 ) as classification_code,
                (SELECT purchase_order_header.`po_remarks` FROM `purchase_order_header` WHERE purchase_order_header.`order_number` = receiving_header.reference_po_number LIMIT 0,1) as po_remarks,
                (SELECT purchase_order_details.`quantity` 
                FROM `purchase_order_details` 
                WHERE purchase_order_details.`order_number` = receiving_header.reference_po_number and 
                receiving_header.receiving_number = receiving_details.receiving_number and
                receiving_details.item_id = purchase_order_details.item_id LIMIT 0,1) as po_balance,
                (SELECT hold_invoice_remarks.`remarks` FROM `hold_invoice_remarks` WHERE hold_invoice_remarks.`reference_po_number` = receiving_header.reference_po_number LIMIT 0,1) as hold_remarks
    
                FROM receiving_header, receiving_details, item, supplier, currency, unit_of_measure
                WHERE receiving_header.receiving_number=receiving_details.receiving_number
                AND supplier.id=receiving_header.supplier_id 
                AND receiving_header.currency=currency.id
                AND item.id=receiving_details.item_id 
                AND item.unit_of_measure_id=unit_of_measure.id
                AND other_reference = "'.$request_data->invoice_no.'"
                AND item.item_name = "'.$request_data->prod_name.'"
                AND receiving_details.unit_price = "'.$request_data->unit_price.'"
            ');
    
            if(count($eprpo_data) == 1){
                $po_number      = ReconciliationController::getRefReqNum($eprpo_data[0]->reference_po_number);
                $allocation     = ReconciliationController::getAllocation($po_number);
                $po_date        = ReconciliationController::getPoDate($eprpo_data[0]->reference_po_number);
                $assigned_to    = ReconciliationController::getPoAssignedTo($eprpo_data[0]->reference_po_number);

                $item_name = $eprpo_data[0]->item_name == '' ? $eprpo_data[0]->item_name1 : $eprpo_data[0]->item_name;
                $long_description = $eprpo_data[0]->long_description == '' ? $eprpo_data[0]->long_description1 : $eprpo_data[0]->long_description;
                $po_balance = ( $eprpo_data[0]->po_balance - $eprpo_data[0]->quantity_received );
                $received_by    = ReconciliationController::getFullName($eprpo_data[0]->received_by);
                // $received_by    = "Christian Pagtalunan";
        
        
                Reconciliation::where('id', $request_data->id)
                ->update([
                    'recon_status'   => 0,
                    'po_date'        => $po_date,
                    'po_num'         => $eprpo_data[0]->reference_po_number,
                    'pr_num'         => $po_number,
                    'prod_code'      => $eprpo_data[0]->item_code,
                    'prod_name'      => $item_name,
                    'prod_desc'      => $long_description,
                    'supplier'       => $eprpo_data[0]->supplier_name,
                    'currency'       => $eprpo_data[0]->currency_code,
                    'uom'            => $eprpo_data[0]->unit_of_measure_code,
                    'unit_price'     => $eprpo_data[0]->unit_price,
                    'received_qty'   => $eprpo_data[0]->quantity_received,
                    'po_balance'     => $po_balance,
                    'received_date'  => $eprpo_data[0]->received_date,
                    'delivery_date'  => $eprpo_data[0]->actual_delivery_date,
                    'received_by'    => $received_by,
                    'invoice_no'     => $eprpo_data[0]->other_reference,
                    'rcv_no'         => $eprpo_data[0]->receiving_number,
                    'classification' => $eprpo_data[0]->classification_code,
                    'allocation'     => $allocation,
                    'po_remarks'     => $eprpo_data[0]->po_remarks,
                    'hold_remarks'   => $eprpo_data[0]->hold_remarks,
                ]);

                ReconRequest::where('id', $decrypt_id)
                ->update([
                    'status' => 1
                ]);

                DB::commit();
                return response()->json([
                    'result' => true,
                    'msg' => "Transaction Successful"
                ]);
            }
            else{
                return response()->json([
                    'result' => false,
                    'msg' => "Something went wrong! Please contact ISS.",
                    'debug' => "Data is multiple, please check the where in eprpo_data"
                ], 422);
            }


        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
       
        

    }
}
