<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_MaterialesDetalle extends Model
{
    // MODULO: PRESUPUESTO UNIDADES
    use HasFactory;
    protected $table = 'p_materiales_detalle';
    public $timestamps = false;
}
