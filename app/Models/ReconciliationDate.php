<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconciliationDate extends Model
{
    use HasFactory;
    protected $table = "reconciliation_dates";
    protected $connection = "mysql";
    protected $fillable = ['month', 'year', 'cutoff'];
}
