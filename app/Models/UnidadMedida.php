<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'unidades_inventarios';
    protected $fillable = ['nombre', 'created_at'];
}
