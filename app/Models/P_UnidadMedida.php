<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_UnidadMedida extends Model
{
    // MODULO: PRESUPUESTO UNIDADES
    use HasFactory;
    protected $table = 'p_unidadmedida';
    public $timestamps = false;
}
