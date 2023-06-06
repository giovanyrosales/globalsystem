<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisicionAgrupada extends Model
{
    use HasFactory;
    protected $table = 'requisicion_agrupada';
    public $timestamps = false;
}
