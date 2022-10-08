<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaProyRestante extends Model
{
    use HasFactory;
    protected $table = 'cuentaproy_restante';
    public $timestamps = false;
}
