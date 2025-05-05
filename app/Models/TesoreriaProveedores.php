<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesoreriaProveedores extends Model
{
    use HasFactory;
    protected $table = 'teso_proveedor';
    public $timestamps = false;
}
