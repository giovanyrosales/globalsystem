<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudITDatosTabla extends Model
{
    use HasFactory;
    protected $table = 'solicitudit_datostabla';
    public $timestamps = false;
}
