<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_PresupUnidad extends Model
{
    // MODULO: PRESUPUESTO UNIDADES
    use HasFactory;
    protected $table = 'p_presup_unidad';
    public $timestamps = false;
}
