<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'almacenes';
    protected $primaryKey = 'clave';
    protected $fillable = ['nombre', 'id_company', 'id_sucursal', 'principal'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
}
