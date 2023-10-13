<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cn_ppts_user extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $connection = "mysql";
}
