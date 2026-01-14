<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaGuardadoPDF extends Model
{
    protected $table = 'bodega_guardadopdf';
    public $timestamps = false;
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'id_pdepartamento',
        'descripcion',
        'numero_solicitud',
        'fecha_desde',
        'fecha_hasta',
        'fecha_generada',
        'monto_total',
    ];

}
