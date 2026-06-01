<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    public $timestamps = false;
    use HasFactory;

    public function lugar()
    {
        return $this->belongsTo(Lugares::class, 'id_lugares');
    }
}
