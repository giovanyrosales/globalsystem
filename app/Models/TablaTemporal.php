<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TablaTemporal extends Model
{
    use HasFactory;
    protected $table = 'tablatemporal';
    public $timestamps = false;
}
