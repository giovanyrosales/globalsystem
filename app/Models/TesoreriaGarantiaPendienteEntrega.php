<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesoreriaGarantiaPendienteEntrega extends Model
{
    use HasFactory;
    protected $table = 'teso_garantia_pendi_entrega';
    public $timestamps = false;
}
