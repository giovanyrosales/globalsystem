<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesoreriaTipoGarantia extends Model
{
    use HasFactory;
    protected $table = 'teso_tipo_garantia';
    public $timestamps = false;
}
