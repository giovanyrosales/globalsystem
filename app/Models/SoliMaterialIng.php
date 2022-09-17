<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoliMaterialIng extends Model
{
    // materiales solicitados por ingenieria para agregar al catalogo
    use HasFactory;
    protected $table = 'solicitar_material_ing';
    public $timestamps = false;
}
