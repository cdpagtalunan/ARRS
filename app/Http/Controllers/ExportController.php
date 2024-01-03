<?php

namespace App\Http\Controllers;

use App\Exports\Recon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; 

class ExportController extends Controller
{
    public function export(Request $request){

        // $rapid_shipment_records = RapidShipmentRecord::selectRaw('*')
        // // ->groupBy('ControlNumber','ItemCode','LotNo')
        // ->where('ControlNumber',$invoice_number)
        // ->orderBy('id')
        // ->get();

        /* Old Code - 05032023 - Chris */
        // $rapid_shipment_records = RapidShipmentRecord::selectRaw('*, SUM(ShipoutQty) AS TotalShipoutQty')
        // ->groupBy('ControlNumber','ItemCode','LotNo')
        // ->where('ControlNumber',$invoice_number)
        // ->orderBy('id')
        // ->get();

        
        $date = date('Ymd',strtotime(NOW()));
        return Excel::download(new Recon($date), 'wbs-upload.xlsx');
    }

}
