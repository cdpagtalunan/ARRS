<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\cn_ppts_user;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function get_user(Request $request){
        $test = cn_ppts_user::where('status', 1)
        ->select(DB::raw('id, CONCAT(`firstname`, " ", `lastname`) as fullname'))
        ->get();
        
        // return $test;
        // return DataTables::of($test)
        // ->addColumn('action', function($test){
        //     $result = "";

        //     $result .= '<button class="btn btn-success">Test</button>';

        //     return $result;
        // })
        // ->rawColumns(['action'])
        // ->make('true');
        return response()->json(['data'=>$test]);
    }
}
