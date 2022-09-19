<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresuSaldoRetenido extends Model
{
    use HasFactory;
    protected $table = 'presupuesto_saldo_retenido';
    public $timestamps = false;

}
