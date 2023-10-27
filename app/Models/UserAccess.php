<?php

namespace App\Models;

use App\Models\RapidxUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAccess extends Model
{
    use HasFactory;
    protected $table = "user_accesses";
    protected $connection = "mysql";
    protected $hidden = ['id'];
    
    public function rapidx_user_details(){
        return $this->hasOne(RapidxUser::class, 'id', 'rapidx_emp_no');
    }

}
