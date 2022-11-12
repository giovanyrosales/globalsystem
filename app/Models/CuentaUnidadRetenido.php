<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaUnidadRetenido extends Model
{
    use HasFactory;
    protected $table = 'cuentaunidad_retenido';
    public $timestamps = false;
}
