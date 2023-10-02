<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionConsolidador extends Model
{
    use HasFactory;
    protected $table = 'informacion_consolidador';
    public $timestamps = false;
}
