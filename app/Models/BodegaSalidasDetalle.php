<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaSalidasDetalle extends Model
{
    protected $table = 'bodega_salidas_detalle';
    public $timestamps = false;
    use HasFactory;
}
