<?php

namespace App\Http\Controllers;

use App\Models\CutOff;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function get_cutoff(Request $request){
        $cutoff_data = CutOff::all();

        return response()->json(['data' => $cutoff_data]);
    }
    public function save_cutoff(Request $request){
        // return $request->all();
        CutOff::insert([
            'day_from' => 1,
            'day_to' => 1,
            'cut_off' => 1,
            'day_email' => 1
        ]);
        return response()->json(['test' => 1]);
    }  
}
