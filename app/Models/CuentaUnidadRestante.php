<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaUnidadRestante extends Model
{
    use HasFactory;
    protected $table = 'cuentaunidad_restante';
    public $timestamps = false;
}
