<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionUnidad extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_unidad';
    public $timestamps = false;
}
