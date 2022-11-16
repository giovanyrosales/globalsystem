<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P_SolicitudMaterial extends Model
{
    use HasFactory;
    protected $table = 'p_solicitud_material';
    public $timestamps = false;
}
