<?php

namespace App\Http\Controllers;

use Mail;
use Helpers;;
use DataTables;
use Carbon\Carbon;
use App\Models\CutOff;
use App\Models\UserAccess;
use App\Models\ReconRequest;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use App\Models\Reconciliation;
use App\Models\ReconciliationDate;
use Illuminate\Support\Facades\DB;
use App\Models\ReconRequestRemarks;

use App\Http\Requests\UserReconRequest;
use App\Http\Requests\RemoveReconRequest;

class ReconciliationController extends Controller
{
    public function get_category_of_user(Request $request){
        // for ($i=0; $i < count($request->access); $i++) { 
            
        // }
        $user_cat = UserCategory::whereIn('id', $request->access)
        ->get();
        return response()->json(['uAccess' => $user_cat]);
    }

    public function get_eprpo_data(Request $request){

        // $cut_off = CutOff::where('cut_off', $request->cutoff)
        // ->whereNull('deleted_at')
        // ->first();

        $mutable = Carbon::today();
        $current_year = $mutable->format('Y');
        $month_today = $mutable->format('m');

        // $date_to = $mutable->format('Y-m')."-".$cut_off->day_to;
        // // $month = $mutable->format('M'); // this is for reconciliation_dates

        // $date_from = $mutable->subMonth()->format('Y-m')."-".$cut_off->day_from;
        // $from_date = $mutable->format('M').". ".$cut_off->day_from; // this is for reconciliation_dates
        // $date_range = "$from_date - $to_date"; // this is for reconciliation_dates
        // return;
        if($request->cutoff == 1){
            $day_to = 15;
            $day_from = 26;
            $date_to = $mutable->format('Y-m')."-".$day_to;
            $date_from = $mutable->subMonth()->format('Y-m')."-".$day_from;

        }
        else{
            $day_to = 25;
            $day_from = 16;
            $date_to = $mutable->format('Y-m')."-".$day_to;
            $date_from = $mutable->format('Y-m')."-".$day_from;
        }

        $recon_date = ReconciliationDate::firstOrCreate(
            ['month' => $month_today],
            ['year' => $current_year, 'cutoff' => $request->cutoff ]
        );
        if($recon_date->cutoff != $request->cutoff){
            ReconciliationDate::where('id', $recon_date->id)
            ->update([
                'cutoff' => $request->cutoff
            ]);
        }
        $categories = DB::connection('mysql')->table('user_categories')
        ->whereNull('deleted_at')
        ->get();

        $distinct_cat = collect($categories)->unique('classification')->flatten(0);
      
        $cat_array = array();
        for($x = 0; $x < count($distinct_cat); $x++){
            // $result .= $cat_details[$x]->classification."-".$cat_details[$x]->department;
            array_push($cat_array, $distinct_cat[$x]->classification);
        }

        // return;
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
            AND date(received_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"
        ');

        $collection = collect($eprpo_data)->whereIn('classification_code', $cat_array)->flatten(0);


        for ($i=0; $i < count($collection); $i++) { 
            $po_number      = $this->getRefReqNum($collection[$i]->reference_po_number);
            $allocation     = $this->getAllocation($po_number);
            $po_date        = $this->getPoDate($collection[$i]->reference_po_number);
            $assigned_to    = $this->getPoAssignedTo($collection[$i]->reference_po_number);
            $received_by    = $this->getFullName($collection[$i]->received_by);
            $item_name = $collection[$i]->item_name == '' ? $collection[$i]->item_name1 : $collection[$i]->item_name;
            $long_description = $collection[$i]->long_description == '' ? $collection[$i]->long_description1 : $collection[$i]->long_description;
			$po_balance = ( $collection[$i]->po_balance - $collection[$i]->quantity_received );

            Reconciliation::insert([
                'po_date'           => $po_date,
                'po_num'            => $collection[$i]->reference_po_number,
                'pr_num'            => $po_number,
                'prod_code'         => $collection[$i]->item_code,
                'prod_name'         => $item_name,
                'prod_desc'         => $long_description,
                'supplier'          => $collection[$i]->supplier_name,
                'currency'          => $collection[$i]->currency_code,
                'uom'               => $collection[$i]->unit_of_measure_code,
                'unit_price'        => $collection[$i]->unit_price,
                'received_qty'      => $collection[$i]->quantity_received,
                'po_balance'        => $po_balance,
                // 'pic'               => 
                'received_date'     => $collection[$i]->received_date,
                // 'delivery_date'     => $collection[$i]->item_code,
                'delivery_date'     => $collection[$i]->actual_delivery_date,
                'received_by'       => $received_by,
                'invoice_no'        => $collection[$i]->other_reference,
                'rcv_no'            => $collection[$i]->receiving_number,
                'classification'    => $collection[$i]->classification_code,
                'allocation'        => $allocation,
                'po_remarks'        => $collection[$i]->po_remarks,
                'hold_remarks'      => $collection[$i]->hold_remarks,

                'recon_date_from'   => $date_from,
                'recon_date_to'     => $date_to
            ]);

        }
        return response()->json([
            'query1' => $collection,
            'date_from' => $date_from,
            'date_to' => $date_to
        ]);
    }

    function getRefReqNum($po){
        $result = 'No PR Reference';
        // $rs = query("select * from purchase_order_header where order_number='$po'");
        $query = DB::connection('mysql_eprpo')->table('purchase_order_header')
        // ->select("
        // select * from purchase_order_header where order_number='$po'
        // ");
        ->where('order_number', $po)
        ->select('*')
        ->first();

        if($query != null){
            $result = $query->reference_requisition_number;
        }
        // while($obj=fetch($rs)){
        //     $result = $obj->reference_requisition_number;
        // }
        return $result;
    }
    function getAllocation($prnumber){
        $allocation = '';
        // $rs = query("SELECT 
		// 			(SELECT section_department_name FROM section_department WHERE section_department.id = purchase_requisition_allocation.department_id LIMIT 0,1) as allocation, percentage 
		// 				FROM `purchase_requisition_allocation` 
		// 				WHERE purchase_requisition_allocation.requisition_number = '".$prnumber."'");
        // while($obj=fetch($rs)){
        //     $allocation .= $obj->allocation.' ('.$obj->percentage.' %)<br>';
        // }
        $query = DB::connection('mysql_eprpo')
        ->select("
            SELECT 
            (SELECT section_department_name FROM section_department WHERE section_department.id = purchase_requisition_allocation.department_id LIMIT 0,1) as allocation, percentage 
            FROM `purchase_requisition_allocation` 
            WHERE purchase_requisition_allocation.requisition_number = '$prnumber'
        ");
        if(count($query) > 0){
            $allocation .= $query[0]->allocation.' ('.$query[0]->percentage.' %)'.PHP_EOL;
        }
        return $allocation;
    }
    // function strUtfEncode($str){ // ! For further enhancement if may have encountered.
    //     $str = str_replace("'", '&#39', $str);
    //     $string = utf8_encode($str);
    //     return $string;
    // }

    function getPoDate($po){
        // $query = "select order_date from purchase_order_header where order_number='$po'";
        $query = DB::connection('mysql_eprpo')->table('purchase_order_header')
        ->where('order_number', $po)
        ->select('order_date')
        ->first();
        // $rs = query($query);
        $date = '';
        // while($obj=fetch($rs)){
        //     $date = $obj->order_date;
        //     $date = formatDate($date);
        // }
        if($query != null){
            $date = date('M d, Y', strtotime($query->order_date));

        }
        return $date;
    }

    // function convertWithDecimal($number,$decimalplaces){ // ! For further enhancement if may have encountered with decimal.
    //     if($decimalplaces==0&&is_numeric($number) && ((float) $number != (int) $number)){
    //         $temp = number_format($number,2,'.',',');
    //     }
    //     else{
    //         $temp = number_format($number,$decimalplaces,'.',',');
    //     }
       
    //     return $temp;
    // }

    function getPoAssignedTo($po){
        // $query = "select posted_by from purchase_order_header where order_number='$po'";
        $query = DB::connection('mysql_eprpo')->table('purchase_order_header')
        ->where('order_number', $po)
        ->join('user', 'purchase_order_header.posted_by', '=', 'user.id')
        ->select('*')
        ->first();
        // $rs = query($query);
        $fName = '';
        if($query != null){
            $fName = "$query->first_name $query->middle_name $query->last_name";
        }
        // while($obj=fetch($rs)){
        //     $staff = $obj->posted_by;
        //     $staff = getUserName($staff);
        // }
        return $fName;
    }

    function getFullName($id){
        $query = DB::connection('mysql_eprpo')->table('user')
        ->where('id', $id)
        ->select('*')
        ->first();

        $full_name = "$query->first_name $query->middle_name $query->last_name";
        return $full_name;
    }

    public function get_recon(Request $request){
        $dtFrom = 0000-00-00;
        $dtTo = 0000-00-00;
        if($request->cutoff_date != ""){
            $explode_date = explode('to', $request->cutoff_date);

            $dtFrom = Carbon::createFromFormat('m-d-Y', trim($explode_date[0]));
            $dtTo = Carbon::createFromFormat('m-d-Y', trim($explode_date[1]));
            $dtFrom = $dtFrom->format('Y-m-d');
            $dtTo = $dtTo->format('Y-m-d');
        }
        
        // $dtFrom = Carbon::create($explode_date[0])->format('Y-m-d');
        // $dtFrom = new Carbon($explode_date[0]);
        
      

        $recon_data = Reconciliation::whereNull('deleted_at')
        ->where('pr_num', 'LIKE', "%".$request->param['department']."%")
        ->where('classification', $request->param['classification'])
        ->where('recon_date_from', '>=', $dtFrom)
        ->where('recon_date_to', '<=', $dtTo)
        ->select('*');

        return DataTables::of($recon_data)
        ->addColumn('action', function($recon_data){
            
            $encrypt_id = Helpers::encryptId($recon_data->id);

            $result = "";
            // $result .= "<div class='d-flex flex-row'>";
            $result .= "<center>";
            $result .= "<button class='btn btn-primary btn-sm btnOpenReconDetails' data-id='$encrypt_id'><i class='fas fa-eye'></i></button>";
            if($recon_data->recon_status == 0){
                $result .= "<button class='btn btn-warning btn-sm btnReconcileData ml-1' data-id='$encrypt_id'><i class='fas fa-plus'></i></button>";

                $result .= "<button class='btn btn-danger btn-sm btnRemoveData ml-1' data-id='$encrypt_id'><i class='fas fa-xmark'></i></button>";
            }
            else if($recon_data->recon_status == 1){
                $result .= "<button class='btn btn-secondary btn-sm btnEditReconcileData ml-1' data-id='$encrypt_id'><i class='fas fa-pen-to-square'></i></button>";
            }
            $result .= "</center>";
            // $result .= "</div>";

            return $result;

        })
        ->addColumn('status', function($recon_data){
            $result = "";
            $result .= "<center>";
            if($recon_data->recon_status == 0){
                $result .= "<span class='badge rounded-pill text-bg-warning'>Pending</span>";
            }
            else if($recon_data->recon_status == 2){
                $result .= "<span class='badge rounded-pill text-bg-danger'>For Removal</span>";
                $result .= "<br><span class='badge rounded-pill text-bg-info'>Waiting for logistics<br> Approval</span>";
            }
            else{
                $result .= "<span class='badge rounded-pill text-bg-success'>Done</span>";
            }
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function get_recon_details(Request $request){
        // return $request->all();
        $decrypt_id = Helpers::decryptId($request->recon);

        // return $decrypt_id;
        // $recon_details = DB::connection('mysql')->table('reconciliations')
        // ->where('id', $decrypt_id)
        // ->select('*')
        // ->first();
        $recon_details = Reconciliation::where('id', $decrypt_id)->first();
        // $encrypt_id = Helpers::encryptId($request->recon);


        return response()->json(['reconDetails' => $recon_details, 'recon' => $request->recon]);
    }

    public function save_recon(UserReconRequest $request){
        $fields = $request->validated();
        DB::beginTransaction();

        $decrypt_id = Helpers::decryptId($request->recon);
        try{
            $selected_recon_data = DB::table('reconciliations')
            ->where('id', $decrypt_id)
            ->select('*')
            ->first();
            // return $decrypt_id;
            $user_recon_array = array(
                'recon_amount'          => $request->amount,
                'recon_invoice_no'      => $request->invoiceNum,
                'recon_received_qty'    => $request->receivedQty
            );
            if($selected_recon_data->recon_status == 0){
                $user_recon_array['recon_status'] = 1;

                Reconciliation::where('id', $decrypt_id)
                ->update($user_recon_array);
                // DB::table('reconciliations')
                // ->where('id', $decrypt_id)
                // ->update($user_recon_array);

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Successfully reconcile this data.']);
            }
            else{
                // return "edit";
                Reconciliation::where('id', $decrypt_id)
                ->update($user_recon_array);

                DB::commit();
                return response()->json(['result' => 1, 'msg' => 'Successfully Edited Data.']);
            }
        }
        catch(Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function request_remove_recon(RemoveReconRequest $request){
       
        $request->validated();

        DB::beginTransaction();
        try{
            $recon_control = ReconRequest::orderBy('ctrl_num_ext', 'DESC')->first();
            $control_ext = 0;
            if(isset($recon_control)){
                $control_ext = $recon_control->ctrl_num_ext + 1;
            }
            else{
                $control_ext = 1;
            }
            $control = $request->extraParams['department'] . "-" . $request->extraParams['classification'];


            $decrypt_id = Helpers::decryptId($request->reconId);

            // * For Email
            $recon_details = Reconciliation::where('id', $decrypt_id)
            ->first();
            $data = array(
                'type' => "Removal",
                'control' => $control."-".$control_ext, // change to $control-$control_ext
                'remove_request_data' => $recon_details,
                'user_remarks' => $request->reasons,
                // 'cutoff_date_req' => $request->cutoff_date,
                'requestor' => $_SESSION['rapidx_name']
            );
            // return $data['remove_request_data'];
            $get_cat = UserCategory::where('classification', $request->extraParams['classification'])
            ->where('department', $request->extraParams['department'])
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
            
            // return $user_email;
            
            Mail::send('mail.user_request', $data, function($message) use ($request, $admin_email, $user_email){
                $message->to($admin_email);
                $message->cc($user_email);
                $message->bcc('cpagtalunan@pricon.ph');
                $message->subject("Reconciliation Request For Removal <ARRS Generated Email Do Not Reply>");
            });
            // * End Email
            
            ReconRequest::insert([
                'request_type'      => 1,
                'recon_fkid'        => $decrypt_id,
                'ctrl_num'          => $control,
                'ctrl_num_ext'      => $control_ext,
                'created_by'        => $_SESSION['rapidx_user_id']
            ]);

            Reconciliation::where('id', $decrypt_id)
            ->update([
                'recon_remove_remarks' => $request->reasons,
                'recon_status' => 2
            ]);


            DB::commit();

            return response()->json([
                'result' => 1,
                'msg'    => 'Successfully Requested'
            ]);
            
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function get_recon_for_add(Request $request){
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
            AND reference_po_number = "'.$request->po_number.'"
        ');

        for ($i=0; $i < count($eprpo_data) ; $i++) {
            // $test = $eprpo_data[$i]->item_name1;
            // return $test;

            $eprpo_data[$i]->po_number = $this->getRefReqNum($eprpo_data[$i]->reference_po_number);
            $eprpo_data[$i]->po_date = $this->getPoDate($eprpo_data[$i]->reference_po_number);
            $eprpo_data[$i]->received_by = $this->getPoAssignedTo($eprpo_data[$i]->reference_po_number);
            $eprpo_data[$i]->description = $eprpo_data[$i]->long_description == '' ? $eprpo_data[$i]->long_description1 : $eprpo_data[$i]->long_description;
            $eprpo_data[$i]->allocation     = $this->getAllocation($eprpo_data[$i]->po_number);

            // return $eprpo_data[$i];
        }
        // $collection = collect($eprpo_data)->where('classification_code', $request->param['classification'])
        // ->flatten(0);

        $collection = collect($eprpo_data)->filter(
            function($item) use ($request){
                return ($item->classification_code == $request->param['classification'] && str_contains($item->po_number, $request->param['department']));
            })
        ->flatten(0);

        // ->where('pr_num', 'LIKE', "%".$request->param['department']."%")
        // ->where('classification', $request->param['classification'])

        

        return DataTables::of($collection)
        ->addColumn('action', function($collection){
            $result = "";
            $result .= "<input type='checkbox' class='checkedRecon' data-eprpo='".json_encode($collection)."' name='checking[]'>";
            return $result;
        })
        // ->addColumn('po_number', function($collection){
        //     return $this->getRefReqNum($collection->reference_po_number);
        // })
        // // ->addColumn('allocation', function($collection){
        // //     return $this->getAllocation($po_number);
        // // })
        // ->addColumn('po_date', function($collection){
        //     return $this->getPoAssignedTo($collection->reference_po_number);
        // })
        // ->addColumn('received_by', function($collection){
        //     return $this->getPoAssignedTo($collection->reference_po_number);
        // })
        // ->addColumn('description', function($collection){
        //     $long_description = "";
        //     $long_description = $collection->long_description == '' ? $collection->long_description1 : $collection->long_description;
        //     return $long_description;
        //     // return $this->getPoAssignedTo($collection->reference_po_number);
        // })
        ->rawColumns(['action'])
        ->make(true);

    }

    public function request_for_addition(Request $request){
        date_default_timezone_set('Asia/Manila');

        $exploded_cutoff = explode('to', $request->cutoff_date);

        $from = Carbon::createFromFormat('m-d-Y', trim($exploded_cutoff[0]));
        $req_from = $from->format('Y-m-d');
        $to = Carbon::createFromFormat('m-d-Y',  trim($exploded_cutoff[1]));
        $req_to = $to->format('Y-m-d');

        // return;
        $recon_control = ReconRequest::orderBy('ctrl_num_ext', 'DESC')->first();
        
        $control = $request->addEprpoData['reconClassification']['department'] . "-" . $request->addEprpoData['reconClassification']['classification'];

        DB::beginTransaction();
        try{
            if(isset($recon_control)){
                $control_ext = $recon_control->ctrl_num_ext + 1;
                
                ReconRequestRemarks::insert([
                    'recon_request_ctrl_num' => $control,
                    'recon_request_ctrl_num_ext' => $control_ext,
                    'remarks' => $request->addEprpoData['userRemarks']
                ]);
                for ($i=0; $i < count($request->addEprpoData['data']); $i++) { 
                    $jsn_decoded_recon_req = json_decode($request->addEprpoData['data'][$i]);
                    // return $jsn_decoded_recon_req;
                    ReconRequest::insert([
                        'ctrl_num'           => $control,
                        'ctrl_num_ext'       => $control_ext,
                        'po_date'            => $jsn_decoded_recon_req->po_date,
                        'po_num'             => $jsn_decoded_recon_req->reference_po_number,
                        'pr_num'             => $jsn_decoded_recon_req->po_number,
                        'prod_code'          => $jsn_decoded_recon_req->item_code,
                        'prod_name'          => $jsn_decoded_recon_req->item_name,
                        'prod_desc'          => $jsn_decoded_recon_req->long_description,
                        'supplier'           => $jsn_decoded_recon_req->supplier_name,
                        'currency'           => $jsn_decoded_recon_req->currency_code,
                        'uom'                => $jsn_decoded_recon_req->unit_of_measure_code,
                        'unit_price'         => $jsn_decoded_recon_req->unit_price,
                        'received_qty'       => $jsn_decoded_recon_req->quantity_received,
                        'po_balance'         => $jsn_decoded_recon_req->po_balance,
                        'pic'                => $jsn_decoded_recon_req->item_code,
                        'received_date'      => $jsn_decoded_recon_req->received_date,
                        'delivery_date'      => $jsn_decoded_recon_req->actual_delivery_date,
                        'received_by'        => $jsn_decoded_recon_req->received_by,
                        'invoice_no'         => $jsn_decoded_recon_req->other_reference,
                        'rcv_no'             => $jsn_decoded_recon_req->receiving_number,
                        'classification'     => $jsn_decoded_recon_req->classification_code,
                        'allocation'         => $jsn_decoded_recon_req->allocation,
                        'po_remarks'         => $jsn_decoded_recon_req->po_remarks,
                        'hold_remarks'       => $jsn_decoded_recon_req->hold_remarks,
                        'recon_date_from'    => $req_from,
                        'recon_date_to'      => $req_to,
                        'created_by'         => $_SESSION['rapidx_user_id'],
                        'created_at'         => NOW()
                    ]);
                    DB::commit();
                }
                return response()->json([
                    'result' => 1,
                    'msg' => 'Successfully Requested'
                ]);
            }
            else{
                $control_ext = 1;
                ReconRequestRemarks::insert([
                    'recon_request_ctrl_num' => $control,
                    'recon_request_ctrl_num_ext' => $control_ext,
                    'remarks' => $request->addEprpoData['userRemarks']
                ]);
                for ($i=0; $i < count($request->addEprpoData['data']); $i++) { 
                    $jsn_decoded_recon_req = json_decode( $request->addEprpoData['data'][$i]);
                    // return $jsn_decoded_recon_req;
                    ReconRequest::insert([
                        'ctrl_num'           => $control,
                        'ctrl_num_ext'       => $control_ext,
                        'po_date'            => $jsn_decoded_recon_req->po_date,
                        'po_num'             => $jsn_decoded_recon_req->reference_po_number,
                        'pr_num'             => $jsn_decoded_recon_req->po_number,
                        'prod_code'          => $jsn_decoded_recon_req->item_code,
                        'prod_name'          => $jsn_decoded_recon_req->item_name,
                        'prod_desc'          => $jsn_decoded_recon_req->long_description,
                        'supplier'           => $jsn_decoded_recon_req->supplier_name,
                        'currency'           => $jsn_decoded_recon_req->currency_code,
                        'uom'                => $jsn_decoded_recon_req->unit_of_measure_code,
                        'unit_price'         => $jsn_decoded_recon_req->unit_price,
                        'received_qty'       => $jsn_decoded_recon_req->quantity_received,
                        'po_balance'         => $jsn_decoded_recon_req->po_balance,
                        'pic'                => $jsn_decoded_recon_req->item_code,
                        'received_date'      => $jsn_decoded_recon_req->received_date,
                        'delivery_date'      => $jsn_decoded_recon_req->actual_delivery_date,
                        'received_by'        => $jsn_decoded_recon_req->received_by,
                        'invoice_no'         => $jsn_decoded_recon_req->other_reference,
                        'rcv_no'             => $jsn_decoded_recon_req->receiving_number,
                        'classification'     => $jsn_decoded_recon_req->classification_code,
                        'allocation'         => $jsn_decoded_recon_req->allocation,
                        'po_remarks'         => $jsn_decoded_recon_req->po_remarks,
                        'hold_remarks'       => $jsn_decoded_recon_req->hold_remarks,
                        'recon_date_from'    => $req_from,
                        'recon_date_to'      => $req_to,
                        'created_by'         => $_SESSION['rapidx_user_id'],
                        'created_at'         => NOW()

                    ]);
                    DB::commit();
                }
                return response()->json([
                    'result' => 1,
                    'msg' => 'Successfully Requested'
                ]);
            }

            // * Mail
            $data = array(
                'type' => "Addition",
                'control' => $control."-".$control_ext, // change to $control-$control_ext
                'add_request_data' => $request->addEprpoData['data'],
                'user_remarks' => $request->addEprpoData['userRemarks'],
                'cutoff_date_req' => $request->cutoff_date,
                'requestor' => $_SESSION['rapidx_name']
            );

            $get_cat = UserCategory::where('classification', $request->addEprpoData['reconClassification']['classification'])
            ->where('department', $request->addEprpoData['reconClassification']['department'])
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
            
            Mail::send('mail.user_request', $data, function($message) use ($request, $admin_email, $user_email){
                $message->to($admin_email);
                $message->cc($user_email);
                $message->bcc('cpagtalunan@pricon.ph');
                $message->subject("Reconciliation Request For Approval <ARRS Generated Email Do Not Reply>");
            });

            return response()->json([
                'result' => 1,
                'msg' => 'Successfully Requested'
            ]);
        }
        catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function get_recon_dates(Request $request){
        $recon_dates = ReconciliationDate::whereNull('deleted_at')->orderBy('id', 'desc')->get();
        $cutoff_date_array = array();
        for ($i=0; $i < count($recon_dates); $i++) {
            // return $recon_dates[$i];
            $month = $recon_dates[$i]['month'];
            $year = $recon_dates[$i]['year'];
            $day = 0;
            if($recon_dates[$i]['cutoff'] == 1){
                $day = 15;
            }
            else{
                $day = 26;
            }
            $dtTo = Carbon::create($year, $month, $day)->format('m-d-Y');
            $dtFrom = Carbon::create($year, $month, 26);
            $dtSubMonth = $dtFrom->subMonth()->format('m-d-Y');
            $cutoff = $dtSubMonth." to ".$dtTo;
            array_push($cutoff_date_array, $cutoff);
        }
        return $cutoff_date_array;
    }
}
