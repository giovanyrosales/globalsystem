<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudITDatos extends Model
{
    use HasFactory;
    protected $table = 'solicitudit_datos';
    public $timestamps = false;
}
