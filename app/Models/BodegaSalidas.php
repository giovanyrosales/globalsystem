<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaSalidas extends Model
{
    protected $table = 'bodega_salidas';
    public $timestamps = false;
    use HasFactory;
}
