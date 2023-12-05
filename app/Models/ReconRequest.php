<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconRequest extends Model
{
    use HasFactory;
    protected $table = "recon_requests";
    protected $connection = "mysql";
}
