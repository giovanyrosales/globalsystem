<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SindicoInmueble extends Model
{
    use HasFactory;
    protected $table = 'sindico_inmueble';
    public $timestamps = false;
}
