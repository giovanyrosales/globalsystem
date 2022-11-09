<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaUnidad extends Model
{
    use HasFactory;
    protected $table = 'cuenta_unidad';
    public $timestamps = false;
}
