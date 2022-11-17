<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_SolicitudMaterialDetalle extends Model
{
    use HasFactory;
    protected $table = 'p_solicitud_material_detalle';
    public $timestamps = false;
}
