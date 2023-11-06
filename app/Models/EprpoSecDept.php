<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EprpoSecDept extends Model
{
    use HasFactory;
    protected $table = "section_department";
    protected $connection = "mysql_eprpo";
}
