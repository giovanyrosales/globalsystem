<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaProyDetalle extends Model
{
    use HasFactory;
    protected $table = 'cuentaproy_detalle';
    public $timestamps = false;
}
