<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesoreriaGarantiaEstados extends Model
{
    use HasFactory;
    protected $table = 'teso_garantias_estados';
    public $timestamps = false;
}
