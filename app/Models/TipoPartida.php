<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPartida extends Model
{
    use HasFactory;
    protected $table = 'tipo_partida';
    public $timestamps = false;
}
