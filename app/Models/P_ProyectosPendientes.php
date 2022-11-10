<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_ProyectosPendientes extends Model
{
    use HasFactory;
    protected $table = 'p_proyectos_pendientes';
    public $timestamps = false;
}
