<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaSalidaManual extends Model
{
    protected $table = 'bodega_salidamanual';
    public $timestamps = false;
    use HasFactory;
}
