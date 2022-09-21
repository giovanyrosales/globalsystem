<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaProyRetenido extends Model
{
    use HasFactory;
    protected $table = 'cuentaproy_retenido';
    public $timestamps = false;
}
