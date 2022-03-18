<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisicionDetalle extends Model
{
    use HasFactory;
    protected $table = 'requisicion_detalle';
    public $timestamps = false;
}
