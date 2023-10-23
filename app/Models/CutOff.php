<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutOff extends Model
{
    use HasFactory;
    protected $table        = "cut_offs";
    protected $connection   = "mysql";
}
