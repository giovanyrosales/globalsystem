<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodegaMateriales extends Model
{
    protected $table = 'bodega_materiales';
    public $timestamps = false;
    use HasFactory;
}
