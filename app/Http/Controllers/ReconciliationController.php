<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;
use App\Models\CutOff;
use Helpers;;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use App\Models\Reconciliation;
use Illuminate\Support\Facades\DB;

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

        $cut_off = CutOff::where('cut_off', $request->cutoff)
        ->whereNull('deleted_at')
        ->first();

        $mutable = Carbon::today();
        $date_to = $mutable->format('Y-m')."-".$cut_off->day_to;
        $date_from = $mutable->subMonth()->format('Y-m')."-".$cut_off->day_from;

        $categories = DB::connection('mysql')->table('user_categories')
        ->whereNull('deleted_at')
        ->get();

        $distinct_cat = collect($categories)->unique('classification')->flatten(0);
      
        // return $distinct_cat;

        $cat_array = array();
        for($x = 0; $x < count($distinct_cat); $x++){
            // $result .= $cat_details[$x]->classification."-".$cat_details[$x]->department;
            array_push($cat_array, $distinct_cat[$x]->classification);
        }
        // return $cat_array;
        $eprpo_test = DB::connection('mysql_eprpo')
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

        $collection = collect($eprpo_test)->whereIn('classification_code', $cat_array)->flatten(0);

        // return $collection;
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
                'hold_remarks'      => $collection[$i]->hold_remarks
            ]);
            // return;
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
            $allocation .= $query[0]->allocation.' ('.$query[0]->percentage.' %)<br>';
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
        // return $request->param['classification'];
        // return $request->param['department'];
        // $recon_data = DB::connection('mysql')->table('reconciliations')
        $recon_data = Reconciliation::whereNull('deleted_at')
        ->where('pr_num', 'LIKE', "%".$request->param['department']."%")
        ->where('classification', $request->param['classification'])
        ->select('*');

        return DataTables::of($recon_data)
        ->addColumn('action', function($recon_data){
            
            $encrypt_id = Helpers::encryptId($recon_data->id);

            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-primary btn-sm btnOpenReconDetails' data-id='$encrypt_id'><i class='fas fa-eye'></i></button>";
            $result .= "</center>";

            return $result;

        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function get_recon_details(Request $request){
        // return $request->all();
        $decrypt_id = Helpers::decryptId($request->recon);

        // return $decrypt_id;
        $recon_details = DB::connection('mysql')->table('reconciliations')
        ->where('id', $decrypt_id)
        ->select('*')
        ->first();

        return response()->json(['reconDetails' => $recon_details]);
    }
}
