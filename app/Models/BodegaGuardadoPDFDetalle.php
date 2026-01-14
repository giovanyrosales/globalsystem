<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaGuardadoPDFDetalle extends Model
{
    protected $table = 'bodega_guardadopdf_deta';
    public $timestamps = false;
    use HasFactory;

    protected $fillable = [
        'id_pdepartamento',
        'nombre',
        'unidad',
        'cantidad',
        'precio_unitario',
        'total',
    ];


}
