<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoMateriales extends Model
{
    use HasFactory;
    protected $table = 'materiales';
    public $timestamps = false;
}
