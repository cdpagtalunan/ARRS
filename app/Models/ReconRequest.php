<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconRequest extends Model
{
    use HasFactory;
    protected $table = "recon_requests";
    protected $connection = "mysql";

    public function recon_remarks(){
        return $this->hasOne(ReconRequestRemarks::class,'recon_request_ctrl_num_ext', 'ctrl_num_ext');
    }
}
