<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesoreriaGarantia extends Model
{
    use HasFactory;
    protected $table = 'teso_garantia';
    public $timestamps = false;
}
