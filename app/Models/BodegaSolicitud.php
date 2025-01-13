<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaSolicitud extends Model
{
    protected $table = 'bodega_solicitud';
    public $timestamps = false;
    use HasFactory;
}
