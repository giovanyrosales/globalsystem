<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineaTrabajo extends Model
{
    use HasFactory;
    protected $table = 'linea';
    public $timestamps = false;
}
