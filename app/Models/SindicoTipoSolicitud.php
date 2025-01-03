<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SindicoTipoSolicitud extends Model
{
    use HasFactory;
    protected $table = 'sindico_tiposolicitud';
    public $timestamps = false;
}
