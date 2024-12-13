<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bodega_solicitud extends Model
{
    protected $table = 'bodega_solicitudes';
    public $timestamps = false;
    use HasFactory;
}
