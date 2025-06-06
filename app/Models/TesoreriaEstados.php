<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesoreriaEstados extends Model
{
    use HasFactory;
    protected $table = 'teso_estados';
    public $timestamps = false;
}
