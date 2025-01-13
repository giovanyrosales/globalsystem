<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaEntradasDetalle extends Model
{
    protected $table = 'bodega_entradas_detalle';
    public $timestamps = false;
    use HasFactory;
}
