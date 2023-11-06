<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EprpoClassification extends Model
{
    use HasFactory;
    protected $table = "classification_code";
    protected $connection = "mysql_eprpo";
}
