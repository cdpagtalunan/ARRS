<?php

namespace App\Http\Controllers;

use App\Models\cn_ppts_user;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get_user(Request $request){
        return cn_ppts_user::all();
    }
}
