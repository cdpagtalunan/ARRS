<?php

namespace App\Models;

use App\Models\Reconciliation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReconRequest extends Model
{
    use HasFactory;
    protected $table = "recon_requests";
    protected $connection = "mysql";
    protected $fillable = ['status'];
    protected $hidden = ['id'];

    public function recon_remarks(){
        return $this->hasOne(ReconRequestRemarks::class,'recon_request_id', 'id');
    }

    public function recon_details(){
        return $this->hasOne(Reconciliation::class,'id', 'recon_fkid');

    }
}
