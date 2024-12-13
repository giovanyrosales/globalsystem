<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bodega_material extends Model
{
    protected $table = 'bodega_materiales';
    public $timestamps = false;
    use HasFactory;
}
