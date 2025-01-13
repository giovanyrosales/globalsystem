<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    use HasFactory;
    protected $table = 'viajes';
    protected $fillable = ['nombre', 'acompanantes', 'lugar', 'fecha', 'subida', 'telefono'];
}
