<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaSolicitudDetalle extends Model
{
    protected $table = 'bodega_solicitud_detalle';
    public $timestamps = false;
    use HasFactory;
}
